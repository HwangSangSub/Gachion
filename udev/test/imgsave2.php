<?
include "../common/inc/inc_header.php";  //헤더

$idx = trim($idx);						// 콘텐츠고유번호 
$DB_con = db1();
// 콘텐츠 정보 가져오기
$query = "
	SELECT *
	FROM 
		TB_PLACE 
	WHERE idx = :idx" ;	
//echo $query."<BR>";
//exit;

$stmt = $DB_con->prepare($query);
$stmt->bindparam(":idx",$idx);
$stmt->execute();
$num = $stmt->rowCount();

if($num < 1)  { //아닐경우
} else {
	
  while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

	$idx = trim($row['idx']);
	$place_Name =  trim($row['place_Name']);
	$lng = trim($row['lng']);
	$lat = trim($row['lat']);
  }
}
?>
<meta name='viewport' content='initial-scale=1,maximum-scale=1,user-scalable=no' />
<script src='https://api.tiles.mapbox.com/mapbox-gl-js/v1.5.0/mapbox-gl.js'></script>
<link type="text/css" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/themes/base/jquery-ui.css" rel="stylesheet" />
<link href='https://api.tiles.mapbox.com/mapbox-gl-js/v1.5.0/mapbox-gl.css' rel='stylesheet' />
<script src="../js/FileSaver.min.js"></script>
<script src="../js/html2canvas.js"></script>
<script src="../js/canvas2image.js"></script>

<style>
	body {
	  margin: 0;
	}

	#map {
	  position: absolute;
	  top:0;
	  bottom:0;
	  width: 600px;
	  height: 500px;
	  margin:1%;
	  background-color:black;
	  border-style: solid;
	  border-width: 5px;
	  border-color: grey;
	}

	#screenshotPlaceholder {
	  position:absolute;
	  left:50%;
	  top:0;
	  bottom:0;
	  width: 600px;
	  height: 500px;
	  margin:1%;
	}

	#buttonDiv {
	  pointer-events: none;
	  position: absolute;
	  top:0;
	  bottom:0;
	  width:46%;
	  margin:1%;
	  display: flex;
	  flex-direction: column;
	  align-items: center;
	  justify-content: center;
	}

	h1 {
	  color:white;
	  text-align:center;
	  position: relative;
	  top:50%;
	  transform: translateY(-50%);
	  margin:0;
	  height:0;
	}

	button {
	  pointer-events:auto;
	  transform: translate(0, 200%);
	  padding: 10px 20px;
	  border: none;
	  border-radius: 10px;
	  cursor: pointer;
	  color:white;
	  background-color: rgb(42, 42, 242);
	  box-shadow: 0px 5px 0px 0px rgb(11, 11, 92);
	}

	button:active {
	  transform: translate(0, calc(200% + 5px));
	  -webkit-transform: translate(0, calc(200% + 5px));
	  text-decoration: none;
	  box-shadow: 0px 2px 0px 0px rgb(11, 11, 92);
	}

	/* removes firefox dotted line around button text */
	::-moz-focus-inner {border:0;}
</style>
<?

$fname = $idx."_".$place_Name.".png";
?>
<div id='map'>
  <h1>Map</h1>
</div>
<script>
	mapboxgl.accessToken = 'pk.eyJ1IjoiZ2FjaGkiLCJhIjoiY2xnM2JuZmk5MGR0ZjNybW03dG43OG05bSJ9.PsIawcEsXiXHP5SXrdsQYA';
	var lng = <?php echo $lng ?>;
	var lat = <?php echo $lat ?>;
	var place_Name = "<?php echo $place_Name ?>";
	var map = new mapboxgl.Map({
		container: 'map',
		style: 'mapbox://styles/gachi/clg3dgna8000601rrrv6eovqu',
		zoom: 12,
		center: [lng, lat],
		preserveDrawingBuffer: true
	});
	map.on('load', function () {
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
			content: [25, 25, 115, 100],
			pixelRatio: 2
		});
		map.addLayer({
			"id": "points",
			"type": "symbol",
			"source": {
				"type": "geojson",
				"data": {
					"type": "FeatureCollection",
					"features": [{
						"type": "Feature",
						"geometry": {
							"type": "Point",
							"coordinates": [lng, lat]
						},
						"properties": {
							"image-name": 'popup-debug'
							,"name": place_Name
						}
					}]
				}
			},
			"layout": {
				"icon-image": "{icon}-11",
				"text-field": "{title}",
				"text-font": ["Open Sans Semibold", "Arial Unicode MS Bold"],
				"text-offset": [0, 0.6],
				"text-anchor": "top"
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
	});
	
	function loadImages(urls, callback) {
		var results = {};
		for (var name in urls) {
			map.loadImage(urls[name], makeCallback(name));
		}
		 
		function makeCallback(name) {
			return function(err, image) {
			results[name] = err ? null : image;
			 
			// if all images are loaded, call the callback
			if (Object.keys(results).length === Object.keys(urls).length) {
				callback(results);
				}
			};
		}
	} 
</script>
<script type="text/javascript">
	function printDiv(div){
		var filename = "<?php echo $fname?>"
		div = div[0];
		html2canvas(div).then(function(canvas){
			var myImage = canvas.toDataURL();
			downloadURI(myImage, filename);
		});
	}
	function downloadURI(uri, name){
		var link = document.createElement("a");
		link.download = name;
		link.href = uri;
		document.body.appendChild(link);
		link.click();
	}
	/*
	$(window).load(function() {
		setTimeout(function(){printDiv($('.mapboxgl-canvas'));},2000);
	});
	*/
	/*
	$(window).ready(function() {
		setTimeout(function(){printDiv($('.mapboxgl-canvas'));},5000);
	});
	*/
	$(window).ready(function() {
		setTimeout(function(){printDiv($('.mapboxgl-canvas'));},3000);
	});
</script>
<?
dbClose($DB_con);
$stmt = null;
$meInfoStmt = null;
$mEtcStmt = null;
$mstmt = null;
?>