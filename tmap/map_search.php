<?php

//검색시 필요값
//등록된 키값 정보
$tmap_appKey = "AIzaSyBAm_wDUkAZwtSDHp8OX3HM8h85tKnSR7c";
$naver_client_id = "14cmzzp3d4";
$naver_client_secret = "aONHhBH7FnWsLubSRtXpjRqsrgWvzU3aOC06qblu";

$type = $_GET['map_type'];											//검색구분값 -> API호출 주소가 다르므로 구분이 지어져야 함.
$coordinateX = $_GET['coordinateX'];							//현재위치값X(네이버 검색시 현재 위치값을 기준으로 검색되어지기 위함)
$coordinateY = $_GET['coordinateY'];							//현재위치값Y(네이버 검색시 현재 위치값을 기준으로 검색되어지기 위함)
// 테스트 위한 임시값-----------
//$coordinateX="128.6410154444359";
//$coordinateY="35.91915595507744";

//텍스트 검색시 
if($type == "poi")
{
	// step1. tmap 접속시도
	$version ="1";
	$format = " json";

	$searchKeyword = $_GET['searchKeyword'];					
	$searchKeyword = urlencode($searchKeyword);

	$remote_file = "https://api2.sktelecom.com/tmap/pois?version={$version}&searchKeyword={$searchKeyword}&appKey={$tmap_appKey}";

	// curl리소스를 이용하여 검색값 가져오기------------- T-map
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $remote_file); 
	// 헤더는 제외하고 content 만 받음
	curl_setopt($ch, CURLOPT_HEADER, 0); 
	// 응답 값을 브라우저에 표시하지 말고 값을 리턴
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	// 브라우저처럼 보이기 위해 user agent 사용
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0'); 
	$content = curl_exec($ch); 
	//print_r($content);
	// 리소스 해제를 위해 세션 연결 닫음
	curl_close($ch);
	// curl리소스를 이용하여 검색값 가져오기------------- T-map

	$key = json_decode($content, true);
	$total_arry = array();
	$i=0;
	foreach($key["searchPoiInfo"]["pois"]["poi"] as $item => $val)
	{
		$total_arry[$i]['name'] = $key['searchPoiInfo']['pois']['poi'][$i]['name'];
		$total_arry[$i]['addr'] = $key['searchPoiInfo']['pois']['poi'][$i]['upperAddrName']." ".$key['searchPoiInfo']['pois']['poi'][$i]['middleAddrName']." ".$key['searchPoiInfo']['pois']['poi'][$i]['lowerAddrName'];
		$total_arry[$i]['lat'] = $key['searchPoiInfo']['pois']['poi'][$i]['noorLat'];
		$total_arry[$i]['lon'] = $key['searchPoiInfo']['pois']['poi'][$i]['noorLon'];
		$i++;
	}

	//tmap 오류 발생시 naver 
	// step2. naver map 접속시도
	if(!$content)
	{
		$remote_file_naver = "https://naveropenapi.apigw.ntruss.com/map-place/v1/search?query={$searchKeyword}&coordinate={$coordinateX},{$coordinateY}&X-NCP-APIGW-API-KEY-ID={$naver_client_id}&X-NCP-APIGW-API-KEY={$naver_client_secret}";

		// curl리소스를 이용하여 검색값 가져오기------------- Naver map
		$ch = curl_init(); 
		// url을 설정
		curl_setopt($ch, CURLOPT_URL, $remote_file_naver); 
		// 헤더는 제외하고 content 만 받음
		curl_setopt($ch, CURLOPT_HEADER, 0); 
		// 응답 값을 브라우저에 표시하지 말고 값을 리턴
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		// 브라우저처럼 보이기 위해 user agent 사용
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0'); 
		$content = curl_exec($ch); 
		//print_r($content);
		// 리소스 해제를 위해 세션 연결 닫음
		curl_close($ch);
		// curl리소스를 이용하여 검색값 가져오기------------- Naver map

		$key = json_decode($content, true);
		//print_r($key[places]);
		$total_arry = array();
		$i=0;
		foreach($key['places'] as $item => $val)
		{
			$total_arry[$i]['name'] = $key['places'][$i]['name'];
			$total_arry[$i]['addr'] = $key['places'][$i]['road_address'];
			$total_arry[$i]['lat'] = $key['places'][$i]['y'];					//y: 35.7763583
			$total_arry[$i]['lon'] = $key['places'][$i]['x'];				//x: 129.300625
			$i++;
		}
	}
	// step3. 결과값  retun
}
elseif($type == "geo")
{
	// step1. tmap 접속시도
	$version ="1";
	$format = " json";
	$endX = $_GET["endX"];
	$endY = $_GET["endY"];	
	$startX = $_GET["startX"];
	$startY = $_GET["startY"];
	// 테스트 위한 임시값-----------
	//$endX = "128.692245";
	//$endY = "35.843648";
	//$startX="128.6410154444359";
	//$startY="35.91915595507744";
	// 테스트 위한 임시값-----------
	$viaX = $_GET["viaX"];
	$viaY = $_GET["viaY"];

	if ($viaX != "" && $viaY != "" ){
		$remote_file = "http://api2.sktelecom.com/tmap/routes?version=1&tollgateFareOption=2&endX=".$endX."&endY=".$endY."&startX=".$startX."&startY=".$startY."&appKey=".$tmap_appKey."&passList=".$viaX.",".$viaY;
	}else {
		$remote_file = "http://api2.sktelecom.com/tmap/routes?version=1&tollgateFareOption=2&endX=".$endX."&endY=".$endY."&startX=".$startX."&startY=".$startY."&appKey=".$tmap_appKey;
	}

	// curl리소스를 이용하여 검색값 가져오기------------- T-map
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $remote_file); 
	// 헤더는 제외하고 content 만 받음
	curl_setopt($ch, CURLOPT_HEADER, 0); 
	// 응답 값을 브라우저에 표시하지 말고 값을 리턴
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	// 브라우저처럼 보이기 위해 user agent 사용
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0'); 
	$content = curl_exec($ch); 
	//print_r($content);
	// 리소스 해제를 위해 세션 연결 닫음
	curl_close($ch);

	//리턴값을 위한 리턴값 가공
	$key = json_decode($content, true);
	
	$total_arry = array();
	$total_arry['totalDistance'] = $key['features'][0]['properties']['totalDistance'];
	$total_arry['totalTime'] = $key['features'][0]['properties']['totalTime'];
	$total_arry['totalFare'] = $key['features'][0]['properties']['totalFare'];
	$total_arry['taxiFare'] = $key['features'][0]['properties']['taxiFare'];
	
   
	// curl리소스를 이용하여 검색값 가져오기------------- T-map

	// step2. naver map 접속시도
	if(!$content)
	{
		if($viaX != "" && $viaY != "") {
			$remote_file_naver = "https://naveropenapi.apigw.ntruss.com/map-direction/v1/driving?start={$startX},{$startY}&goal={$endX},{$endY}&waypoints={$viaX},{$viaY}&option=trafast&X-NCP-APIGW-API-KEY-ID={$naver_client_id}&X-NCP-APIGW-API-KEY={$naver_client_secret}";
		} else {
			$remote_file_naver = "https://naveropenapi.apigw.ntruss.com/map-direction/v1/driving?start={$startX},{$startY}&goal={$endX},{$endY}&option=trafast&X-NCP-APIGW-API-KEY-ID={$naver_client_id}&X-NCP-APIGW-API-KEY={$naver_client_secret}";			
		}
		

		// curl리소스를 이용하여 검색값 가져오기------------- Naver map
		$ch = curl_init(); 
		// url을 설정
		curl_setopt($ch, CURLOPT_URL, $remote_file_naver); 
		// 헤더는 제외하고 content 만 받음
		curl_setopt($ch, CURLOPT_HEADER, 0); 
		// 응답 값을 브라우저에 표시하지 말고 값을 리턴
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		// 브라우저처럼 보이기 위해 user agent 사용
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0'); 
		$content = curl_exec($ch); 		
		//print_r($content);
		// 리소스 해제를 위해 세션 연결 닫음
		curl_close($ch);
		// curl리소스를 이용하여 검색값 가져오기------------- Naver map

		//리턴값을 위한 리턴값 가공
		$key = json_decode($content, true);
		
		$total_arry = array();
		$total_arry['totalDistance'] = $key['route']['trafast'][0]['summary']['distance'];
		$total_arry['totalTime'] = round($key['route']['trafast'][0]['summary']['duration']/1000);
		$total_arry['totalFare'] = $key['route']['trafast'][0]['summary']['tollFare'];
		$total_arry['taxiFare'] = $key['route']['trafast'][0]['summary']['taxiFare'];

	}

	// step3. 이동경로값 텍스트 저장
	// step4. 결과값  retun
}
//reverse - 좌표값이 들어올 경우 
elseif($type == "reverse")
{
	// step1. tmap 접속시도
	$version ="1";
	$format = " json";
	
	$startX = $_GET["startX"];
	$startY = $_GET["startY"];
	$lat = $_GET['lat'];			
	$lon = $_GET['lon'];			

	$remote_file = "https://api2.sktelecom.com/tmap/reversegeocoding?version={$version}&lat={$lat}&lon={$lon}&appKey={$tmap_appKey}";

	// curl리소스를 이용하여 검색값 가져오기------------- T-map
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $remote_file); 
	// 헤더는 제외하고 content 만 받음
	curl_setopt($ch, CURLOPT_HEADER, 0); 
	// 응답 값을 브라우저에 표시하지 말고 값을 리턴
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	// 브라우저처럼 보이기 위해 user agent 사용
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0'); 
	$content = curl_exec($ch); 
	print_r($content);
	// 리소스 해제를 위해 세션 연결 닫음
	curl_close($ch);
	// curl리소스를 이용하여 검색값 가져오기------------- T-map

	/*
	$key = json_decode($content, true);
	$total_arry = array();
	$i=0;
	foreach($key[searchPoiInfo][pois][poi] as $item => $val)
	{
		$total_arry[$i]['name'] = $key[searchPoiInfo][pois][poi][$i][name];
		$total_arry[$i]['addr'] = $key[searchPoiInfo][pois][poi][$i][upperAddrName]." ".$key[searchPoiInfo][pois][poi][$i][middleAddrName]." ".$key[searchPoiInfo][pois][poi][$i][lowerAddrName];
		$total_arry[$i]['lat'] = $key[searchPoiInfo][pois][poi][$i][noorLat];
		$total_arry[$i]['lon'] = $key[searchPoiInfo][pois][poi][$i][noorLon];
		$i++;
	}
	*/
}

if(!$content)
{
	$result = array("result" => "error", "map"=>'검색정보가 없습니다.');
}
else 
{

	$result = array("result" => "success", "map"=>$total_arry);


    /*
    //파일로 저장하기
	$md = "test_".time();
	$local_file = "/hd/webFolder/places/tmap/cache/".$md;

	if (file_exists($local_file)) {
	  //
	}else {
		echo"<br>-----------------<br>";
		print_r(json_encode($result, JSON_UNESCAPED_UNICODE));
		echo"<br>-----------------<br>";
	  $file = fopen($local_file , "w");
  	  fwrite($file, json_encode($result, JSON_UNESCAPED_UNICODE));
	  fclose($file);
    }
    */


	
}

//결과값 return 
echo json_encode($result, JSON_UNESCAPED_UNICODE); 
?>
