#!/usr/bin/php -q
<?
	$con_Idx = '309';
	function mapImg($url, $param=array()){
		$url = $url.'?'.http_build_query($param, '', '&');
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);//헤더 정보를 보내도록 함(*필수)
		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$contents = curl_exec($ch); 
		//print_r($contents);
		$contents_json = json_decode($contents, true); // 결과값을 파싱
		curl_close($ch);
		return $contents_json;
	}
//	echo "success";
//	mapImg('https://places.gachita.kr/contents/map_img/contents/map_img_save.php',array("idx" => $con_Idx));
//	Proc_Close (Proc_Open ("/hd/webFolder/places/contents/map_img/contents/map_img_save.php", array("idx" => $con_Idx)));
//	system("php -e /hd/webFolder/places/contents/map_img/contents/map_img_save.php idx 309");
//	system("php -e /hd/webFolder/places/contents/map_img/contents/map_img_save.php idx ".$con_Idx." >> /dev/null 2>&1");
	exec("/var/www/places/contents/map_img/contents/map_img_save.php ".$con_Idx);

?>