<?php
// +-----------------------------------------------------------------------+
// | Plugin face_tag - English language file                               |
// +-----------------------------------------------------------------------+

// boutons
$lang['Afficher les visages tagués'] = 'Show tagged faces';
$lang['Voir les métadonnées XMP'] = 'View XMP metadata';
// entete paramètres
$lang['Configuration générale'] = 'General Settings';
$lang['Aide'] = 'Help';
$lang['Configuration enregistrée avec succès'] = 'Configuration saved successfully';

$lang['Face Tag - Configuration générale'] = 'Face Tag - General Settings';
$lang['facetag_save'] = 'Save settings';
$lang['Init'] = 'Default values';
$lang['Bouton d\'interface'] = 'Interface button';
$lang['Afficher le bouton "Afficher les visages"'] = 'Show "Show faces" button';
$lang['Mode d\'affichage des visages :'] = 'Face display mode:';
$lang['Afficher/masquer à chaque clic (bouton temporaire)'] = 'Show/Hide on Click (Temporary Button)';
$lang['Activer/désactiver globalement (interrupteur persistant)'] = 'Enable/Disable globally (Persistent Switch)';
$lang['Voir en survolant'] = 'View by hovering'; 
$lang['En mode interrupteur, les visages restent affichés...'] = 'In toggle mode, faces remain displayed on all photos until the next click.';
$lang['Apparence du rectangle'] = 'Rectangle Appearance';
$lang['Couleur du cadre :'] = 'Frame color:';
$lang['Épaisseur du cadre (px) :'] = 'Frame thickness (px):';
$lang['Opacité du cadre :'] = 'Frame opacity:';
$lang['0 = transparent, 1 = opaque'] = '0 = transparent, 1 = opaque';
$lang['Apparence du libellé'] = 'Label appearance';
$lang['Couleur du texte :'] = 'Text color :';
$lang['Taille de la police (px) :'] = 'Font size (px) :';
$lang['Couleur de fond :'] = 'Background color :';
$lang['Opacité du fond :'] = 'Background opacity :';
$lang['Bouton XMP'] = 'XMP button';
$lang['Afficher le bouton XMP'] = 'Show "View XMP" button - for debugging';
$lang['Couleur de l\'icône active :'] = 'Active icon color:';
$lang['Couleur de l\'icône inactif & tag existant :'] = 'Inactive icon & existing tag color:';

$lang['Paramètres'] = 'Settings';
$lang['Modes de fonctionnement'] = 'Operating modes';
$lang['Affichage temporaire ou Affichage permanent'] = 'Temporary display or Permanent display';
$lang['On peut utiliser le plugin de trois façons :'] = 'The plugin can be used in three ways:';
$lang['Affichage temporaire :'] = 'Temporary Display:';
$lang['Dans ce mode cliquez sur le bouton pour afficher les visages, un deuxième clic enlève l\'affichage'] = 'In this mode, click the button to display faces. A second click removes the display.';
$lang['Quand on change de photo, il faut recliquer sur le bouton pour afficher les visages'] = 'When you change photos, you must click the button again to display the faces.';
$lang['Affichage persistant :'] = 'Persistent display:';
$lang['Dans ce mode le bouton fonctionne comme un interrupteur, si des visages sont tagués sur la photo ils s\'affichent, quelle que soit la photo'] = 'In this mode, the button acts like a switch. If faces are tagged in the photo, they will be displayed, regardless of the photo.';
$lang['Un deuxième clic stoppe l\'affichage des visages'] = 'A second click stops the display of faces.';
$lang['Dans ce mode, les visages sont étiquetés lors du survol des zones qui les délimitent'] = 'In this mode, faces are tagged when hover the areas that delimit them';
$lang['Vous pouvez toujours activer ou désactiver le marquage en cliquant sur le bouton'] = 'You can always turn tagging on or off by clicking the button';
$lang['bouton voir les XMP'] = 'view XMP button';
$lang['Quand il est affiché ce bouton est le plus à gauche sur la barre de navigation, il affiche un popup avec toutes les métadonnées XMP de la photo. Utile pour le débugage'] = 'When displayed, this button is the leftmost button on the navigation bar. It displays a popup with all the XMP metadata for the photo. Useful for debugging.';
$lang['Apparences du rectangle et du libellé'] = 'Rectangle and Label appearance';
$lang['Définir les caractéristiques du cadre et du libellé.'] = 'Define the characteristics of the frame and label.';
$lang['Important'] = 'Important';
$lang['N\'oubliez pas d\'enregistrer les modifications !!!'] = 'Don\'t forget to save your changes !!!';

$lang['Généralités'] = 'General Information';
$lang['Face Tag - Reconnaissance faciale'] = 'Face Tag - Facial Recognition';
$lang['Le plugin Face Tag permet d\'afficher les visages tagués dans votre galerie Piwigo.'] = 'The Face Tag plugin allows you to display tagged faces in your Piwigo gallery.';
$lang['Il existe deux méthodes pour enregistrer les visages dans les métadonnées XMP des jpg :'] = 'There are two methods for saving faces in the XMP metadata of JPGs:';
$lang['MPReg (Microsoft Photo Region)'] = 'MPReg (Microsoft Photo Region)';
$lang['MWG-RS (Metadata Working Group - Region Schema).'] = 'MWG-RS (Metadata Working Group - Region Schema).';
$lang['Ces méthodes sont utilisées par digiKam, Light Room, Apple Photos, ACDSee Photo Studio et feu-Picasa'] = 'These methods are used by digiKam, Lightroom, Apple Photos, ACDSee Photo Studio, and the now-defunct Picasa.';
$lang['Ces programmes permettent d\'enregistrer les informations dans les métadonnées des photos'] = 'These programs allow you to save information in the photo metadata.';
$lang['Face Tag détecte automatiquement la méthode utilisée.'] = 'Face Tag automatically detects the method used.';
$lang['digiKam'] = 'digiKam';
$lang['digiKam est un programme open source de catalogage et d\'édition de photos.'] = 'digiKam is an open-source photo cataloging and editing program.';
$lang['Il permet de détecter de manière semi automatique les visages, et d\'enregistrer les données dans le jpg'] = 'It allows semi-automatic face detection and saves the data in the JPG.';
$lang['Ces données sont lisibles dans piwigo avec le plugin Face Tag , quelle que soit la manière dont vous importez les photos dans piwigo :'] = 'This data is readable in Piwigo with the Face Tag plugin, regardless of how you import the photos into Piwigo:';
$lang['Upload des photos'] = 'Photos upload';
$lang['Synchronisation'] = 'Synchronization';

// onglet admin
$lang['Affichage des tags'] = 'Tags display';

// onglet Affichage des tags
$lang['Face Tag - Affichage des tags'] = 'Face Tag - Tags display';
$lang['Nuage de tags'] = 'Tag cloud';
$lang['Ne pas afficher les tags visage dans le nuage de tags'] = 'Do not show face tags in the tag cloud';
$lang['Ces tags restent visibles dans Administration > Tags'] = 'These tags remain visible in Administration > Tags';

$lang['Synchronisation de la table des tags visage'] = 'Face tags table synchronization';
$lang['Restreindre la synchronisation à un album (facultatif) :'] = 'Restrict synchronization to an album (optional):';
$lang['-- Toute la photothèque --'] = '-- Entire photo library --';
$lang['Cliquer sur un album pour restreindre le scan, cliquer à nouveau pour désélectionner (= toute la photothèque)'] = 'Click an album to restrict the scan, click again to deselect (= entire photo library)';
$lang['Aucune sélection = scan de toutes les photos taguées, ce qui peut être long sur une grosse photothèque'] = 'No selection = scan of all tagged photos, which can take a while on a large photo library';
$lang['Le triangle '] = 'The ';
$lang[' en vert indique un album virtuel (sans dossier lié) — il peut contenir des photos au même titre qu\'un album réel'] = ' triangle in green indicates a virtual album (no linked folder) — it can contain photos just like a real album';
$lang['Album virtuel (sans dossier lié)'] = 'Virtual album (no linked folder)';
$lang['Inclure les sous-albums'] = 'Include sub-albums';
$lang['Synchroniser la table des tags visage'] = 'Synchronize the face tags table';
$lang['Arrêter'] = 'Stop';
$lang['À relancer après un import externe (digiKam, XnView...) ou une resynchronisation des métadonnées'] = 'Run this again after an external import (digiKam, XnView...) or a metadata resynchronization';
$lang['La synchronisation se fait par petits lots (comme le plugin AlbumPilot) : aucun risque de timeout (504), même sur une très grosse photothèque.'] = 'Synchronization runs in small batches (like the AlbumPilot plugin): no risk of timeout (504), even on a very large photo library.';
$lang['Actualiser la page pour voir les résultats'] = 'Refresh the page to see the results';
$lang['Arrêt demandé — ce qui a déjà été traité reste enregistré.'] = 'Stop requested — everything already processed remains saved.';
$lang['Erreur'] = 'Error';
$lang['photos analysées'] = 'photos scanned';
$lang['Synchronisation terminée'] = 'Synchronization complete';
$lang['tags visage identifiés'] = 'face tags identified';
$lang['visages sans tag correspondant'] = 'faces without a matching tag';
$lang['photo(s) en erreur (voir le journal du serveur)'] = 'photo(s) failed (see server log)';
$lang['Erreur réseau — nouvel essai automatique, le travail déjà fait reste enregistré.'] = 'Network error — automatic retry, work already done remains saved.';
$lang['Synchronisation en cours...'] = 'Synchronization in progress...';
$lang['Démarrage — comptage des photos en cours, ça peut prendre quelques secondes sur un gros album, merci de patienter...'] = 'Starting — counting photos, this may take a few seconds on a large album, please wait...';
$lang['Aucune photo à traiter dans ce périmètre.'] = 'No photos to process in this scope.';
$lang['Erreur au démarrage de la synchronisation.'] = 'Error starting synchronization.';

$lang['Visages sans tag correspondant'] = 'Faces without a matching tag';
$lang['La synchro tente automatiquement de corriger ces cas (relecture des métadonnées, puis création directe du tag si besoin) — ce qui reste ici a échoué même après ces tentatives (erreur technique lors de la création du tag). Voir le journal du serveur pour le détail de l\'erreur.'] = 'The synchronization automatically tries to fix these cases (re-reading metadata, then creating the tag directly if needed) — what remains here failed even after these attempts (technical error while creating the tag). See the server log for the error details.';
$lang['Photo'] = 'Photo';
$lang['Visage(s) non résolu(s)'] = 'Unresolved face(s)';
$lang['Tags actuellement sur la photo'] = 'Tags currently on the photo';
$lang['aucun'] = 'none';
$lang['Tenter une resynchronisation des métadonnées sur ces photos uniquement'] = 'Try a metadata resynchronization on these photos only';
$lang['Retente les mêmes corrections automatiques que la synchro (vérification, relecture des métadonnées Piwigo, puis création directe du tag) juste pour les photos ci-dessus, sans toucher au reste de la photothèque. Utile après une correction manuelle externe (ex: digiKam), sans tout rescanner.'] = 'Retries the same automatic corrections as the synchronization (check, re-read Piwigo metadata, then create the tag directly) just for the photos above, without touching the rest of the photo library. Useful after an external manual correction (e.g. digiKam), without rescanning everything.';
$lang['Resynchronisation en cours...'] = 'Resynchronization in progress...';
$lang['Démarrage...'] = 'Starting...';
$lang['Rien à traiter.'] = 'Nothing to process.';
$lang['Erreur au démarrage.'] = 'Error starting.';
$lang['photos traitées'] = 'photos processed';
$lang['Resynchronisation terminée'] = 'Resynchronization complete';
$lang['discordance(s) résolue(s)'] = 'discrepancy(ies) resolved';

$lang['Vérification automatique à l\'affichage (avancé)'] = 'Automatic check on display (advanced)';
$lang['En complément de la synchronisation manuelle ci-dessus, il est possible d\'activer une vérification automatique : à chaque affichage d\'une photo, si un visage détecté dans le XMP n\'a pas de tag Piwigo correspondant, le tag est créé et attaché immédiatement. Utile notamment pour les photos ajoutées par upload web, qui ne sont pas toujours synchronisées correctement.'] = 'In addition to the manual synchronization above, an automatic check can be enabled: every time a photo is displayed, if a face detected in the XMP has no matching Piwigo tag, the tag is created and attached immediately. Useful in particular for photos added by web upload, which are not always synchronized correctly.';
$lang['Ce n\'est pas un réglage de cet écran : ça s\'active en ajoutant cette ligne dans local/config/config.inc.php de Piwigo :'] = 'This is not a setting on this screen: it is enabled by adding this line to Piwigo\'s local/config/config.inc.php:';
$lang['Désactivé par défaut, car cette vérification ajoute une lecture du fichier à chaque affichage de photo — à réserver aux installations qui rencontrent réellement des photos mal synchronisées après upload.'] = 'Disabled by default, because this check adds a file read on every photo display — reserve it for installations that actually experience photos being incorrectly synchronized after upload.';

$lang['Réinitialisation des visages'] = 'Reset faces';
$lang['Effacer toute la table des tags visage ? Cette action est irréversible.'] = 'Clear the entire face tags table? This action is irreversible.';
$lang['Efface entièrement la table de correspondance des tags visage (auto ET manuel). Le nuage de tags public affichera de nouveau tous les tags jusqu\'à la prochaine synchronisation. Les corrections manuelles éventuellement faites sont définitivement perdues.'] = 'Completely clears the face tags matching table (auto AND manual). The public tag cloud will show all tags again until the next synchronization. Any manual corrections made are permanently lost.';
$lang['Utile uniquement en cas de problème (ex: repartir de zéro après un changement de configuration) — la synchronisation habituelle (ci-dessus) suffit dans l\'immense majorité des cas.'] = 'Only useful in case of trouble (e.g. starting fresh after a configuration change) — the usual synchronization (above) is enough in the vast majority of cases.';
$lang['Effacer la table des tags visage'] = 'Clear the face tags table';
$lang['Table des tags visage réinitialisée : plus aucun tag n\'est marqué comme tag visage. Relancez une synchronisation pour la repeupler.'] = 'Face tags table reset: no tag is marked as a face tag anymore. Run a synchronization to repopulate it.';

// aide - onglet Affichage des tags
$lang['En mode interrupteur et \'Voir en survolant\', la couleur de l\'icône indique l\'état du bouton : une couleur quand il est actif, une autre quand il est inactif mais que la photo affichée a des visages tagués. Ces deux couleurs se règlent dans cet onglet.'] = 'In toggle mode and \'View by hovering\', the icon color indicates the button state: one color when active, another when inactive but the displayed photo has tagged faces. Both colors are set in this tab.';
$lang['Permet de masquer les tags visage dans le nuage de tags public, sans les supprimer : ils restent visibles et modifiables dans Administration > Tags.'] = 'Lets you hide face tags from the public tag cloud without deleting them: they remain visible and editable in Administration > Tags.';
$lang['Cette table fait la correspondance entre les tags Piwigo et les visages détectés dans le XMP des photos ; c\'est elle qui permet au plugin de savoir quels tags sont des tags visage. Il faut la (re)synchroniser après un import externe (digiKam, XnView...) ou une resynchronisation des métadonnées Piwigo.'] = 'This table matches Piwigo tags to faces detected in photo XMP; it is what lets the plugin know which tags are face tags. It must be (re)synchronized after an external import (digiKam, XnView...) or a Piwigo metadata resynchronization.';
$lang['La synchronisation peut être restreinte à un album, et se déroule par petits lots, comme le plugin AlbumPilot : aucun risque de timeout, même sur une très grosse photothèque.'] = 'Synchronization can be restricted to an album, and runs in small batches, like the AlbumPilot plugin: no risk of timeout, even on a very large photo library.';
$lang['Si un visage détecté dans le XMP ne trouve aucun tag Piwigo correspondant sur sa photo, la synchronisation tente automatiquement de corriger le problème (relecture des métadonnées, puis création directe du tag si besoin). Les cas qui échouent malgré tout apparaissent dans cette liste.'] = 'If a face detected in the XMP has no matching Piwigo tag on its photo, the synchronization automatically tries to fix the problem (re-reading metadata, then creating the tag directly if needed). Cases that still fail appear in this list.';
$lang['Un bouton permet de retenter la correction uniquement sur ces photos, sans relancer une synchronisation complète.'] = 'A button lets you retry the correction on just these photos, without running a full synchronization again.';
$lang['Efface entièrement la table des tags visage. Utile uniquement en cas de problème : la synchronisation habituelle suffit dans l\'immense majorité des cas.'] = 'Completely clears the face tags table. Only useful in case of trouble: the usual synchronization is enough in the vast majority of cases.';
