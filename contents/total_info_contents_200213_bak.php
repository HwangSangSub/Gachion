<?
/*
* 프로그램				: 지도에 등록된 지점의 정보를 보여줌
* 페이지 설명			: 지도에 등록된 지점의 정보를 보여줌
* 파일명					: total_info_contents.php
* 관련DB					: TB_CONTENTS, TB_PLACE
*/
include "../lib/common.php";

$con_Idx = trim($conIdx);						// 지도고유번호 
$DB_con = db1();
if ($con_Idx != "") {
	$chk_query = "
		SELECT idx, con_Lv, locat_Cnt, kml_File
		FROM TB_CONTENTS 
		WHERE idx = :idx
		";
	$chk_stmt = $DB_con->prepare($chk_query);
	$chk_stmt->bindParam(":idx", $con_Idx);
	$chk_stmt->execute();
	$chk_row=$chk_stmt->fetch(PDO::FETCH_ASSOC);
	$con_Lv = $chk_row['con_Lv'];
	$kml_File = $chk_row['kml_File'];
	$area_query = "
        SELECT area_Code
        FROM TB_PLACE
        WHERE con_Idx = :con_Idx
		ORDER BY area_Code DESC, mod_Date DESC;
	";
	$area_stmt = $DB_con->prepare($area_query);
	$area_stmt->bindParam(":con_Idx", $con_Idx);
	$area_stmt->execute();
	$areacode = [];
	$area_Idx = 0;
	while($area_row=$area_stmt->fetch(PDO::FETCH_ASSOC)){
		$area = $area_row['area_Code'];
		if($area_Idx == 0){
			array_push($areacode, $area);
			$chk_Area = $area;
			$area_Idx = $area_Idx + 1;
		}
		if($area == $chk_Area){
		}else{
			array_push($areacode, $area);
			$chk_Area = $area;
		}
	}
	$areacode = array_values(array_filter(array_map('trim',$areacode)));
	$area_Cnt = count($areacode);
	if($area_Cnt != "0" && $kml_File != ""){
		$careacode = implode(',', $areacode);
		$carea_Code = "'".str_replace(",","' ,'",$careacode)."'";
		// 장소정보
		$query = "
			SELECT idx, area_Code, place_Name, place_Icon, lng, lat, category, like_Cnt, coupon_Cnt, reserv_Bit, coupon_Bit
			FROM TB_PLACE 
			WHERE con_Idx = :con_Idx
				AND area_Code IN ('',".$carea_Code.")
			ORDER BY area_Code DESC, mod_Date DESC;
			";
		$stmt = $DB_con->prepare($query);
		$stmt->bindParam(":con_Idx", $con_Idx);
		$stmt->execute();
		$num = $stmt->rowCount();
	}else{
		// 장소정보
		$query = "
			SELECT idx, area_Code, place_Name, place_Icon, lng, lat, category, like_Cnt, coupon_Cnt, reserv_Bit, coupon_Bit
			FROM TB_PLACE 
			WHERE con_Idx = :con_Idx
				OR idx in (SELECT place_Idx FROM TB_MEMBERS_SHARE WHERE con_Idx = :con_Idx AND use_Bit = 'Y')
			ORDER BY mod_Date DESC;
			";
		$stmt = $DB_con->prepare($query);
		$stmt->bindParam(":con_Idx", $con_Idx);
		$stmt->execute();
		$num = $stmt->rowCount();
	}
	if($num == 1){
		$lists = [];
		$row=$stmt->fetch(PDO::FETCH_ASSOC);
		$idx = $row['idx'];								// 지점고유번호
		$place_Name = $row['place_Name'];		// 지점명
		$place_Icon = $row['place_Icon'];			// 지점아이콘
		$like_Cnt = $row['like_Cnt'];				// 좋아요 수
		$coupon_Idx = $row['coupon_Idx'];		// 쿠폰보유시 쿠폰고유번호
		$coupon_Bit = $row['coupon_Bit'];		// 쿠폰사용여부
		$reserv_Bit = $row['reserv_Bit'];			// 예약가능여부
		if($place_Icon == ""){
			$place_Icon = "0";
		}
		$color_query = "
			SELECT code_on_Img, code_Color
			FROM TB_CONFIG_CODE
			WHERE code_Div = 'placeicon'
				AND code = :code;
			";
		$color_stmt = $DB_con->prepare($color_query);
		$color_stmt->bindParam(":code", $place_Icon);
		$color_stmt->execute();
		$color_row=$color_stmt->fetch(PDO::FETCH_ASSOC);
		$code_Color = $color_row['code_Color'];
		$lng = $row['lng'];							// 경도
		$lat = $row['lat'];								// 위도
		$category = $row['category'];				// 카테고리
		$listresult = ["place_Idx" => $idx, "place_Name" => $place_Name, "place_Icon" => $place_Icon, "icon_Color" => $code_Color, "category" => $category, "lng" => $lng, "lat" => $lat, "like_Cnt" => (string)$like_Cnt, "coupon_Bit" => $coupon_Bit, "reserv_Bit" => $reserv_Bit];
		array_push($lists, $listresult);
		$result = array("result" => "success", "pin_Cnt" => $num, "lists" => $lists);
		echo json_encode($result, JSON_UNESCAPED_UNICODE); 
	}else if($num > 1){
		$data = [];
		if($area_Cnt > 0){
			$a_Cnt = count($areacode);
			for($i = 0; $i < $a_Cnt; $i++){
				$area_Name = $areacode[$i];
				$data[$i]= [];
				// 장소정보
				$chkquery = "
					SELECT idx, area_Code, place_Name, place_Icon, lng, lat, category, like_Cnt, coupon_Cnt, reserv_Bit, coupon_Bit
					FROM TB_PLACE 
					WHERE con_Idx = :con_Idx
						AND area_Code = :area_Code
					ORDER BY area_Code DESC, mod_Date DESC;
					";
				$chkstmt = $DB_con->prepare($chkquery);
				$chkstmt->bindParam(":con_Idx", $con_Idx);
				$chkstmt->bindParam(":area_Code", $area_Name);
				$chkstmt->execute();
				$chknum = $chkstmt->rowCount();
				if($chknum == 0){
					$mresult = ["place_Idx" => "", "place_Name" => "", "place_Icon" => "", "icon_Color" => "", "category" => "", "lng" => "", "lat" => "", "like_Cnt" => "", "coupon_Bit" => "", "reserv_Bit" => ""];
					array_push($data[$i], $mresult);
				}
			}
			$chkidx = 0;
			$chk_idx = 0;
			$c_Areacode = '';
			while($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
				$idx = $row['idx'];								// 지점고유번호
				$chk_Areacode = $row['area_Code'];		// 지역코드
				$place_Name = $row['place_Name'];		// 장소면
				$place_Icon = $row['place_Icon'];			// 지점아이콘
				if($place_Icon == ""){
					$place_Icon = "0";
				}
				$color_query = "
					SELECT code_on_Img, code_Color
					FROM TB_CONFIG_CODE
					WHERE code_Div = 'placeicon'
						AND code = :code;
					";
				$color_stmt = $DB_con->prepare($color_query);
				$color_stmt->bindParam(":code", $place_Icon);
				$color_stmt->execute();
				$color_row=$color_stmt->fetch(PDO::FETCH_ASSOC);
				$code_Color = $color_row['code_Color'];
				$like_Cnt = $row['like_Cnt'];				// 좋아요 수
				$coupon_Idx = $row['coupon_Idx'];		// 쿠폰보유시 쿠폰고유번호
				$coupon_Bit = $row['coupon_Bit'];		// 쿠폰사용여부
				$reserv_Bit = $row['reserv_Bit'];			// 예약가능여부
				$lng = $row['lng'];							// 경도
				$lat = $row['lat'];								// 위도
				$category = $row['category'];				// 카테고리
				if($chk_idx == 0){
					$c_Areacode = $chk_Areacode;
					$chk_idx = $chk_idx + 1;
				}
				if($chk_Areacode == $c_Areacode){		// 같다면
					$mresult = ["place_Idx" => $idx, "place_Name" => $place_Name, "place_Icon" => $place_Icon, "icon_Color" => $code_Color, "category" => $category, "lng" => $lng, "lat" => $lat, "like_Cnt" => (string)$like_Cnt, "coupon_Bit" => $coupon_Bit, "reserv_Bit" => $reserv_Bit];
					array_push($data[$chkidx], $mresult);
				}else{												// 다르다면
					$chkidx = $chkidx + 1;
					$mresult = ["place_Idx" => $idx, "place_Name" => $place_Name, "place_Icon" => $place_Icon, "icon_Color" => $code_Color, "category" => $category, "lng" => $lng, "lat" => $lat, "like_Cnt" => (string)$like_Cnt, "coupon_Bit" => $coupon_Bit, "reserv_Bit" => $reserv_Bit];
					array_push($data[$chkidx], $mresult);
					$c_Areacode = $chk_Areacode;
				}
			}
		}else{
			while($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
				$idx = $row['idx'];								// 지점고유번호
				$place_Name = $row['place_Name'];		// 지점명
				$place_Icon = $row['place_Icon'];	// 지점아이콘
				if($place_Icon == ""){
					$place_Icon = "0";
				}
				$color_query = "
					SELECT code_on_Img, code_Color
					FROM TB_CONFIG_CODE
					WHERE code_Div = 'placeicon'
						AND code = :code;
					";
				$color_stmt = $DB_con->prepare($color_query);
				$color_stmt->bindParam(":code", $place_Icon);
				$color_stmt->execute();
				$color_row=$color_stmt->fetch(PDO::FETCH_ASSOC);
				$code_Color = $color_row['code_Color'];
				$like_Cnt = $row['like_Cnt'];				// 좋아요 수
				$coupon_Idx = $row['coupon_Idx'];		// 쿠폰보유시 쿠폰고유번호
				$coupon_Bit = $row['coupon_Bit'];		// 쿠폰사용여부
				$reserv_Bit = $row['reserv_Bit'];			// 예약가능여부
				$lng = $row['lng'];							// 경도
				$lat = $row['lat'];								// 위도
				$category = $row['category'];				// 카테고리
				$mresult = ["place_Idx" => $idx, "place_Name" => $place_Name, "place_Icon" => $place_Icon, "icon_Color" => $code_Color, "category" => $category, "lng" => $lng, "lat" => $lat, "like_Cnt" => (string)$like_Cnt, "coupon_Bit" => $coupon_Bit, "reserv_Bit" => $reserv_Bit];
				array_push($data, $mresult);
			}
		}
		if($kml_File != ""){
			$xml = file_get_contents("../contents/kmlfile/".$kml_File);
			$result_xml = simplexml_load_string($xml);
			$a_Cnt = count($areacode);
			$like_Bit = [];
			for($nm = 0; $nm < $a_Cnt; $nm++){
				$locat = $result_xml->Document->Placemark[$nm]->Polygon->outerBoundaryIs->LinearRing->coordinates; 
				$areaName = $areacode[$nm];
				$locat_poi = explode( ',', $locat);
				$poi_cnt = count($locat_poi);
				$lat = [];  //위도
				$lng = []; //경도
				for($i = 0; $i < $poi_cnt; $i++){
					if($i % 2 != 0){
						//위도
						array_push($lat, (double)$locat_poi[$i]);
					}else{
						//경도
						array_push($lng, (double)str_replace(" ","",str_replace("0 ", "", $locat_poi[$i])));
					}
				}
				$lng_chk = array_pop($lng); 
				$lat_min = min($lat);
				$lat_max = max($lat);
				$lng_min = min($lng);
				$lng_max = max($lng);
				// 좋아요가 많은 장소
				$like_query = "
					SELECT idx, place_Name, place_Icon, lng, lat, category, like_Cnt, like_Cnt, coupon_Cnt, reserv_Bit, coupon_Bit
					FROM TB_PLACE 
					WHERE (".$lng_min." < lng AND lng < ".$lng_max.")
						AND (".$lat_min." < lat AND lat < ".$lng_max.")
						AND like_Cnt > 9
					ORDER BY like_Cnt DESC, mod_Date DESC, reg_Date DESC;
					";
				$like_stmt = $DB_con->prepare($like_query);
				$like_stmt->execute();
				$like_num = $like_stmt->rowCount();
				$like_data[$nm] = [];
				if($like_num < 1){
					 array_push($like_Bit, "0");
				}else{
					 array_push($like_Bit, "1");
					while($like_row=$like_stmt->fetch(PDO::FETCH_ASSOC)) {
						$idx = $like_row['idx'];								// 지점고유번호
						$place_Name = $like_row['place_Name'];		// 지점명
						$place_Icon = $like_row['place_Icon'];			// 지점아이콘
						if($place_Icon == ""){
							$place_Icon = "0";
						}
						$color_query = "
							SELECT code_on_Img, code_Color
							FROM TB_CONFIG_CODE
							WHERE code_Div = 'placeicon'
								AND code = :code;
							";
						$color_stmt = $DB_con->prepare($color_query);
						$color_stmt->bindParam(":code", $place_Icon);
						$color_stmt->execute();
						$color_row=$color_stmt->fetch(PDO::FETCH_ASSOC);
						$code_Color = $color_row['code_Color'];
						$lng = $like_row['lng'];								// 경도
						$lat = $like_row['lat'];								// 위도
						$category = $like_row['category'];					// 카테고리
						$like_Cnt = $like_row['like_Cnt'];					// 좋아요 수
						$coupon_Idx = $like_row['coupon_Idx'];			// 쿠폰보유시 쿠폰고유번호
						$coupon_Bit  = $like_row['coupon_Bit'];			// 쿠폰사용여부
						$reserv_Bit = $like_row['reserv_Bit'];				// 예약가능여부

						$lresult = ["place_Idx" => $idx, "place_Name" => $place_Name, "place_Icon" => $place_Icon, "icon_Color" => $code_Color, "category" => $category, "lng" => $lng, "lat" => $lat, "like_Cnt" => (string)$like_Cnt, "coupon_Bit" => $coupon_Bit, "reserv_Bit" => $reserv_Bit];
						 array_push($like_data[$nm], $lresult);
					}
				}

			}
		}
		if($con_Lv != "1"){
			$chk_query = "
				SELECT *
				FROM TB_CONGESTION A
					INNER JOIN TB_PLACE B
						ON A.place_Idx = B.idx
				WHERE B.con_Idx = :con_Idx;
			";
			$chk_stmt = $DB_con->prepare($chk_query);
			$chk_stmt->bindParam(":con_Idx", $con_Idx);
			$chk_stmt->execute();
			$chk_num = $chk_stmt->rowCount();
			$poi_cnt = count($areacode);
			if($chk_num < 1){
				$mdata = [];
				for($i = 0; $i < $poi_cnt; $i++){
					$area_Code = $areacode[$i];
					$mdata[$area_Code]= [];
					$mresult = ["people_Cnt" => "0", "male_Rate" => "0", "female_Rate" => "0","cong_Cnt" => "0"];
					 array_push($mdata[$area_Code], $mresult);
				}
			}else{
				$poi_cnt = count($areacode);
				$mdata = [];
				for($i = 0; $i < $poi_cnt; $i++){
					$area_Code = $areacode[$i];
					$mdata[$i]= [];
					$chk_query2 = "
							SELECT *
							FROM TB_CONGESTION
							WHERE area_Code = :area_Code;
					";
					$chk_stmt2 = $DB_con->prepare($chk_query2);
					$chk_stmt2->bindParam(":area_Code", $area_Code);
					$chk_stmt2->execute();
					$chk_num2 = $chk_stmt2->rowCount();
					//"area_Name" => $area_Code, 
					if($chk_num2 < 1){
						$mresult = ["people_Cnt" => "0", "male_Rate" => "0", "female_Rate" => "0","cong_Cnt" => "0"];
						 array_push($mdata[$i], $mresult);
					}else{
						$query = "
							SELECT count(idx) as tCnt, SUM(tot_Cnt) as pCnt, SUM(cong_Rate) as cCnt, SUM(male_Cnt) as mCnt, SUM(female_Cnt) as fCnt
							FROM TB_CONGESTION
							WHERE area_Code = :area_Code;
						";
						$stmt = $DB_con->prepare($query);
						$stmt->bindParam(":area_Code", $area_Code);
						$stmt->execute();
						while($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
							$tCnt = $row['tCnt'];								// 총행수
							$pCnt = $row['pCnt'];							// 총인구수
							$mCnt = $row['mCnt'];							// 남자인구수
							$fCnt = $row['fCnt'];								// 여자인구수
							$cCnt = $row['cCnt'];								// 총혼잡정도점수
							$mRate = (($mCnt / $pCnt) * 100);
							$fRate =  (($fCnt / $pCnt) * 100);
							$cong_Cnt = ((int)$cCnt / (int)$tCnt);		// 평균혼잡정도"area_Name" => $area_Code, 
							$mresult = ["people_Cnt" => (string)$pCnt, "male_Rate" => (string)(round($mRate)), "female_Rate" => (string)(round($fRate)),"cong_Cnt" => (string)$cong_Cnt];
							 array_push($mdata[$i], $mresult);
						}
					}
				}
			}
		}	
		$chkData = [];
		$chkData["result"] = "success";
		$chkData["pin_Cnt"] = $num;
		if($con_Lv != "1"){
			$chkData["area_Cnt"] = $area_Cnt;
			$chkData["name"] = $areacode;
		}
		$chkData["lists"] = $data;
		//print_r($chkData["lists"][0]);
		if($con_Lv != "1"){
			if(in_array("1", $like_Bit)){
				$chkData["like_lists"] = $like_data;
			}else{
			}
			$chkData["info_lists"] = $mdata;
		}
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



