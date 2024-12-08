<?
/*
* 프로그램				: 등록된 지점 목록을 보여줌
* 페이지 설명			: 등록된 지점 목록을 보여줌(지도에 표시하기 위함)
* 파일명					: detail_place.php
* 관련DB					: TB_CONTENTS, TB_PLACE
*/
include "../lib/common.php";

$con_Idx = trim($con_Idx);						// 지도고유번호 
$DB_con = db1();
if ($con_Idx != "") {
	$chk_query = "
		SELECT idx, area_Code, locat_Cnt
		FROM TB_CONTENTS 
		WHERE idx = :idx
		";
	$chk_stmt = $DB_con->prepare($chk_query);
	$chk_stmt->bindParam(":idx", $con_Idx);
	$chk_stmt->execute();
	$chk_row=$chk_stmt->fetch(PDO::FETCH_ASSOC);
	$area_Cnt = $chk_row['locat_Cnt'];
	if($area_Cnt == ''){
		$area_Cnt = 0;
	}
	$area_Code = $chk_row['area_Code'];
	if($area_Code != ""){
		$carea_Code = "'".str_replace(",","' ,'",$chk_row['area_Code'])."'";
		// 장소정보
		$query = "
			SELECT idx, area_Code, place_Name, lng, lat, category
			FROM TB_PLACE 
			WHERE con_Idx = :con_Idx
				AND area_Code IN (".$carea_Code.");
			";
		$stmt = $DB_con->prepare($query);
		$stmt->bindParam(":con_Idx", $con_Idx);
		$stmt->execute();
		$num = $stmt->rowCount();
	}else{
		// 장소정보
		$query = "
			SELECT idx, area_Code, place_Name, lng, lat, category
			FROM TB_PLACE 
			WHERE con_Idx = :con_Idx;
			";
		$stmt = $DB_con->prepare($query);
		$stmt->bindParam(":con_Idx", $con_Idx);
		$stmt->execute();
		$num = $stmt->rowCount();
	}
	if($num == 1){
		$row=$stmt->fetch(PDO::FETCH_ASSOC);
		$idx = $row['idx'];								// 지점고유번호
		$place_Name = $row['place_Name'];		// 장소면
		$lng = $row['lng'];							// 경도
		$lat = $row['lat'];								// 위도
		$category = $row['category'];				// 카테고리
		$result = array("result" => "success", "pin_Cnt" => $num, "place_Idx" => $idx, "category" => $category, "place_Name" => $place_Name, "lng" => $lng, "lat" => $lat);
		echo json_encode($result, JSON_UNESCAPED_UNICODE); 
	}else if($num > 1){
		$data = [];
		if($area_Cnt > 0){
			$area = explode(",",$area_Code);
			$a_Cnt = count($area);
			for($i = 0; $i < $a_Cnt; $i++){
				$area_Name = $area[$i];
				$data[$area_Name]= [];
			}
			while($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
				$idx = $row['idx'];								// 지점고유번호
				$chk_Areacode = $row['area_Code'];		// 지역코드
				$place_Name = $row['place_Name'];		// 장소면
				$lng = $row['lng'];							// 경도
				$lat = $row['lat'];								// 위도
				$category = $row['category'];				// 카테고리
				$c_Areacode = '';
				if($chk_Areacode == $c_Areacode){		// 같다면
					$mresult = ["place_Idx" => $idx, "category" => $category, "place_Name" => $place_Name, "lng" => $lng, "lat" => $lat];
					array_push($data[$chk_Areacode], $mresult);
				}else{												// 다르다면
					$mresult = ["place_Idx" => $idx, "category" => $category, "place_Name" => $place_Name, "lng" => $lng, "lat" => $lat];
					array_push($data[$chk_Areacode], $mresult);
					$chk_Areacode = $c_Areacode;
				}
			}
		}else{
			$area = "";											// 지역수가 없을 경우 빈값으로 출력
			while($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
				$idx = $row['idx'];								// 지점고유번호
				$place_Name = $row['place_Name'];		// 장소면
				$lng = $row['lng'];							// 경도
				$lat = $row['lat'];								// 위도
				$category = $row['category'];				// 카테고리
				$mresult = ["place_Idx" => $idx, "category" => $category, "place_Name" => $place_Name, "lng" => $lng, "lat" => $lat];
				array_push($data, $mresult);
			}
		}
		$chkData = [];
		$chkData["result"] = "success";
		$chkData["pin_Cnt"] = $num;
		$chkData["area_Cnt"] = $area_Cnt;
		$chkData["name"] = $area;
		$chkData["lists"] = $data;
		$output = str_replace('\\\/', '/', json_encode($chkData, JSON_UNESCAPED_UNICODE));
		echo  urldecode($output);
	}else{
		$result = array("result" => "success", "errorMsg" => "등록된 지점이 없습니다.");
		echo json_encode($result, JSON_UNESCAPED_UNICODE); 
	}
    dbClose($DB_con);
    $stmt = null;
	//$result = array("result" => "success", "Msg" => "리스트조회성공", "mIdx" => $mIdx);
} else { //빈값일 경우
	$result = array("result" => "error", "errorMsg" => "등록된지점이없음");
	echo json_encode($result, JSON_UNESCAPED_UNICODE); 
}
?>



