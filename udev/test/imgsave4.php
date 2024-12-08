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
?><!DOCTYPE html>
<html>
	<head>
	<meta charset="utf-8" />
		<title>Add custom icons with Markers</title>
		<meta name="viewport" content="initial-scale=1,maximum-scale=1,user-scalable=no" />
		<script src="https://api.mapbox.com/mapbox-gl-js/v1.7.0/mapbox-gl.js"></script>
		<link href="https://api.mapbox.com/mapbox-gl-js/v1.7.0/mapbox-gl.css" rel="stylesheet" />
		<script src="../../js/FileSaver.min.js"></script>
		<script src="../../js/html2canvas.js"></script>
		<script src="../../js/canvas2image.js"></script>
		<style>
			body { margin: 0; padding: 0; }
			#map { position: absolute; top: 0; bottom: 0; width: 100%; }
		</style>
	</head>
	<body>
		<style>
			.marker {
				display: block;
				border: none;
				border-radius: 50%;
				cursor: pointer;
				padding: 0;
			}
		</style>

<style>
	body {
	  margin: 0;
	}

	#map {
	  position: absolute;
	  top:0;
	  bottom:0;
	  width: 540px;
	  height: 360px;
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
	  width: 540px;
	  height: 360px;
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
		<div id="map"></div>
		<script>
			var lng = <?php echo $lng ?>;
			var lat = <?php echo $lat ?>;
			var place_Name = "<?php echo $place_Name ?>";
			mapboxgl.accessToken = 'pk.eyJ1IjoiZ2FjaGkiLCJhIjoiY2xnM2JuZmk5MGR0ZjNybW03dG43OG05bSJ9.PsIawcEsXiXHP5SXrdsQYA';
			var geojson = {
				'type': 'FeatureCollection',
				'features': [
					{
						'type': 'Feature',
						'properties': {
							'name': place_Name,
							'iconSize': [140, 70]
						},
						'geometry': {
							'type': 'Point',
							'coordinates': [lng, lat]
						}
					}
				]
			};
			 
			var map = new mapboxgl.Map({
				container: 'map',
				style: 'mapbox://styles/gachi/clg3dgna8000601rrrv6eovqu',
				center: [lng, lat],
				zoom: 18,
				preserveDrawingBuffer: true
			});
		 
			// add markers to map
			geojson.features.forEach(function(marker) {
				// create a DOM element for the marker
				var el = document.createElement('div');
				el.style.backgroundImage = 'url(https://places.gachita.kr/contents/map_img/places/map_pin_bg_01_lunch_box_70.png';
				el.innerHTML = "<div style='height: 100%;width: 100%;text-align: center;position: absolute;top: 25%;left: 8%;font-size:15px;'>"+place_Name+"</div>";
				el.style.width = marker.properties.iconSize[0] + 'px';
				el.style.height = marker.properties.iconSize[1] + 'px';
				 
				el.addEventListener('click', function() {
					window.alert(marker.properties.name);
				});
				 
				// add marker to map
				new mapboxgl.Marker(el)
				.setLngLat(marker.geometry.coordinates)
				.addTo(map);
			});
		</script>
		<script type="text/javascript">
			function printDiv(div){
				$(div).closest("div").each(function(){ 
					//o.toast.info($(this).attr("id")); 
					html2canvas(this).then(function(canvas){
						var myImage = canvas.toDataURL();
						downloadURI(myImage, filename);
					});
				});
				var filename = "<?php echo $fname?>"
				for(var key in div){
					console.log("Attributes : "+key+", value : "+div[key]);
				}
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
	</body>
</html>
<?
dbClose($DB_con);
$stmt = null;
$meInfoStmt = null;
$mEtcStmt = null;
$mstmt = null;
?>