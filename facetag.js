// Face Tag - JavaScript
(function($) {
  'use strict';
  
  $(document).ready(function() {
    
    var xmpData = null;
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


/*
console.log('=== DEBUG MODE ===');
console.log('window.facetag_display_mode:', window.facetag_display_mode);
console.log('displayMode final:', displayMode);
console.log('localStorage facetag_persistent_mode:', localStorage.getItem('facetag_persistent_mode'));
console.log('==================');
console.log('Face Tag JS chargé');
console.log('Mode d affichage:', displayMode); 
console.log('Bouton XMP:', $('#facetag_show_xmp').length);
console.log('Bouton Faces:', $('#facetag_show_faces').length);
*/    
    
    // ==================== BOUTON 1 : AFFICHER LES XMP ====================
    $(document).on('click', '#facetag_show_xmp', function(e) {
      e.preventDefault();
      //console.log('Clic sur bouton XMP');
      
      var ajaxUrl = $(this).data('ajax-url');
      //console.log('AJAX URL:', ajaxUrl);
      
      // Si on a déjà les données, on les affiche
      if (xmpData) {
        displayXmpData(xmpData);
        return;
      }
      
      // Afficher un message de chargement
      showXmpModal('<div style="text-align:center; padding:20px;">Chargement des données XMP...</div>');
      
      // Récupérer les données XMP via AJAX
      $.ajax({
        url: ajaxUrl,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
          if (data.stat === 'ok' && data.result.xmp) {
            xmpData = data.result.xmp;
            displayXmpData(xmpData);
          } else if (data.result && data.result.error) {
            showXmpModal('<div style="color:red; padding:10px;">Erreur: ' + data.result.error + '</div>');
          } else {
            showXmpModal('<div style="color:red; padding:10px;">Erreur lors de la récupération des données XMP</div>');
          }
        },
        error: function(xhr, status, error) {
          showXmpModal('<div style="color:red; padding:20px;">Erreur de connexion: ' + error + '</div>');
        }
      });
    });
    
    // Fonction pour afficher les données XMP
    function displayXmpData(data) {
      var html = '<div style="max-height:600px; overflow-y:auto;">';
      html += '<h3 style="margin-top:0;">Données XMP</h3>';
      
      if (Object.keys(data).length === 0) {
        html += '<p>Aucune donnée XMP trouvée dans cette image.</p>';
      } else {
        html += '<table style="width:100%; font-size:14px; border-collapse:collapse;">';
        
        // Afficher les données XMP
        $.each(data, function(key, value) {
          if (key !== '_raw_xmp') {
            html += '<tr style="border-bottom:1px solid #eee;">';
            html += '<td style="font-weight:bold; padding:8px; vertical-align:top; width:40%;">' + escapeHtml(key) + '</td>';
            html += '<td style="padding:8px; word-break:break-word;">' + escapeHtml(value) + '</td>';
            html += '</tr>';
          }
        });
        
        html += '</table>';
        
        // Afficher le XMP brut si disponible
        if (data._raw_xmp) {
          html += '<hr style="margin:20px 0;">';
          html += '<h4>XMP Brut:</h4>';
          html += '<pre style="background:#f5f5f5; padding:10px; overflow-x:auto; font-size:11px; max-height:300px;">' + escapeHtml(data._raw_xmp) + '</pre>';
        }
      }
      
      html += '</div>';
      
      showXmpModal(html);
    }
    
    // Fonction pour afficher le modal XMP
    function showXmpModal(content) {
      // Supprimer les modaux existants
      $('#xmp_modal, #xmp_overlay').remove();
      
      var modal = '<div id="xmp_modal" style="position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); background:white; padding:20px; border:2px solid #ccc; z-index:10000; max-width:80%; max-height:80%; overflow:auto; box-shadow:0 0 20px rgba(0,0,0,0.5); border-radius:5px;">';
      modal += content;
      modal += '<div style="text-align:center; margin-top:20px;"><button id="close_xmp_modal" style="padding:8px 20px; cursor:pointer; background:#333; color:white; border:none; border-radius:3px;">Fermer</button></div>';
      modal += '</div>';
      
      var overlay = '<div id="xmp_overlay" style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:9999;"></div>';
      
      $('body').append(overlay + modal);
      
      // Fermeture du modal
      $('#xmp_overlay, #close_xmp_modal').click(function() {
        $('#xmp_modal, #xmp_overlay').remove();
      });
      
      // Fermeture avec la touche Escape
      $(document).on('keyup.xmpmodal', function(e) {
        if (e.keyCode === 27) { // ESC
          $('#xmp_modal, #xmp_overlay').remove();
          $(document).off('keyup.xmpmodal');
        }
      });
    }
    


    // ==================== BOUTON 2 : AFFICHER LES VISAGES ====================
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
      return;
    } else {
      // Activer le mode persistant
      localStorage.setItem('facetag_persistent_mode', 'true');
      $(this).addClass('facetag-active');
    }
  } else {
    // MODE TOGGLE : comportement actuel
    if (facesVisible) {
      hideFaces();
      $(this).removeClass('facetag-active'); // AJOUT ICI
      return;
    } else {
      $(this).addClass('facetag-active'); // AJOUT ICI
    }
  }
  
  // Si on a déjà les données XMP, on parse directement
  if (xmpData) {
    facesData = parseFacesFromXMP(xmpData);
    //console.log('Visages trouvés:', facesData);
    
    if (facesData.length > 0) {
      displayFaces(facesData);
    } else {
      // Aucun visage : retirer la classe active
      $(this).removeClass('facetag-active'); // AJOUT ICI
    }
    return;
  }
  
  // Sinon on charge les données XMP d'abord
  var $button = $(this); // Stocker la référence au bouton
  
  $.ajax({
    url: ajaxUrl,
    type: 'GET',
    dataType: 'json',
    success: function(data) {
      if (data.stat === 'ok' && data.result.xmp) {
        xmpData = data.result.xmp;
        facesData = parseFacesFromXMP(xmpData);
        
        //console.log('XMP data:', xmpData);
        //console.log('Visages trouvés:', facesData);
        
        if (facesData.length > 0) {
          displayFaces(facesData);
        } else {
          // Aucun visage : retirer la classe active
          $button.removeClass('facetag-active'); // AJOUT ICI
        }
      } else {
        alert('Erreur lors de la récupération des données XMP');
        $button.removeClass('facetag-active'); // AJOUT ICI
      }
    },
    error: function(xhr, status, error) {
      alert('Erreur de connexion: ' + error);
      $button.removeClass('facetag-active'); // AJOUT ICI
    }
  });
});
    
    // Parser les données XMP pour extraire les visages
    function parseFacesFromXMP(data) {
      var faces = [];
      
      //console.log('Parsing XMP data...');
      
      // Si on a le XMP brut, on le parse
      if (data._raw_xmp) {
        //console.log('Parsing _raw_xmp...');
        
        var xmlString = data._raw_xmp;
        
        // Parser avec DOMParser
        var parser = new DOMParser();
        var xmlDoc = parser.parseFromString(xmlString, "text/xml");
        
        // === FORMAT 1 : MPReg (Microsoft Photo Region) ===
        var mpriRegions = xmlDoc.getElementsByTagName('MPRI:Regions');
        //console.log('MPRI:Regions trouvés:', mpriRegions.length);
        
        if (mpriRegions.length > 0) {
          var rdfLis = mpriRegions[0].getElementsByTagName('rdf:li');
          //console.log('Nombre de rdf:li dans MPRI:', rdfLis.length);
          
          for (var i = 0; i < rdfLis.length; i++) {
            var li = rdfLis[i];
            var name = null;
            var rect = null;
            
            // CAS 1 : Attributs directs (format inline)
            name = li.getAttribute('MPReg:PersonDisplayName');
            rect = li.getAttribute('MPReg:Rectangle');
            
            //console.log('rdf:li', i, '(attributs) - Name:', name, 'Rectangle:', rect);
            
            // CAS 2 : Balises enfants (format multiligne)
            if (!name || !rect) {
              var personNodes = li.getElementsByTagName('MPReg:PersonDisplayName');
              var rectNodes = li.getElementsByTagName('MPReg:Rectangle');
              
              if (personNodes.length > 0) {
                name = personNodes[0].textContent.trim();
              }
              if (rectNodes.length > 0) {
                rect = rectNodes[0].textContent.trim();
              }
              
              //console.log('rdf:li', i, '(balises) - Name:', name, 'Rectangle:', rect);
            }
            
            if (name && rect) {
              var coords = rect.split(',').map(function(v) {
                return parseFloat(v.trim());
              });
              
              if (coords.length === 4) {
                faces.push({
                  name: name,
                  x: coords[0],
                  y: coords[1],
                  w: coords[2],
                  h: coords[3],
                  format: 'mpreg'
                });
                //console.log('✓ Visage ajouté (MPReg):', name, coords);
              }
            }
          }
        }
        
        // === FORMAT 2 : mwg-rs (Metadata Working Group - Regions) ===
        var mwgRegionList = xmlDoc.getElementsByTagName('mwg-rs:RegionList');
        //console.log('mwg-rs:RegionList trouvés:', mwgRegionList.length);
        
        if (mwgRegionList.length > 0) {
          var descriptions = mwgRegionList[0].getElementsByTagName('rdf:Description');
          //console.log('Nombre de rdf:Description dans mwg-rs:', descriptions.length);
          
          for (var i = 0; i < descriptions.length; i++) {
            var desc = descriptions[i];
            
            var name = desc.getAttribute('mwg-rs:Name');
            var type = desc.getAttribute('mwg-rs:Type');
            
            //console.log('Description', i, '- Name:', name, 'Type:', type);
            
            if (name && type === 'Face') {
              // Chercher la balise mwg-rs:Area
              var areas = desc.getElementsByTagName('mwg-rs:Area');
              
              if (areas.length > 0) {
                var area = areas[0];
                var x = area.getAttribute('stArea:x');
                var y = area.getAttribute('stArea:y');
                var w = area.getAttribute('stArea:w');
                var h = area.getAttribute('stArea:h');
                
                //console.log('Area:', x, y, w, h);
                
                if (x && y && w && h) {
                  // Vérifier qu'on n'a pas déjà ajouté ce visage (éviter les doublons)
                  var alreadyExists = false;
                  for (var j = 0; j < faces.length; j++) {
                    if (faces[j].name === name) {
                      alreadyExists = true;
                      break;
                    }
                  }
                  
                  if (!alreadyExists) {
                    faces.push({
                      name: name,
                      x: parseFloat(x),
                      y: parseFloat(y),
                      w: parseFloat(w),
                      h: parseFloat(h),
                      format: 'mwg-rs'
                    });
                    //console.log('✓ Visage ajouté (mwg-rs):', name);
                  } else {
                    //console.log('⊘ Visage ignoré (doublon):', name);
                  }
                }
              }
            }
          }
        }
      }
      
      //console.log('=== Total visages trouvés:', faces.length, '===');
      //console.log('Détails:', faces);
      return faces;
    }
    
    // Afficher les visages sur l'image
    function displayFaces(faces) {
      // Chercher d'abord la balise IMG
      var $img = $('img#theMainImage, img#theImage, .theMainImage img, .theImage img, #theImage img').first();
      
      // Si pas trouvé, chercher dans les conteneurs communs
      if ($img.length === 0) {
        $img = $('#theImage img, #theMainImage img, .row img').first();
      }
      
      if ($img.length === 0) {
        //console.error('Image <img> non trouvée !');
        alert('Image non trouvée');
        return;
      }
      
      //console.log('DEBUG - $img:', $img);
      //console.log('DEBUG - Tag:', $img[0].tagName);
      
      var $container = $img.parent();
      if (!$container.hasClass('facetag-container')) {
        $container.css('position', 'relative').addClass('facetag-container');
      }
      
      $('.facetag-overlay').remove();
      
      // Obtenir les dimensions réelles de l'image
      var imgElement = $img[0];
      var naturalWidth = imgElement.naturalWidth;
      var naturalHeight = imgElement.naturalHeight;
      var displayWidth = $img.width();
      var displayHeight = $img.height();
      
      //console.log('=== Affichage des visages ===');
      //console.log('Image RÉELLE - Largeur:', naturalWidth, 'Hauteur:', naturalHeight);
      //console.log('Image AFFICHÉE - Largeur:', displayWidth, 'Hauteur:', displayHeight);
      
      $.each(faces, function(index, face) {
        var left, top, width, height;
        
        //console.log('--- Visage:', face.name, '---');
        //console.log('Format:', face.format);
        //console.log('Coordonnées brutes - x:', face.x, 'y:', face.y, 'w:', face.w, 'h:', face.h);
        
        if (face.format === 'mpreg') {
          // Format MPReg: x,y sont le COIN SUPÉRIEUR GAUCHE
          left = face.x * 100;
          top = face.y * 100;
          width = face.w * 100;
          height = face.h * 100;
          
          //console.log('MPReg - Coin supérieur gauche direct');
        } else {
          // Format mwg-rs: x,y sont le CENTRE
          left = (face.x - face.w / 2) * 100;
          top = (face.y - face.h / 2) * 100;
          width = face.w * 100;
          height = face.h * 100;
          
          //console.log('mwg-rs - Centre converti en coin supérieur gauche');
        }
        
        console.log('Position finale - left:', left.toFixed(2) + '%', 'top:', top.toFixed(2) + '%', 'width:', width.toFixed(2) + '%', 'height:', height.toFixed(2) + '%');
        
        // Convertir la couleur hexa en rgba avec opacité
          function hexToRgba(hex, opacity) {
            var r = parseInt(hex.slice(1, 3), 16);
            var g = parseInt(hex.slice(3, 5), 16);
            var b = parseInt(hex.slice(5, 7), 16);
            return 'rgba(' + r + ', ' + g + ', ' + b + ', ' + opacity + ')';
          }


       // Créer l'overlay avec les paramètres configurables
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

    var $label = $('<div class="facetag-label">')
      .text(face.name)
      .css({
        color: config.label_text_color,
        fontSize: config.label_font_size + 'px',
        background: labelBgRgba
      });
        
        $overlay.append($label);
        $container.append($overlay);
      });
      
      facesVisible = true;
      $('#facetag_show_faces .pwg-button-text').text('Masquer');
    }
    
    // Masquer les visages
    function hideFaces() {
      $('.facetag-overlay').remove();
      facesVisible = false;
      $('#facetag_show_faces .pwg-button-text').text('Visages');
      // NE PAS retirer la classe ici, c'est géré dans le click handler
    }
    
    // Fonction pour échapper le HTML
    function escapeHtml(text) {
      if (typeof text !== 'string') {
        text = String(text);
      }
      var map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
      };
      return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    // NOUVEAU : Convertir hexa en rgba
function hexToRgba(hex, opacity) {
  var r = parseInt(hex.slice(1, 3), 16);
  var g = parseInt(hex.slice(3, 5), 16);
  var b = parseInt(hex.slice(5, 7), 16);
  return 'rgba(' + r + ', ' + g + ', ' + b + ', ' + opacity + ')';
}
  
 // MODE PERSISTENT : afficher automatiquement si activé
if (displayMode === 'persistent') {
  var isPersistentActive = localStorage.getItem('facetag_persistent_mode') === 'true';
  //console.log('Vérification mode persistant:', isPersistentActive);
  
  if (isPersistentActive) {
    //console.log('Mode persistant activé - chargement automatique des visages');
    $('#facetag_show_faces').addClass('facetag-active');
    
    // Charger et afficher automatiquement les visages
    var ajaxUrl = $('#facetag_show_faces').data('ajax-url');
    
    $.ajax({
      url: ajaxUrl,
      type: 'GET',
      dataType: 'json',
      success: function(data) {
        if (data.stat === 'ok' && data.result.xmp) {
          xmpData = data.result.xmp;
          facesData = parseFacesFromXMP(xmpData);
          
          //console.log('Visages trouvés (auto):', facesData.length);
          
          if (facesData.length > 0) {
            displayFaces(facesData);
          }
        }
      },
      error: function(xhr, status, error) {
        //console.error('Erreur chargement auto visages:', error);
      }
    });
  }
}

  }); // fin document.ready
})(jQuery); // fin IIFE