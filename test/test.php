<?
set_time_limit(0);
include "../lib/common.php";
//공통 폼 (결제에 사용중)

$access_token = "25360d4503de070c69a485787fe24b739b159dd78d870a37665100acf2153874";

function common_Form($url, $param=array(), $access_token_value){
	$url = $url.'?'.http_build_query($param, '', '&');
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 0);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($param));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, false);//헤더 정보를 보내도록 함(*필수)
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization:'.$access_token_value));
	//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	$contents = curl_exec($ch); 
	$contents_json = json_decode($contents, true); // 결과값을 파싱
	curl_close($ch);
	return $contents_json;
}	
//공통 폼 (결제에 사용중)
function common_Form2($url, $param=array()){
	$url = $url.'?'.http_build_query($param, '', '&');
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($param));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, false);//헤더 정보를 보내도록 함(*필수)
	//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$contents = curl_exec($ch); 
	var_dump($response);        //결과 값 출력
	print_r(curl_getinfo($contents)); //모든 정보 출력
	echo curl_errno($ch);       //에러 정보 출력
	echo curl_error($ch);       //에러 정보 출력
	$contents_json = json_decode($contents, true); // 결과값을 파싱
	curl_close($ch);
}	
//공통 폼 (결제에 사용중)
function common_Form3($url, $param=array()){
	$url = $url.'?'.http_build_query($param, '', '&');
	$ch = curl_init();													//curl 초기화
	curl_setopt($ch, CURLOPT_URL, $url);						//URL 지정하기
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);		//요청 결과를 문자열로 반환 
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);		//connection timeout 10초 
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);		//원격 서버의 인증서가 유효한지 검사 안함
	 
	$response = curl_exec($ch);
	curl_close($ch);
	 
	return $response;
}	


echo "asdkfsdakhbnsadjklgbsadkgbnsadjkfnldksanflskdajnflasdfnalskj";
$res = common_Form('https://api.bigdatahub.co.kr/v1/datahub/datasets/search.json',array("pid" => "1002261"), $access_token);
print_r($res);
/*
$res = common_Form('https://api.bigdatahub.co.kr/v1/datahub/datasets/search.json',array("pid" => "1002261", "$page" => "2", "$count" => "5", "$select" => "년,영화관,총 이용건수,30 대남", "$where" => "30 대남 <= 1"), $access_token);
print_r($res);*/
/*
$res = common_Form('https://api.bigdatahub.co.kr/v1/datahub/datasets/search.json',array("pid" => "1002261"), $access_token);
print_r($res);
$res2 = common_Form2('https://api.bigdatahub.co.kr/v1/datahub/datasets/search.json',array("TDCAccessKey" => $access_token, "pid" => "1002261"));
print_r($res2);
$res3 = common_Form3('https://api.bigdatahub.co.kr/v1/datahub/datasets/search.json',array("TDCAccessKey" => $access_token, "pid" => "1002261"));
print_r($res3);
*/
?>