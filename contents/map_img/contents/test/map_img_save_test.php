#!/usr/bin/php
<?php
	echo "argv params: ";
	print_r($argv); 
	include "../../../lib/common.php";
	include 'inc/dbcon.php';
	function convexHull($points)
	{
		/* Ensure point doesn't rotate the incorrect direction as we process the hull halves */
		$cross = function($o, $a, $b) {
			return ($a[0] - $o[0]) * ($b[1] - $o[1]) - ($a[1] - $o[1]) * ($b[0] - $o[0]);
		};

 		$pointCount = count($points);
 		sort($points);
		if ($pointCount > 1) {

			$n = $pointCount;
			$k = 0;
			$h = array();
 
			/* Build lower portion of hull */
			for ($i = 0; $i < $n; ++$i) {
				while ($k >= 2 && $cross($h[$k - 2], $h[$k - 1], $points[$i]) <= 0)
					$k--;
				$h[$k++] = $points[$i];
			}
 
			/* Build upper portion of hull */
			for ($i = $n - 2, $t = $k + 1; $i >= 0; $i--) {
				while ($k >= $t && $cross($h[$k - 2], $h[$k - 1], $points[$i]) <= 0)
					$k--;
				$h[$k++] = $points[$i];
			}

			/* Remove all vertices after k as they are inside of the hull */
			if ($k > 1) {

				/* If you don't require a self closing polygon, change $k below to $k-1 */
				$h = array_splice($h, 0, $k); 
			}

			return $h;

		}
		else if ($pointCount <= 1)
		{
			return $points;
		}
		else
		{
			return null;
		}
	}/*
	function distance($lat1, $lng1, $lat2, $lng2, $miles = true){
echo "lat1 : ".$lat1."/ lng1 : ".$lng1."/ lat2 : ".$lat2."/ lng2 : ".$lng2."\n";
		$pi80 = M_PI / 180;
echo "pi80 : ".$pi80."\n";
		$lat1 *= $pi80;
echo "lat1 : ".$lat1."\n";
		$lng1 *= $pi80;
echo "lng1 : ".$lng1."\n";
		$lat2 *= $pi80;
echo "lat2 : ".$lat2."\n";
		$lng2 *= $pi80;
echo "lng2 : ".$lng2."\n";

		$r = 6372.797;
		$dlat = $lat2 = $lat1;
echo "dlat : ".$dlat."\n";
		$dlng = $lng2 - $lng1;
echo "dlng : ".$dlng."\n";
		$a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlng / 2) * sin($dlng / 2);
echo "a : ".$a."\n";
		$c = (2 * atan2(sqrt($a), $sqrt(1 - $a)));
echo "c : ".$c."\n";
		$km = $r * $c;
echo "km : ".$km."\n";

		return ($miles ? ($km * 0.621371192) : $km);
	}
	*/
/**
 * 두 좌표간의 거리를 구하기(WGS84 기준)
 * Get distance between coordinates in km
 * @param double $lat1 : 좌표1 위도
 * @param double $lon1 : 좌표1 경도
 * @param double $lat2 : 좌표2 위도
 * @param double $lon2 : 좌표2 경도
 * return double
 */
function get_distance($lat1, $lon1, $lat2, $lon2) {
  /* WGS84 stuff */
  $a = 6378137;
  $b = 6356752.3142;
  $f = 1/298.257223563;
  /* end of WGS84 stuff */

  $L = deg2rad($lon2-$lon1);
  $U1 = atan((1-$f) * tan(deg2rad($lat1)));
  $U2 = atan((1-$f) * tan(deg2rad($lat2)));
  $sinU1 = sin($U1);
  $cosU1 = cos($U1);
  $sinU2 = sin($U2);
  $cosU2 = cos($U2);

  $lambda = $L;
  $lambdaP = 2*pi();
  $iterLimit = 20;
  while ((abs($lambda-$lambdaP) > pow(10, -12)) && ($iterLimit-- > 0)) {
    $sinLambda = sin($lambda);
    $cosLambda = cos($lambda);
    $sinSigma = sqrt(($cosU2*$sinLambda) * ($cosU2*$sinLambda) + ($cosU1*$sinU2-$sinU1*$cosU2*$cosLambda) * ($cosU1*$sinU2-$sinU1*$cosU2*$cosLambda));

    if ($sinSigma == 0) {
      return 0;
    }

    $cosSigma   = $sinU1*$sinU2 + $cosU1*$cosU2*$cosLambda;
    $sigma      = atan2($sinSigma, $cosSigma);
    $sinAlpha   = $cosU1 * $cosU2 * $sinLambda / $sinSigma;
    $cosSqAlpha = 1 - $sinAlpha*$sinAlpha;
    $cos2SigmaM = $cosSigma - 2*$sinU1*$sinU2/$cosSqAlpha;

    if (is_nan($cos2SigmaM)) {
      $cos2SigmaM = 0;
    }

    $C = $f/16*$cosSqAlpha*(4+$f*(4-3*$cosSqAlpha));
    $lambdaP = $lambda;
    $lambda = $L + (1-$C) * $f * $sinAlpha *($sigma + $C*$sinSigma*($cos2SigmaM+$C*$cosSigma*(-1+2*$cos2SigmaM*$cos2SigmaM)));
  }

  if ($iterLimit == 0) {
    // formula failed to converge
    return NaN;
  }

  $uSq = $cosSqAlpha * ($a*$a - $b*$b) / ($b*$b);
  $A = 1 + $uSq/16384*(4096+$uSq*(-768+$uSq*(320-175*$uSq)));
  $B = $uSq/1024 * (256+$uSq*(-128+$uSq*(74-47*$uSq)));
  $deltaSigma = $B*$sinSigma*($cos2SigmaM+$B/4*($cosSigma*(-1+2*$cos2SigmaM*$cos2SigmaM)- $B/6*$cos2SigmaM*(-3+4*$sinSigma*$sinSigma)*(-3+4*$cos2SigmaM*$cos2SigmaM)));

  return round($b*$A*($sigma-$deltaSigma) / 1000);


/* sphere way */
  $distance = rad2deg(acos(sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($lon1 - $lon2))));

  $distance *= 111.18957696; // Convert to km

  return $distance;
}
$idx = trim($argv['1']);						// 지도고유번호 
$DB_con = db1();
$latlng = [];
$chk_lng = [];
$chk_lat = [];
// 지도 정보 가져오기
$con_query = "
	SELECT *
	FROM TB_CONTENTS
	WHERE idx = :idx;
";
$con_stmt = $DB_con->prepare($con_query);
$con_stmt->bindparam(":idx",$idx);
$con_stmt->execute();
$con_num = $con_stmt->rowCount();
if($con_num < 1)  { //아닐경우
} else {
	while($con_row = $con_stmt->fetch(PDO::FETCH_ASSOC)) {
	$con_Idx = trim($con_row['idx']);
	$con_Name =  trim($con_row['con_Name']);
	$query = "
		SELECT *
		FROM 
			TB_PLACE 
		WHERE con_Idx = :con_Idx" ;	
	$stmt = $DB_con->prepare($query);
	$stmt->bindparam(":con_Idx",$con_Idx);
	$stmt->execute();
	$num = $stmt->rowCount();
	if($num < 1)  { //아닐경우
	} else {
		$idx_cnt = 0;
		$placelocat = "";
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$idx = trim($row['idx']);
			$place_Name =  trim($row['place_Name']);
			$lng = trim($row['lng']);
			array_push($chk_lng, $lng);
			$lat = trim($row['lat']);
			array_push($chk_lat, $lat);
			array_push($latlng, array($lng, $lat));
			$place_Icon = trim($row['place_Icon']);
			$icon_query = "
				SELECT code_Color
				FROM 
					TB_CONFIG_CODE
				WHERE code = :place_Icon
					AND code_Div = 'placeicon'
					AND use_Bit = '0';" ;	
			$icon_stmt = $DB_con->prepare($icon_query);
			$icon_stmt->bindparam(":place_Icon",$place_Icon);
			$icon_stmt->execute();
			$icon_row = $icon_stmt->fetch(PDO::FETCH_ASSOC);
			$color = trim($icon_row['code_Color']);
			$place_locat = "
				{
					'type': 'Feature',
					'properties': {
						'name': '".$place_Name."',
						'iconSize': [140, 70]
					},
					'geometry': {
						'type': 'Point',
						'coordinates': [".$lng.", ".$lat."]
					}
				}
			";
			if($idx_cnt == 0){
				$placelocat = $place_locat;
			}else{
				$placelocat = $placelocat.", ".$place_locat;
			}
			$draw_locat = "
				// add markers to map
				geojson.features.forEach(function(marker) {
					// create a DOM element for the marker
					var el = document.createElement('div');
					el.innerHTML = \"<div style='display:inline-block;padding:3px 5px;background-color:".$color.";color:#fff;font-weight:bold;text-align:center;font-size:15px;border-radius:8px;border:1px solid #fff;'>".$place_Name."</div>\";
					el.style.width = marker.properties.iconSize[0] + 'px';
					el.style.height = marker.properties.iconSize[1] + 'px';
					 
					el.addEventListener('click', function() {
						window.alert('".$place_Name."');
					});
					 
					// add marker to map
					new mapboxgl.Marker(el)
					.setLngLat([".$lng.", ".$lat."])
					.addTo(map);
				});
			";
			if($idx_cnt == 0){
				$drawlocat = $draw_locat;
			}else{
				$drawlocat = $drawlocat.$draw_locat;
			}
			$idx_cnt++;
	  }
	}
  }
}
//print_r($latlng);
$output = convexHull($latlng);
//print_r($output);
$min_lng = min($chk_lng);
$max_lng = max($chk_lng);
$min_lat = min($chk_lat);
$max_lat = max($chk_lat);
//echo "min_lng : ".$min_lng."\n";
//echo "max_lng : ".$max_lng."\n";
//echo "min_lat : ".$min_lat."\n";
//echo "max_lat : ".$max_lat."\n";
$center_lng = (double)$min_lng + (((double)$max_lng - (double)$min_lng) / 2);		//x
$center_lat = (double)$min_lat + (((double)$max_lat - (double)$min_lat) / 2);			//y
//echo "center_lng : ".$center_lng."\n";
//echo "center_lat : ".$center_lat."\n";
$lnglat_Cnt = count($chk_lng);
//echo "lnglat_Cnt : ".$lnglat_Cnt."\n";
$distance = [];
for($i = 0; $i < $lnglat_Cnt; $i++){
	//$dist = distance($center_lng, $center_lat, $chk_lng[$i], $chk_lat[$i]);
	$dist = get_distance($center_lng, $center_lat, $chk_lng[$i], $chk_lat[$i]);
	array_push($distance, $dist);
	//echo "dist_".$i." : ".$dist."\n";
}
$max_dist = max($distance);
//echo "max_dist : ".$max_dist."\n";
?><!DOCTYPE html>
<html>
	<head>
	<meta charset="utf-8" />
		<title>Add custom icons with Markers</title>
		<meta name="viewport" content="initial-scale=1,maximum-scale=1,user-scalable=no" />
		<script src="/udev/common/js/jquery-1.8.3.min.js"></script>
		<script src="/udev/common/js/jquery.menu.js?ver=<?=rand();?>"></script>
		<script src="/udev/common/js/common.js?ver=<?=rand();?>"></script>
		<script src="/udev/common/js/wrest.js?ver=<?=rand();?>"></script>
		<script src="/udev/common/js/placeholders.min.js"></script>
		<link rel="stylesheet" href="/udev/common/js/font-awesome/css/font-awesome.min.css">
		<link type="text/css" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/themes/base/jquery-ui.css" rel="stylesheet" />
		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
		<link type="text/css" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/themes/base/jquery-ui.css" rel="stylesheet" />
		<script src="https://api.mapbox.com/mapbox-gl-js/v1.7.0/mapbox-gl.js"></script>
		<link href="https://api.mapbox.com/mapbox-gl-js/v1.7.0/mapbox-gl.css" rel="stylesheet" />
		<script src="../../../js/FileSaver.min.js"></script>
		<script src="../../../js/html2canvas.js"></script>
		<script src="../../../js/canvas2image.js"></script>
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
	.mapboxgl-ctrl-bottom-left {
		display:none;
	}
	.mapboxgl-ctrl-bottom-right{
		display:none;
	}
</style>
<?php

$fname = $con_Idx.".jpg";
?>		 
		<div id="map"></div>
		<script>
			var lng = <?php echo $center_lng ?>;
			var lat = <?php echo $center_lat ?>;
			mapboxgl.accessToken = 'pk.eyJ1IjoiZ2FjaGkiLCJhIjoiY2xnM2JuZmk5MGR0ZjNybW03dG43OG05bSJ9.PsIawcEsXiXHP5SXrdsQYA';
			var geojson = {
				'type': 'FeatureCollection',
				'features': [<?php echo $placelocat ?>
				]
			};
			 
			var map = new mapboxgl.Map({
				container: 'map',
				style: 'mapbox://styles/gachi/clg3dgna8000601rrrv6eovqu',
				center: [lng, lat],
				zoom: 6,
				preserveDrawingBuffer: true
			});
			<?php echo $drawlocat ?>
		</script>
		<script type="text/javascript">
			function printDiv(div){
				var filename = "<?php echo $fname?>"
				div = div[0];
				html2canvas(div).then(function(canvas){
					var myImage = canvas.toDataURL();
					//console.log(myImage);
					//downloadURI(myImage, filename);
					uploadImage(myImage, filename);
				});
			}
			function downloadURI(uri, name){
				var link = document.createElement("a");
				link.download = name;
				link.href = uri;
				document.body.appendChild(link);
				link.click();
			}
			function uploadImage(uri) {
				var filename = "<?php echo $fname?>"
				var con_Idx = "<?php echo $con_Idx?>"
				var request = $.ajax({
					type:'POST',
					data: {imgUpload:uri, filename:filename, idx:con_Idx},
					url:'./img_save.php',
					success:function(result){
						//alert(result);
					}
				});
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
				setTimeout(function(){printDiv($('#map'));},3000);
			});    
		</script>
	</body>
</html>
<?php
dbClose($DB_con);
$stmt = null;
$meInfoStmt = null;
$mEtcStmt = null;
$mstmt = null;
?>