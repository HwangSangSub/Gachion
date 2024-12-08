<?
/*
* 프로그램				: 핀을 등록하는 기능
* 페이지 설명			: 핀을 등록하는 기능
* 파일명					: reg_place.php
* 관련DB					: TB_CONTENTS, TB_PLACE
*/
include "../lib/common.php";
include "../lib/functionDB.php";  //공통 db함수

$con_Idx = trim($con_Idx);						//콘텐츠그룹
$category = trim($category);					//카테고리 
$place_Name = trim($place_Name);			//장소명
$place_Icon = trim($place_Icon);				//장소대표아이콘
$memo = trim($memo);							//상세설명
$smemo = trim($smemo);						//한줄설명
$tel = trim($tel);									//연락처
$otime_Day = trim($otime_Day);				//영업시간(평일)
$otime_Week = trim($otime_Week);			//영업시간(주말)
$lng = trim($lng);									//경도
$lat = trim($lat);									//위도
$open_Bit = trim($open_Bit);					//공개설정 (0: 공개, 1: 비공개)
if($open_Bit == ""){
	$open_Bit = "0";								//디폴트값은 0 공개로 설정
}

$now_time = time();										// 추후 파일 디렉토리가 될 예정
//좌표 지정
/*
$lng = "126.921804310000000";

$lat = "37.554260160000000";
*/
//공통 폼 (맵좌표 확인)
function common_Form($address){
	$url = 'https://apis.openapi.sk.com/tmap/geo/fullAddrGeo?version=1&coordType=WGS84GEO&addressFlag=F00&fullAddr='.$address.'';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);//헤더 정보를 보내도록 함(*필수)
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0'); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept:application/json', 'appKey:l7xx9dc45675484b429189bdddc5f4885e5d'));
	$contents = curl_exec($ch); 
	$contents_json = json_decode($contents, true); // 결과값을 파싱
	curl_close($ch);
//	return $contents_json['searchPoiInfo']['pois']['poi']['0'];	// 최종 위치정보만 가져오기
	return $contents_json;
}
function search_Addr($url, $param=array()){
	$url = $url.'?'.http_build_query($param, '', '&');
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($param));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, false);//헤더 정보를 보내도록 함(*필수)
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept:application/json', 'appKey:l7xx9dc45675484b429189bdddc5f4885e5d'));
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
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept:application/json', 'appKey:l7xx9dc45675484b429189bdddc5f4885e5d'));
	//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	$contents = curl_exec($ch); 
	$contents_json = json_decode($contents, true); // 결과값을 파싱
	curl_close($ch);
	return $contents_json;
}
$res = search_Addr('https://apis.openapi.sk.com/tmap/geo/reversegeocoding',array("version" => "1", "format" => "json", "callback" => "result", "coordType" => "WGS84GEO", "lon" => $lng,"lat" => $lat));
$address = $res['addressInfo']['fullAddress'];
if($address != ""){
	//$address = urlencode($address);
	//echo "-----------------------------------------------------";
			//주소검색 방법
			//NtoO : 새주소 -> 구주소 변환 검색
			//OtoN : 구주소(법정동) -> 새주소 변환 검색
	$res2 = search_Addr2('https://apis.openapi.sk.com/tmap/geo/convertAddress',array("version" => "1", "format" => "json", "callback" => "result", "searchTypCd" => "OtoN", "reqAdd" => $address, "resCoordType" => "WGS84GEO", "reqMulti" => "M"));
	//print_r($res2);
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
$reg_Id = trim($reg_Id);							//등록자
$mIdx = memIdxInfo($reg_Id);
$reg_Date = DU_TIME_YMDHIS;				//등록일
$DB_con = db1();

$chk_place_query = "
	SELECT count(*) as cnt
	FROM TB_PLACE
	WHERE member_Idx = :member_Idx
		AND con_Idx = :con_Idx
		AND delete_Bit = '0'

	;
";
$chk_place_Stmt = $DB_con->prepare($chk_place_query);
$chk_place_Stmt->bindparam(":member_Idx",$mIdx);
$chk_place_Stmt->bindparam(":con_Idx",$con_Idx);
$chk_place_Stmt->execute();
$chk_place_row = $chk_place_Stmt->fetch(PDO::FETCH_ASSOC);
$cnt = $chk_place_row['cnt'];	//회원등급
$total_cnt = (int)$cnt + 1;
$placeMaxCnt = (int)$place_MaxCnt + 1;
if($total_cnt < $placeMaxCnt){
	if ($reg_Id != "") {
		//지점 등록
		$query = "INSERT INTO TB_PLACE (member_Idx, con_Idx, category, place_Name, place_Icon, memo, smemo, tel, img, otime_Day, otime_Week, addr, lng, lat, open_Bit, reg_Id, mod_Date, reg_date) VALUES (:member_Idx, :con_Idx, :category, :place_Name, :place_Icon, :memo, :smemo, :tel, '', :otime_Day, :otime_Week, :addr, :lng, :lat, :open_Bit, :reg_Id, NOW(), :reg_Date)";
		
		$stmt = $DB_con->prepare($query);
		$stmt->bindParam(":member_Idx", $mIdx);
		$stmt->bindParam(":con_Idx", $con_Idx);
		$stmt->bindParam(":category", $category);
		$stmt->bindParam(":place_Name", $place_Name);
		$stmt->bindParam(":place_Icon", $place_Icon);
		$stmt->bindParam(":memo", $memo);
		$stmt->bindParam(":smemo", $smemo);
		$stmt->bindParam(":tel", $tel);
		$stmt->bindParam(":otime_Day", $otime_Day);
		$stmt->bindParam(":otime_Week", $otime_Week);
		$stmt->bindParam(":addr", $addr);
		$stmt->bindParam(":lng", $lng);
		$stmt->bindParam(":lat", $lat);
		$stmt->bindParam(":open_Bit", $open_Bit);
		$stmt->bindParam(":reg_Id", $reg_Id);
		$stmt->bindParam(":reg_Date", $reg_Date);
		$stmt->execute();
		$pIdx = $DB_con->lastInsertId();  //저장된 idx 값

		$con_query ="
			UPDATE TB_CONTENTS
			SET mod_Date = NOW()
			WHERE idx = :idx
				AND member_Idx = :member_Idx";
		$con_stmt = $DB_con->prepare($con_query);
		$con_stmt->bindParam(":member_Idx", $mIdx);
		$con_stmt->bindParam(":idx", $con_Idx);
		$con_stmt->execute();
		// 이미지가 있는 경우 없는 경우는 빈값처리
		if(isset($_FILES['img'])){
			$now_time = time();										// 추후 파일 디렉토리가 될 예정
			// 이미지 경로
			$file_dir = $_SERVER["DOCUMENT_ROOT"].'/contents/place_img/'.$now_time;
			if(!is_dir($file_dir)){
				mkdir($file_dir, 0777, true);
			}
			// 이미지 ------------------------------------------------------------------
			foreach ($_FILES['img']['name'] as $f => $name) {   
				$img_ok = array("image/png", "image/gif", "image/jpg", "image/jpeg", "image/bmp", "image/GIF", "image/PNG", "image/JPG", "image/JPEG", "image/BMP");
				$detectedType = $_FILES['img']['type'][$f];
				if(in_array($detectedType, $img_ok)){
					if($detectedType == "image/png"){
						$uploadFName = "png";
					}else if($detectedType == "image/gif"){
						$uploadFName = "gif";
					}else if($detectedType == "image/jpg"){
						$uploadFName = "jpg";
					}else if($detectedType == "image/jpeg"){
						$uploadFName = "jpeg";
					}else if($detectedType == "image/bmp"){
						$uploadFName = "bmp";
					}else if($detectedType == "image/GIF"){
						$uploadFName = "gif";
					}else if($detectedType == "image/PNG"){
						$uploadFName = "png";
					}else if($detectedType == "image/JPG"){
						$uploadFName = "jpg";
					}else if($detectedType == "image/JPEG"){
						$uploadFName = "jpeg";
					}else if($detectedType == "image/BMP"){
						$uploadFName = "bmp";
					}
				}else{
				}
				
				$name = $_FILES['img']['name'][$f];
				$uploadName = explode('.', $name);
				// $fileSize = $_FILES['upload']['size'][$f];
				// $fileType = $_FILES['upload']['type'][$f];
				$uploadname = time().$f.'.'.$uploadFName;
				$uploadFile = $file_dir."/".$uploadname;
				if(move_uploaded_file($_FILES['img']['tmp_name'][$f], $uploadFile)){	
					$upload_Bit = "1";
				}else{
					$upload_Bit = "0";
				}
			}
			$insQuery = "
				update TB_PLACE 
				set 
					img ='".$now_time."' 
				where 
					idx =	".$pIdx." 
			";		
			$DB_con->exec($insQuery);  
		}else{
			$insQuery = "
				update TB_PLACE 
				set 
					img ='' 
				where 
					idx =	".$pIdx." 
			";		
			$DB_con->exec($insQuery);  
		}
		if($con_Idx == ""){
			$con_Idx = "";
		}
		$history = "지점등록";
		$his_query ="INSERT INTO TB_HISTORY (member_Idx, mem_Id, con_Idx, place_Idx, history, reg_Id, reg_date) VALUES (:member_Idx, :mem_Id, :con_Idx, :place_Idx, :history, :reg_Id, :reg_Date)";
		$his_stmt = $DB_con->prepare($his_query);
		$his_stmt->bindParam(":member_Idx", $mIdx);
		$his_stmt->bindParam(":mem_Id", $reg_Id);
		$his_stmt->bindParam(":history", $history);
		$his_stmt->bindParam(":place_Idx", $pIdx);
		$his_stmt->bindParam(":con_Idx", $con_Idx);
		$his_stmt->bindParam(":reg_Id", $reg_Id);
		$his_stmt->bindParam(":reg_Date", $reg_Date);
		$his_stmt->execute();

		if($pIdx != ""){
			$chk_map_query = "
				SELECT COUNT(*) as map_cnt
				FROM TB_CONTENTS_MAP
				WHERE con_Idx = :con_Idx
					AND status = 'READY'
				;
			";
			$chk_map_stmt = $DB_con->prepare($chk_map_query);
			$chk_map_stmt->bindParam(":con_Idx", $con_Idx);
			$chk_map_stmt->execute();
			$chk_map_row=$chk_map_stmt->fetch(PDO::FETCH_ASSOC);
			$map_cnt = $chk_map_row['map_cnt'];
			if($map_cnt > 0){
				$cm_up_query ="UPDATE TB_CONTENTS_MAP SET reg_Date = NOW() WHERE con_Idx = :con_Idx AND status = 'READY' LIMIT 1;";
				$cm_up_stmt = $DB_con->prepare($cm_up_query);
				$cm_up_stmt->bindParam(":con_Idx", $con_Idx);
				$cm_up_stmt->execute();
			}else{
				$cm_query ="INSERT INTO TB_CONTENTS_MAP (con_Idx, reg_Date) VALUES (:con_Idx, NOW());";
				$cm_stmt = $DB_con->prepare($cm_query);
				$cm_stmt->bindParam(":con_Idx", $con_Idx);
				$cm_stmt->execute();
			}
		}

		dbClose($DB_con);
		$stmt = null;
		$chkStmt = null;
		$his_stmt = null;
		$result = array("result" => "success");
	} else { //빈값일 경우
		$result = array("result" => "error", "errorMsg" => "지점등록실패");
	}
}else{
	$result = array("result" => "error", "errorMsg" => "등록된 지점이 300개가 넘어 지점등록을 할 수 없습니다.");
}

	echo json_encode($result);
?>



