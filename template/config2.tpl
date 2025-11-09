<div class="titrePage">
  <h2>Face Tag</h2>
</div>

<form method="post" action="" class="properties">
  <fieldset>
    <legend>Configuration avancée</legend>

    <!-- Saisie texte/valeur numérique -->
    <ul>
      <li>
        <label for="numeric_value">Valeur numérique :</label>
        <input type="number" name="numeric_value" id="numeric_value" value="{$NUMERIC_VALUE}" min="0" step="1">
        <br><small>Entrez une valeur numérique</small>
      </li>

      <!-- Boutons radio -->
      <li>
        <label>Option de configuration :</label>
        <br>
        <label>
          <input type="radio" name="radio_option" value="option1" {if $RADIO_OPTION == 'option1'}checked{/if}>
          Option 1
        </label>
        <br>
        <label>
          <input type="radio" name="radio_option" value="option2" {if $RADIO_OPTION == 'option2'}checked{/if}>
          Option 2
        </label>
      </li>

      <!-- Cases à cocher -->
      <li>
        <label>Cases à cocher :</label>
        <br>
        <label>
          <input type="checkbox" name="checkbox_cas1" value="1" {if $CHECKBOX_CAS1}checked{/if}>
          Cas 1
        </label>
        <br>
        <label>
          <input type="checkbox" name="checkbox_cas2" value="1" {if $CHECKBOX_CAS2}checked{/if}>
          Cas 2
        </label>
        <br>
        <label>
          <input type="checkbox" name="checkbox_cas3" value="1" {if $CHECKBOX_CAS3}checked{/if}>
          Cas 3
        </label>
      </li>

      <!-- Liste déroulante -->
      <li>
        <label for="select_option">Liste déroulante :</label>
        <select name="select_option" id="select_option">
          <option value="A1" {if $SELECT_OPTION == 'A1'}selected{/if}>A1</option>
          <option value="A2" {if $SELECT_OPTION == 'A2'}selected{/if}>A2</option>
          <option value="A3" {if $SELECT_OPTION == 'A3'}selected{/if}>A3</option>
          <option value="A4" {if $SELECT_OPTION == 'A4'}selected{/if}>A4</option>
        </select>
      </li>

      <!-- Slider horizontal -->
      <li>
        <label for="slider_value">Slider (0-100) : <span id="slider_display">{$SLIDER_VALUE}</span></label>
        <br>
        <input type="range" name="slider_value" id="slider_value" min="0" max="100" value="{$SLIDER_VALUE}" style="width: 300px;">
      </li>
    </ul>
  </fieldset>

 <p style="text-align: left; margin-left: 50;">
    <input type="submit" name="submit" value="Enregistrer la configuration">
  </p>
</form>

<script type="text/javascript">
// Mise à jour de l'affichage de la valeur du slider
document.getElementById('slider_value').addEventListener('input', function() {
  document.getElementById('slider_display').textContent = this.value;
});
</script>