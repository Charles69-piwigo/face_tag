<div class="titrePage">
  <h2>{'Face Tag - Configuration générale'|@translate}</h2>
</div>

<form method="post" action="" class="properties">
   <p style="text-align: left; margin-left: 40px;">
    <input type="submit" name="submit" value="{'Enregistrer'|@translate}">
  </p>
  <fieldset>
    
    
    <ul>

      <legend>{'Bouton d\'interface'|@translate}</legend>
      <li>
        <label>
          <input type="checkbox" name="show_faces_button" {if $SHOW_FACES_BUTTON}checked="checked{/if}>
          {'Afficher le bouton "Afficher les visages"'|@translate}
        </label>
      </li>

      <li>
        <label>{'Mode d\'affichage des visages :'|@translate}</label>
          <select name="faces_display_mode">
            <option value="toggle" {if $FACES_DISPLAY_MODE == 'toggle'}selected{/if}>
            {'Afficher/masquer à chaque clic (bouton temporaire)'|@translate}
            </option>
            <option value="persistent" {if $FACES_DISPLAY_MODE == 'persistent'}selected{/if}>
            {'Activer/désactiver globalement (interrupteur persistant)'|@translate}
            </option>
          </select>
        <p class="formHint">{'En mode interrupteur, les visages restent affichés...'|@translate}</p>
      </li>

    </ul>
  </fieldset>

  <fieldset>
  <legend>{'Apparence du rectangle'|@translate}</legend>
  
  <ul>
    <li>
      <label>
        {'Couleur du cadre :'|@translate}
        <input type="color" name="rect_color" value={$RECT_COLOR}>
      </label>
    </li>
    
    <li>
      <label>
        {'Épaisseur du cadre (px) :'|@translate}
        <input type="number" name="rect_width" value={$RECT_WIDTH} min="1" max="10" style="width: 80px;">
      </label>
    </li>
    
    <li>
      <label>
        {'Opacité du cadre :'|@translate}
        <input type="range" name="rect_opacity" value={$RECT_OPACITY} min="0" max="1" step="0.1" style="width: 200px;" oninput="this.nextElementSibling.textContent = this.value">
        <span>{$RECT_OPACITY}</span>
      </label>
      <p class="formHint">{'0 = transparent, 1 = opaque'|@translate}</p>
    </li>
  </ul>
</fieldset>

<fieldset>
  <legend>{'Apparence du libellé'|@translate}</legend>
  
  <ul>
    <li>
      <label>
        {'Couleur du texte :'|@translate}
        <input type="color" name="label_text_color" value={$LABEL_TEXT_COLOR}>
      </label>
    </li>
    
    <li>
      <label>
        {'Taille de la police (px) :'|@translate}
        <input type="number" name="label_font_size" value={$LABEL_FONT_SIZE} min="8" max="24" style="width: 80px;">
      </label>
    </li>
    
    <li>
      <label>
        {'Couleur de fond :'|@translate}
        <input type="color" name="label_bg_color" value={$LABEL_BG_COLOR}>
      </label>
    </li>
    
    <li>
      <label>
        {'Opacité du fond :'|@translate}
        <input type="range" name="label_bg_opacity" value={$LABEL_BG_OPACITY} min="0" max="1" step="0.1" style="width: 200px;" oninput="this.nextElementSibling.textContent = this.value">
        <span>{$LABEL_BG_OPACITY}</span>
      </label>
      <p class="formHint">{'0 = transparent, 1 = opaque'|@translate}</p>
    </li>
  </ul>
<legend>{'Bouton XMP'|@translate}</legend>
    <ul>  
    <li>
        <label>
          <input type="checkbox" name="show_xmp_button" {if $SHOW_XMP_BUTTON}checked="checked{/if}>
          {'Afficher le bouton "Voir les XMP" - pour débugage'|@translate}
        </label>
      </li>
</ul>
</fieldset>
  
  <p style="text-align: left; margin-left: 40px;">
    <input type="submit" name="submit" value="{'Enregistrer'|@translate}">
  </p>
</form>