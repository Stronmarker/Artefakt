var $fileInput = $(".file-input");
var $droparea = $(".file-drop-area");

// Button to tcheck if is a BAT, set to false by default
var bat_button_is_clicked;
var ping;
var ping_1;

bat_button_is_clicked = false;
ping = false;
ping_1 = false;

// Button to tcheck if is a Mask, set to false by default
var mask_button_is_clicked;
var ping_2;
var ping_3;

mask_button_is_clicked = false;
ping_2 = false;
ping_3 = false;

id = 0;
const queryString = window.location.search;
console.log(queryString);

var url_string = window.location.href
var url = new URL(url_string);
var startScene = url.searchParams.get("startScene");

if (startScene == "flyer-A4") {
  _X1_ = 52
  _X2_ = 1988
  _Y1_ = 52
  _Y2_ = 1420
  _SORTIE_ = 2048
  _XMAX_ = 2048
  _YMAX_ = 1479

} else if (startScene == "flyer-A5-simple") {
  _X1_ = 137
  _X2_ = 1885
  _Y1_ = 137
  _Y2_ = 2617
  _SORTIE_ = 2048
  _XMAX_  = 2022
  _YMAX_  = 2754

} else if (startScene == "flyer-A5") {
  _X1_ = 137
  _X2_ = 3645
  _Y1_ = 137
  _Y2_ = 2619
  _SORTIE_ = 2048
  _XMAX_  = 3782
  _YMAX_  = 2754

} else if (startScene == "carte-vertical-85-54") {
  _X1_ = 45
  _X2_ = 258
  _Y1_ = 45
  _Y2_ = 379
  _SORTIE_ = 2048
  _XMAX_  = 304
  _YMAX_  = 426

} else if (startScene == "carte-horizontal-85-54") {
  _X1_ = 46
  _X2_ = 381
  _Y1_ = 43
  _Y2_ = 261
  _SORTIE_ = 2048
  _XMAX_  = 426
  _YMAX_  = 304

} else if (startScene == "carte-horizontal-85-55") {
  _X1_ = 46
  _X2_ = 381
  _Y1_ = 43
  _Y2_ = 261
  _SORTIE_ = 2048
  _XMAX_  = 426
  _YMAX_  = 304

} else {
  _X1_ = 137
  _X2_ = 1142
  _Y1_ = 137
  _Y2_ = 787
  _SORTIE_ = 2048
  _XMAX_  = 1279
  _YMAX_  = 925
}


// Déclaration de variables globals pour le canvas
var __PDF_DOC,
  __TOTAL_PAGES,
  __PAGE_RENDERING_IN_PROGRESS = 0,
  __CANVAS_ORIGINE_RECTO = $("#pdf-canvas-recto").get(0), // Nous récupérons le Canvas
  __CANVAS_CTX_ORIGINE_RECTO = __CANVAS_ORIGINE_RECTO.getContext("2d"),  // Nous récupérons context du Canvas pour pouvoir dessiner dedans
  __CANVAS_ORIGINE_VERSO = $("#pdf-canvas-verso").get(0),
  __CANVAS_CTX_ORIGINE_VERSO = __CANVAS_ORIGINE_VERSO.getContext("2d"),
  __CANVAS_ORIGINE_RECTO_CROP = $("#pdf-canvas-recto-crop").get(0),
  __CANVAS_CTX_ORIGINE_RECTO_CROP =
  __CANVAS_ORIGINE_RECTO_CROP.getContext("2d"),
  __CANVAS_ORIGINE_VERSO_CROP = $("#pdf-canvas-verso-crop").get(0),
  __CANVAS_CTX_ORIGINE_VERSO_CROP =
  __CANVAS_ORIGINE_VERSO_CROP.getContext("2d"),
  __CANVAS_RECTO_RESIZE = $("#pdf-canvas-recto-resize").get(0),
  __CANVAS_CTX_RECTO_RESIZE = __CANVAS_RECTO_RESIZE.getContext("2d"),
  __CANVAS_VERSO_RESIZE = $("#pdf-canvas-verso-resize").get(0),
  __CANVAS_CTX_VERSO_RESIZE = __CANVAS_VERSO_RESIZE.getContext("2d"),
  __CUT_X1 =  _X1_ / _XMAX_ , // Les trais de coupê du pdf
  __CUT_X2 = _X2_ / _XMAX_ ,
  __CUT_Y1 = _Y1_ / _YMAX_ ,
  __CUT_Y2 = _Y2_ / _YMAX_ ,
  __TAILLE_SORTIE = _SORTIE_ ;


function traitePDF(pdf_url, is_mask) { // Fonction pour traiter le pdf
  console.log("Traitement du PDF");
  $("#pdf-canvas-recto").attr("width", __TAILLE_SORTIE); // Définir la taille du canvas width="2048"></canvas>
  $("#pdf-canvas-verso").attr("width", __TAILLE_SORTIE);
  $("#pdf-canvas-recto-crop").attr("width", __TAILLE_SORTIE);
  $("#pdf-canvas-recto-resize")
    .attr("width", __TAILLE_SORTIE)
    .attr("height", __TAILLE_SORTIE);
  $("#pdf-canvas-verso-crop").attr("width", __TAILLE_SORTIE);
  $("#pdf-canvas-verso-crop").attr("height", __TAILLE_SORTIE);
  $("#pdf-canvas-verso-resize")
    .attr("width", __TAILLE_SORTIE)
    .attr("height", __TAILLE_SORTIE);

  $("#pdf-loader").show();

  PDFJS.getDocument({ // Appelle de l' API pdf.js pour charger le pdf
      url: pdf_url,
    })
    .then(function (pdf_doc) {
      __PDF_DOC = pdf_doc;
      __TOTAL_PAGES = __PDF_DOC.numPages; // On récupère la réponse pour le nombre de page
      if (__TOTAL_PAGES == 2) {
        $("#pdf-loader").hide();
        $("#pdf-contents").show();
        showPage(is_mask); // Fonction de l'api pour charger le pdf, page par page avec vernis ou non
      } else {
        alert(
          "Le nombre de page du PDF n'est pas égal à 2 ! Impossible de poursuivre."
        );
      }
    })
    .catch(function (error) {
      // Si il y a une erreur on remontre le bouton d'upload
      $("#pdf-loader").hide();
      $("#upload-button-bat").show();
      $("#upload-button-mask").show();

      alert(error.message);
    }); 
}


function showPage(is_mask) {
  __PAGE_RENDERING_IN_PROGRESS = 1;

  //Pendant que la page est rendu, cacher le canvas et montrer le message de chargement
  $("#pdf-canvas").hide();
  $("#page-loader").show();
  $("#download-image").hide();

  // Aller chercher la page recto
  __PDF_DOC.getPage(1).then(function (page) {
    //Comme le canvas a une largeur fixe, nous devons définir l'échelle de la fenêtre en conséquence
    var scale_required =
      __CANVAS_ORIGINE_RECTO.width / page.getViewport(1).width; // Cette méthodeles dimensions de la page actuelle du PDF au zoom (1)

    // Obtenir la fenêtre d'affichage de la page à l'échelle requise
    var viewport = page.getViewport(scale_required);

    // Définir la hauteur du canvas
    __CANVAS_ORIGINE_RECTO.height = viewport.height;

    var renderContext = {
      canvasContext: __CANVAS_CTX_ORIGINE_RECTO,
      viewport: viewport,
    };

    // Rendre le contenu de la page dans le canvas
    page.render(renderContext).then(function () {
      __PAGE_RENDERING_IN_PROGRESS = 0;

      $("#pdf-canvas").show();
      $("#page-loader").hide();
      $("#download-image").show();

      var imageData = __CANVAS_CTX_ORIGINE_RECTO.getImageData( // Extraction de l'image avec les cuts désigner pour le canvas
        __CUT_X1 * __TAILLE_SORTIE, // La coordonnée x du coin supérieur gauche
        __CUT_Y1 * __CANVAS_ORIGINE_RECTO.height, // La coordonnée y du coin supérieur gauche 
        (__CUT_X2 - __CUT_X1) * __TAILLE_SORTIE, // La largeur du rectangle à partir duquel ImageData sera extrait
        (__CUT_Y2 - __CUT_Y1) * __CANVAS_ORIGINE_RECTO.height // La hauteur du rectangle à partir duquel ImageData sera extrait
      );

      __CANVAS_ORIGINE_RECTO_CROP.width =
        (__CUT_X2 - __CUT_X1) * __TAILLE_SORTIE;
      __CANVAS_ORIGINE_RECTO_CROP.height =
        (__CUT_Y2 - __CUT_Y1) * __CANVAS_ORIGINE_RECTO.height;

      if (is_mask) {
        invert_couleur(__CANVAS_CTX_ORIGINE_RECTO_CROP, imageData); // Si c'est un vernis on inverse les couleurs pour le brilliant
      } else {
        __CANVAS_CTX_ORIGINE_RECTO_CROP.putImageData(imageData, 0, 0);
      }

      resize_image(__CANVAS_CTX_RECTO_RESIZE, imageData); // On resize l'image extraite dans un canvas 2048x2048
    });
  });

  // Aller chercher la page verso
  __PDF_DOC.getPage(2).then(function (page) {
    console.log(2)
    // As the canvas is of a fixed width we need to set the scale of the viewport accordingly
    var scale_required =
      __CANVAS_ORIGINE_VERSO.width / page.getViewport(1).width;

    // Get viewport of the page at required scale
    var viewport = page.getViewport(scale_required);

    // Set canvas height
    __CANVAS_ORIGINE_VERSO.height = viewport.height;

    var renderContext = {
      canvasContext: __CANVAS_CTX_ORIGINE_VERSO,
      viewport: viewport,
    };

    // Render the page contents in the canvas
    page.render(renderContext).then(function () {
      __PAGE_RENDERING_IN_PROGRESS = 0;

      $("#pdf-canvas").show();
      $("#page-loader").hide();
      $("#download-image").show();

      /*console.log(__CUT_X1)
      console.log(__CUT_X2)
      console.log(__CUT_Y1)
      console.log(__CUT_Y2)*/
      var imageData = __CANVAS_CTX_ORIGINE_VERSO.getImageData(
        __CUT_X1 * __TAILLE_SORTIE,
        __CUT_Y1 * __CANVAS_ORIGINE_VERSO.height,
        (__CUT_X2 - __CUT_X1) * __TAILLE_SORTIE,
        (__CUT_Y2 - __CUT_Y1) * __CANVAS_ORIGINE_VERSO.height
      );

      __CANVAS_ORIGINE_VERSO_CROP.width =
        (__CUT_X2 - __CUT_X1) * __TAILLE_SORTIE;
      __CANVAS_ORIGINE_VERSO_CROP.height =
        (__CUT_Y2 - __CUT_Y1) * __CANVAS_ORIGINE_VERSO.height;

      if (is_mask) {
        invert_couleur(__CANVAS_CTX_ORIGINE_VERSO_CROP, imageData);
      } else {
        __CANVAS_CTX_ORIGINE_VERSO_CROP.putImageData(imageData, 0, 0);
      }
      resize_image(__CANVAS_CTX_VERSO_RESIZE, imageData);
      to_upload();
    });
  });
} 

function resize_image(cavas_ctx, imageData) { // canvas 2048 + image extraite
  var destCtx = cavas_ctx;

  var newCanvas = $("<canvas>")
    .attr("width", imageData.width)
    .attr("height", imageData.height)[0];
  newCanvas.getContext("2d").putImageData(imageData, 0, 0);

  if (startScene == "carte-horizontal-85-54" || startScene == "carte-horizontal-85-55" || startScene == "flyer-A4" || startScene == "flyer-A5") {

    var scale = __TAILLE_SORTIE / imageData.width;
    var scale_sup = 1282 / 1326;

    var pos_y =(__TAILLE_SORTIE - imageData.height * scale * scale_sup) / 2;
    pos_x = 0;
    destCtx.drawImage(
      newCanvas,
      0,
      0,
      imageData.width,
      imageData.height,
      pos_x,
      pos_y,
      __TAILLE_SORTIE,
      imageData.height * scale * scale_sup
    );
  }
  if (startScene == "carte-vertical-85-54" || startScene == "flyer-A5-simple") {

    var scale = __TAILLE_SORTIE / imageData.height;
    var scale_sup = 1282 / 1326
    var pos_y = 0;
    var pos_x = (__TAILLE_SORTIE - imageData.width * scale * scale_sup) / 2;

    destCtx.drawImage(
      newCanvas,
      0,
      0,
      imageData.width,
      imageData.height,
      pos_x,
      pos_y,
      imageData.width * scale * scale_sup,
      __TAILLE_SORTIE
    );
  }
}

function invert_couleur(cavas_ctx, imageData) {
  console.log(4)
  var data = imageData.data;
  for (var i = 0; i < data.length; i += 4) {
    /*data[i]     = 255 - data[i];     // rouge
    data[i + 1] = 255 - data[i + 1]; // vert
    data[i + 2] = 255 - data[i + 2]; // bleu*/
    if (data[i] > 127) {
      data[i] = 0;
    } else {
      data[i] = 255;
    }
    if (data[i + 1] > 127) {
      data[i + 1] = 0;
    } else {
      data[i + 1] = 255;
    }
    if (data[i + 2] > 127) {
      data[i + 2] = 0;
    } else {
      data[i + 2] = 255;
    }
  }
  cavas_ctx.putImageData(imageData, 0, 0);
}
var TriggerMessage = 0;

// Quand l'utilisateur choisit un pdf
$("#file-to-upload-bat").on("change", function () {
  
  if (
    ["application/pdf"].indexOf(
      $("#file-to-upload-bat").get(0).files[0].type // Validation si c'est un pdf
    ) == -1
  ) {
    alert("Error : Not a PDF");
    return;
  }
  bat_button_is_clicked = true;
  // Appelle de la fonction pour traiter le pdf
  traitePDF( 
    URL.createObjectURL($("#file-to-upload-bat").get(0).files[0]), // création d'une chaîne contenant l'URL du pdf
    false
  );
});

// Quand l'utilisateur choisit un pdf
$("#file-to-upload-mask").on("change", function () {
  // Validation si c'est un pdf
  if (
    ["application/pdf"].indexOf(
      $("#file-to-upload-mask").get(0).files[0].type
    ) == -1
  ) {
    alert("Error : Not a PDF");
    window.location.reload();
    return;
  }

  mask_button_is_clicked = true;
  traitePDF(
    URL.createObjectURL($("#file-to-upload-mask").get(0).files[0]),
    true
  );
});
// Bouton pour dowload les png des pdfs
$("#download-png-recto").on("click", function () {
  if (TriggerMessage == 0) {
    $(this)
      .attr("href", __CANVAS_RECTO_RESIZE.toDataURL())
      .attr("download", "verso-texture.png");
  } else {
    $(this)
      .attr("href", __CANVAS_RECTO_RESIZE.toDataURL())
      .attr("download", "recto-texture-rgh.png");
  }
});
$("#download-png-verso").on("click", function () {
  if (TriggerMessage == 0) {
    $(this)
      .attr("href", __CANVAS_VERSO_RESIZE.toDataURL())
      .attr("download", "verso-texture.png");
  } else {
    $(this)
      .attr("href", __CANVAS_VERSO_RESIZE.toDataURL())
      .attr("download", "verso-texture-rgh.png.png");
  }
});

// Fonction pour Upload les fichiers dans le dossier /texture
function to_upload() {
  if (bat_button_is_clicked == true) {
    bat_button_is_clicked = false;
    var success_div = document.getElementById("status_texture");
    var canvas = document.getElementById("pdf-canvas-recto-resize");
    console.log("Début du call Ajax");
    $.ajax({
      type: "post",
      url: "upload-recto.php", // <-- point to server-side PHP script
      data: {
        imgBase64: canvas.toDataURL(),
      },
    }, ).done(function (response) {
      success_div.innerHTML = " | Téléchargement réussi"
      console.log("saved: " + response);
    });
    var canvas_1 = document.getElementById("pdf-canvas-verso-resize");
    $.ajax({
      type: "post",
      url: "upload-verso.php", // <-- point to server-side PHP script
      data: {
        imgBase64: canvas_1.toDataURL(),
      },
    }).done(function (response) {
      console.log("saved: " + response);
    });
  }
  if (mask_button_is_clicked == true) {
    mask_button_is_clicked = false;
    var success_div1 = document.getElementById("status-mask");
    var canvas = document.getElementById("pdf-canvas-recto-resize");
    $.ajax({
      type: "post",
      url: "upload-recto-rgh-2.php", // <-- point to server-side PHP script
      data: {
        imgBase64: canvas.toDataURL(),
      },
    }).done(function (response) {
      success_div1.innerHTML = " | Téléchargement réussi"
      console.log("saved: " + response);
    });
    var canvas_1 = document.getElementById("pdf-canvas-verso-resize");
    $.ajax({
      type: "post",
      url: "upload-verso-rgh-2.php", // <-- point to server-side PHP script
      data: {
        imgBase64: canvas_1.toDataURL(),
      },
    }).done(function (response) {
      console.log("saved: " + response);
    });
  }
}

function to_upload_masque() {
  var canvas = document.getElementById("pdf-canvas-recto-resize");
  $.ajax({
    type: "post",
    url: "upload-recto-rgh-2.php", // <-- point to server-side PHP script
    data: {
      imgBase64: canvas.toDataURL(),
    },
  }).done(function (response) {
    console.log("saved: " + response);
  });
  var canvas_1 = document.getElementById("pdf-canvas-verso-resize");
  $.ajax({
    type: "post",
    url: "upload-verso-rgh-2.php", // <-- point to server-side PHP script
    data: {
      imgBase64: canvas_1.toDataURL(),
    },
  }).done(function (response) {
    console.log("saved: " + response);
  });
}


// Script pour upload les pdf d'une page
var pdf_document,
  __TOTAL_PAGES,
  __PAGE_RENDERING_IN_PROGRESS = 0,
  __CANVAS_ORIGINE_RECTO = $("#pdf-canvas-recto").get(0),
  __CANVAS_CTX_ORIGINE_RECTO = __CANVAS_ORIGINE_RECTO.getContext("2d"),
  __CANVAS_ORIGINE_RECTO_CROP = $("#pdf-canvas-recto-crop").get(0),
  __CANVAS_CTX_ORIGINE_RECTO_CROP =
  __CANVAS_ORIGINE_RECTO_CROP.getContext("2d"),
  __CANVAS_RECTO_RESIZE = $("#pdf-canvas-recto-resize").get(0),
  __CANVAS_CTX_RECTO_RESIZE = __CANVAS_RECTO_RESIZE.getContext("2d"),
  __CUT_X1 =  _X1_ / _XMAX_ ,
  __CUT_X2 = _X2_ / _XMAX_ ,
  __CUT_Y1 = _Y1_ / _YMAX_ ,
  __CUT_Y2 = _Y2_ / _YMAX_ ,
  __TAILLE_SORTIE = _SORTIE_ ;

function changePDF(pdf_url, is_mask) {

  console.log("Traitement du PDF");
  $("#pdf-canvas-recto").attr("width", __TAILLE_SORTIE); // width="2048"></canvas>
  $("#pdf-canvas-recto-crop").attr("width", __TAILLE_SORTIE);
  $("#pdf-canvas-recto-resize")
    .attr("width", __TAILLE_SORTIE)
    .attr("height", __TAILLE_SORTIE);

  $("#pdf-loader").show();

  PDFJS.getDocument({
      url: pdf_url
    })
    .then(function (pdf_doc) {
      pdf_document = pdf_doc;
      __TOTAL_PAGES = pdf_document.numPages;

      if (__TOTAL_PAGES == 1) {
        $("#pdf-loader").hide();
        $("#pdf-contents").show();
        ShowPage_2(is_mask);
      } else {
        alert(
          "Le nombre de page du PDF n'est pas égal à 1 ! Impossible de poursuivre."
        );
      }
    })
    .catch(function (error) {
      // If error
      alert(error.message);
    });
}


function ShowPage_2(is_mask) {
  __PAGE_RENDERING_IN_PROGRESS = 1;

  // While page is being rendered hide the canvas and show a loading message
  $("#pdf-canvas").hide();
  $("#page-loader").show();
  $("#download-image").hide();

  // Fetch the page recto
  pdf_document.getPage(1).then(function (page) {
    // As the canvas is of a fixed width we need to set the scale of the viewport accordingly
    var scale_required =
      __CANVAS_ORIGINE_RECTO.width / page.getViewport(1).width;

    // Get viewport of the page at required scale
    var viewport = page.getViewport(scale_required);

    // Set canvas height
    __CANVAS_ORIGINE_RECTO.height = viewport.height;

    var renderContext = {
      canvasContext: __CANVAS_CTX_ORIGINE_RECTO,
      viewport: viewport,
    };

    // Render the page contents in the canvas
    page.render(renderContext).then(function () {
      __PAGE_RENDERING_IN_PROGRESS = 0;

      $("#pdf-canvas").show();
      $("#page-loader").hide();
      $("#download-image").show();

      /*console.log(__CUT_X1)
      console.log(__CUT_X2)
      console.log(__CUT_Y1)
      console.log(__CUT_Y2)*/
      var imageData = __CANVAS_CTX_ORIGINE_RECTO.getImageData(
        __CUT_X1 * __TAILLE_SORTIE,
        __CUT_Y1 * __CANVAS_ORIGINE_RECTO.height,
        (__CUT_X2 - __CUT_X1) * __TAILLE_SORTIE,
        (__CUT_Y2 - __CUT_Y1) * __CANVAS_ORIGINE_RECTO.height
      );

      __CANVAS_ORIGINE_RECTO_CROP.width =
        (__CUT_X2 - __CUT_X1) * __TAILLE_SORTIE;
      __CANVAS_ORIGINE_RECTO_CROP.height =
        (__CUT_Y2 - __CUT_Y1) * __CANVAS_ORIGINE_RECTO.height;

      if (is_mask) {
        invert_couleur_2(__CANVAS_CTX_ORIGINE_RECTO_CROP, imageData);
      } else {
        __CANVAS_CTX_ORIGINE_RECTO_CROP.putImageData(imageData, 0, 0);
      }

      resize_image_2(__CANVAS_CTX_RECTO_RESIZE, imageData);
      upload_png();
    });
  });
}
// Fetch the page verso
// Render the page contents in the canvas

function resize_image_2(cavas_ctx, imageData) {
  var destCtx = cavas_ctx;

  var newCanvas = $("<canvas>")
    .attr("width", imageData.width)
    .attr("height", imageData.height)[0];
  newCanvas.getContext("2d").putImageData(imageData, 0, 0);

  if (startScene == "carte-horizontal-85-54" || startScene == "carte-horizontal-85-55" || startScene == "flyer-A4" || startScene == "flyer-A5") {

    var scale = __TAILLE_SORTIE / imageData.width;
    var scale_sup = 1282 / 1326;

    var pos_y =
      (__TAILLE_SORTIE - imageData.height * scale * scale_sup) / 2;
    pos_x = 0;
    destCtx.drawImage(
      newCanvas,
      0,
      0,
      imageData.width,
      imageData.height,
      pos_x,
      pos_y,
      __TAILLE_SORTIE,
      imageData.height * scale * scale_sup
    );
  }
  if (startScene == "carte-vertical-85-54" || startScene == "flyer-A5-simple") {
    var scale = __TAILLE_SORTIE / imageData.height;
    var scale_sup = 1282 / 1326
    var pos_y = 0;
    var pos_x = (__TAILLE_SORTIE - imageData.width * scale * scale_sup) / 2;

    destCtx.drawImage(
      newCanvas,
      0,
      0,
      imageData.width,
      imageData.height,
      pos_x,
      pos_y,
      imageData.width * scale * scale_sup,
      __TAILLE_SORTIE
    );
  }
}

function invert_couleur_2(cavas_ctx, imageData) {
  var data = imageData.data;
  for (var i = 0; i < data.length; i += 4) {
    /*data[i]     = 255 - data[i];     // rouge
    data[i + 1] = 255 - data[i + 1]; // vert
    data[i + 2] = 255 - data[i + 2]; // bleu*/
    if (data[i] > 127) {
      data[i] = 0;
    } else {
      data[i] = 255;
    }
    if (data[i + 1] > 127) {
      data[i + 1] = 0;
    } else {
      data[i + 1] = 255;
    }
    if (data[i + 2] > 127) {
      data[i + 2] = 0;
    } else {
      data[i + 2] = 255;
    }
  }
  cavas_ctx.putImageData(imageData, 0, 0);
}

// Quand l'utilisateur choisit un pdf
$("#verso-texture").on("change", function () {
  // Validation si c'est un pdf
  console.log("texture")
  if (
    ["application/pdf"].indexOf(
      $("#verso-texture").get(0).files[0].type
    ) == -1
  ) {
    alert("Error : Not a PDF");
    return;
  }
  ping = true;

  changePDF(
    URL.createObjectURL($("#verso-texture").get(0).files[0]),
    false
  );
});

$("#recto-texture").on("change", function () {
  // Validation si c'est un pdf
  if (
    ["application/pdf"].indexOf(
      $("#recto-texture").get(0).files[0].type
    ) == -1
  ) {
    alert("Error : Not a PDF");
    return;
  }
  ping_1 = true;
  changePDF(
    URL.createObjectURL($("#recto-texture").get(0).files[0]),
    false
  );
});

// Quand l'utilisateur choisit un pdf
$("#verso-mask").on("change", function () {
  // Validation si c'est un pdf
  if (
    ["application/pdf"].indexOf($("#verso-mask").get(0).files[0].type) ==
    -1
  ) {
    alert("Error : Not a PDF");
    return;
  }
  ping_2 = true;
  changePDF(URL.createObjectURL($("#verso-mask").get(0).files[0]),
    true);
});

$("#recto-mask").on("change", function () {
  // Validation si c'est un pdf
  if (
    ["application/pdf"].indexOf(
      $("#recto-mask").get(0).files[0].type
    ) == -1
  ) {
    alert("Error : Not a PDF");
    return;
  }
  ping_3 = true;
  changePDF(
    URL.createObjectURL($("#recto-mask").get(0).files[0]),
    true
  );
});

// bouton pour télécharger les png 
$("#dowload-texture-verso").on("click", function () {
  $(this)
    .attr("href", __CANVAS_RECTO_RESIZE.toDataURL())
    .attr("download", "recto2048x2048.png");
});

$("#dowload-texture-recto").on("click", function () {
  $(this)
    .attr("href", __CANVAS_RECTO_RESIZE.toDataURL())
    .attr("download", "recto2048x2048.png");
});

// Fonction pour Upload les fichiers dans le dossier /texture
function upload_png() {
  if (ping == true) {
    ping = false;
    var canvas = document.getElementById("pdf-canvas-recto-resize");
    var success_div = document.getElementById("status-texture-alone-1");
    console.log("Début du call Ajax");
    $.ajax({
      type: "post",
      url: "upload-recto.php", // <-- point to server-side PHP script
      data: {
        imgBase64: canvas.toDataURL(),
      },
    }).done(function (response) {
      success_div.innerHTML = " | Téléchargement réussi"
      console.log("saved: " + response);
    });
  }
  if (ping_1 == true) {
    ping_1 = false;
    var canvas = document.getElementById("pdf-canvas-recto-resize");
    var success_div_2 = document.getElementById("status-texture-alone-2");
    console.log("Début du call Ajax");
    $.ajax({
      type: "post",
      url: "upload-verso.php", // <-- point to server-side PHP script
      data: {
        imgBase64: canvas.toDataURL(),
      },
    }).done(function (response) {
      success_div_2.innerHTML = " | Téléchargement réussi"
      console.log("saved: " + response);
    });
  }
  if (ping_2 == true) {
    ping_2 = false;
    var canvas = document.getElementById("pdf-canvas-recto-resize");
    var success_div_3 = document.getElementById("status-mask-alone-1");
    console.log("Début du call Ajax");
    $.ajax({
      type: "post",
      url: "upload-recto-rgh-2.php", // <-- point to server-side PHP script
      data: {
        imgBase64: canvas.toDataURL(),
      },
    }).done(function (response) {
      success_div_3.innerHTML = " | Téléchargement réussi"
      console.log("saved: " + response);
    });
  }
  if (ping_3 == true) {
    ping_3 = false;
    var canvas = document.getElementById("pdf-canvas-recto-resize");
    var success_div_4 = document.getElementById("status-mask-alone-2");
    console.log("Début du call Ajax");
    $.ajax({
      type: "post",
      url: "upload-verso-rgh-2.php", // <-- point to server-side PHP script
      data: {
        imgBase64: canvas.toDataURL(),
      },
    }).done(function (response) {
      success_div_4.innerHTML = " | Téléchargement réussi"
      console.log("saved: " + response);
    });
  }
}

// Requête Ajax pour sauvgarder l'url au click.
function buildUrl() {
  $.ajax({
    type: "post",
    url: "save_url.php", // <-- point to server-side PHP script
  }).done(function (response) {
    displayurl();
  });
}

// Fonction pour Afficher l'url au click du bouton "valider" et rediriger vers la page de gestion de compte
// function displayurl() {

//   const queryString = window.location.search;

//   var url_string = window.location.href

//   var url = new URL(url_string);

//   var c = url.searchParams.get("token");

//   url = `clientCanvas.html?token=${c}`;

//   window.location.href = "landing.php";
// }



// Fin ajout de Kélian