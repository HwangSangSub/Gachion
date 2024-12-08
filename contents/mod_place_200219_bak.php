<?
/*
* 프로그램				: 지점정보를 수정하는 기능
* 페이지 설명			: 지점위치, 지점대표아이콘, 지점명, 지점설명을 변경할 수 있다.
* 파일명					: mod_place.php
* 관련DB					: TB_PLACE
*/
include "../lib/common.php";
include "../lib/functionDB.php";  //공통 db함수

$place_Idx = trim($placeIdx);						//지점고유번호
$place_Name = trim($placeName);				//지점명
if($place_Name == ""){
	$place_Name = "";
}
$place_Icon = trim($placeIcon);					//지점대표아이콘
if($place_Icon == ""){
	$place_Icon = "";
}
$memo = trim($memo);								//상세설명
if($memo == ""){
	$memo = "";
}
$lng = trim($lng);										//경도
if($lng == ""){
	$lng = "";
}
$lat = trim($lat);										//위도
if($lat == ""){
	$lat = "";
}

$now_time = time();										// 추후 파일 디렉토리가 될 예정
//좌표 지정
/*
$lng = "126.921804310000000";

$lat = "37.554260160000000";
*/
function search_Addr($url, $param=array()){
	$url = $url.'?'.http_build_query($param, '', '&');
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($param));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, false);//헤더 정보를 보내도록 함(*필수)
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
	//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	$contents = curl_exec($ch); 
	$contents_json = json_decode($contents, true); // 결과값을 파싱
	curl_close($ch);
	return $contents_json;
}
$res = search_Addr('https://apis.openapi.sk.com/tmap/geo/reversegeocoding',array("version" => "1", "format" => "json", "callback" => "result", "coordType" => "WGS84GEO", "lon" => $lng,"lat" => $lat, "appKey" => "AIzaSyBAm_wDUkAZwtSDHp8OX3HM8h85tKnSR7c"));
$address = $res['addressInfo']['fullAddress'];
if($address != ""){
	//$address = urlencode($address);
	//echo "-----------------------------------------------------";
			//주소검색 방법
			//NtoO : 새주소 -> 구주소 변환 검색
			//OtoN : 구주소(법정동) -> 새주소 변환 검색
	$res2 = search_Addr2('https://apis.openapi.sk.com/tmap/geo/convertAddress',array("version" => "1", "format" => "json", "callback" => "result", "searchTypCd" => "OtoN", "reqAdd" => $address, "resCoordType" => "WGS84GEO", "reqMulti" => "M", "appKey" => "AIzaSyBAm_wDUkAZwtSDHp8OX3HM8h85tKnSR7c"));
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
$DB_con = db1();

// 지점고유번호를 확인한다.
$chk_query = "
		SELECT count(idx) as cnt
		FROM TB_PLACE
		WHERE idx = :place_Idx ;
	";
$chk_stmt = $DB_con->prepare($chk_query);
$chk_stmt->bindParam(":place_Idx", $place_Idx);
$chk_stmt->execute();
$chk_row = $chk_stmt->fetch(PDO::FETCH_ASSOC);
$chk_Cnt = $chk_row['cnt'];
if($chk_Cnt > 0){				//지점이 있는 경우
	$query = "
		SELECT con_Idx, member_Idx, place_Name, place_Icon, memo, addr, lng, lat
		FROM TB_PLACE
		WHERE idx = :place_Idx
	";
	$stmt = $DB_con->prepare($query);
	$stmt->bindParam(":place_Idx", $place_Idx);
	$stmt->execute();
	while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
		$con_Idx = $row['con_Idx'];
		$member_Idx = $row['member_Idx'];
		$org_place_Name = $row['place_Name'];
		if($place_Name == ""){
			$place_Name = $org_place_Name;
		}else{
			$place_Name = $place_Name;
		}
		$org_place_Icon = $row['place_Icon'];
		if($place_Icon == ""){
			$place_Icon = $org_place_Icon;
		}else{
			$place_Icon = $place_Icon;
		}
		$category_query = "
			SELECT code_Sub_Div
			FROM TB_CONFIG_CODE
			WHERE code = :code
				AND code_Div = 'placeicon'
		";
		$category_stmt = $DB_con->prepare($category_query);
		$category_stmt->bindParam(":code", $place_Icon);
		$category_stmt->execute();
		$category_row=$category_stmt->fetch(PDO::FETCH_ASSOC);
		$category = $category_row['code_Sub_Div'];

		$org_memo = $row['memo'];
		if($memo == ""){
			$memo = $org_memo;
		}else{
			$memo = $memo;
		}
		$org_lng = $row['lng'];
		if($lng == ""){
			$lng = $org_lng;
		}else{
			$lng = $lng;
		}
		$org_lat = $row['lat'];
		if($lat == ""){
			$lat = $org_lat;
		}else{
			$lat = $lat;
		}
		$org_addr = $row['addr'];
		if($lat == "" || $lng == ""){
			$addr = $org_addr;
		}else{
			$addr = $addr;
		}
		$mod_query = "
			UPDATE TB_PLACE
			SET category = :category, 
				place_Name = :place_Name,
				place_Icon = :place_Icon,
				memo = :memo,
				lng = :lng,
				lat = :lat,
				addr = :addr,
				mod_Date = NOW()
			WHERE member_Idx = :member_Idx
				AND idx = :place_Idx
			LIMIT 1;
		";
		$mod_stmt = $DB_con->prepare($mod_query);
		$mod_stmt->bindParam(":category", $category);
		$mod_stmt->bindParam(":place_Name", $place_Name);
		$mod_stmt->bindParam(":place_Icon", $place_Icon);
		$mod_stmt->bindParam(":memo", $memo);
		$mod_stmt->bindParam(":lng", $lng);
		$mod_stmt->bindParam(":lat", $lat);
		$mod_stmt->bindParam(":addr", $addr);
		$mod_stmt->bindParam(":member_Idx", $member_Idx);
		$mod_stmt->bindParam(":place_Idx", $place_Idx);
		$mod_stmt->execute();
		
		if($con_Idx != ""){
			$mod_query2 = "
				UPDATE TB_CONTENTS
				SET mod_Date = NOW()
				WHERE idx = :con_Idx
					AND member_Idx = :member_Idx
			";
			$mod_stmt2 = $DB_con->prepare($mod_query2);
			$mod_stmt2->bindParam(":member_Idx", $member_Idx);
			$mod_stmt2->bindParam(":con_Idx", $con_Idx);
			$mod_stmt2->execute();
		}
	}

    dbClose($DB_con);
	$chk_stmt = null;
    $stmt = null;
	$mod_stmt = null;
	$mod_stmt2 = null;
	$result = array("result" => "success");
} else { //빈값일 경우
	$result = array("result" => "error", "errorMsg" => "지점고유번호오류");
}

echo json_encode($result);