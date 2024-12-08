<?

	//공통 폼 (결제에 사용중)
	function common_Form($addr){
		$url = 'https://api2.sktelecom.com/tmap/pois?version=1&searchKeyword='.$addr.'&appKey=AIzaSyBAm_wDUkAZwtSDHp8OX3HM8h85tKnSR7c';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);//헤더 정보를 보내도록 함(*필수)
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0'); 
		$contents = curl_exec($ch); 
		$contents_json = json_decode($contents, true); // 결과값을 파싱
		curl_close($ch);
//		return $contents_json['searchPoiInfo']['pois']['poi']['0'];
		return $contents_json;
	}	
	// naver map key
	/*
	public $naver_client_id = "hiznu965vx";
	public $naver_client_secret = "2adILXyRAWoc3jpyonesul3e2BDv1A5vHEQPCzwX";
*/
	function common_Form2($addr){
		$url = 'https://naveropenapi.apigw.ntruss.com/map-geocode/v2/geocode?query='.$addr.'&X-NCP-APIGW-API-KEY-ID=hiznu965vx&X-NCP-APIGW-API-KEY=2adILXyRAWoc3jpyonesul3e2BDv1A5vHEQPCzwX';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);//헤더 정보를 보내도록 함(*필수)
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0'); 
		$contents = curl_exec($ch); 
		$contents_json = json_decode($contents, true); // 결과값을 파싱
		curl_close($ch);
//		return $contents_json['searchPoiInfo']['pois']['poi']['0'];
		return $contents_json;
	}	
$address ="경상북도 문경시 문경읍 여우목로 1717"; 
$addr = urlencode($address);
$addr2 = urldecode($addr);
$res = common_Form($addr);
//$res2 = common_Form2($addr);

if(is_array($res['error']) == 1 || $res == '') {
	$res2 = common_Form2($addr);
	echo $res2['status'];
	print_r($res2);
	echo "<BR><BR>";
	echo $res2['addresses']['0']['x'];
	echo "<BR><BR>";
	echo $res2['addresses']['0']['y'];
} else {
	print_r($res);
	echo "<BR><BR>";
	echo $res['searchPoiInfo']['pois']['poi']['0']['frontLon'];
	echo "<BR><BR>";
	echo $res['searchPoiInfo']['pois']['poi']['0']['frontLat'];
}
/*
echo $res['frontLat'];
echo "<BR><BR>";
echo $res['frontLon'];
*/
?>