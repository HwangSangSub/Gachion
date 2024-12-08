<?
	$mIdx = "5";
	$mem_Id = "placet01@gmail.com";

	$md = md5($mIdx."_".$mem_Id);
	$local_file = "/hd/webFolder/places/contents/cache/".$md;
	$nowTime = date("Y-m-d H:i:s.", time());
	$fileTime = date("Y-m-d H:i:s.", filemtime($local_file));
	$r = strtotime($nowTime) - strtotime($fileTime) ;
		echo $r."<br>" ;
	$time_min = ceil($r / 60);
		echo $time_min."<br>" ;
	if((int)$time_min < 0){
		$time_min = 0;
	}
	if ($time_min < 60) {
		echo $nowTime."<br>" ;
		echo $fileTime."<br>";
		echo ceil($r / 60)."<br>";
	}
	echo $md;

/*
	$con_Idx = '309';

				$file_dir = "/hd/webFolder/places/contents/map_img/contents/'".$con_Idx."'$'\r''.jpg'";
				unlink($file_dir);
				shell_exec("rm -f '".$con_Idx."'$'\r''.jpg'");
				
*/
/*
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
		print_r($contents);
		$contents_json = json_decode($contents, true); // 결과값을 파싱
		curl_close($ch);
		return $contents_json;
	}
//	echo "success";
	$map_Img = mapImg('https://places.gachita.kr/node/exec2.php',array("conIdx" => $con_Idx));
	echo $map_Img;
	print_r($map_Img);
	*/
?>