<div class="titrePage">
  <h2>Face Tag</h2>
</div>

<style>
.help-container {
  display: flex;
  gap: 20px;
  margin-top: 20px;
}

.help-column {
  flex: 1;
  background: #f9f9f9;
  padding: 15px;
  border-radius: 5px;
  border: 1px solid #ddd;
}

.help-column h3 {
  margin-top: 0;
  color: #333;
  border-bottom: 2px solid #ff7700;
  padding-bottom: 10px;
}

.help-section {
  margin-bottom: 25px;
}

.help-section h4 {
  color: #555;
  margin-top: 15px;
  margin-bottom: 8px;
}

.help-section p {
  text-align : left ;
  line-height: 1.4;
  color: #666;
}

.help-section ul {
  margin: 10px 0;
  padding-left: 25px;
}

.help-section li {
  margin-bottom: 8px;
  line-height: 1.5;
}

.help-note {
  background: #fff3cd;
  border-left: 4px solid #ffc107;
  padding: 10px 15px;
  margin: 15px 0;
  border-radius: 3px;
}

.help-tip {
  background: #d1ecf1;
  border-left: 4px solid #17a2b8;
  padding: 10px 15px;
  margin: 15px 0;
  border-radius: 3px;
}

@media (max-width: 768px) {
  .help-container {
    flex-direction: column;
  }
}
</style>

<div class="help-container">

 <!-- Colonne droite -->
  <div class="help-column">
    <h3>{'Paramètres'|@translate}</h3>
    
    <div class="help-section">
      <h4>{'Modes de fonctionnement'|@translate}</h4>  
      <h4>{'Affichage temporaire ou Affichage permanent'|@translate}</h4>
      <p>{'On peut utiliser le plugin de deux façons :'|@translate} </p>
      <ul>
        <li><strong>{'Affichage temporaire :'|@translate}</strong></li>
        <p>{'Dans ce mode cliquez sur le bouton pour afficher les visages, un deuxième clic enlève l\'affichage'|@translate} </p>
        <p>{'Quand on change de photo, il faut recliquer sur le bouton pour afficher les visages'|@translate} </p>
        <li><strong>{'Affichage persistant :'|@translate}</strong></li>
        <p>{'Dans ce mode le bouton fonctionne comme un interrupteur, si des visages sont tagués sur la photo ils s\'affichent, quelle que soit la photo'|@translate} </p>
        <p>{'Un deuxième clic stoppe l\'affichage des visages'|@translate} </p>
      </ul>
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
      <strong> Important  :</strong> {'N\'oubliez pas d\'enregistrer les modifications !!!'|@translate}
    </div>

        
  </div>

  <!-- Colonne gauche -->
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
      <p>{'Face Tag détecte automatiquement la méthode utilisée. '|@translate}</p>
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

 
</div>