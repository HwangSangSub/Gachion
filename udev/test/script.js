mapboxgl.accessToken = 'pk.eyJ1IjoiZ2FjaGkiLCJhIjoiY2xnM2JuZmk5MGR0ZjNybW03dG43OG05bSJ9.PsIawcEsXiXHP5SXrdsQYA';

$(document).ready(function() {

  var map = new mapboxgl.Map({
    container: 'map',
    style: 'mapbox://styles/gachi/clg3dgna8000601rrrv6eovqu',
    center: {
      lat: 37.55231594,
      lng: 126.92191547
    },
    zoom: 14,
    preserveDrawingBuffer: true
  });

  $('button').click(function() {
    var img  = map.getCanvas().toDataURL();
    var width = $('#screenshotPlaceholder').width()
    var height = $('#screenshotPlaceholder').height()
    var imgHTML = `<img src="${img}", width=${width}, height = ${height}/>`
    $('#screenshotPlaceholder').empty();
    $('#screenshotPlaceholder').append(imgHTML);
  }); 
});
  
