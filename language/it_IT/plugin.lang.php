<?php
// +-----------------------------------------------------------------------+
// | Plugin face_tag - File lingua italiana                                |
// +-----------------------------------------------------------------------+

// boutons
$lang['Afficher les visages tagués'] = 'Mostra le facce taggate';
$lang['Voir les métadonnées XMP'] = 'Visualizzare i metadata XMP';
// entete paramètres
$lang['Configuration générale'] = 'Configurazione generale';
$lang['Aide'] = 'Aiuto';
$lang['Configuration enregistrée avec succès'] = 'Configurazione salvata con successo';

$lang['Face Tag - Configuration générale'] = 'Face Tag - Configurazione generale';
$lang['facetag_save'] = 'Salvataggio configurazione';
$lang['Init'] = 'Valori di default';
$lang['Bouton d\'interface'] = 'Funzionamento interfaccia';
$lang['Afficher le bouton "Afficher les visages"'] = 'Visualizza il bottone "Visualizza volti"';
$lang['Mode d\'affichage des visages :'] = 'Modalità visualizzazione volti:';
$lang['Afficher/masquer à chaque clic (bouton temporaire)'] = 'Visualizza/nascondi con un clic (Temporaneo)';
$lang['Activer/désactiver globalement (interrupteur persistant)'] = 'Abilita/Disabilita globalmente (Persistente)';
$lang['Voir en survolant'] = 'Visualizza passando il puntatore'; 
$lang['En mode interrupteur, les visages restent affichés...'] = 'In modo persistente i volti vengono visualizzati su tutte le foto fino alla disattivazione della funzione.';
$lang['Apparence du rectangle'] = 'Aspetto riquadro';
$lang['Couleur du cadre :'] = 'Colore riquadro :';
$lang['Épaisseur du cadre (px) :'] = 'Spessore riquadro (px):';
$lang['Opacité du cadre :'] = 'Opacità riquadro:';
$lang['0 = transparent, 1 = opaque'] = '0 = transparente, 1 = opaco';
$lang['Apparence du libellé'] = 'Aspetto etichetta';
$lang['Couleur du texte :'] = 'Colore del testo :';
$lang['Taille de la police (px) :'] = 'Dimensione font (px) :';
$lang['Couleur de fond :'] = 'Colore sfondo :';
$lang['Opacité du fond :'] = 'Opacità sfondo :';
$lang['Bouton XMP'] = 'Bottone XMP';
$lang['Afficher le bouton XMP'] = 'Visualizza il bottone "Vedi XMP" - per debug';
$lang['Couleur de l\'icône active :'] = 'Colore icona "Attiva":';
$lang['Couleur de l\'icône inactif & tag existant :'] = 'Colore icona "Inattiva" e "tag esistente":';

$lang['Paramètres'] = 'Impostazioni';
$lang['Modes de fonctionnement'] = 'Modalità di funzionamento';
$lang['Affichage temporaire ou Affichage permanent'] = 'Visualizzazione temporanea o permanente';
$lang['On peut utiliser le plugin de trois façons :'] = 'Il plugin può essere utilizzato in tre modalità:';
$lang['Affichage temporaire :'] = 'Visualizzazione temporanea:';
$lang['Dans ce mode cliquez sur le bouton pour afficher les visages, un deuxième clic enlève l\'affichage'] = 'In questa modalità premendo il bottone vengono visualizzati i volti, ripremendolo la visualizzazione viene disattivata.';
$lang['Quand on change de photo, il faut recliquer sur le bouton pour afficher les visages'] = 'Cambiando la foto è necessario ripremere il pulsante per visualizzare i volti.';
$lang['Affichage persistant :'] = 'Visualizzazione persistente:';
$lang['Dans ce mode le bouton fonctionne comme un interrupteur, si des visages sont tagués sur la photo ils s\'affichent, quelle que soit la photo'] = 'In questa modalità premendo il bottone si attiva permanentemente la visualizzazione dei volti. La visualizzazione rimane attiva anche cambiando foto.';
$lang['Un deuxième clic stoppe l\'affichage des visages'] = 'Ripremendo il bottone la visualizzazione viene disattivata.';
$lang['Dans ce mode, les visages sont étiquetés lors du survol des zones qui les délimitent'] = 'In questa modalità, i volti vengono contrassegnati quando si passa il mouse sulle aree che li delimitano.';
$lang['Vous pouvez toujours activer ou désactiver le marquage en cliquant sur le bouton'] = 'Si può comunque attivare o disattivare il tagging cliccando sul pulsante.';
$lang['bouton voir les XMP'] = 'Visualizza il bottone XMP';
$lang['Quand il est affiché ce bouton est le plus à gauche sur la barre de navigation, il affiche un popup avec toutes les métadonnées XMP de la photo. Utile pour le débugage'] = 'Permette di attivare un bottone sulla barra di navigazione che visualizza i dati XMP in una finestra pop up. Utile per il debug.';
$lang['Apparences du rectangle et du libellé'] = 'Aspetto riquadro e etichetta';
$lang['Définir les caractéristiques du cadre et du libellé.'] = 'Definisce le caratteristiche del riquadro e dell\'etichetta.';
$lang['Important'] = 'Importante';
$lang['N\'oubliez pas d\'enregistrer les modifications !!!'] = 'Non dimenticare di salvare la configurazione  !!!';

$lang['Généralités'] = 'Informazioni generali';
$lang['Face Tag - Reconnaissance faciale'] = 'Face Tag - Riconoscimento volti';
$lang['Le plugin Face Tag permet d\'afficher les visages tagués dans votre galerie Piwigo.'] = 'Il plugin Face Tag vi permette di taggare i volti nei vostri album Piwigo.';
$lang['Il existe deux méthodes pour enregistrer les visages dans les métadonnées XMP des jpg :'] = 'Ci sono due metodi per salvare i volti nei metadati XMP di un file JPG:';
$lang['MPReg (Microsoft Photo Region)'] = 'MPReg (Microsoft Photo Region)';
$lang['MWG-RS (Metadata Working Group - Region Schema).'] = 'MWG-RS (Metadata Working Group - Region Schema).';
$lang['Ces méthodes sont utilisées par digiKam, Light Room, Apple Photos, ACDSee Photo Studio et feu-Picasa'] = 'Questi metodi sono utilizzati da digiKam, Lightroom, Apple Photos, ACDSee Photo Studio, e nel defunto Picasa.';
$lang['Ces programmes permettent d\'enregistrer les informations dans les métadonnées des photos'] = 'Questi programmi ti permettono di salvare le informazioni nei metadati delle foto.';
$lang['Face Tag détecte automatiquement la méthode utilisée.'] = 'Face Tag rileva automaticamente il metodo usato.';
$lang['digiKam'] = 'digiKam';
$lang['digiKam est un programme open source de catalogage et d\'édition de photos.'] = 'digiKam è un programma  open-source di catalogazione e modifica delle foto.';
$lang['Il permet de détecter de manière semi automatique les visages, et d\'enregistrer les données dans le jpg'] = 'Permette il rilevamento semiautomatico dei volti e salva i dati nel JPG.';
$lang['Ces données sont lisibles dans piwigo avec le plugin Face Tag , quelle que soit la manière dont vous importez les photos dans piwigo :'] = 'Questi dati sono leggibili in Piwigo con il plugin Face Tag, indipendentemente da come si importano le foto in Piwigo:';
$lang['Upload des photos'] = 'Upload';
$lang['Synchronisation'] = 'Sincronizzazione';

// onglet admin
$lang['Affichage des tags'] = 'Visualizzazione tag';

// onglet Affichage des tags
$lang['Face Tag - Affichage des tags'] = 'Face Tag - Visualizzazione tag';
$lang['Nuage de tags'] = 'Tag cloud';
$lang['Ne pas afficher les tags visage dans le nuage de tags'] = 'Non visualizzare i tag dei volti nella nuvola dei tag';
$lang['Ces tags restent visibles dans Administration > Tags'] = 'Questi tag rimangono visibili in Amministrazione > Tags';

$lang['Synchronisation de la table des tags visage'] = 'Sincronizzazione della tabella dei tag dei volti';
$lang['Restreindre la synchronisation à un album (facultatif) :'] = 'Restringere la sincronizzazione ad un album (opzionale):';

$lang['-- Toute la photothèque --'] = '-- Tutta la libreria --';
$lang['Cliquer sur un album pour restreindre le scan, cliquer à nouveau pour désélectionner (= toute la photothèque)'] = 'Fare clic su un album per limitare la scansione, fare nuovamente clic per deselezionare (= intera libreria di foto)';
$lang['Aucune sélection = scan de toutes les photos taguées, ce qui peut être long sur une grosse photothèque'] = 'Nessuna selezione = scansione di tutte le foto taggate, il che può richiedere un po\' di tempo su una grande libreria fotografica';
$lang['Le triangle '] = 'Il triangolo ';
$lang[' en vert indique un album virtuel (sans dossier lié) — il peut contenir des photos au même titre qu\'un album réel'] = ' in verde indica un album virtuale (nessuna cartella collegata) — può contenere foto proprio come un album reale';
$lang['Album virtuel (sans dossier lié)'] = 'Album virtuale (nessuna cartella collegata)';
$lang['Inclure les sous-albums'] = 'Includi sub-album';
$lang['Synchroniser la table des tags visage'] = 'Sincronizza la tabella dei tag dei volti';
$lang['Arrêter'] = 'Stop';
$lang['À relancer après un import externe (digiKam, XnView...) ou une resynchronisation des métadonnées'] = 'Da eseguire nuovamente dopo un\'importazione esterna (digiKam, XnView...) o una risincronizzazione dei metadati';
$lang['La synchronisation se fait par petits lots (comme le plugin AlbumPilot) : aucun risque de timeout (504), même sur une très grosse photothèque.'] = 'La sincronizzazione viene eseguita in piccoli lotti (come il plugin AlbumPilot): nessun rischio di timeout (504), anche su una libreria di foto molto grande.';
$lang['Actualiser la page pour voir les résultats'] = 'Ricaricare la pagina per visualizzare i risultati';
$lang['Arrêt demandé — ce qui a déjà été traité reste enregistré.'] = 'Stop richiesto — ciò che è stato già  processato rimane salvato.';
$lang['Erreur'] = 'Errore';
$lang['photos analysées'] = 'foto analizzate';
$lang['Synchronisation terminée'] = 'Sincronizzazione completata';
$lang['tags visage identifiés'] = 'tag facciali identificati';
$lang['visages sans tag correspondant'] = 'volti senza tag corrispondente';
$lang['photo(s) en erreur (voir le journal du serveur)'] = 'foto con errori (vedere il log del server)';
$lang['Erreur réseau — nouvel essai automatique, le travail déjà fait reste enregistré.'] = 'Network error — ritenta in automatico, il lavoro già fatto rimane salvato.';
$lang['Synchronisation en cours...'] = 'Sincronizzazione in corso...';
$lang['Démarrage — comptage des photos en cours, ça peut prendre quelques secondes sur un gros album, merci de patienter...'] = 'Inizio — conteggio delle foto, potrebbero volerci alcuni secondi su un album di grandi dimensioni, per favore aspetta...';
$lang['Aucune photo à traiter dans ce périmètre.'] = 'Non ci sono foto da elaborare con questi parametri.';
$lang['Erreur au démarrage de la synchronisation.'] = 'Errore durante l\'avvio della sincronizzazione.';

$lang['Visages sans tag correspondant'] = 'Volti senza tag corrispondente';
$lang['La synchro tente automatiquement de corriger ces cas (relecture des métadonnées, puis création directe du tag si besoin) — ce qui reste ici a échoué même après ces tentatives (erreur technique lors de la création du tag). Voir le journal du serveur pour le détail de l\'erreur.'] = 'La sincronizzazione tenta automaticamente di risolvere questi casi (rileggendo i metadati, quindi creando direttamente il tag se necessario) — ciò che rimane qui fallisce anche dopo questi tentativi (errore tecnico durante la creazione del tag). Per i dettagli dell\'errore, consultare il registro del server.';
$lang['Photo'] = 'Foto';

$lang['Visage(s) non résolu(s)'] = 'Volto(i) non risolti';
$lang['Tags actuellement sur la photo'] = 'Tag attualmente sulla foto';
$lang['aucun'] = 'nessuno';
$lang['Tenter une resynchronisation des métadonnées sur ces photos uniquement'] = 'Prova una risincronizzazione dei metadati solo su queste foto';
$lang['Retente les mêmes corrections automatiques que la synchro (vérification, relecture des métadonnées Piwigo, puis création directe du tag) juste pour les photos ci-dessus, sans toucher au reste de la photothèque. Utile après une correction manuelle externe (ex: digiKam), sans tout rescanner.'] = 'Riprova le stesse correzioni automatiche della sincronizzazione (controlla, rileggi i metadati Piwigo, quindi crea direttamente il tag) solo per le foto sopra, senza toccare il resto della libreria fotografica. Utile dopo una correzione manuale esterna (ad esempio digiKam), senza dover scansionare nuovamente tutto.';
$lang['Resynchronisation en cours...'] = 'Risincronizzazione in corso...';
$lang['Démarrage...'] = 'Inizio...';
$lang['Rien à traiter.'] = 'Niente da elaborare.';
$lang['Erreur au démarrage.'] = 'Errore di avvio.';
$lang['photos traitées'] = 'foto elaborate';
$lang['Resynchronisation terminée'] = 'Risincronizzazione completata';
$lang['discordance(s) résolue(s)'] = 'discrepanza(e) risolta(e)';

$lang['Vérification automatique à l\'affichage (avancé)'] = 'Verifica automatica alla visualizzazione (avanzato)';
$lang['En complément de la synchronisation manuelle ci-dessus, il est possible d\'activer une vérification automatique : à chaque affichage d\'une photo, si un visage détecté dans le XMP n\'a pas de tag Piwigo correspondant, le tag est créé et attaché immédiatement. Utile notamment pour les photos ajoutées par upload web, qui ne sont pas toujours synchronisées correctement.'] = 'In aggiunta alla sincronizzazione manuale descritta sopra, è possibile attivare una verifica automatica: ad ogni visualizzazione di una foto, se un volto rilevato nell\'XMP non ha un tag Piwigo corrispondente, il tag viene creato e associato immediatamente. Utile in particolare per le foto aggiunte tramite upload web, che non vengono sempre sincronizzate correttamente.';
$lang['Ce n\'est pas un réglage de cet écran : ça s\'active en ajoutant cette ligne dans local/config/config.inc.php de Piwigo :'] = 'Non è un\'impostazione di questa schermata: si attiva aggiungendo questa riga nel file local/config/config.inc.php di Piwigo:';
$lang['Désactivé par défaut, car cette vérification ajoute une lecture du fichier à chaque affichage de photo — à réserver aux installations qui rencontrent réellement des photos mal synchronisées après upload.'] = 'Disattivato per impostazione predefinita, perché questa verifica aggiunge una lettura del file ad ogni visualizzazione di foto — da riservare alle installazioni che riscontrano realmente foto non sincronizzate correttamente dopo l\'upload.';

$lang['Réinitialisation des visages'] = 'Svuotamento tabella volti';
$lang['Effacer toute la table des tags visage ? Cette action est irréversible.'] = 'Cancellare l\'intera tabella dei tag dei volti? Questa azione è irreversibile.';
$lang['Efface entièrement la table de correspondance des tags visage. Le nuage de tags public affichera de nouveau tous les tags jusqu\'à la prochaine synchronisation, qui reconstruit la table à partir des cadres de visage des photos. Cette table sert aussi aux plugins face_tag_editor et tag_manager.'] = 'Cancella completamente la tabella dei tag dei volti. La nuvola dei tag mostrerà di nuovo tutti i tag fino alla successiva sincronizzazione, che ricostruisce la tabella a partire dai riquadri dei volti delle foto. Questa tabella è usata anche dai plugin face_tag_editor e tag_manager.';
$lang['Utile uniquement en cas de problème (ex: repartir de zéro après un changement de configuration) — la synchronisation habituelle (ci-dessus) suffit dans l\'immense majorité des cas.'] = 'Utile solo in caso di problemi (ad esempio ricominciare da zero dopo una modifica della configurazione) — la consueta sincronizzazione (sopra) è sufficiente nella stragrande maggioranza dei casi.';
$lang['Effacer la table des tags visage'] = 'Pulisce la tabella dei tag dei volti';
$lang['Table des tags visage réinitialisée : plus aucun tag n\'est marqué comme tag visage. Relancez une synchronisation pour la repeupler.'] = 'Ripristino della tabella dei tag dei volti: nessun tag è più contrassegnato come tag facciale. Esegui una sincronizzazione per ripopolarla.';

// aide - onglet Affichage des tags
$lang['En mode interrupteur et \'Voir en survolant\', la couleur de l\'icône indique l\'état du bouton : une couleur quand il est actif, une autre quand il est inactif mais que la photo affichée a des visages tagués. Ces deux couleurs se règlent dans cet onglet.'] = 'In modalità persistente e in \'Visualizza passando il puntatore\', il colore dell\'icona indica lo stato del pulsante: un colore quando è attivo, un altro quando è inattivo ma la foto visualizzata ha volti taggati. Entrambi i colori sono impostati in questa scheda. Se non sono disponibili tag l\'icona viene visualizzata in bianco';
$lang['Permet de masquer les tags visage dans le nuage de tags public, sans les supprimer : ils restent visibles et modifiables dans Administration > Tags.'] = 'Consente di nascondere i tag dei volti dal tag cloud senza eliminarli: rimangono visibili e modificabili in Administrazione > Tags.';
$lang['Cette table fait la correspondance entre les tags Piwigo et les visages détectés dans le XMP des photos ; c\'est elle qui permet au plugin de savoir quels tags sont des tags visage. Il faut la (re)synchroniser après un import externe (digiKam, XnView...) ou une resynchronisation des métadonnées Piwigo.'] = 'Questa tabella abbina i tag Piwigo ai volti rilevati in Photo XMP; è ciò che consente al plugin di sapere quali tag sono tag facciali. Deve essere (ri)sincronizzata dopo un\'importazione esterna (digiKam, XnView...) o una risincronizzazione dei metadati Piwigo.';
$lang['La synchronisation peut être restreinte à un album, et se déroule par petits lots, comme le plugin AlbumPilot : aucun risque de timeout, même sur une très grosse photothèque.'] = 'La sincronizzazione può essere limitata a un album e comunque viene eseguita in piccoli lotti, come il plugin AlbumPilot: nessun rischio di timeout, anche su una libreria di foto molto ampia.';
$lang['Si un visage détecté dans le XMP ne trouve aucun tag Piwigo correspondant sur sa photo, la synchronisation tente automatiquement de corriger le problème (relecture des métadonnées, puis création directe du tag si besoin). Les cas qui échouent malgré tout apparaissent dans cette liste.'] = 'Se un volto rilevato negli XMP non ha un tag Piwigo corrispondente, la sincronizzazione tenta automaticamente di risolvere il problema (rileggendo i metadati, quindi creando direttamente il tag se necessario). I casi che ancora falliscono compaiono in questo elenco.';
$lang['Un bouton permet de retenter la correction uniquement sur ces photos, sans relancer une synchronisation complète.'] = 'Un pulsante consente di riprovare la correzione solo su queste foto, senza dover eseguire nuovamente una sincronizzazione completa.';
$lang['Efface entièrement la table des tags visage. Utile uniquement en cas de problème : la synchronisation habituelle suffit dans l\'immense majorité des cas.'] = 'Cancella completamente la tabella dei tag dei volti. Utile solo in caso di problemi: nella stragrande maggioranza dei casi è sufficiente la consueta sincronizzazione.';
