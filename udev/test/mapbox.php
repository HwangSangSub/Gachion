<?
	include "../common/inc/inc_header.php";  //헤더
$kml_File = "1.kml";
?>

<script src='https://api.tiles.mapbox.com/mapbox-gl-js/v1.5.0/mapbox-gl.js'></script>
<link href='https://api.tiles.mapbox.com/mapbox-gl-js/v1.5.0/mapbox-gl.css' rel='stylesheet' />
<div id='map' style='width: 2000px; height: 800px;'></div>

		<?
		 //xml파일 지정
		$xml = file_get_contents("../../contents/kmlfile/".$kml_File);
		//xml파일 읽어오기
		$result_xml = simplexml_load_string($xml);
		// 읽어온 파일 확인하기
		//echo '<pre>' . print_r($result_xml, true) . '</pre>';
		//특정부분만 가져오기
		/*
		stdClass Object
		(
			[data] => stdClass Object
				(
					[employee] => Array
						(
							[0] => stdClass Object
								(
									[firstName] => John
									[lastName] => Doe
								)
		 
							[1] => stdClass Object
								(
									[firstName] => Anna
									[lastName] => Smith
								)
		 
							[2] => stdClass Object
								(
									[firstName] => Peter
									[lastName] => Jones
								)
						)
				)
		)
		객체나 배열이나 본인이 원하는 형태로 decode 해서 필요한 데이터만 가져올 수 있습니다.
		객체인 경우 -> 
		배열인 경우 employee[0] 표현하여 가져오기
		print_r($result_json2->data->employee[0]->firstName); // John
		echo " ";
		*/
		//echo $result_xml->Document->Placemark->Polygon->outerBoundaryIs->LinearRing->coordinates; // John
		$Placemark = $result_xml->Document->Placemark;
		$pm_cnt = count($Placemark);
		//echo $pm_cnt;
		$name = [];
		for($pm = 0; $pm < $pm_cnt; $pm++){
			$name_chk = $result_xml->Document->Placemark[$pm]->name;
			array_push($name, $name_chk);
		}
		//print_r($name);
		$name_cnt = count($name);
		//echo $name_cnt;
		//echo "<br>";
		$res_data = [];
		for($nm = 0; $nm < $name_cnt; $nm++){
			$locat = $result_xml->Document->Placemark[$nm]->Polygon->outerBoundaryIs->LinearRing->coordinates; 
		//	echo $locat;
			$locat_poi = explode( ',', $locat);
			//print_r($locat_poi);
			$poi_cnt = count($locat_poi);
			//echo $name[$nm]['0'];
			$lng = []; //경도
			$lnt = []; //위도
			for($i = 0; $i < $poi_cnt; $i++){
				if($i % 2 != 0){
					//경도
					//echo $locat_poi[$i];
					array_push($lng, $locat_poi[$i]);
				}else{
					//위도
					//echo $locat_poi[$i];		
					array_push($lnt, str_replace(" ","",str_replace("0 ", "", $locat_poi[$i])));
					/*
					echo "locat_poi : ".$locat_poi[$i];
					print_r($lng);
					echo "<br>";
					*/
				}
			}
			/*
			print_r($lnt);
			print_r($lng);
			*/
			$poi_cnt2 = count($lnt);
			$$coord_f = "";
			$coordinates = "";
			for($j = 0; $j < $poi_cnt2; $j++){
				$coord = "[".$lnt[$j].",".$lng[$j]."]";
				if($j == 0){
					$coordinates = $coord;
					$coord_f = $coord;
				}else{
					if($j % 6 == 0){
						$coordinates = $coordinates.",".$coord;
					}
				}
			}
			$name_sort = $name[$nm];
			$data = array("$nm" =>$coordinates);
			array_push($res_data, $data);
		}
		$res_cnt = count($res_data);
		$map_on = "";
		$color = ["#f53953","#ff8800","#ffca00","#7ed273","#33bce8"];
		for($k = 0; $k < $res_cnt; $k++){
			$rdata = $res_data[$k][$k];
			$mapadd =
				"
					map.addLayer({
						'id': '".($k+1)."',
						'type': 'fill',
						'source': {
							'type': 'geojson',
							'data': {
								'type': 'Feature',
								'geometry': {
									'type': 'Polygon',
									'coordinates': [[".$rdata."]]
								}
							}
						},
						'layout': {},
						'paint': {
							'fill-color': '".$color[$k]."',
							'fill-opacity': 0.4
						}
					});"
			;
			if($k == 0){
				$map_on = $mapadd;
			}else{
				$map_on = $map_on.$mapadd;
			}
		}
			//php echo $coord_f ,
		?>
		<script>
			mapboxgl.accessToken = 'pk.eyJ1IjoiZ2FjaGkiLCJhIjoiY2xnM2JuZmk5MGR0ZjNybW03dG43OG05bSJ9.PsIawcEsXiXHP5SXrdsQYA';
			var map = new mapboxgl.Map({
				container: 'map',
				style: 'mapbox://styles/gachi/clg3dgna8000601rrrv6eovqu',
				center: [126.92191547,37.55431594],
				zoom: 13
			});
			map.on('load', function () {
				<?php echo $map_on ?>
			});
		</script>
	<?
	?>