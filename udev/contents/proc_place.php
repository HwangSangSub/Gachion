<?
/*
* 프로그램				: 지도 등록 및 수정(관리자페이지)
* 페이지 설명			: 지도를 등록하는 기능
* 파일명					: proc_contents.php
* 관련DB					: TB_CONTENTS
*/
include "../lib/common.php";
include "../../lib/functionDB.php";
include "../../lib/alertLib.php"; 
include "../../lib/thumbnail.lib.php";   //썸네일
			
$DB_con = db1();
$place_Name = trim($place_Name);									//지점명
$con_Idx = trim($con_Idx);												//지도고유번호
$reg_Id = contentsIdInfo($con_Idx);									//등록자
$mIdx = memIdxInfo($reg_Id);
$area_Code = trim($area_Code);										//소속지역(kml파일이 있는 지도의 경우에만 등록)
if($area_Code == ""){
	$area_Code = "";
}
$tel = trim($tel);															//연락처
$addr = trim($addr);														//주소
$addr_etc = trim($addr_etc);													//상세주소
$taddr = $addr." ".$addr_etc;											//합친주소
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
$address = urlencode($addr);
$res = common_Form($address);
$rLng = $res['coordinateInfo']['coordinate']['0']['newLonEntr'];		//경도
if($rLng ==''){
	$lng = '0.000000000000000';
}else{
	$lng = $rLng;
}
$rLat = $res['coordinateInfo']['coordinate']['0']['newLatEntr'];		//위도
if($rLat ==''){
	$lat = '0.000000000000000';
}else{
	$lat = $rLat;
}
$place_Icon = trim($place_Icon);										//지점대표아이콘

$open_Bit = trim($open_Bit);

if($open_Bit == ""){
	$open_Bit = "0";
}

if($mode == "mod"){
	$idx = trim($idx);														// 지점 고유번호	
	$place_Icon = trim($place_Icon);										//지점대표아이콘
	$org_place_Icon = trim($org_place_Icon);					// 기존 지점명
	if($place_Icon == $org_place_Icon || $place_Icon == ""){
		$placeIcon = $org_place_Icon;
	}else{
		$placeIcon = $place_Icon;
	}

	$category_query = "
		SELECT code_Sub_Div
		FROM TB_CONFIG_CODE
		WHERE code = :code
			AND code_Div = 'placeicon'
			AND use_Bit = '0'
	";
	$category_stmt = $DB_con->prepare($category_query);
	$category_stmt->bindParam(":code", $placeIcon);
	$category_stmt->execute();
	$category_row=$category_stmt->fetch(PDO::FETCH_ASSOC);
	$category = $category_row['code_Sub_Div'];								//카테고리
	$chk_place_query = "
		SELECT *
		FROM TB_PLACE
		WHERE idx = :idx
	";	
	$chk_place_stmt = $DB_con->prepare($chk_place_query);
	$chk_place_stmt->bindParam(":idx", $idx);
	$chk_place_stmt->execute();
	$chk_place_row=$chk_place_stmt->fetch(PDO::FETCH_ASSOC);
	$org_img = $chk_place_row['img'];								// 지점이미지
	$org_con_Idx = trim($org_con_Idx);								// 지도 고유번호
	$org_area_Code = trim($org_area_Code);
	if($con_Idx == $org_con_Idx || $con_Idx == ""){
		$con_Idx = $org_con_Idx;
		$area_Code = $org_area_Code;
	}else{
		$con_Idx = $con_Idx;
		$area_Code = $area_Code;
	}
	$reg_Id = contentsIdInfo($con_Idx);								//등록자
	$memberIdx = memIdxInfo($reg_Id);							// 회원고유번호
	$org_place_Name = trim($org_place_Name);					// 기존 지점명
	if($place_Name == $org_place_Name || $place_Name == ""){
		$place_Name = $org_place_Name;
	}else{
		$place_Name = $place_Name;
	}
/*
	$org_reg_Id = trim($org_reg_Id);									// 기존 등록자
	$org_memberIdx = memIdxInfo($org_reg_Id);					// 기존 회원고유번호
	if($reg_Id == $org_reg_Id){
		$reg_Id = $org_reg_Id;
		$memberIdx = $org_memberIdx;
	}else{
		$reg_Id = $reg_Id;
		$memberIdx = $memberIdx;
	}
	*/
	$up_con_query = "
		UPDATE TB_PLACE
		SET member_Idx = :member_Idx,
			con_Idx = :con_Idx,
			area_Code = :area_Code,
			place_Name = :place_Name,
			place_Icon = :place_Icon,
			category = :category,
			open_Bit = :open_Bit,
			memo = :memo,
			tel = :tel,
			addr = :addr,
			lat = :lat,
			lng = :lng,
			reg_Id = :reg_Id,
			mod_Date = NOW()
		WHERE idx = :idx
	";	
	$up_con_stmt = $DB_con->prepare($up_con_query);
	$up_con_stmt->bindParam(":idx", $idx);
	$up_con_stmt->bindParam(":member_Idx", $memberIdx);
	$up_con_stmt->bindParam(":con_Idx", $con_Idx);
	$up_con_stmt->bindParam(":area_Code", $area_Code);
	$up_con_stmt->bindParam(":place_Name", $place_Name);
	$up_con_stmt->bindParam(":place_Icon", $placeIcon);
	$up_con_stmt->bindParam(":category", $category);
	$up_con_stmt->bindParam(":open_Bit", $open_Bit);
	$up_con_stmt->bindParam(":memo", $memo);
	$up_con_stmt->bindParam(":tel", $tel);
	$up_con_stmt->bindParam(":addr", $taddr);
	$up_con_stmt->bindParam(":lat", $lat);
	$up_con_stmt->bindParam(":lng", $lng);
	$up_con_stmt->bindParam(":reg_Id", $reg_Id);
	$up_con_stmt->execute();

	// 이미지가 있는 경우 (없는 경우는 빈값처리)
	if(isset($_FILES['img'])){
		$now_time = time();										// 추후 파일 디렉토리가 될 예정
		// 이미지 경로
		$file_dir = $_SERVER["DOCUMENT_ROOT"].'/contents/place_img/'.$now_time;
		if(!is_dir($file_dir)){
			mkdir($file_dir, 0777, true);
		}
		// 이미지 ------------------------------------------------------------------
		foreach ($_FILES['img']['name'] as $f => $name) {  
			$name = $_FILES['img']['name'][$f];
			$uploadName = explode('.', $name);
			// $fileSize = $_FILES['upload']['size'][$f];
			// $fileType = $_FILES['upload']['type'][$f];
			$uploadname = time().$f.'.jpg';
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
				idx =	".$idx." 
		";		
		$DB_con->exec($insQuery);  
	}else{
		$insQuery = "
			update TB_PLACE 
			set 
				img ='' 
			where 
				idx =	".$idx." 
		";		
		$DB_con->exec($insQuery);  
	}

	if($idx != ""){
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

	$preUrl = "reg_place.php?mode=mod&idx=".$idx;
	$message = "mod";
	proc_msg($message, $preUrl);
}else{
	$category_query = "
		SELECT code_Sub_Div
		FROM TB_CONFIG_CODE
		WHERE code = :code
			AND code_Div = 'placeicon'
			AND use_Bit = '0'
	";
	$category_stmt = $DB_con->prepare($category_query);
	$category_stmt->bindParam(":code", $place_Icon);
	$category_stmt->execute();
	$category_row=$category_stmt->fetch(PDO::FETCH_ASSOC);
	$category = $category_row['code_Sub_Div'];								//카테고리
	if($area_Code != ""){
		//지점 등록
		$query = "INSERT INTO TB_PLACE (member_Idx, con_Idx, area_Code, category, place_Name, place_Icon, memo, tel, img, otime_Day, otime_Week, addr, lng, lat, open_Bit, reg_Id, mod_Date, reg_date) VALUES (:member_Idx, :con_Idx, :area_Code, :category, :place_Name, :place_Icon, :memo, :tel, '', '', '', :addr, :lng, :lat, :open_Bit, :reg_Id, NOW(), NOW())";
		
		$stmt = $DB_con->prepare($query);
		$stmt->bindParam(":member_Idx", $mIdx);
		$stmt->bindParam(":con_Idx", $con_Idx);
		$stmt->bindParam(":area_Code", $area_Code);
		$stmt->bindParam(":category", $category);
		$stmt->bindParam(":place_Name", $place_Name);
		$stmt->bindParam(":place_Icon", $place_Icon);
		$stmt->bindParam(":memo", $memo);
		$stmt->bindParam(":tel", $tel);
		$stmt->bindParam(":addr", $taddr);
		$stmt->bindParam(":lng", $lng);
		$stmt->bindParam(":lat", $lat);
		$stmt->bindParam(":open_Bit", $open_Bit);
		$stmt->bindParam(":reg_Id", $reg_Id);
		$stmt->execute();
		$pIdx = $DB_con->lastInsertId();  //저장된 idx 값
	}else{
		//지점 등록
		$query = "INSERT INTO TB_PLACE (member_Idx, con_Idx, category, place_Name, place_Icon, memo, tel, img, otime_Day, otime_Week, addr, lng, lat, open_Bit, reg_Id, mod_Date, reg_date) VALUES (:member_Idx, :con_Idx, :category, :place_Name, :place_Icon, :memo, :tel, '', '', '', :addr, :lng, :lat, :open_Bit, :reg_Id, NOW(), NOW())";
		
		$stmt = $DB_con->prepare($query);
		$stmt->bindParam(":member_Idx", $mIdx);
		$stmt->bindParam(":con_Idx", $con_Idx);
		$stmt->bindParam(":category", $category);
		$stmt->bindParam(":place_Name", $place_Name);
		$stmt->bindParam(":place_Icon", $place_Icon);
		$stmt->bindParam(":memo", $memo);
		$stmt->bindParam(":tel", $tel);
		$stmt->bindParam(":addr", $taddr);
		$stmt->bindParam(":lng", $lng);
		$stmt->bindParam(":lat", $lat);
		$stmt->bindParam(":open_Bit", $open_Bit);
		$stmt->bindParam(":reg_Id", $reg_Id);
		$stmt->execute();
		$pIdx = $DB_con->lastInsertId();  //저장된 idx 값
	}
	// 이미지가 있는 경우 없는 경우는 빈값처리
	if(isset($_FILES['img'])){
		$now_time = time();										// 추후 파일 디렉토리가 될 예정
		// 이미지 경로
		$file_dir = $_SERVER["DOCUMENT_ROOT"].'/contents/place_img/'.$now_time;
		if(!is_dir($file_dir)){
			mkdir($file_dir, 0777, true);
		}
		// 이미지 ------------------------------------------------------------------
			$name = $_FILES['img']['name'][$f];
			$uploadName = explode('.', $name);
			// $fileSize = $_FILES['upload']['size'][$f];
			// $fileType = $_FILES['upload']['type'][$f];
			$uploadname = time().$f.'.jpg';
			$uploadFile = $file_dir."/".$uploadname;
			if(move_uploaded_file($_FILES['img']['tmp_name'][$f], $uploadFile)){	
				$upload_Bit = "1";
			}else{
				$upload_Bit = "0";
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
	$history = "지점등록";
	$his_query ="INSERT INTO TB_HISTORY (member_Idx, mem_Id, con_Idx, place_Idx, history, reg_Id, reg_date) VALUES (:member_Idx, :mem_Id, :con_Idx, :place_Idx, :history, :reg_Id, NOW())";
	$his_stmt = $DB_con->prepare($his_query);
	$his_stmt->bindParam(":member_Idx", $mIdx);
	$his_stmt->bindParam(":mem_Id", $reg_Id);
	$his_stmt->bindParam(":history", $history);
	$his_stmt->bindParam(":place_Idx", $pIdx);
	$his_stmt->bindParam(":con_Idx", $con_Idx);
	$his_stmt->bindParam(":reg_Id", $reg_Id);
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
	$preUrl = "list_place.php";
	$message = "reg";
	proc_msg($message, $preUrl);
}
echo json_encode($result);
?>



