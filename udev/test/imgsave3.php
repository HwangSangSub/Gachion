<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>Add a stretchable image to the map</title>
		<meta name="viewport" content="initial-scale=1,maximum-scale=1,user-scalable=no" />
		<script src="https://api.mapbox.com/mapbox-gl-js/v1.7.0/mapbox-gl.js"></script>
		<link href="https://api.mapbox.com/mapbox-gl-js/v1.7.0/mapbox-gl.css" rel="stylesheet" />
		<style>
			body { margin: 0; padding: 0; }
			#map { position: absolute; top: 0; bottom: 0; width: 100%; }
		</style>
	</head>
	<body>
	<div id="map"></div>
	 
	<script>
		mapboxgl.accessToken = 'pk.eyJ1IjoiZ2FjaGkiLCJhIjoiY2xnM2JuZmk5MGR0ZjNybW03dG43OG05bSJ9.PsIawcEsXiXHP5SXrdsQYA';
		var map = new mapboxgl.Map({
			container: 'map',
			style: 'mapbox://styles/mapbox/streets-v11'
		});
		 
		var images = {
			'popup': 'https://places.gachita.kr/contents/map_img/places/map_pin_bg_01_lunch_box_70.png',
			'popup-debug':'https://docs.mapbox.com/mapbox-gl-js/assets/popup_debug.png'
		};
		 
		loadImages(images, function(loadedImages) {
			map.on('load', function() {
				map.addImage('popup-debug', loadedImages['popup-debug'], {
					// The two (blue) columns of pixels that can be stretched horizontally:
					//   - the pixels between x: 25 and x: 55 can be stretched
					//   - the pixels between x: 85 and x: 115 can be stretched.
					stretchX: [[25, 55], [85, 115]],
					// The one (red) row of pixels that can be stretched vertically:
					//   - the pixels between y: 25 and y: 100 can be stretched
					stretchY: [[25, 100]],
					// This part of the image that can contain text ([x1, y1, x2, y2]):
					content: [25, 25, 115, 100],
					// This is a high-dpi image:
					pixelRatio: 2
				});
				map.addImage('popup', loadedImages['popup'], {
					stretchX: [[25, 55], [85, 115]],
					stretchY: [[25, 100]],
					content: [45, 25, 115, 100],
					pixelRatio: 2
				});
				 
				map.addSource('points', {
					'type': 'geojson',
					'data': {
					'type': 'FeatureCollection',
					'features': [
							{
								'type': 'Feature',
								'geometry': {
									'type': 'Point',
									'coordinates': [30, -30]
								},
								'properties': {
									'image-name': 'popup-debug',
									'name': 'Line 1\nLine 2\nLine 3'
								}
							},
							{
								'type': 'Feature',
								'geometry': {
									'type': 'Point',
									'coordinates': [40, 30]
								},
								'properties': {
									'image-name': 'popup',
									'name': 'Line 1\nLine 2\nLine 3'
								}
							},
							{
								'type': 'Feature',
								'geometry': {
									'type': 'Point',
									'coordinates': [-40, -30]
								},
								'properties': {
									'image-name': 'popup-debug',
									'name': 'One longer line'
								}
							},
							{
								'type': 'Feature',
								'geometry': {
									'type': 'Point',
									'coordinates': [-40, 30]
								},
								'properties': {
									'image-name': 'popup',
									'name': 'One longer line'
								}
							}
						]
					}
				});
				map.addLayer({
					'id': 'points',
					'type': 'symbol',
					'source': 'points',
					'layout': {
						'text-field': ['get', 'name'],
						'icon-text-fit': 'both',
						'icon-image': ['get', 'image-name'],
						'icon-allow-overlap': true,
						'text-allow-overlap': true
					}
				});
				 
				// the original, unstretched image for comparison
				map.addSource('original', {
					'type': 'geojson',
					'data': {
						'type': 'FeatureCollection',
						'features': [
							{
								'type': 'Feature',
								'geometry': {
									'type': 'Point',
									'coordinates': [0, -70]
								}
							}
						]
					}
				});
				map.addLayer({
					'id': 'original',
					'type': 'symbol',
					'source': 'original',
					'layout': {
						'text-field': 'unstretched',
						'icon-image': 'popup',
						'icon-allow-overlap': true,
						'text-allow-overlap': true
					}
				});
			});
		});
		 
		function loadImages(urls, callback) {
			console.log('시작해볼까');
			var results = {};
			for (var name in urls) {
				map.loadImage(urls[name], makeCallback(name));
				for( var key in urls){
					//console.log("urls : "+ key +", value : "+name[key]);
				}
			}
			 
			function makeCallback(name) {
				return function(err, image) {
				results[name] = err ? null : image;
				for( var key in image){
					console.log("Attributes : "+ key +", value : "+image[key]);
				}
				for( var key in results){
					console.log("results[name] : "+ key +", value : "+results[key]);
				}
				 
				// if all images are loaded, call the callback
				console.log(Object.keys(results).length);
				console.log(Object.keys(urls).length);
				if (Object.keys(results).length === Object.keys(urls).length) {
					callback(results);	
						for( var key in results){
							console.log("results : "+ key +", value : "+results[key]);
						}
					}
				};
			}
		}
	</script>
	 
	</body>
</html>