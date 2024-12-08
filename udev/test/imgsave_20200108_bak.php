<?
include "../common/inc/inc_header.php";  //헤더
$kml_File = "1.kml";
?>
<meta name='viewport' content='initial-scale=1,maximum-scale=1,user-scalable=no' />
<script src='https://api.tiles.mapbox.com/mapbox-gl-js/v1.5.0/mapbox-gl.js'></script>
<link href='https://api.tiles.mapbox.com/mapbox-gl-js/v1.5.0/mapbox-gl.css' rel='stylesheet' />
<script src="/js/FileSaver.min.js"></script>
<script src="/js/html2canvas.js"></script>
<script src="/js/canvas2image.js"></script>

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
$con_Idx = "1";	
$area_Code = "홍대";
$time_Chk = "0";
$month ="10"; //date('m');
$day = "24";	//date('d');
$time = "23";	//date('H');

$fname = "".$con_Idx."_".$area_Code."_".$month.$day."_".$time.".png";
?>
<div id='map'>
  <h1>Map</h1>
</div>
<div id='screenshotPlaceholder'>
  <h1>Screenshot</h1>
</div>
<div>
   <button id="save" type="button">저장하기</button>
</div>
<script>
	mapboxgl.accessToken = 'pk.eyJ1IjoiZ2FjaGkiLCJhIjoiY2xnM2JuZmk5MGR0ZjNybW03dG43OG05bSJ9.PsIawcEsXiXHP5SXrdsQYA';
	var map = new mapboxgl.Map({
		container: 'map',
		style: 'mapbox://styles/gachi/clg3dgna8000601rrrv6eovqu',
		center: [126.92191547,37.55231594],
		zoom: 13.5,
		preserveDrawingBuffer: true
	});
	map.on('load', function () {
		// Add a geojson point source.
		// Heatmap layers also work with a vector tile source.
		map.addSource('congestion', {
			"type": "geojson",
			"data": "https://places.gachita.kr/contents/view_congestion.php?con_Idx=<?php echo $con_Idx ?>&area_Code=<?php echo $area_Code ?>&time_Chk=<?php echo $time_Chk ?>"
		});
		map.addLayer({
		"id": "congestion-heat",
		"type": "heatmap",
		"source": "congestion",
		"maxzoom": 17,
		"paint": {
				// Increase the heatmap weight based on frequency and property magnitude
				"heatmap-weight": [
					"interpolate",
					["linear"],
					["get", "cong_Rate"],
					0, 1,
					5, 0
				],
				// Increase the heatmap color weight weight by zoom level
				// heatmap-intensity is a multiplier on top of heatmap-weight
				"heatmap-intensity": [
					"interpolate",
					["linear"],
					["zoom"],
					0,0,
					15,1
				],
				// Color ramp for heatmap.  Domain is 0 (low) to 1 (high).
				// Begin color ramp at 0-stop with a 0-transparancy color
				// to create a blur-like effect.
				"heatmap-color": [
					"interpolate",
					["linear"],
					["heatmap-density"],
					0, "rgba(51,188,232,0)",
					0.1, "rgb(51,188,232)",
					0.3, "rgb(210,115,98)",
					0.5, "rgb(255,202,0)",
					0.7, "rgb(255,136,0)",
					1, "rgb(245,57,83)"
				],
				// Adjust the heatmap radius by zoom level
				"heatmap-radius": [
					"interpolate",
					["linear"],
					["zoom"],
					0, 0,
					15, 20
				],
				// Transition from heatmap to circle layer by zoom level
				"heatmap-opacity": [
					"interpolate",
					["linear"],
					["zoom"],
					0, 1,
					15, 1
				]
			}
		}, 'waterway-label');
		map.addLayer({
			"id": "congestion-point",
			"type": "circle",
			"source": "congestion",
			"minzoom": 16,
			"paint": {
				// Size circle radius by earthquake magnitude and zoom level
				// 14줌 인 경우 cong_Rate값 별로 원 크기 지정 
				"circle-radius": [
					"interpolate",
					["linear"],
					["zoom"],
					16, [
						"interpolate",
						["linear"],
						["get", "cong_Rate"],
						0, 0,
						1, 15,
						2, 19,
						3, 21,
						4, 23,
						5, 25
					]
				],
				// Color circle by earthquake magnitude //"cong_Rate"(1~5) 값에 대한 색깔지정
				"circle-color": [
					"interpolate",
					["linear"],
					["get", "cong_Rate"],
					0, "rgba(51,188,232,0)",
					1, "rgb(51,188,232)",
					2, "rgb(126,210,115)",
					3, "rgb(255,202,0)",
					4, "rgb(255,136,0)",
					5, "rgb(245,57,83)"
				],
				"circle-stroke-color": "white",
				"circle-stroke-width": 0,
				// Transition from heatmap to circle layer by zoom level
				"circle-opacity": [
					"interpolate",
					["linear"],
					["zoom"],
					16, 1
				]
			}
		}, 'waterway-label');
	});
</script>
<script type="text/javascript">
	function printDiv(div){
		var filename = "<?php echo $fname?>";
		div = div[0];
		html2canvas(div).then(function(canvas){
			var myImage = canvas.toDataURL();
			//downloadURItest(myImage, filename);
			$.ajax({
			  type: "POST",
			  url: "upload.php",
			  data: { 
				 img: myImage,
				 con_Idx: "<?php echo $con_Idx?>",
				 area_Code: "<?php echo $area_Code?>",
				 month: "<?php echo $month?>",
				 day: "<?php echo $day?>",
				 time: "<?php echo $time?>"
			  }
			}).done(function(o) {
			  console.log('saved'); 
			});
		});
	}
	function downloadURI(uri, name){
		var link = document.createElement("a");
		link.download = name;
		link.href = uri;
		document.body.appendChild(link);
		link.click();
	}
	function downloadURItest(uri, name){
		$.ajax({
		  type: "POST",
		  url: "upload.php",
		  data: { 
			 imgBase64: uri
		  }
		}).done(function(o) {
		  console.log('saved'); 
		});
	}
	/*
	$(window).load(function() {
		setTimeout(function(){printDiv($('.mapboxgl-canvas'));},3000);
	});
	*/
	$(window).ready(function() {
		setTimeout(function(){printDiv($('.mapboxgl-canvas'));},3000);
	});
</script>