<!-- with help from https://stackoverflow.com/questions/42483449/mapbox-gl-js-export-map-to-png-or-pdf   -->

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title></title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://github.com/niklasvh/html2canvas/releases/download/0.4.1/html2canvas.js"></script>
    <script src='https://api.mapbox.com/mapbox-gl-js/v0.42.0/mapbox-gl.js'></script>
    <link href='https://api.mapbox.com/mapbox-gl-js/v0.42.0/mapbox-gl.css' rel='stylesheet' />
    <script src="script.js"></script> 
    <link rel="stylesheet" type="text/css" href="style.css">
  </head>
  <body>

    <div id='map'>
      <h1>Map</h1>
    </div>
    
    <div id='screenshotPlaceholder'>
      <h1>Screenshot</h1>
    </div>
    
    <div id='buttonDiv'>
      <button type="button">Take Screenshot</button>
    </div>
    

  </body>
</html>