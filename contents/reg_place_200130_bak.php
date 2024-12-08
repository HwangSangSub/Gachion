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
$address = trim($addr);							//주소
$zaddr = trim($zaddr);							//상세주소
$taddr = $address." ".$zaddr;					//전체 주소
/* 이미지 파일 업로드 시작 */
if(isset($_FILES['img'])){
	$img_f_name = $_FILES['img']['name'];
	$img_fname = iconv("UTF-8", "EUC-KR", $img_f_name);
	$img_target = "./place_img/".$img_fname ; 
	move_uploaded_file($_FILES['img']['tmp_name'],$img_target);
	$img = trim($_FILES['img']['name']);				//썸네일이미지
}else{
	$img = "";												//썸네일이미지
}
//공통 폼 (맵좌표 확인)
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
//	return $contents_json['searchPoiInfo']['pois']['poi']['0'];	// 최종 위치정보만 가져오기
	return $contents_json;
}
$addr = urlencode($address);
$res = common_Form($addr);
$rLng = $res['searchPoiInfo']['pois']['poi']['0']['frontLon'];		//경도
if($rLng ==''){
	$lng = '0.000000000000000';
}else{
	$lng = $rLng;
}
$rLat = $res['searchPoiInfo']['pois']['poi']['0']['frontLat'];		//위도
if($rLat ==''){
	$lat = '0.000000000000000';
}else{
	$lat = $rLat;
}
$reg_Id = trim($reg_Id);							//등록자
$mIdx = memIdxInfo($reg_Id);
$reg_Date = DU_TIME_YMDHIS;				//등록일

if ($reg_Id != "") {
    
    $DB_con = db1();
	if($con_Idx != ""){
		//지점 등록
		$query = "INSERT INTO TB_PLACE (member_Idx, con_Idx, category, place_Name, place_Icon, memo, smemo, tel, img, otime_Day, otime_Week, addr, lng, lat, reg_Id, reg_date) VALUES (:member_Idx, :con_Idx, :category, :place_Name, :place_Icon, :memo, :smemo, :tel, :img, :otime_Day, :otime_Week, :addr, :lng, :lat, :reg_Id, :reg_Date)";
		
		$stmt = $DB_con->prepare($query);
		$stmt->bindParam(":member_Idx", $mIdx);
		$stmt->bindParam(":con_Idx", $con_Idx);
		$stmt->bindParam(":category", $category);
		$stmt->bindParam(":place_Name", $place_Name);
		$stmt->bindParam(":place_Icon", $place_Icon);
		$stmt->bindParam(":memo", $memo);
		$stmt->bindParam(":smemo", $smemo);
		$stmt->bindParam(":tel", $tel);
		$stmt->bindParam(":img", $img);
		$stmt->bindParam(":otime_Day", $otime_Day);
		$stmt->bindParam(":otime_Week", $otime_Week);
		$stmt->bindParam(":addr", $taddr);
		$stmt->bindParam(":lng", $lng);
		$stmt->bindParam(":lat", $lat);
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
	}else{
		//핀 등록
		$query = "INSERT INTO TB_PLACE (member_Idx, category, place_Name, place_Icon, memo, smemo, tel, img, otime_Day, otime_Week, addr, lng, lat, reg_Id, reg_date) VALUES (:member_Idx, :category, :place_Name, :place_Icon, :memo, :smemo, :tel, :img, :otime_Day, :otime_Week, :addr, :lng, :lat, :reg_Id, :reg_Date)";
		
		$stmt = $DB_con->prepare($query);
		$stmt->bindParam(":member_Idx", $mIdx);
		$stmt->bindParam(":category", $category);
		$stmt->bindParam(":place_Name", $place_Name);
		$stmt->bindParam(":place_Icon", $place_Icon);
		$stmt->bindParam(":memo", $memo);
		$stmt->bindParam(":smemo", $smemo);
		$stmt->bindParam(":tel", $tel);
		$stmt->bindParam(":img", $img);
		$stmt->bindParam(":otime_Day", $otime_Day);
		$stmt->bindParam(":otime_Week", $otime_Week);
		$stmt->bindParam(":addr", $taddr);
		$stmt->bindParam(":lng", $lng);
		$stmt->bindParam(":lat", $lat);
		$stmt->bindParam(":reg_Id", $reg_Id);
		$stmt->bindParam(":reg_Date", $reg_Date);
		$stmt->execute();
		$pIdx = $DB_con->lastInsertId();  //저장된 idx 값
	}

	$history = "지점등록";
	$his_query ="INSERT INTO TB_HISTORY (member_Idx, mem_Id, place_Idx, history, reg_Id, reg_date) VALUES (:member_Idx, :mem_Id, :place_Idx, :history, :reg_Id, :reg_Date)";
	$his_stmt = $DB_con->prepare($his_query);
	$his_stmt->bindParam(":member_Idx", $mIdx);
	$his_stmt->bindParam(":mem_Id", $reg_Id);
	$his_stmt->bindParam(":history", $history);
	$his_stmt->bindParam(":place_Idx", $pIdx);
	$his_stmt->bindParam(":reg_Id", $reg_Id);
	$his_stmt->bindParam(":reg_Date", $reg_Date);
	$his_stmt->execute();


    dbClose($DB_con);
    $stmt = null;
	$chkStmt = null;
	$his_stmt = null;
	$result = array("result" => "success");
} else { //빈값일 경우
	$result = array("result" => "error", "errorMsg" => "지점등록실패");
}

	echo json_encode($result);
?>



