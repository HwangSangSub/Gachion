<?
 //xml파일 지정
$xml = file_get_contents("../contents/kmlfile/184.kml");
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
echo $pm_cnt;
$name = [];
for($pm = 0; $pm < $pm_cnt; $pm++){
	$name_chk = $result_xml->Document->Placemark[$pm]->name;
	array_push($name, str_replace('SimpleXMLElement Object ( [0] => ', '',$name_chk));
}
print_r($name);
$name_cnt = count($name);
//echo $name_cnt;
echo "<br>";
$res_data = [];
for($nm = 0; $nm < $name_cnt; $nm++){
	$locat = $result_xml->Document->Placemark[$nm]->Polygon->outerBoundaryIs->LinearRing->coordinates; 
//	echo $locat;
	$locat_poi = explode( ',', $locat);
	//print_r($locat_poi);
	$poi_cnt = count($locat_poi);
	//echo $name[$nm]['0'];
	$lnt = []; //위도
	$lng = []; //경도
	for($i = 0; $i < $poi_cnt; $i++){
		if($i % 2 != 0){
			//위도
			//echo $locat_poi[$i];
			array_push($lnt, (double)$locat_poi[$i]);
		}else{
			//경도
			//echo $locat_poi[$i];		
			array_push($lng, (double)str_replace(" ","",str_replace("0 ", "", $locat_poi[$i])));
			/*
			echo "locat_poi : ".$locat_poi[$i];
			print_r($lng);
			echo "<br>";
			*/
		}
	}
	$lng_chk = array_pop($lng);
	//echo min($lnt)." - ".max($lnt);
	//echo "<Br>";
	//echo min($lng)." - ".max($lng);
	/*
	print_r($lnt);
	print_r($lng);
	*/
	$poi_cnt2 = count($lnt);
	$coordinates = "";
	for($j = 0; $j < $poi_cnt2; $j++){
		$coord = "[".$lnt[$j].",".$lng[$j]."]";
		if($j == 0){
			$coordinates = $coord;
		}else{
			$coordinates = $coordinates.",".$coord;
		}
	}
	$data = array(str_replace("SimpleXMLElement Object ( [0] => ", "",$name[$nm]['0']) =>$coordinates);
	array_push($res_data, $data);
}
	print_r($res_data);
?>