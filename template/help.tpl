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
  line-height: 1.6;
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
  <!-- Colonne gauche -->
  <div class="help-column">
    <h3>🚀 Démarrage rapide</h3>
    
    <div class="help-section">
      <h4>Installation</h4>
      <p>Le plugin Face Tag permet d'afficher les visages tagués avec Digikam dans votre galerie Piwigo.</p>
      <ul>
        <li>Installez le plugin depuis le gestionnaire de plugins</li>
        <li>Activez-le</li>
        <li>Configurez les paramètres selon vos besoins</li>
      </ul>
    </div>

    <div class="help-section">
      <h4>Configuration générale</h4>
      <p>Dans l'onglet "Configuration générale", vous pouvez définir :</p>
      <ul>
        <li><strong>Paramètre 1 :</strong> Description du paramètre 1</li>
        <li><strong>Paramètre 2 :</strong> Description du paramètre 2</li>
      </ul>
    </div>

    <div class="help-note">
      <strong>💡 Note :</strong> Pensez à sauvegarder vos modifications après chaque changement de configuration.
    </div>

    <div class="help-section">
      <h4>Intégration avec Digikam</h4>
      <p>Pour que le plugin fonctionne correctement avec Digikam :</p>
      <ol>
        <li>Assurez-vous que vos photos sont taguées dans Digikam</li>
        <li>Exportez vos photos vers Piwigo</li>
        <li>Le plugin détectera automatiquement les tags de visages</li>
      </ol>
    </div>
  </div>

  <!-- Colonne droite -->
  <div class="help-column">
    <h3>⚙️ Paramètres avancés</h3>
    
    <div class="help-section">
      <h4>Valeur numérique</h4>
      <p>Définit le nombre d'éléments à afficher par page. Valeur recommandée : entre 10 et 50.</p>
    </div>

    <div class="help-section">
      <h4>Options de configuration</h4>
      <ul>
        <li><strong>Option 1 :</strong> Mode d'affichage standard</li>
        <li><strong>Option 2 :</strong> Mode d'affichage avancé avec filtres</li>
      </ul>
    </div>

    <div class="help-section">
      <h4>Cases à cocher</h4>
      <ul>
        <li><strong>Cas 1 :</strong> Activer la fonctionnalité X</li>
        <li><strong>Cas 2 :</strong> Activer la fonctionnalité Y</li>
        <li><strong>Cas 3 :</strong> Activer la fonctionnalité Z</li>
      </ul>
    </div>

    <div class="help-section">
      <h4>Liste déroulante</h4>
      <p>Sélectionnez le mode de tri des visages :</p>
      <ul>
        <li><strong>A1 :</strong> Tri par nom</li>
        <li><strong>A2 :</strong> Tri par date</li>
        <li><strong>A3 :</strong> Tri par popularité</li>
        <li><strong>A4 :</strong> Tri personnalisé</li>
      </ul>
    </div>

    <div class="help-tip">
      <strong>💡 Astuce :</strong> Le slider permet d'ajuster la sensibilité de détection. Une valeur plus élevée détecte plus de visages mais peut générer des faux positifs.
    </div>

    <div class="help-section">
      <h4>Slider horizontal</h4>
      <p>Ajuste le seuil de confiance pour la reconnaissance faciale (0-100). Valeur recommandée : 70-80.</p>
    </div>

    <div class="help-section">
      <h4>Besoin d'aide ?</h4>
      <p>Pour toute question ou problème :</p>
      <ul>
        <li>Consultez la documentation sur le forum Piwigo</li>
        <li>Contactez le support</li>
        <li>Visitez le site du plugin</li>
      </ul>
    </div>
  </div>
</div>