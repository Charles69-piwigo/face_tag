# face_tag

[Français](#français) · [English](#english)

---

## Français

Plugin [Piwigo](https://piwigo.org) qui **affiche les visages nommés** sur la page photo. Il lit les régions de visage (nom + cadre) enregistrées dans les métadonnées XMP des photos par digiKam, Lightroom, Apple Photos, ACDSee ou l'ancien Picasa, et les dessine en surimpression sur la photo. Il peut aussi masquer ces noms du nuage de tags public.

face_tag **ne modifie jamais les fichiers photo**. Pour dessiner ou nommer des visages depuis Piwigo, utiliser le plugin complémentaire [face_tag_editor](https://piwigo.org/ext/extension_view.php?eid=1053).

Page du plugin : <https://piwigo.org/ext/extension_view.php?eid=1051>

### Fonctionnalités

- Bouton **« Afficher les visages tagués »** dans la barre d'outils de la page photo, avec trois modes d'affichage :
  - **temporaire** : les cadres s'affichent ou se masquent à chaque clic, et disparaissent quand on change de photo ;
  - **interrupteur persistant** (par défaut) : les cadres restent affichés d'une photo à l'autre tant que le bouton est actif ;
  - **voir en survolant** : le nom n'apparaît qu'au survol de la zone du visage, pratique pour les photos de groupe.
- Couleur de l'icône du bouton selon son état : blanc (inactif), vert (actif), bleu (inactif, photo avec visages). Couleurs réglables.
- Apparence réglable des cadres (couleur, épaisseur, opacité) et des libellés (couleur du texte et du fond, opacité, taille de police).
- Prise en compte de l'orientation EXIF de la photo.
- Bouton **XMP** facultatif, qui affiche toutes les métadonnées XMP de la photo (utile au débogage).
- Compatible avec le thème par défaut et les thèmes Bootstrap (bootstrapdefault, bootstrap_darkroom).

### Formats lus

- MWG-RS (Metadata Working Group – Region Schema) ;
- MPReg (Microsoft Photo Region).

Ces deux formats sont ceux qu'utilisent digiKam, Lightroom, Apple Photos, ACDSee Photo Studio et Picasa. Le format est détecté automatiquement. Les régions sont lues dans le XMP des fichiers **JPEG**.

### Masquage des tags visage

L'onglet **Affichage des tags** permet de ne pas afficher les noms de personnes dans le nuage de tags public, sans les supprimer : ils restent visibles et modifiables dans **Administration > Tags**. Le masquage s'applique :

- à la page des tags (`tags.php`) ;
- au compteur de tags du menu « Découvrir » ;
- au widget **Tags** du plugin PWG_Stuffs, quel que soit le thème.

Pour savoir quels tags sont des visages, le plugin tient sa propre table, alimentée par une **synchronisation** lancée depuis cet onglet :

- sur toute la photothèque ou sur un album (sous-albums inclus ou non) ;
- par petits lots, sans risque de timeout même sur une très grosse photothèque ;
- avec **correction automatique** : si un visage du XMP n'a pas de tag Piwigo correspondant sur sa photo, le plugin relit les métadonnées, puis crée le tag si besoin. Les cas restants sont listés pour vérification manuelle.

### Vérification à l'affichage (option avancée)

Des photos envoyées par upload n'ont parfois jamais leurs tags de visage enregistrés dans Piwigo. Pour les corriger au fil des consultations, ajouter dans `local/config/config.inc.php` :

```php
$conf['face_tag_check'] = true;
```

À chaque affichage d'une photo, le plugin compare alors les visages du XMP aux tags de la photo, et crée ceux qui manquent. Cette option est désactivée par défaut, car elle ajoute une lecture du fichier à chaque affichage.

### Installation

1. Installer le plugin depuis **Administration > Plugins** (recherche « face_tag »), ou copier le dossier `face_tag` dans `plugins/`.
2. Activer le plugin.
3. Régler les options dans **Plugins > face_tag**.
4. Pour masquer les tags visage : onglet **Affichage des tags**, cocher l'option, puis lancer une synchronisation.

Après une mise à jour, si un affichage ne change pas, vider le cache des modèles : **Administration > Outils > Maintenance > Purger les templates compilés**.

### Langues

Français, anglais, italien (merci Gvf63).

### Plugins liés

- [face_tag_editor](https://piwigo.org/ext/extension_view.php?eid=1053) : dessiner et nommer les visages depuis Piwigo, en écrivant les métadonnées dans le fichier. Partage avec face_tag la table des tags visage.
- PWG_Stuffs : son widget Tags respecte le masquage des tags visage.

---

## English

[Piwigo](https://piwigo.org) plugin that **shows named faces** on the photo page. It reads the face regions (name + box) stored in the photos' XMP metadata by digiKam, Lightroom, Apple Photos, ACDSee or the old Picasa, and draws them over the photo. It can also hide these names from the public tag cloud.

face_tag **never modifies photo files**. To draw or name faces from Piwigo, use the companion plugin [face_tag_editor](https://piwigo.org/ext/extension_view.php?eid=1053).

Plugin page: <https://piwigo.org/ext/extension_view.php?eid=1051>

### Features

- **"Show tagged faces"** button in the photo page toolbar, with three display modes:
  - **temporary**: boxes show or hide on each click, and disappear when moving to another photo;
  - **persistent toggle** (default): boxes stay visible from one photo to the next while the button is active;
  - **view by hovering**: a name only appears when hovering over the face area, handy for group photos.
- Button icon color reflects its state: white (inactive), green (active), blue (inactive, photo has faces). Colors are configurable.
- Configurable box style (color, width, opacity) and labels (text and background color, opacity, font size).
- EXIF orientation is taken into account.
- Optional **XMP** button, showing all the photo's XMP metadata (useful for debugging).
- Works with the default theme and Bootstrap themes (bootstrapdefault, bootstrap_darkroom).

### Supported formats

- MWG-RS (Metadata Working Group – Region Schema);
- MPReg (Microsoft Photo Region).

These are the two formats used by digiKam, Lightroom, Apple Photos, ACDSee Photo Studio and Picasa. The format is detected automatically. Regions are read from the XMP of **JPEG** files.

### Hiding face tags

The **Tags display** tab lets you hide people's names from the public tag cloud without deleting them: they remain visible and editable in **Administration > Tags**. Hiding applies to:

- the tags page (`tags.php`);
- the tag counter in the "Explore" menu;
- the **Tags** widget of the PWG_Stuffs plugin, whatever the theme.

To know which tags are faces, the plugin keeps its own table, filled by a **synchronization** started from this tab:

- over the whole library or one album (with or without sub-albums);
- in small batches, with no risk of timeout even on a very large library;
- with **automatic correction**: if a face from the XMP has no matching Piwigo tag on its photo, the plugin re-reads the metadata, then creates the tag if needed. Remaining cases are listed for manual review.

### Check on display (advanced option)

Photos added by upload sometimes never get their face tags recorded in Piwigo. To fix them as they are viewed, add to `local/config/config.inc.php`:

```php
$conf['face_tag_check'] = true;
```

Each time a photo is displayed, the plugin then compares the XMP faces with the photo's tags, and creates the missing ones. This option is off by default, as it adds a file read on every photo view.

### Installation

1. Install the plugin from **Administration > Plugins** (search "face_tag"), or copy the `face_tag` folder into `plugins/`.
2. Activate the plugin.
3. Adjust the settings in **Plugins > face_tag**.
4. To hide face tags: **Tags display** tab, tick the option, then run a synchronization.

After an update, if a display does not change, clear the template cache: **Administration > Tools > Maintenance > Purge compiled templates**.

### Languages

French, English, Italian (thanks to Gvf63).

### Related plugins

- [face_tag_editor](https://piwigo.org/ext/extension_view.php?eid=1053): draw and name faces from Piwigo, writing the metadata into the file. Shares the face tag table with face_tag.
- PWG_Stuffs: its Tags widget respects face tag hiding.
