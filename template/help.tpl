<div class="titrePage">
  <h2>Face Tag</h2>
</div>

<style>
.help-container {
  max-width: 900px;
  margin-top: 20px;
}

.help-column {
  background: #f9f9f9;
  padding: 15px 20px;
  border-radius: 5px;
  border: 1px solid #ddd;
  margin-bottom: 20px;
  text-align: left;
}

.help-column h3 {
  margin-top: 0;
  color: #333;
  border-bottom: 2px solid #ff7700;
  padding-bottom: 10px;
  text-align: left;
}

.help-section {
  margin-bottom: 25px;
}

.help-section h4 {
  color: #555;
  margin-top: 15px;
  margin-bottom: 8px;
  text-align: left;
}

.help-section p {
  text-align: left;
  line-height: 1.4;
  color: #666;
}

.help-section ul {
  margin: 10px 0;
  padding-left: 25px;
  text-align: left;
}

.help-section li {
  margin-bottom: 8px;
  line-height: 1.5;
  text-align: left;
}

.help-note {
  background: #fff3cd;
  border-left: 4px solid #ffc107;
  padding: 10px 15px;
  margin: 15px 0;
  border-radius: 3px;
  text-align: left;
}

.help-tip {
  background: #d1ecf1;
  border-left: 4px solid #17a2b8;
  padding: 10px 15px;
  margin: 15px 0;
  border-radius: 3px;
  text-align: left;
}
</style>

<div class="help-container">

  <!-- Généralités -->
  <div class="help-column">
    <h3>{'Généralités'|@translate}</h3>

    <div class="help-section">
      <h4>{'Face Tag - Reconnaissance faciale'|@translate}</h4>
      <p>{'Le plugin Face Tag permet d\'afficher les visages tagués dans votre galerie Piwigo.'|@translate}</p>
      <p>{'Il existe deux méthodes pour enregistrer les visages dans les métadonnées XMP des jpg :'|@translate} </p>
      <ul>
        <li>{'MPReg (Microsoft Photo Region)'|@translate}</li>
        <li>{'MWG-RS (Metadata Working Group - Region Schema).'|@translate}</li>
      </ul>
      <p>{'Ces méthodes sont utilisées par digiKam, Light Room, Apple Photos, ACDSee Photo Studio et feu-Picasa'|@translate}  </p>
      <p>{'Ces programmes permettent d\'enregistrer les informations dans les métadonnées des photos'|@translate}  </p>
      <p>{'Face Tag détecte automatiquement la méthode utilisée.'|@translate}</p>
    </div>

    <div class="help-section">
      <h4>{'digiKam'|@translate}</h4>
      <p>{'digiKam est un programme open source de catalogage et d\'édition de photos.'|@translate}</p>
      <p>{'Il permet de détecter de manière semi automatique les visages, et d\'enregistrer les données dans le jpg'|@translate}</p>
      <p>{'Ces données sont lisibles dans piwigo avec le plugin Face Tag , quelle que soit la manière dont vous importez les photos dans piwigo :'|@translate}</p>
      <ul>
        <li>{'Upload des photos'|@translate}</li>
        <li>{'Synchronisation'|@translate}</li>
      </ul>
    </div>
  </div>

  <!-- Paramètres -->
  <div class="help-column">
    <h3>{'Paramètres'|@translate}</h3>

    <div class="help-section">
      <h4>{'Modes de fonctionnement'|@translate}</h4>
      <h4>{'Affichage temporaire ou Affichage permanent'|@translate}</h4>
      <p>{'On peut utiliser le plugin de trois façons :'|@translate} </p>
      <ul>
        <li><strong>{'Affichage temporaire :'|@translate}</strong></li>
        <p>{'Dans ce mode cliquez sur le bouton pour afficher les visages, un deuxième clic enlève l\'affichage'|@translate} </p>
        <p>{'Quand on change de photo, il faut recliquer sur le bouton pour afficher les visages'|@translate} </p>
        <li><strong>{'Affichage persistant :'|@translate}</strong></li>
        <p>{'Dans ce mode le bouton fonctionne comme un interrupteur, si des visages sont tagués sur la photo ils s\'affichent, quelle que soit la photo'|@translate} </p>
        <p>{'Un deuxième clic stoppe l\'affichage des visages'|@translate} </p>
        <li><strong>{'Voir en survolant'|@translate}:</strong></li>
        <p>{'Dans ce mode, les visages sont étiquetés lors du survol des zones qui les délimitent'|@translate} </p>
        <p>{'Vous pouvez toujours activer ou désactiver le marquage en cliquant sur le bouton'|@translate} </p>
      </ul>
      <p>{'En mode interrupteur et \'Voir en survolant\', la couleur de l\'icône indique l\'état du bouton : une couleur quand il est actif, une autre quand il est inactif mais que la photo affichée a des visages tagués. Ces deux couleurs se règlent dans cet onglet.'|@translate}</p>
    </div>

    <div class="help-section">
      <h4>{'bouton voir les XMP'|@translate}</h4>
      <p>{'Quand il est affiché ce bouton est le plus à gauche sur la barre de navigation, il affiche un popup avec toutes les métadonnées XMP de la photo. Utile pour le débugage'|@translate} </p>
    </div>

    <div class="help-section">
      <h4>{'Apparences du rectangle et du libellé'|@translate}</h4>
      <p>{'Définir les caractéristiques du cadre et du libellé.'|@translate}</p>
    </div>

    <div class="help-tip">
      <strong> {'Important'|@translate} :</strong> {'N\'oubliez pas d\'enregistrer les modifications !!!'|@translate}
    </div>
  </div>

  <!-- Affichage des tags -->
  <div class="help-column">
    <h3>{'Affichage des tags'|@translate}</h3>

    <div class="help-section">
      <h4>{'Nuage de tags'|@translate}</h4>
      <p>{'Permet de masquer les tags visage dans le nuage de tags public, sans les supprimer : ils restent visibles et modifiables dans Administration > Tags.'|@translate}</p>
    </div>

    <div class="help-section">
      <h4>{'Synchronisation de la table des tags visage'|@translate}</h4>
      <p>{'Cette table fait la correspondance entre les tags Piwigo et les visages détectés dans le XMP des photos ; c\'est elle qui permet au plugin de savoir quels tags sont des tags visage. Il faut la (re)synchroniser après un import externe (digiKam, XnView...) ou une resynchronisation des métadonnées Piwigo.'|@translate}</p>
      <p>{'La synchronisation peut être restreinte à un album, et se déroule par petits lots, comme le plugin AlbumPilot : aucun risque de timeout, même sur une très grosse photothèque.'|@translate}</p>
    </div>

    <div class="help-section">
      <h4>{'Visages sans tag correspondant'|@translate}</h4>
      <p>{'Si un visage détecté dans le XMP ne trouve aucun tag Piwigo correspondant sur sa photo, la synchronisation tente automatiquement de corriger le problème (relecture des métadonnées, puis création directe du tag si besoin). Les cas qui échouent malgré tout apparaissent dans cette liste.'|@translate}</p>
      <p>{'Un bouton permet de retenter la correction uniquement sur ces photos, sans relancer une synchronisation complète.'|@translate}</p>
    </div>

    <div class="help-section">
      <h4>{'Vérification automatique à l\'affichage (avancé)'|@translate}</h4>
      <p>{'En complément de la synchronisation manuelle ci-dessus, il est possible d\'activer une vérification automatique : à chaque affichage d\'une photo, si un visage détecté dans le XMP n\'a pas de tag Piwigo correspondant, le tag est créé et attaché immédiatement. Utile notamment pour les photos ajoutées par upload web, qui ne sont pas toujours synchronisées correctement.'|@translate}</p>
      <div class="help-note">
        {'Ce n\'est pas un réglage de cet écran : ça s\'active en ajoutant cette ligne dans local/config/config.inc.php de Piwigo :'|@translate}
        <br><code>$conf['face_tag_check'] = true;</code>
        <p>{'Désactivé par défaut, car cette vérification ajoute une lecture du fichier à chaque affichage de photo — à réserver aux installations qui rencontrent réellement des photos mal synchronisées après upload.'|@translate}</p>
      </div>
    </div>

    <div class="help-section">
      <h4>{'Réinitialisation des visages'|@translate}</h4>
      <p>{'Efface entièrement la table des tags visage. Utile uniquement en cas de problème : la synchronisation habituelle suffit dans l\'immense majorité des cas.'|@translate}</p>
    </div>
  </div>

</div>
