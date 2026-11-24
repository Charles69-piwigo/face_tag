<?php
/**
 * Extraction XMP et détection des cadres de visage (lecture seule).
 *
 * Depuis 2.5a.1, les cadres sont lus région par région avec un analyseur XML : le nom et
 * la zone d'un cadre sont pris dans la MÊME région. Jusqu'à 2.5a, les noms et les zones
 * étaient relevés séparément par regex sur tout le document puis appariés par rang : un
 * nom Microsoft sans rectangle, une région sans nom (visage non identifié), une région
 * BarCode/Focus ou une zone écrite en attributs décalaient les noms ou faisaient
 * disparaître des cadres.
 *
 * Copie autonome (non partagée) de la lecture de face_tag_editor 2.6
 * (lib/editor_xmp_ex.php, facetag_editor_faces_from_xmp()), même règle que tag_manager
 * (tagmanager_parse_faces) : un cadre = région de type Face, avec un nom ET une zone.
 * Différence voulue : pour MPReg, on renvoie le coin supérieur gauche (comme avant),
 * facetag.js fait la conversion d'après 'methode'.
 */

if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

if (!defined('FACETAG_LIB_NS_RDF')) {
    define('FACETAG_LIB_NS_RDF', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
    define('FACETAG_LIB_NS_MWGRS', 'http://www.metadataworkinggroup.com/schemas/regions/');
    define('FACETAG_LIB_NS_AREA', 'http://ns.adobe.com/xmp/sType/Area#');
    define('FACETAG_LIB_NS_MPRI', 'http://ns.microsoft.com/photo/1.2/t/RegionInfo#');
    define('FACETAG_LIB_NS_MPREG', 'http://ns.microsoft.com/photo/1.2/t/Region#');
}

/**
 * Paquets XMP d'un fichier JPEG : segment principal et éventuel « Extended XMP »
 * (paquet de plus de 64 Ko découpé en segments APP1 d'extension, recollés ici).
 *
 * @param string $filepath Chemin vers le fichier JPEG
 * @return array|null array('main' => string, 'ext' => string|null), ou null sans XMP
 */
function facetag_lib_xmp_segment_parts($filepath) {
    $fp = @fopen($filepath, 'rb');
    if (!$fp) {
        return null;
    }

    // Vérifier SOI (Start Of Image)
    $soi = fread($fp, 2);
    if ($soi !== "\xFF\xD8") {
        fclose($fp);
        return null;
    }

    $xmp_id = "http://ns.adobe.com/xap/1.0/\0";
    $ext_id = "http://ns.adobe.com/xmp/extension/\0";

    $main_xmp = null;
    $extended_chunks = array(); // offset => octets du morceau

    // Parcourir tous les segments : les segments d'extension sont plus loin
    while (!feof($fp)) {
        $marker = fread($fp, 2);
        if ($marker === false || strlen($marker) !== 2) break;
        if ($marker[0] !== "\xFF") break;

        $marker_type = ord($marker[1]);

        // SOS ou EOI = fin
        if ($marker_type === 0xDA || $marker_type === 0xD9) break;

        // Marqueurs sans longueur
        if ($marker_type === 0x01 || ($marker_type >= 0xD0 && $marker_type <= 0xD7)) {
            continue;
        }

        // Lire la longueur du segment
        $length_bytes = fread($fp, 2);
        if (strlen($length_bytes) !== 2) break;
        $length = unpack('n', $length_bytes)[1];
        $data_length = $length - 2;
        if ($data_length <= 0) break;

        // Lire les données
        $data = fread($fp, $data_length);
        if (strlen($data) !== $data_length) break;

        if ($marker_type === 0xE1) {
            if ($main_xmp === null && substr($data, 0, strlen($xmp_id)) === $xmp_id) {
                $main_xmp = substr($data, strlen($xmp_id));
            } elseif (substr($data, 0, strlen($ext_id)) === $ext_id) {
                // Après l'identifiant : GUID (32 octets) + longueur totale (4) + offset (4)
                $rest = substr($data, strlen($ext_id));
                if (strlen($rest) > 40) {
                    $offset = unpack('N', substr($rest, 36, 4))[1];
                    $extended_chunks[$offset] = substr($rest, 40);
                }
            }
        }
    }

    fclose($fp);

    if ($main_xmp === null) {
        return null;
    }

    $ext_xmp = null;
    if (!empty($extended_chunks)) {
        ksort($extended_chunks);
        $ext_xmp = implode('', $extended_chunks);
    }

    return array('main' => $main_xmp, 'ext' => $ext_xmp);
}

/**
 * Segment XMP principal d'un fichier JPEG (conservé pour compatibilité).
 *
 * @param string $filepath Chemin vers le fichier JPEG
 * @return string|null Le contenu XML XMP ou null si non trouvé
 */
function facetag_lib_extract_xmp_segment($filepath) {
    $parts = facetag_lib_xmp_segment_parts($filepath);
    return ($parts === null) ? null : $parts['main'];
}

/**
 * Charge un paquet XMP dans un DOMDocument (octets nuls de fin retirés ; si le texte complet
 * est refusé, nouvel essai sur le seul bloc x:xmpmeta ou rdf:RDF).
 *
 * @param string|null $xml
 * @param bool $had_errors mis à true si libxml a signalé une erreur, même non bloquante
 *             (ex. préfixe non déclaré : le document se charge, mais ses balises n'ont plus
 *             d'espace de noms et les régions concernées deviennent introuvables)
 * @return DOMDocument|null null si le XML est invalide
 */
function facetag_lib_xmp_dom($xml, &$had_errors = false) {
    $had_errors = false;
    if (!is_string($xml) || !class_exists('DOMDocument')) {
        return null;
    }
    $xml = rtrim($xml, "\0 \t\r\n");
    if ($xml === '') {
        return null;
    }
    $prev = libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    $ok = $dom->loadXML($xml, LIBXML_NONET);
    if (!$ok && preg_match('~<x:xmpmeta\b.*</x:xmpmeta>|<rdf:RDF\b.*</rdf:RDF>~s', $xml, $m)) {
        $dom = new DOMDocument();
        $ok = $dom->loadXML($m[0], LIBXML_NONET);
    }
    $had_errors = (count(libxml_get_errors()) > 0);
    libxml_clear_errors();
    libxml_use_internal_errors($prev);
    return $ok ? $dom : null;
}

/**
 * Éléments qui portent les propriétés d'une ressource RDF : l'élément lui-même
 * (rdf:parseType="Resource" ou propriétés en attributs) et ses rdf:Description enfants.
 *
 * @return DOMElement[]
 */
function facetag_lib_rdf_holders($node) {
    $holders = array($node);
    foreach ($node->childNodes as $c) {
        if ($c instanceof DOMElement && $c->namespaceURI === FACETAG_LIB_NS_RDF && $c->localName === 'Description') {
            $holders[] = $c;
        }
    }
    return $holders;
}

/**
 * Valeur d'une propriété simple d'une ressource RDF, en attribut ou en balise,
 * sur l'élément ou sur un rdf:Description enfant.
 *
 * @return string|null null si absente
 */
function facetag_lib_rdf_prop($node, $ns, $local) {
    if (!($node instanceof DOMElement)) {
        return null;
    }
    foreach (facetag_lib_rdf_holders($node) as $h) {
        if ($h->hasAttributeNS($ns, $local)) {
            return $h->getAttributeNS($ns, $local);
        }
        foreach ($h->childNodes as $c) {
            if ($c instanceof DOMElement && $c->namespaceURI === $ns && $c->localName === $local) {
                return $c->textContent;
            }
        }
    }
    return null;
}

/**
 * Élément d'une propriété structurée (ex. mwg-rs:Area), sur l'élément ou sur un rdf:Description enfant.
 *
 * @return DOMElement|null
 */
function facetag_lib_rdf_struct($node, $ns, $local) {
    if (!($node instanceof DOMElement)) {
        return null;
    }
    foreach (facetag_lib_rdf_holders($node) as $h) {
        foreach ($h->childNodes as $c) {
            if ($c instanceof DOMElement && $c->namespaceURI === $ns && $c->localName === $local) {
                return $c;
            }
        }
    }
    return null;
}

/**
 * Cadre porté par une région MWG, ou null : type autre que Face (BarCode, Focus, Pet…),
 * pas de nom (visage non identifié), zone absente, incomplète ou pas normalisée.
 * Coordonnées MWG : centre de la zone.
 *
 * @return array|null
 */
function facetag_lib_mwg_region_face($li) {
    $type = facetag_lib_rdf_prop($li, FACETAG_LIB_NS_MWGRS, 'Type');
    if ($type !== null && strtolower(trim($type)) !== 'face') {
        return null;
    }
    $name = trim((string)facetag_lib_rdf_prop($li, FACETAG_LIB_NS_MWGRS, 'Name'));
    if ($name === '') {
        return null;
    }
    $area = facetag_lib_rdf_struct($li, FACETAG_LIB_NS_MWGRS, 'Area');
    if ($area === null) {
        return null;
    }
    $unit = facetag_lib_rdf_prop($area, FACETAG_LIB_NS_AREA, 'unit');
    if ($unit !== null && strtolower(trim($unit)) !== 'normalized') {
        return null;
    }
    $v = array();
    foreach (array('x', 'y', 'w', 'h') as $k) {
        $raw = facetag_lib_rdf_prop($area, FACETAG_LIB_NS_AREA, $k);
        if ($raw === null || !is_numeric(trim($raw))) {
            return null;
        }
        $v[$k] = floatval(trim($raw));
    }
    if ($v['w'] <= 0 || $v['h'] <= 0) {
        return null;
    }
    return array('nom' => $name, 'x' => $v['x'], 'y' => $v['y'], 'w' => $v['w'], 'h' => $v['h'], 'methode' => 'MWG-RS');
}

/**
 * Propriété d'une région Microsoft. Certains fichiers (digiKam / ExifTool 13.41) lient le
 * préfixe MPReg à l'URI de MPRI (…/t/RegionInfo#) : ExifTool les lit quand même, on fait de même.
 *
 * @return string|null
 */
function facetag_lib_mp_prop($li, $local) {
    $v = facetag_lib_rdf_prop($li, FACETAG_LIB_NS_MPREG, $local);
    return ($v !== null) ? $v : facetag_lib_rdf_prop($li, FACETAG_LIB_NS_MPRI, $local);
}

/**
 * Cadre porté par une région Microsoft (nom + rectangle « x, y, w, h »), ou null
 * (un nom sans rectangle n'est pas un cadre). Coordonnées : coin supérieur gauche.
 *
 * @return array|null
 */
function facetag_lib_mp_region_face($li) {
    $name = trim((string)facetag_lib_mp_prop($li, 'PersonDisplayName'));
    if ($name === '') {
        return null;
    }
    $rect = facetag_lib_mp_prop($li, 'Rectangle');
    if ($rect === null) {
        return null;
    }
    $coords = array_map('trim', explode(',', $rect));
    if (count($coords) !== 4) {
        return null;
    }
    foreach ($coords as $c) {
        if (!is_numeric($c)) {
            return null;
        }
    }
    $w = floatval($coords[2]);
    $h = floatval($coords[3]);
    if ($w <= 0 || $h <= 0) {
        return null;
    }
    return array(
        'nom' => $name,
        'x' => floatval($coords[0]),
        'y' => floatval($coords[1]),
        'w' => $w,
        'h' => $h,
        'methode' => 'MPReg'
    );
}

/**
 * Cadres d'un paquet XMP, lus région par région.
 *
 * @param string $xml
 * @return array|null array('mwg' => [...], 'mp' => [...], 'errors' => bool), ou null si
 *                    le XML est invalide
 */
function facetag_lib_faces_from_dom($xml) {
    $had_errors = false;
    $dom = facetag_lib_xmp_dom($xml, $had_errors);
    if ($dom === null) {
        return null;
    }
    $xp = new DOMXPath($dom);
    $xp->registerNamespace('rdf', FACETAG_LIB_NS_RDF);
    $xp->registerNamespace('mwgrs', FACETAG_LIB_NS_MWGRS);
    $xp->registerNamespace('mpri', FACETAG_LIB_NS_MPRI);

    $out = array('mwg' => array(), 'mp' => array(), 'errors' => $had_errors);
    foreach ($xp->query('//mwgrs:RegionList/rdf:Bag/rdf:li | //mwgrs:RegionList/rdf:Seq/rdf:li') as $li) {
        $face = facetag_lib_mwg_region_face($li);
        if ($face !== null) {
            $out['mwg'][] = $face;
        }
    }
    foreach ($xp->query('//mpri:Regions/rdf:Bag/rdf:li | //mpri:Regions/rdf:Seq/rdf:li') as $li) {
        $face = facetag_lib_mp_region_face($li);
        if ($face !== null) {
            $out['mp'][] = $face;
        }
    }
    return $out;
}

/**
 * Ancienne lecture par regex (jusqu'à 2.5a), gardée en SECOURS seulement pour un paquet
 * que l'analyseur XML refuse (XMP invalide, ex. préfixe non déclaré) ou si l'extension
 * DOM de PHP est absente : mieux vaut un cadre peut-être décalé que plus aucun cadre.
 *
 * @param string $xmp
 * @return array liste de cadres (nom, x, y, w, h, methode)
 */
function facetag_lib_faces_from_regex($xmp) {
    $faces = array();

    // === FORMAT MPReg (Microsoft Photo Region) ===
    if (preg_match_all('/<MPReg:PersonDisplayName>([^<]+)<\/MPReg:PersonDisplayName>/i', $xmp, $names_mpreg) &&
        preg_match_all('/<MPReg:Rectangle>([^<]+)<\/MPReg:Rectangle>/i', $xmp, $rects_mpreg)) {
        $count = min(count($names_mpreg[1]), count($rects_mpreg[1]));
        for ($i = 0; $i < $count; $i++) {
            $coords = array_map('trim', explode(',', $rects_mpreg[1][$i]));
            if (count($coords) === 4) {
                $faces[] = array(
                    'nom' => trim($names_mpreg[1][$i]),
                    'x' => floatval($coords[0]),
                    'y' => floatval($coords[1]),
                    'w' => floatval($coords[2]),
                    'h' => floatval($coords[3]),
                    'methode' => 'MPReg'
                );
            }
        }
    }

    // === FORMAT MWG-RS avec BALISES (Digikam) puis ATTRIBUTS (Lightroom) ===
    $patterns = array(
        array('/<mwg-rs:Name>([^<]+)<\/mwg-rs:Name>/i', '/<stArea:%s>([^<]+)<\/stArea:%s>/i'),
        array('/mwg-rs:Name="([^"]+)"/i', '/stArea:%s="([^"]+)"/i'),
    );
    foreach ($patterns as $p) {
        if (!preg_match_all($p[0], $xmp, $names)) {
            continue;
        }
        $vals = array();
        foreach (array('x', 'y', 'w', 'h') as $k) {
            preg_match_all(sprintf($p[1], $k, $k), $xmp, $m);
            $vals[$k] = $m[1];
        }
        $count = min(count($names[1]), count($vals['x']), count($vals['y']), count($vals['w']), count($vals['h']));
        for ($i = 0; $i < $count; $i++) {
            $nom = trim($names[1][$i]);
            $exists = false;
            foreach ($faces as $face) {
                if ($face['nom'] === $nom) {
                    $exists = true;
                    break;
                }
            }
            if (!$exists) {
                $faces[] = array(
                    'nom' => $nom,
                    'x' => floatval($vals['x'][$i]),
                    'y' => floatval($vals['y'][$i]),
                    'w' => floatval($vals['w'][$i]),
                    'h' => floatval($vals['h'][$i]),
                    'methode' => 'MWG-RS'
                );
            }
        }
    }

    return $faces;
}

/**
 * Cadres de visage d'un ou plusieurs paquets XMP (principal, extension).
 * Les régions MWG sont prises d'abord ; une région Microsoft n'est ajoutée que si aucune
 * région MWG de même nom ne lui correspond encore (digiKam écrit les deux formats pour le
 * même visage). Deux visages de même nom restent deux cadres.
 *
 * @param array $xmps chaînes XMP (null ignoré)
 * @return array liste de cadres (nom, x, y, w, h, methode)
 */
function facetag_lib_faces_from_xmp($xmps) {
    $mwg = array();
    $mp = array();
    $fallback = array();
    foreach ($xmps as $xml) {
        if (!is_string($xml) || (strpos($xml, 'regions/') === false && strpos($xml, 'Region#') === false
            && stripos($xml, 'MPReg:') === false && stripos($xml, 'mwg-rs:') === false)) {
            continue; // ni MWG ni Microsoft
        }
        $read = facetag_lib_faces_from_dom($xml);
        if ($read === null) {
            $fallback = array_merge($fallback, facetag_lib_faces_from_regex($xml));
            continue;
        }
        if ($read['errors']) {
            // XMP abîmé mais chargé : si l'ancienne lecture trouve davantage de cadres,
            // c'est que des régions ont perdu leur espace de noms -> secours regex.
            $legacy = facetag_lib_faces_from_regex($xml);
            if (count($legacy) > count($read['mwg']) + count($read['mp'])) {
                $fallback = array_merge($fallback, $legacy);
                continue;
            }
        }
        $mwg = array_merge($mwg, $read['mwg']);
        $mp = array_merge($mp, $read['mp']);
    }

    $faces = $mwg;
    $matched = array();
    foreach ($mp as $face) {
        $twin = false;
        foreach ($mwg as $i => $f) {
            if (!isset($matched[$i]) && $f['nom'] === $face['nom']) {
                $matched[$i] = true;
                $twin = true;
                break;
            }
        }
        if (!$twin) {
            $faces[] = $face;
        }
    }
    return array_merge($faces, $fallback);
}

/**
 * Extraire toutes les données de visages d'un fichier JPEG
 *
 * @param string $filepath Chemin vers le fichier JPEG
 * @return array Tableau avec wjpg, hjpg, or (orientation), faces
 */
function facetag_lib_extract_faces_data($filepath) {
    $result = array(
        'wjpg' => 0,
        'hjpg' => 0,
        'or' => 1,
        'faces' => array()
    );

    // 1. DIMENSIONS de l'image
    $size = @getimagesize($filepath);
    if ($size) {
        $result['wjpg'] = $size[0];
        $result['hjpg'] = $size[1];
    }

    // 2. ORIENTATION EXIF
    if (function_exists('exif_read_data')) {
        $exif = @exif_read_data($filepath);
        if ($exif && isset($exif['Orientation'])) {
            $result['or'] = intval($exif['Orientation']);
        }
    }

    // 3. CADRES, région par région (segment principal et extension lus séparément)
    $parts = facetag_lib_xmp_segment_parts($filepath);
    if ($parts !== null) {
        $result['faces'] = facetag_lib_faces_from_xmp(array($parts['main'], $parts['ext']));
    }

    return $result;
}
