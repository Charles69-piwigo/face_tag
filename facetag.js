// Face Tag v2.0 - JavaScript SIMPLIFIÉ

(function($) {
  'use strict';
  
  $(document).ready(function() {
    
    var facesData = null;
    var facesVisible = false;
    var displayMode = window.facetag_display_mode || 'toggle'; 

    var config = window.facetag_config || {
          rect_color: '#00ff00',
          rect_width: 3,
          rect_opacity: 0.8,
          label_text_color: '#ffffff',
          label_font_size: 12,
          label_bg_color: '#000000',
          label_bg_opacity: 0.8
    };

    console.log('Face Tag chargé - Mode:', displayMode);
    
    // ==================== BOUTON : AFFICHER LES VISAGES ====================
    $(document).on('click', '#facetag_show_faces', function(e) {
      e.preventDefault();
      //console.log('Clic sur bouton Visages');
      
      var ajaxUrl = $(this).data('ajax-url');
      
      // MODE PERSISTENT : vérifier le localStorage
      if (displayMode === 'persistent') {
        var isPersistentActive = localStorage.getItem('facetag_persistent_mode') === 'true';
        
        if (isPersistentActive) {
          // Désactiver le mode persistant
          localStorage.setItem('facetag_persistent_mode', 'false');
          hideFaces();
          $(this).removeClass('facetag-active');
          // Remettre l'indicateur bleu si des faces existent en cache
          if (facesData && facesData.faces && facesData.faces.length > 0) {
            $(this).addClass('facetag-has-faces');
          }
          return;
        } else {
          // Activer le mode persistant
          localStorage.setItem('facetag_persistent_mode', 'true');
          $(this).removeClass('facetag-has-faces').addClass('facetag-active');
        }

      } else {
        // MODE TOGGLE : comportement actuel
        if (facesVisible) {
          hideFaces();
          $(this).removeClass('facetag-active');
          return;
        } else {
          $(this).addClass('facetag-active');
        }
      }
      

      // Si on a déjà les données, on affiche directement
      if (facesData) {
        //console.log('Utilisation des données en cache');
        displayFaces(facesData);
        return;
      }
      
      // Sinon on charge les données via AJAX
      var $button = $(this);
      
      $.ajax({
        url: ajaxUrl,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
          //console.log('Réponse AJAX:', data);
          
          if (data.stat === 'ok' && data.result) {
            facesData = data.result;
            
            //console.log('=== DONNÉES REÇUES ===');
            //console.log('Dimensions:', facesData.wjpg, 'x', facesData.hjpg);
            //console.log('Orientation:', facesData.orientation);
            //console.log('Visages:', facesData.faces.length);
            //console.log('Détails:', facesData.faces);

            if (facesData.faces && facesData.faces.length > 0) {
              displayFaces(facesData);
            } else {
              //console.log('Aucun visage trouvé');
              alert('Aucun visage trouvé dans cette photo');
              $button.removeClass('facetag-active');
            }
          } else {
            console.error('Erreur dans la réponse:', data);
            alert('Erreur lors de la récupération des données');
            $button.removeClass('facetag-active');
          }
        },
        error: function(xhr, status, error) {
          console.error('Erreur AJAX:', error);
          alert('Erreur de connexion: ' + error);
          $button.removeClass('facetag-active');
        }
      });
    });
    
    //------------------------------------------------------------------------------------------------------------------
    // Afficher les visages sur l'image
    function displayFaces(data) {
      //console.log('=== AFFICHAGE DES VISAGES ===');
      
      // Chercher l'image
      var $img = $('img#theMainImage, img#theImage, .theMainImage img, .theImage img, #theImage img').first();
      
      if ($img.length === 0) {
        $img = $('#theImage img, #theMainImage img, .row img').first();
      }
      
      if ($img.length === 0) {
        console.error('Image <img> non trouvée !');
        alert('Image non trouvée');
        return;
      }
      
      //console.log('Image trouvée:', $img[0].src);
      
      // Wrapper l'image si nécessaire
      var $container = $img.parent();
      if (!$container.hasClass('facetag-container')) {
        $img.wrap('<div class="facetag-container"></div>');
        $container = $img.parent();
      }
      
      var $parentContainer = $container.parent();
      if (!$parentContainer.hasClass('facetag-wrapper')) {
        $container.wrap('<div class="facetag-wrapper"></div>');
        $parentContainer = $container.parent();
      }
      
      // Supprimer les anciens overlays
      $('.facetag-overlay').remove();
      
      // Parcourir chaque visage
      $.each(data.faces, function(index, face) {
        //console.log('--- Visage', index + 1, ':', face.nom, '---');
        //console.log('Méthode:', face.methode);
        //console.log('Coordonnées brutes - x:', face.x, 'y:', face.y, 'w:', face.w, 'h:', face.h);
        
        var left, top, width, height;
        
        if (face.methode === 'MPReg') {
          // MPReg: x,y sont le COIN SUPÉRIEUR GAUCHE
          left = face.x * 100;
          top = face.y * 100;
          width = face.w * 100;
          height = face.h * 100;
          //console.log('MPReg - Coin supérieur gauche direct');
        } else {
          // MWG-RS (et Lightroom): x,y sont le CENTRE
          left = (face.x - face.w / 2) * 100;
          top = (face.y - face.h / 2) * 100;
          width = face.w * 100;
          height = face.h * 100;
          //console.log('MWG-RS/Lightroom - Centre converti en coin supérieur gauche');
        }
        
        // Appliquer la transformation selon l'orientation EXIF
        var orientation = data.orientation || 1;
        var transformed = transformCoordinates(left, top, width, height, orientation);
        left = transformed.left;
        top = transformed.top;
        width = transformed.width;
        height = transformed.height;
        
        ////console.log('Position finale - left:', left.toFixed(2) + '%', 'top:', top.toFixed(2) + '%', 'width:', width.toFixed(2) + '%', 'height:', height.toFixed(2) + '%');
        
        // Créer l'overlay
        var $overlay = $('<div class="facetag-overlay">')
          .css({
            left: left + '%',
            top: top + '%',
            width: width + '%',
            height: height + '%',
            border: config.rect_width + 'px solid ' + hexToRgba(config.rect_color, config.rect_opacity),
            boxShadow: '0 0 10px ' + hexToRgba(config.rect_color, config.rect_opacity * 0.5)
          });

        var labelBgRgba = hexToRgba(config.label_bg_color, config.label_bg_opacity);
        var paddingVertical = Math.max(3, Math.round(config.label_font_size * 0.3));
        var paddingHorizontal = Math.max(8, Math.round(config.label_font_size * 0.7));

        var $label = $('<div class="facetag-label">')
          .text(face.nom)
          .css({
            color: config.label_text_color,
            fontSize: config.label_font_size + 'px',
            background: labelBgRgba,
            padding: paddingVertical + 'px ' + paddingHorizontal + 'px',
            fontWeight: 'normal',
            textShadow: '1px 1px 2px rgba(0, 0, 0, 0.8)',
            lineHeight: 'normal'
          });
        
        $overlay.append($label);
        $container.append($overlay);
        
        //console.log('✓ Overlay créé pour:', face.nom);
      });
      
      facesVisible = true;
      $('#facetag_show_faces .pwg-button-text').text('Masquer');
      
      //console.log('=== AFFICHAGE TERMINÉ ===');
    }
    
    //-----------------------------------------------------------------------------------
    // Masquer les visages
    function hideFaces() {
      $('.facetag-overlay').remove();
      facesVisible = false;
      $('#facetag_show_faces .pwg-button-text').text('Visages');
    }
    
    //-----------------------------------------------------------------------------------
    // Transformer les coordonnées selon l'orientation EXIF
    function transformCoordinates(left, top, width, height, orientation) {
      var newLeft, newTop, newWidth, newHeight;
      
      switch(orientation) {
        case 1: // Normal
          return {left: left, top: top, width: width, height: height};
          
        case 2: // Flip horizontal
          newLeft = 100 - left - width;
          return {left: newLeft, top: top, width: width, height: height};
          
        case 3: // Rotate 180
          newLeft = 100 - left - width;
          newTop = 100 - top - height;
          return {left: newLeft, top: newTop, width: width, height: height};
          
        case 4: // Flip vertical
          newTop = 100 - top - height;
          return {left: left, top: newTop, width: width, height: height};
          
            case 6: // Rotate 90 CW
    // Pour afficher correctement après rotation
    newLeft = 100 - top - height;
    newTop = left;
    newWidth = height;
    newHeight = width;
    return {left: newLeft, top: newTop, width: newWidth, height: newHeight};
          
       case 8: // Rotate 90 CCW (270 CW)  // V1.9A
  newLeft = top;
  newTop = 100 - left - width;
  newWidth = height;
  newHeight = width;
  return {left: newLeft, top: newTop, width: newWidth, height: newHeight};
      }
    }
    
    //-------------------------------------------------------------------------------------------------------
    // Convertir hexa en rgba
    function hexToRgba(hex, opacity) {
      var r = parseInt(hex.slice(1, 3), 16);
      var g = parseInt(hex.slice(3, 5), 16);
      var b = parseInt(hex.slice(5, 7), 16);
      return 'rgba(' + r + ', ' + g + ', ' + b + ', ' + opacity + ')';
    }
    
    //--------------------------------------------------------------------------------------------------------
    // MODE PERSISTENT : afficher automatiquement si activé
    if (displayMode === 'persistent') {
      var isPersistentActive = localStorage.getItem('facetag_persistent_mode') === 'true';
      //console.log('Mode persistant:', isPersistentActive);
      
      if (isPersistentActive) {

        $('#facetag_show_faces').addClass('facetag-active');
        var ajaxUrl = $('#facetag_show_faces').data('ajax-url');

        $.ajax({
          url: ajaxUrl,
          type: 'GET',
          dataType: 'json',
          success: function(data) {
            if (data.stat === 'ok' && data.result) {
              facesData = data.result;
              if (facesData.faces && facesData.faces.length > 0) {
                displayFaces(facesData);
              }else{
                //console.log('pas de visage');
              }
            }
          }
        });
      } else {
        // Mode désactivé : vérifier silencieusement s'il y a des faces pour afficher l'indicateur bleu
        var ajaxUrl = $('#facetag_show_faces').data('ajax-url');
        if (ajaxUrl) {
          $.ajax({
            url: ajaxUrl,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
              if (data.stat === 'ok' && data.result) {
                facesData = data.result;
                if (facesData.faces && facesData.faces.length > 0) {
                  $('#facetag_show_faces').addClass('facetag-has-faces');
                }
              }
            }
          });
        }
      }
    }

// ==================== BOUTON : AFFICHER XMP (DEBUG) ====================
$(document).on('click', '#facetag_show_xmp', function(e) {
  e.preventDefault();
  //console.log('Clic sur bouton XMP Debug');
  
  var ajaxUrl = $(this).data('ajax-url');
  
  $.ajax({
    url: ajaxUrl,
    type: 'GET',
    dataType: 'json',
    success: function(data) {
      //console.log('=== XMP DEBUG DATA ===');
      //console.log('Response:', data);
      
      if (data.stat === 'ok' && data.result) {
        //console.log('Image ID:', data.result.image_id);
        //console.log('Dimensions:', data.result.wjpg + 'x' + data.result.hjpg);
        //console.log('Orientation:', data.result.orientation);
        //console.log('Faces:', data.result.faces);
        
        // Afficher une modale avec les données
        var jsonStr = JSON.stringify(data.result, null, 2);
        
        var $modal = $('<div>')
          .css({
            position: 'fixed',
            top: '50%',
            left: '50%',
            transform: 'translate(-50%, -50%)',
            background: 'white',
            padding: '20px',
            border: '2px solid #333',
            borderRadius: '8px',
            maxWidth: '80%',
            maxHeight: '80%',
            overflow: 'auto',
            zIndex: 99999,
            boxShadow: '0 4px 20px rgba(0,0,0,0.5)'
          });
        
        var $close = $('<button>')
          .text('Fermer')
          .css({
            position: 'absolute',
            top: '10px',
            right: '10px',
            padding: '5px 15px',
            cursor: 'pointer'
          })
          .on('click', function() {
            $modal.remove();
            $overlay.remove();
          });
        
        var $pre = $('<pre>')
          .css({
            background: '#f5f5f5',
            padding: '15px',
            borderRadius: '4px',
            overflow: 'auto',
            fontSize: '12px',
            fontFamily: 'monospace'
          })
          .text(jsonStr);
        
        var $overlay = $('<div>')
          .css({
            position: 'fixed',
            top: 0,
            left: 0,
            right: 0,
            bottom: 0,
            background: 'rgba(0,0,0,0.5)',
            zIndex: 99998
          })
          .on('click', function() {
            $modal.remove();
            $overlay.remove();
          });
        
        $modal.append($close);
        $modal.append('<h3 style="margin-top:0;">XMP Debug Data</h3>');
        $modal.append($pre);
        
        $('body').append($overlay);
        $('body').append($modal);
      } else {
        alert('Erreur: ' + JSON.stringify(data));
      }
    },
    error: function(xhr, status, error) {
      console.error('Erreur AJAX XMP:', error);
      alert('Erreur AJAX: ' + error);
    }
  });
});




  }); // fin document.ready
})(jQuery); // fin IIFE