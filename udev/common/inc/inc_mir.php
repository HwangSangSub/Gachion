<?
function mirpay($url, $param=array()){
	 // POST 로 넘길 데이터가 있을 경우 작성합니다.
	 $ch = curl_init(); 
	 curl_setopt($ch, CURLOPT_URL, $url); 
	 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
	 curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); 
	 curl_setopt($ch, CURLOPT_HEADER, 0); 
	 curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); 
	 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	 curl_setopt($ch, CURLOPT_POST, 1); 
	 curl_setopt($ch, CURLOPT_POSTFIELDS, $param); 

	 $data = curl_exec($ch); 
	 $data_json = json_decode($data, true); // 결과값을 파싱
	 if (curl_error($ch))  
	 { 
		exit('CURL Error('.curl_errno( $ch ).') '.
		curl_error($ch)); 
	 } 
	 curl_close($ch); 
	 return $data_json; //결과값 리턴
}
?>