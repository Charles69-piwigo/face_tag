

<script type="text/javascript">
var facetag_display_mode = '{$FACETAG_DISPLAY_MODE}';
var facetag_config = {
  rect_color: '{$FACETAG_RECT_COLOR}',
  rect_width: {$FACETAG_RECT_WIDTH},
  rect_opacity: {$FACETAG_RECT_OPACITY},
  label_text_color: '{$FACETAG_LABEL_TEXT_COLOR}',
  label_font_size: {$FACETAG_LABEL_FONT_SIZE},
  label_bg_color: '{$FACETAG_LABEL_BG_COLOR}',
  label_bg_opacity: {$FACETAG_LABEL_BG_OPACITY}
};

</script>

<script type="text/javascript" src="{$FACETAG_PATH}facetag.js"></script>

{if $FACETAG_SHOW_XMP}
<a id="facetag_show_xmp" href="#" data-ajax-url="{$FACETAG_AJAX_URL}" title="Voir les métadonnées XMP">
  <img src="{$FACETAG_ICON}" alt="XMP" style="width:24px; height:24px; vertical-align:middle;background-color: white;">
  <span class="pwg-button-text">XMP</span>
</a>
{/if}

{if $FACETAG_SHOW_FACES}
<a id="facetag_show_faces" href="#" data-ajax-url="{$FACETAG_AJAX_URL}" title="Afficher les visages tagués">
  <img src="{$FACETAG_ICON}" alt="Faces" style="width:24px; height:24px; vertical-align:middle;background-color: white">
  <span class="pwg-button-text">Visages</span>
</a>
{/if}