<?php

class places_map {

	// t-map key
	//public $tmap_appKey = "AIzaSyBAm_wDUkAZwtSDHp8OX3HM8h85tKnSR7c";
	public $tmap_appKey="d966c545-ca3a-4d13-8dec-e80a39e78861";
	public $version ="1";
	public $format = "json";

	// naver map key
	public $naver_client_id = "14cmzzp3d4";
	public $naver_client_secret = "aONHhBH7FnWsLubSRtXpjRqsrgWvzU3aOC06qblu";


	//poi 검색
	public function poi($searchKeyword, $coordinateX, $coordinateY)
	{	
		// 검색어 - urlencoding
		$searchKeyword = urlencode($searchKeyword);

		// tmap 호출
		$remote_file = "https://api2.sktelecom.com/tmap/pois?version=".$this->version."&searchKeyword=".$searchKeyword."&appKey=".$this->tmap_appKey;		
		
		// curl리소스를 이용하여 검색값 가져오기------------- T-map
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL, $remote_file); 
		curl_setopt($ch, CURLOPT_HEADER, 0); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0'); 
		$content = curl_exec($ch); 
		curl_close($ch);
		// curl리소스를 이용하여 검색값 가져오기------------- T-map


		$key = json_decode($content, true);

		if($content)
		{
			$total_arry = array();
			$i=0;
			foreach($key['searchPoiInfo']['pois']['poi'] as $item => $val)
			{
				$total_arry[$i]['name'] = $key['searchPoiInfo']['pois']['poi'][$i]['name'];
				$total_arry[$i]['addr'] = $key['searchPoiInfo']['pois']['poi'][$i]['upperAddrName']." ".$key['searchPoiInfo']['pois']['poi'][$i]['middleAddrName']." ".$key['searchPoiInfo']['pois']['poi'][$i]['lowerAddrName'];
				$total_arry[$i]['dong'] = $key['searchPoiInfo']['pois']['poi'][$i]['lowerAddrName'];
				$total_arry[$i]['lat'] = $key['searchPoiInfo']['pois']['poi'][$i]['noorLat'];
				$total_arry[$i]['lon'] = $key['searchPoiInfo']['pois']['poi'][$i]['noorLon'];
				$i++;
			}
		}
		else
		{
		//tmap 오류 발생시 naver 
		// step2. naver map 접속시도
		
			$total_arry = array();
			/* 네이버 지도 서비스(Maps Geocoding API:2019.04.09) 잠정 중단으로 임시 차단__2019.04.08
			// 
			$remote_file_naver = "https://naveropenapi.apigw.ntruss.com/map-place/v1/search?query=".$searchKeyword."&coordinate=".$coordinateX.",".$coordinateY."&X-NCP-APIGW-API-KEY-ID=".$this->naver_client_id."&X-NCP-APIGW-API-KEY=".$this->naver_client_secret;

			// curl리소스를 이용하여 검색값 가져오기------------- Naver map
			$ch = curl_init(); 
			// url을 설정
			curl_setopt($ch, CURLOPT_URL, $remote_file_naver); 
			curl_setopt($ch, CURLOPT_HEADER, 0); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0'); 
			$content = curl_exec($ch); 
			curl_close($ch);
			// curl리소스를 이용하여 검색값 가져오기------------- Naver map

			$key = json_decode($content, true);
			//print_r($key[places]);
			$total_arry = array();
			$i=0;
			foreach($key['places'] as $item => $val)
			{
				$jium_address = explode(" ",$key['places'][$i]['jibun_address']);

				$total_arry[$i]['name'] = $key['places'][$i]['name'];
				$total_arry[$i]['addr'] = $key['places'][$i]['road_address'];
				$total_arry[$i]['dong'] = $jium_address[2];
				$total_arry[$i]['lat'] = $key['places'][$i]['y'];
				$total_arry[$i]['lon'] = $key['places'][$i]['x'];
				$i++;
			}
			*/
		}
		return $total_arry;
	}

	//geo 검색 => 경로
	public function geo($startX, $startY, $endX, $endY, $viaX, $viaY)
	{
		// step1. tmap 접속시도
		if ($viaX != "" && $viaY != "" ){
			$remote_file = "http://api2.sktelecom.com/tmap/routes?version=".$this->version."&tollgateFareOption=2&endX=".$endX."&endY=".$endY."&startX=".$startX."&startY=".$startY."&appKey=".$this->tmap_appKey."&passList=".$viaX.",".$viaY;
		}else {
			$remote_file = "http://api2.sktelecom.com/tmap/routes?version=".$this->version."&tollgateFareOption=2&endX=".$endX."&endY=".$endY."&startX=".$startX."&startY=".$startY."&appKey=".$this->tmap_appKey;
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

		if($content)
		{		
			$total_arry = array();
			$total_arry['totalDistance'] = $key['features'][0]['properties']['totalDistance'];
			$total_arry['totalTime'] = $key['features'][0]['properties']['totalTime'];
			$total_arry['totalFare'] = $key['features'][0]['properties']['totalFare'];
			$total_arry['taxiFare'] = $key['features'][0]['properties']['taxiFare'];		

			$point = array();
			$po = 0;
			for($i = 0; $i < count($key['features'])-1; $i++)
			{
				if($key['features'][$i]['geometry']['type'] == "LineString")
				{
					for($a = 0; $a < count($key['features'][$i]['geometry']['coordinates']); $a++)
					{
						$point[] = $key['features'][$i]['geometry']['coordinates'][$a];
						$po++;
					}
					$po = $po+1;
				}
			}

			$total_arry['point'] = $point;
		   
			// curl리소스를 이용하여 검색값 가져오기------------- T-map
		}
		else 
		{
			// step2. naver map 접속시도
			if($viaX != "" && $viaY != "") {
				$remote_file_naver = "https://naveropenapi.apigw.ntruss.com/map-direction/v1/driving?start=".$startX.",".$startY."&goal=".$endX.",".$endY."&waypoints=".$viaX.",".$viaY."&option=trafast&X-NCP-APIGW-API-KEY-ID=".$this->naver_client_id."&X-NCP-APIGW-API-KEY=".$this->naver_client_secret;
			} else {
				$remote_file_naver = "https://naveropenapi.apigw.ntruss.com/map-direction/v1/driving?start=".$startX.",".$startY."&goal=".$endX.",".$endY."&option=trafast&X-NCP-APIGW-API-KEY-ID=".$this->naver_client_id."&X-NCP-APIGW-API-KEY=".$this->naver_client_secret;			
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
			$total_arry['point'] = $key['route']['trafast'][0]['path'];

			// tmap / naver 둘다 결과값이 없을 경우
			if($total_array) {
				$total_arry['totalDistance'] = 0;
				$total_arry['totalTime'] = 0;
				$total_arry['totalFare'] = 0;
				$total_arry['taxiFare'] = 0;
				$total_arry['point'] = '';
			}

		}

		return $total_arry;
	}


	//reverse 검색
	public function reverse($startX, $startY)
	{
		// step1. tmap 접속시도
		$remote_file = "https://api2.sktelecom.com/tmap/geo/reversegeocoding?version=".$this->version."&lat=".$startY."&lon=".$startX."&appKey=".$this->tmap_appKey;
		//$remote_file = "https://api2.sktelecom.com/tmap/geo/reversegeocoding?version=".$this->version."&lat=".$startX."&lon=".$startY."&appKey=".$this->tmap_appKey;

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
		//에러가 아닌 경우
		if($content)
		{
			//print_r($key[addressInfo][fullAddress]);
			$total_arry = array();
			$total_arry['fullAddress'] = $key['addressInfo']['fullAddress'];
			//$total_arry['area1'] = $key[addressInfo][city_do];
			//$total_arry['area2'] = $key[addressInfo][gu_gun];
			$total_arry['dong'] = $key['addressInfo']['legalDong'];
			//$total_arry['area4'] = $key[addressInfo][bunji];
			$total_arry['name'] = $key['addressInfo']['buildingName'];
			$total_arry['startX'] = $startX;
			$total_arry['startY'] = $startY;
		   
		}
		//에러가 발생한 경우, 
		// step2. naver map 접속시도
		else
		{
			$remote_file_naver = "https://naveropenapi.apigw.ntruss.com/map-reversegeocode/v2/gc?request=coordsToaddr&coords=".$startX.",".$startY."&sourcecrs=epsg:4326&output=".$this->format."&orders=addr,admcode,roadaddr&X-NCP-APIGW-API-KEY-ID=".$this->naver_client_id."&X-NCP-APIGW-API-KEY=".$this->naver_client_secret;

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
			$fullAddress = $key['results'][0]['region']['area1']['name']." ".$key['results'][0]['region']['area2']['name']." ".$key['results'][0]['region']['area3']['name']." ".$key['results'][0]['region']['area4']['name'];

			
			$total_arry = array();
			$total_arry['fullAddress'] = $fullAddress;
			//$total_arry['area1'] = $key[results][0][region][area1][name];
			//$total_arry['area2'] = $key[results][0][region][area2][name];
			$total_arry['dong'] = $key['results'][0]['region']['area3']['name'];
			//$total_arry['area4'] = $key[results][0][region][area4][name];
			$total_arry['name'] = $key['results'][0]['land']['addition0']['value'];
			$total_arry['startX'] = $startX;
			$total_arry['startY'] = $startY;

		}
		
		return $total_arry;
	}



	//reverse_geo 검색
	public function reverse_geo($startX, $startY, $endX, $endY, $viaX, $viaY, $fullAddress)
	{
		// step1. tmap 접속시도
		if ($viaX != "" && $viaY != "" ){
			$remote_file = "http://api2.sktelecom.com/tmap/routes?version=".$this->version."&tollgateFareOption=2&endX=".$endX."&endY=".$endY."&startX=".$startX."&startY=".$startY."&appKey=".$this->tmap_appKey."&passList=".$viaX.",".$viaY;
		}else {
			$remote_file = "http://api2.sktelecom.com/tmap/routes?version=".$this->version."&tollgateFareOption=2&endX=".$endX."&endY=".$endY."&startX=".$startX."&startY=".$startY."&appKey=".$this->tmap_appKey;
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


		if($content)
		{
		
			$total_arry = array();
			$total_arry['totalDistance'] = $key['features'][0]['properties']['totalDistance'];
			$total_arry['totalTime'] = $key['features'][0]['properties']['totalTime'];
			$total_arry['totalFare'] = $key['features'][0]['properties']['totalFare'];
			$total_arry['taxiFare'] = $key['features'][0]['properties']['taxiFare'];		
			$total_arry['fullAddress'] = $fullAddress;		

			// curl리소스를 이용하여 검색값 가져오기------------- T-map

		}
		else
		{

			// step2. naver map 접속시도
			if(!$content)
			{
				if($viaX != "" && $viaY != "") {
					$remote_file_naver = "https://naveropenapi.apigw.ntruss.com/map-direction/v1/driving?start=".$startX.",".$startY."&goal=".$endX.",".$endY."&waypoints=".$viaX.",".$viaY."&option=trafast&X-NCP-APIGW-API-KEY-ID=".$this->naver_client_id."&X-NCP-APIGW-API-KEY=".$this->naver_client_secret;
				} else {
					$remote_file_naver = "https://naveropenapi.apigw.ntruss.com/map-direction/v1/driving?start=".$startX.",".$startY."&goal=".$endX.",".$endY."&option=trafast&X-NCP-APIGW-API-KEY-ID=".$this->naver_client_id."&X-NCP-APIGW-API-KEY=".$this->naver_client_secret;			
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
				$total_arry['fullAddress'] = $fullAddress;

				// tmap / naver 둘다 결과값이 없을 경우
				if($total_array) {
					$total_arry['totalDistance'] = 0;
					$total_arry['totalTime'] = 0;
					$total_arry['totalFare'] = 0;
					$total_arry['taxiFare'] = 0;
					$total_arry['fullAddress'] = '';
				}

			}
		}

		return $total_arry;
	}

}



//======================================================== 지도 검색 type에 따라 함수호출 

// step1. 등록된 키값 정보
$type = $_GET['map_type'];											//검색구분값 -> API호출 주소가 다르므로 구분이 지어져야 함.

// step2. 함수호출을 위한 class 생성
$map = new places_map;

// step3. map_type 별 함수호출
if($type == "poi") {
	
	$coordinateX = $_GET['coordinateX'];							//현재위치값X(네이버 검색시 현재 위치값을 기준으로 검색되어지기 위함)
	$coordinateY = $_GET['coordinateY'];							//현재위치값Y(네이버 검색시 현재 위치값을 기준으로 검색되어지기 위함)
	// 테스트 위한 임시값-----------
	//$coordinateX="128.6410154444359";
	//$coordinateY="35.91915595507744";

	//검색값
	$searchKeyword = $_GET['searchKeyword'];		
	$result = $map->poi($searchKeyword,$coordinateX, $coordinateY);
	
	//파일로 저장하기
	$md = md5($type."_".$coordinateX."_".$coordinateY);

} else if ($type == "geo") {
	
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

	if (isset($_GET["viaX"])) {
		$viaX = $_GET["viaX"];
	}	else	{
		$viaX = "";
	}
	if (isset($_GET["viaY"]))	{
		$viaY = $_GET["viaY"];
	}	else 	{
		$viaY = "";
	}
	

	$result = $map->geo($startX, $startY, $endX, $endY, $viaX, $viaY);
	//echo "geo<br>";
	//print_r($result);

	//파일로 저장하기
	if( $viaX != "" && $viaY != "") {
	  $md = md5($type."_".$startX."_".$startY."_".$endX."_".$endY."_".$viaX."_".$viaY);
	} else {
	  $md = md5($type."_".$startX."_".$startY."_".$endX."_".$endY);
	}

} else if ($type == "reverse") {	

	//$startX="128.6410154444359";
	//$startY="35.91915595507744";
	// 테스트 위한 임시값-----------
	$startX = $_GET["startX"];
	$startY = $_GET["startY"];

	$result = $map->reverse($startX, $startY);

	//파일로 저장하기	
	$md = md5($type."_".$startX."_".$startY);

} else if ($type == "reverse_geo") {	

	$goal = $_GET["goal"];
	if(!$goal) $goal = "end";

	//$endX = "128.692245";
	//$endY = "35.843648";
	//$startX="128.6410154444359";
	//$startY="35.91915595507744";
	// 테스트 위한 임시값-----------
	$endX = $_GET["endX"];
	$endY = $_GET["endY"];	
	$startX = $_GET["startX"];
	$startY = $_GET["startY"];
	$viaX = $_GET["viaX"];
	$viaY = $_GET["viaY"];

	if($goal == "start") {
		$result = $map->reverse($startX, $startY);
	} else {
		$result = $map->reverse($endX, $endY);
	}
	
	$fullAddress = $result['fullAddress'];

	$result = $map->reverse_geo($startX, $startY, $endX, $endY, $viaX, $viaY, $fullAddress);


	//파일로 저장하기
	if( $viaX != "" && $viaY != "") {
	  $md = md5($type."_".$startX."_".$startY."_".$endX."_".$endY."_".$viaX."_".$viaY);
	} else {
	  $md = md5($type."_".$startX."_".$startY."_".$endX."_".$endY);
	}

}


// 
if(!$result)
{
	$map_restult = array("result" => "error", "errorMsg"=>'검색정보가 없습니다.');
}
else 
{
	$map_restult = array("result" => "success", "map"=>$result);

	//파일로 저장하기
	$local_file = "/hd/webFolder/places/tmap/cache/".$md;

	if (file_exists($local_file)) {
	  //
	}else {
	  $file = fopen($local_file , "w");
  	  fwrite($file, json_encode($map_restult, JSON_UNESCAPED_UNICODE));
	  fclose($file);
	}
	
}


//결과값 return 
echo json_encode($map_restult, JSON_UNESCAPED_UNICODE); 

?>
