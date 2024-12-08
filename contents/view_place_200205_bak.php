<?
/*
* 프로그램				: 등록된 지점 목록을 보여줌
* 페이지 설명			: 등록된 지점 목록을 보여줌(지도에 표시하기 위함)
* 파일명					: view_place.php
* 관련DB					: TB_CONTENTS, TB_PLACE
*/
include "../lib/common.php";

$con_Idx = trim($con_Idx);						// 콘텐츠고유번호 
$area_Code = trim($area_Code);				// 지역코드 
$DB_con = db1();
if ($con_Idx != "") {
	if($area_Code != ""){
		// 장소정보
		$query = "
			SELECT idx, place_Name, lng, lat, category
			FROM TB_PLACE 
			WHERE con_Idx = :con_Idx
				AND area_Code = :area_Code;
			";
		$stmt = $DB_con->prepare($query);
		$stmt->bindParam(":con_Idx", $con_Idx);
		$stmt->bindParam(":area_Code", $area_Code);
		$stmt->execute();
		$num = $stmt->rowCount();
	}else{
		// 장소정보
		$query = "
			SELECT idx, place_Name, lng, lat, category
			FROM TB_PLACE 
			WHERE con_Idx = :con_Idx;
			";
		$stmt = $DB_con->prepare($query);
		$stmt->bindParam(":con_Idx", $con_Idx);
		$stmt->execute();
		$num = $stmt->rowCount();
	}
	// 좋아요가 많은 장소
	$like_query = "
		SELECT idx, place_Name, lng, lat, category, like_Cnt
		FROM TB_PLACE 
		ORDER BY like_Cnt DESC LIMIT 5;
		";
	$like_stmt = $DB_con->prepare($like_query);
	$like_stmt->execute();
	$like_data = [];
	while($like_row=$like_stmt->fetch(PDO::FETCH_ASSOC)) {
		$idx = $like_row['idx'];								// 핀고유번호
		$place_Name = $like_row['place_Name'];		// 장소면
		$lng = $like_row['lng'];								// 경도
		$lat = $like_row['lat'];								// 위도
		$category = $like_row['category'];					// 카테고리
		$like_Cnt = $like_row['like_Cnt'];					// 좋아요 수

		$lresult = ["place_Idx" => $idx, "category" => $category, "place_Name" => $place_Name, "lng" => $lng, "lat" => $lat, "like_Cnt" => $like_Cnt];
		 array_push($like_data, $lresult);
	}
	if($num == 1){
		$row=$stmt->fetch(PDO::FETCH_ASSOC);
		$idx = $row['idx'];								// 핀고유번호
		$place_Name = $row['place_Name'];		// 장소면
		$lng = $row['lng'];							// 경도
		$lat = $row['lat'];								// 위도
		$category = $row['category'];				// 카테고리
		$result = array("result" => "success", "pin_Cnt" => $num, "place_Idx" => $idx, "category" => $category, "place_Name" => $place_Name, "lng" => $lng, "lat" => $lat);
		echo json_encode($result, JSON_UNESCAPED_UNICODE); 
	}else if($num > 1){
		$data = [];
		while($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
			$idx = $row['idx'];								// 핀고유번호
			$place_Name = $row['place_Name'];		// 장소면
			$lng = $row['lng'];							// 경도
			$lat = $row['lat'];								// 위도
			$category = $row['category'];				// 카테고리

			$mresult = ["place_Idx" => $idx, "category" => $category, "place_Name" => $place_Name, "lng" => $lng, "lat" => $lat];
			 array_push($data, $mresult);
		}
		$chkData = [];
		$chkData["result"] = "success";
		$chkData["pin_Cnt"] = $num;
		$chkData["lists"] = $data;
		$chkData["like_lists"] = $like_data;
		$output = str_replace('\\\/', '/', json_encode($chkData, JSON_UNESCAPED_UNICODE));
		echo  urldecode($output);
	}else{
		$result = array("result" => "success", "pin_Cnt" => 0, "place_Idx" => "", "category" => "", "place_Name" => "", "lng" => "", "lat" => "");
		echo json_encode($result, JSON_UNESCAPED_UNICODE); 
	}
    dbClose($DB_con);
    $stmt = null;
	//$result = array("result" => "success", "Msg" => "핀리스트조회성공", "mIdx" => $mIdx);
} else { //빈값일 경우
	$result = array("result" => "error", "errorMsg" => "등록된핀이없음");
	echo json_encode($result, JSON_UNESCAPED_UNICODE); 
}
?>



