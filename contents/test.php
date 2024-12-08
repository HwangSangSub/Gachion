<?
//좌표 지정

$lng = "128.656904955418550";

$lat = "35.896991823207710";


function search_Addr($url, $param=array()){
	$url = $url.'?'.http_build_query($param, '', '&');
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($param));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, false);//헤더 정보를 보내도록 함(*필수)
	//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	$contents = curl_exec($ch); 
	$contents_json = json_decode($contents, true); // 결과값을 파싱
	curl_close($ch);
	return $contents_json;
}
function search_Addr2($url, $param=array()){
	$url = $url.'?'.http_build_query($param, '', '&');
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, false);//헤더 정보를 보내도록 함(*필수)
	//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	$contents = curl_exec($ch); 
	$contents_json = json_decode($contents, true); // 결과값을 파싱
	curl_close($ch);
	return $contents_json;
}
$res = search_Addr('https://apis.openapi.sk.com/tmap/geo/reversegeocoding',array("version" => "1", "format" => "json", "callback" => "result", "coordType" => "WGS84GEO", "lon" => $lng,"lat" => $lat, "appKey" => "AIzaSyBAm_wDUkAZwtSDHp8OX3HM8h85tKnSR7c"));
print_r($res);
$address = $res['addressInfo']['fullAddress'];
if($address != ""){
	echo $address;
	//$address = urlencode($address);
	echo "-----------------------------------------------------";
			//주소검색 방법
			//NtoO : 새주소 -> 구주소 변환 검색
			//OtoN : 구주소(법정동) -> 새주소 변환 검색
	$res2 = search_Addr2('https://apis.openapi.sk.com/tmap/geo/convertAddress',array("version" => "1", "format" => "json", "callback" => "result", "searchTypCd" => "OtoN", "reqAdd" => $address, "resCoordType" => "WGS84GEO", "reqMulti" => "M", "appKey" => "AIzaSyBAm_wDUkAZwtSDHp8OX3HM8h85tKnSR7c"));
	print_r($res2);
	//echo $res2['ConvertAdd']['upperDistName'];
	$upperDistName = $res2['ConvertAdd']['upperDistName'];											// 시
	$middleDistName = $res2['ConvertAdd']['middleDistName'];										// 구
	$roadName = $res2['ConvertAdd']['newAddressList']['newAddress']['0']['roadName'];		// 도로명
	$bldNo1 = $res2['ConvertAdd']['newAddressList']['newAddress']['0']['bldNo1'];				// 번지
	$addr = $upperDistName." ".$middleDistName." ".$roadName." ".$bldNo1;
	if($upperDistName == ""){
		$addr = $address;
	}
}
?>