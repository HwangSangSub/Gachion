<?
/*
* 프로그램				: 등록된 핀 상세설명을 보여줌
* 페이지 설명			: 등록된 핀 상세설명을 보여줌
* 파일명					: detail_place.php
* 관련DB					: TB_PLACE
*/
include "../lib/common.php";

$place_Idx = trim($place_Idx);						//핀고유번호
if ($place_Idx != "") {
    
    $DB_con = db1();
	$query = "
		SELECT con_Idx, place_Name, memo, smemo, tel, otime_Day, otime_Week, img, like_Cnt, share_Cnt, addr, lng, lat, reg_Id, reg_date
		FROM TB_PLACE 
		WHERE idx = :idx;
		";
	
	$stmt = $DB_con->prepare($query);
	$stmt->bindParam(":idx", $place_Idx);
	$stmt->execute();

	while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
		$place_Name = $row['place_Name'];		// 장소명
		$memo = $row['memo'];					// 상세설명
		$smemo = $row['smemo'];					// 
		$tel = $row['tel'];								// 연락처
		$otime_Day = $row['otime_Day'];			// 영업시간(평일)
		if($otime_Day != ''){							//영업시간(평일) 등록여부
			$day_Bit = '1';
		}else{
			$day_Bit = '0';
		}
		$otime_Week = $row['otime_Week'];		// 영업시간(주말)
		if($otime_Week != ''){						// 영업시간(주말) 등록여부
			$week_Bit = '1';
		}else{
			$week_Bit = '0';
		}
		$img = $row['img'];							// 이미지
		if($img == ''){
			$img = '';
		}
		$like_Cnt = $row['like_Cnt'];				// 핀좋아요수
		$share_Cnt = $row['share_Cnt'];			// 핀공유수
		$addr = $row['addr'];							// 핀주소
		$lng = $row['lng'];							// 경도
		$lat = $row['lat'];								// 위도
		$reg_Id = $row['reg_Id'];						// 핀등록지
		$reg_date = $row['reg_date'];				// 핀등록일
	}
	$result = array("result" => "success", "place_Name" => $place_Name, "memo" => $memo, "tel" => $tel, "day_Bit" => $day_Bit, "otime_Day" => $otime_Day, "week_Bit" => $week_Bit, "otime_Week" => $otime_Week, "img" => $img, "like_Cnt" => $like_Cnt, "share_Cnt" => $share_Cnt, "addr" => $addr, "lng" => $lng, "lat" => $lat, "reg_Id" => $reg_Id, "reg_date" => $reg_date);
    dbClose($DB_con);
    $stmt = null;
} else { //빈값일 경우
	$result = array("result" => "error", "errorMsg" => "핀정보오류");
}
	echo json_encode($result, JSON_UNESCAPED_UNICODE); 
?>



