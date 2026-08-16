<div class="titrePage">
  <h2>{'Face Tag - Affichage des tags'|@translate}</h2>
</div>

<form method="post" action="" class="properties">

  <fieldset>
    <legend>{'Nuage de tags'|@translate}</legend>

    <ul>
      <li>
        <label>
          <input type="checkbox" name="hide_face_tags_cloud" {if $HIDE_FACE_TAGS_CLOUD}checked='checked'{/if}>
          {'Ne pas afficher les tags visage dans le nuage de tags'|@translate}
        </label>
        <p class="formHint">{'Ces tags restent visibles dans Administration > Tags'|@translate}</p>
      </li>
    </ul>

    <p style="text-align: left; margin-left: 40px;">
      <input type="submit" name="submit_tags_display" value="{'Enregistrer'|@translate}">
    </p>
  </fieldset>

  <fieldset>
    <legend>{'Synchronisation de la table des tags visage'|@translate}</legend>

    <ul>
      <li>
        <label>{'Restreindre la synchronisation à un album (facultatif) :'|@translate}</label>

        <style>
          #facetagCatTree { border:1px solid #ccc; height:20em; overflow-y:auto; width:100%; max-width:600px; padding:4px; box-sizing:border-box; background:#fff; cursor:default; }
          #facetagCatTree ul { list-style:none; margin:0; padding-left:20px !important; }
          #facetagCatTree > ul { padding-left:0 !important; }
          #facetagCatTree li { line-height:1.6em; white-space:nowrap; margin:0; padding:0; }
          .facetag-cat-toggle { display:inline-block; width:14px; text-align:center; cursor:pointer; color:#888; user-select:none; font-size:9px; }
          .facetag-cat-toggle.facetag-virtual { color:#2ea745; }
          .facetag-cat-label { cursor:pointer; padding:1px 5px; border-radius:3px; }
          .facetag-cat-label:hover { background:#e8f0fe; }
          .facetag-cat-label.active { background:#4a90d9; color:#fff; }
        </style>

        <select class="categoryList" name="facetag_sync_cat" size="8" style="display:none">
          <option value="">{'-- Toute la photothèque --'|@translate}</option>
          {html_options options=$facetag_category_options selected=$facetag_category_options_selected}
        </select>
        <div id="facetagCatTree"></div>

        <p class="formHint">{'Cliquer sur un album pour restreindre le scan, cliquer à nouveau pour désélectionner (= toute la photothèque)'|@translate}</p>
        <p class="formHint">{'Aucune sélection = scan de toutes les photos taguées, ce qui peut être long sur une grosse photothèque'|@translate}</p>
        <p class="formHint">{'Le triangle '|@translate}<span style="color:#2ea745">&#9658;</span>{' en vert indique un album virtuel (sans dossier lié) — il peut contenir des photos au même titre qu\'un album réel'|@translate}</p>
      </li>

      {footer_script}
      (function() {
        var cats = {$FACETAG_CAT_JSON};
        var $select = $('select[name="facetag_sync_cat"]');
        var $container = $('#facetagCatTree');
        if (!$select.length || !$container.length || !cats.length) return;

        var roots = [], stack = [];
        cats.forEach(function(cat) {
          cat.children = [];
          while (stack.length > cat.depth) stack.pop();
          (stack.length ? stack[stack.length - 1].children : roots).push(cat);
          stack.push(cat);
        });

        function renderNodes(list) {
          var $ul = $('<ul>');
          list.forEach(function(node) {
            var $li = $('<li>');
            var hasKids = node.children.length > 0;
            var $toggle = $('<span class="facetag-cat-toggle">').html(hasKids ? '&#9658;' : '&nbsp;');
            if (node.is_virtual) { $toggle.addClass('facetag-virtual'); }
            var $label  = $('<span class="facetag-cat-label">').text(node.name).attr('data-id', node.id);
            if (node.is_virtual) { $label.attr('title', '{'Album virtuel (sans dossier lié)'|@translate}'); }
            $li.append($toggle).append($label);
            if (hasKids) {
              var $sub = renderNodes(node.children).hide();
              $li.append($sub);
              $toggle.on('click', function() {
                $sub.toggle();
                $(this).html($sub.is(':visible') ? '&#9660;' : '&#9658;');
              });
            }
            $label.on('click', function() {
              if ($(this).hasClass('active')) {
                $(this).removeClass('active');
                $select.val('');
              } else {
                $container.find('.facetag-cat-label.active').removeClass('active');
                $(this).addClass('active');
                $select.val(node.id);
              }
            });
            $ul.append($li);
          });
          return $ul;
        }

        $container.append(renderNodes(roots));

        var presel = $select.val();
        if (presel) {
          var $active = $container.find('[data-id="' + presel + '"]').addClass('active');
          $active.parents('ul').each(function() {
            $(this).show();
            $(this).prev('.facetag-cat-toggle').html('&#9660;');
          });
        }
      })();
      {/footer_script}

      <li>
        <label>
          <input type="checkbox" name="facetag_subcats_included" checked="checked">
          {'Inclure les sous-albums'|@translate}
        </label>
      </li>

      <li>
        <style>
          #facetagSyncStart:disabled, #facetagSyncStop:disabled { opacity:0.5; cursor:not-allowed; }
          #facetagSyncBar.facetag-indeterminate {
            width:100% !important;
            background-image: repeating-linear-gradient(45deg, #4a90d9, #4a90d9 12px, #6ba7e0 12px, #6ba7e0 24px);
            background-size: 34px 34px;
            animation: facetagStripes 1s linear infinite;
          }
          @keyframes facetagStripes {
            from { background-position: 0 0; }
            to   { background-position: 34px 0; }
          }
        </style>

        <button type="button" id="facetagSyncStart">{'Synchroniser la table des tags visage'|@translate}</button>
        <button type="button" id="facetagSyncStop" style="display:none;">{'Arrêter'|@translate}</button>
        <p class="formHint">{'À relancer après un import externe (digiKam, XnView...) ou une resynchronisation des métadonnées'|@translate}</p>
        <p class="formHint">{'La synchronisation se fait par petits lots (comme le plugin AlbumPilot) : aucun risque de timeout (504), même sur une très grosse photothèque.'|@translate}</p>

        <div id="facetagSyncProgress" style="display:none; margin-top:10px; max-width:600px;">
          <div style="border:1px solid #ccc; border-radius:3px; height:16px; background:#eee; overflow:hidden;">
            <div id="facetagSyncBar" style="height:100%; width:0%; background:#4a90d9; transition:width 0.3s ease;"></div>
          </div>
          <p id="facetagSyncStatus" class="formHint"></p>
          <button type="button" id="facetagSyncReload" style="display:none;">{'Actualiser la page pour voir les résultats'|@translate}</button>
        </div>
      </li>
    </ul>
  </fieldset>

</form>

{footer_script}
(function() {
  var ajaxUrl = '{$FACETAG_AJAX_URL}';
  var pwgToken = '{$FACETAG_PWG_TOKEN}';

  var $startBtn  = $('#facetagSyncStart');
  var $stopBtn   = $('#facetagSyncStop');
  var $progress  = $('#facetagSyncProgress');
  var $bar       = $('#facetagSyncBar');
  var $status    = $('#facetagSyncStatus');
  var $reloadBtn = $('#facetagSyncReload');

  var stopRequested = false;

  function fetchJSON(params) {
    return $.getJSON(ajaxUrl + '&pwg_token=' + encodeURIComponent(pwgToken) + '&' + params);
  }

  $reloadBtn.on('click', function() { window.location.reload(); });

  function runChunk() {
    if (stopRequested) {
      $status.text('{'Arrêt demandé — ce qui a déjà été traité reste enregistré.'|@translate}');
      finish();
      return;
    }

    fetchJSON('facetag_sync_chunk=1').done(function(data) {
      if (data.error) {
        $status.text('{'Erreur'|@translate}: ' + data.error + (data.message ? ' — ' + data.message : ''));
        finish();
        return;
      }

      var percent = data.total > 0 ? Math.floor((data.index / data.total) * 100) : 100;
      $bar.removeClass('facetag-indeterminate').css('width', percent + '%');
      $status.text(
        percent + '% — ' + data.index + '/' + data.total + ' {'photos analysées'|@translate}'
        + (data.last_file ? ' — ' + data.last_file : '')
      );

      if (data.done) {
        var msg = '{'Synchronisation terminée'|@translate} : '
          + data.images_scanned + ' {'photos analysées'|@translate}, '
          + data.tags_found + ' {'tags visage identifiés'|@translate}';
        if (data.unresolved_count > 0) {
          msg += ', ' + data.unresolved_count + ' {'visages sans tag correspondant'|@translate}';
        }
        if (data.images_failed > 0) {
          msg += ' — ' + data.images_failed + ' {'photo(s) en erreur (voir le journal du serveur)'|@translate}';
        }
        $bar.css('width', '100%');
        $status.text(msg);
        $reloadBtn.show();
        finish();
        return;
      }

      runChunk();
    }).fail(function(jqXHR) {
      var detail = jqXHR && jqXHR.responseText ? jqXHR.responseText.substring(0, 200) : '';
      $status.text('{'Erreur réseau — nouvel essai automatique, le travail déjà fait reste enregistré.'|@translate}' + (detail ? ' (' + detail + ')' : ''));
      setTimeout(runChunk, 3000);
    });
  }

  function finish() {
    $startBtn.prop('disabled', false).text('{'Synchroniser la table des tags visage'|@translate}');
    $stopBtn.hide();
    $bar.removeClass('facetag-indeterminate');
  }

  $startBtn.on('click', function() {
    var catId = $('select[name="facetag_sync_cat"]').val() || '';
    var subcats = $('input[name="facetag_subcats_included"]').is(':checked') ? '1' : '0';

    stopRequested = false;
    $reloadBtn.hide();
    $startBtn.prop('disabled', true).text('{'Synchronisation en cours...'|@translate}');
    $stopBtn.show();
    $progress.show();
    $bar.addClass('facetag-indeterminate');
    $status.text('{'Démarrage — comptage des photos en cours, ça peut prendre quelques secondes sur un gros album, merci de patienter...'|@translate}');

    fetchJSON('facetag_sync_start=1&cat_id=' + encodeURIComponent(catId) + '&subcats_included=' + subcats)
      .done(function(data) {
        if (data.error) {
          $bar.removeClass('facetag-indeterminate');
          $status.text('{'Erreur'|@translate}: ' + data.error + (data.message ? ' — ' + data.message : ''));
          finish();
          return;
        }
        if (data.done) {
          $status.text('{'Aucune photo à traiter dans ce périmètre.'|@translate}');
          finish();
          return;
        }
        runChunk();
      })
      .fail(function(jqXHR) {
        var detail = jqXHR && jqXHR.responseText ? jqXHR.responseText.substring(0, 200) : '';
        $status.text('{'Erreur au démarrage de la synchronisation.'|@translate}' + (detail ? ' (' + detail + ')' : ''));
        finish();
      });
  });

  $stopBtn.on('click', function() {
    stopRequested = true;
    $stopBtn.prop('disabled', true);
  });
})();
{/footer_script}

{if !empty($FACETAG_UNRESOLVED_LIST)}
<fieldset>
  <legend>{'Visages sans tag correspondant'|@translate} ({$FACETAG_UNRESOLVED_LIST|@sizeOf})</legend>

  <p class="formHint">
    {'La synchro tente automatiquement de corriger ces cas (relecture des métadonnées, puis création directe du tag si besoin) — ce qui reste ici a échoué même après ces tentatives (erreur technique lors de la création du tag). Voir le journal du serveur pour le détail de l\'erreur.'|@translate}
  </p>

  <table class="table2">
    <tr>
      <th>{'Photo'|@translate}</th>
      <th>{'Visage(s) non résolu(s)'|@translate}</th>
      <th>{'Tags actuellement sur la photo'|@translate}</th>
    </tr>
    {foreach from=$FACETAG_UNRESOLVED_LIST item=entry}
    <tr>
      <td><a href="{$entry.U_EDIT}" target="_blank">{$entry.FILE}</a></td>
      <td>{$entry.FACES}</td>
      <td>{if !empty($entry.TAGS)}{$entry.TAGS}{else}<em>{'aucun'|@translate}</em>{/if}</td>
    </tr>
    {/foreach}
  </table>

  <p style="margin-top:10px;">
    <button type="button" id="facetagResyncStart">{'Tenter une resynchronisation des métadonnées sur ces photos uniquement'|@translate}</button>
    <button type="button" id="facetagResyncStop" style="display:none;">{'Arrêter'|@translate}</button>
  </p>
  <p class="formHint">
    {'Retente les mêmes corrections automatiques que la synchro (vérification, relecture des métadonnées Piwigo, puis création directe du tag) juste pour les photos ci-dessus, sans toucher au reste de la photothèque. Utile après une correction manuelle externe (ex: digiKam), sans tout rescanner.'|@translate}
  </p>

  <div id="facetagResyncProgress" style="display:none; margin-top:10px; max-width:600px;">
    <div style="border:1px solid #ccc; border-radius:3px; height:16px; background:#eee; overflow:hidden;">
      <div id="facetagResyncBar" style="height:100%; width:0%; background:#4a90d9; transition:width 0.3s ease;"></div>
    </div>
    <p id="facetagResyncStatus" class="formHint"></p>
    <button type="button" id="facetagResyncReload" style="display:none;">{'Actualiser la page pour voir les résultats'|@translate}</button>
  </div>
</fieldset>

{footer_script}
(function() {
  var ajaxUrl = '{$FACETAG_AJAX_URL}';
  var pwgToken = '{$FACETAG_PWG_TOKEN}';

  var $startBtn  = $('#facetagResyncStart');
  var $stopBtn   = $('#facetagResyncStop');
  var $progress  = $('#facetagResyncProgress');
  var $bar       = $('#facetagResyncBar');
  var $status    = $('#facetagResyncStatus');
  var $reloadBtn = $('#facetagResyncReload');

  var stopRequested = false;

  function fetchJSON(params) {
    return $.getJSON(ajaxUrl + '&pwg_token=' + encodeURIComponent(pwgToken) + '&' + params);
  }

  $reloadBtn.on('click', function() { window.location.reload(); });

  function finish() {
    $startBtn.prop('disabled', false).text('{'Tenter une resynchronisation des métadonnées sur ces photos uniquement'|@translate}');
    $stopBtn.hide();
    $bar.removeClass('facetag-indeterminate');
  }

  function runChunk() {
    if (stopRequested) {
      $status.text('{'Arrêt demandé — ce qui a déjà été traité reste enregistré.'|@translate}');
      finish();
      return;
    }

    fetchJSON('facetag_resync_chunk=1').done(function(data) {
      if (data.error) {
        $bar.removeClass('facetag-indeterminate');
        $status.text('{'Erreur'|@translate}: ' + data.error + (data.message ? ' — ' + data.message : ''));
        finish();
        return;
      }

      var percent = data.total > 0 ? Math.floor((data.index / data.total) * 100) : 100;
      $bar.removeClass('facetag-indeterminate').css('width', percent + '%');
      $status.text(
        percent + '% — ' + data.index + '/' + data.total + ' {'photos traitées'|@translate}'
        + (data.last_file ? ' — ' + data.last_file : '')
      );

      if (data.done) {
        var msg = '{'Resynchronisation terminée'|@translate} : '
          + data.images_processed + ' {'photos traitées'|@translate}, '
          + data.resolved_count + ' {'discordance(s) résolue(s)'|@translate}';
        if (data.images_failed > 0) {
          msg += ' — ' + data.images_failed + ' {'photo(s) en erreur (voir le journal du serveur)'|@translate}';
        }
        $bar.css('width', '100%');
        $status.text(msg);
        $reloadBtn.show();
        finish();
        return;
      }

      runChunk();
    }).fail(function(jqXHR) {
      var detail = jqXHR && jqXHR.responseText ? jqXHR.responseText.substring(0, 200) : '';
      $status.text('{'Erreur réseau — nouvel essai automatique, le travail déjà fait reste enregistré.'|@translate}' + (detail ? ' (' + detail + ')' : ''));
      setTimeout(runChunk, 3000);
    });
  }

  $startBtn.on('click', function() {
    stopRequested = false;
    $reloadBtn.hide();
    $startBtn.prop('disabled', true).text('{'Resynchronisation en cours...'|@translate}');
    $stopBtn.show();
    $progress.show();
    $bar.addClass('facetag-indeterminate');
    $status.text('{'Démarrage...'|@translate}');

    fetchJSON('facetag_resync_start=1')
      .done(function(data) {
        if (data.error) {
          $bar.removeClass('facetag-indeterminate');
          $status.text('{'Erreur'|@translate}: ' + data.error + (data.message ? ' — ' + data.message : ''));
          finish();
          return;
        }
        if (data.done) {
          $status.text('{'Rien à traiter.'|@translate}');
          finish();
          return;
        }
        runChunk();
      })
      .fail(function(jqXHR) {
        var detail = jqXHR && jqXHR.responseText ? jqXHR.responseText.substring(0, 200) : '';
        $status.text('{'Erreur au démarrage.'|@translate}' + (detail ? ' (' + detail + ')' : ''));
        finish();
      });
  });

  $stopBtn.on('click', function() {
    stopRequested = true;
    $stopBtn.prop('disabled', true);
  });
})();
{/footer_script}
{/if}

<form method="post" action="" onsubmit="return confirm('{'Effacer toute la table des tags visage ? Cette action est irréversible.'|@translate}');">
  <fieldset>
    <legend>{'Réinitialisation des visages'|@translate}</legend>

    <p class="formHint">
      {'Efface entièrement la table de correspondance des tags visage (auto ET manuel). Le nuage de tags public affichera de nouveau tous les tags jusqu\'à la prochaine synchronisation. Les corrections manuelles éventuellement faites sont définitivement perdues.'|@translate}
    </p>
    <p class="formHint">
      {'Utile uniquement en cas de problème (ex: repartir de zéro après un changement de configuration) — la synchronisation habituelle (ci-dessus) suffit dans l\'immense majorité des cas.'|@translate}
    </p>

    <p style="text-align: left; margin-left: 40px;">
      <input type="submit" name="reset_person_tags" value="{'Effacer la table des tags visage'|@translate}" style="color:#a94442;">
    </p>
  </fieldset>
</form>
