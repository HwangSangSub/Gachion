<?
/*
* 프로그램				: 등록된 지점 상세설명을 보여줌
* 페이지 설명			: 등록된 지점 상세설명을 보여줌
* 파일명					: detail_place.php
* 관련DB					: TB_PLACE
*/
include "../lib/common.php";
include "../lib/functionDB.php";  //공통 db함수

    
$DB_con = db1();
$place_Idx = trim($place_Idx);						// 지점고유번호
$mem_Id = trim($memId);							// 회원아이디
if(memChk($mem_Id) == "0"){
	$mem_Id = "GUEST";							//회원아이디가 없는 경우 비회원으로 처리
}else{
	$mIdx = memIdxInfo($mem_Id);			// 회원고유번호
}
$place_chk_query = "
	SELECT idx
	FROM TB_PLACE
	WHERE idx = :idx";
$place_chk_stmt = $DB_con->prepare($place_chk_query);
$place_chk_stmt->bindParam(":idx", $place_Idx);
$place_chk_stmt->execute();
$place_chk_num = $place_chk_stmt->rowCount();
if ($place_chk_num > 0) {
	$query = "
		SELECT con_Idx, category, place_Name, place_Icon, memo, smemo, tel, otime_Day, otime_Week, img, like_Cnt, share_Cnt, comment_Cnt, coupon_Cnt, reserv_Bit, coupon_Bit, addr, lng, lat, reg_Id, reg_date 
		FROM TB_PLACE 
		WHERE idx = :idx;
		";
	$stmt = $DB_con->prepare($query);
	$stmt->bindParam(":idx", $place_Idx);
	$stmt->execute();
	$img_File = [];
	while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
		$category = $row['category'];				// 지점카테고리
		// 카테고리명
		$category_Cquery = "
			SELECT code_Name
			FROM TB_CONFIG_CODE
			WHERE code = :code
				AND code_Div = 'category'
			ORDER BY reg_Date DESC;
			";
		$category_Cstmt = $DB_con->prepare($category_Cquery);
		$category_Cstmt->bindParam(":code", $category);
		$category_Cstmt->execute();
		while($category_Crow=$category_Cstmt->fetch(PDO::FETCH_ASSOC)) {
			$code_Name = $category_Crow['code_Name'];
			$category_query = "
				SELECT code_on_Img as code_Img
				FROM TB_CONFIG_CODE
				WHERE code_Name = :code_Name
					AND code_Div = 'categorylist'
				ORDER BY reg_Date DESC;
				";
			$category_stmt = $DB_con->prepare($category_query);
			$category_stmt->bindParam(":code_Name", $code_Name);
			$category_stmt->execute();
			while($category_row=$category_stmt->fetch(PDO::FETCH_ASSOC)){
				$code_Img = $category_row['code_Img'];
				if($code_Img == ''){
					$code_Img = '';
				}else{
					$code_ImgFile = "https://places.gachita.kr/udev/admin/data/code_img/photo.php?id=".$code_Img;
					$code_Img = $code_ImgFile;
				}
			}
		}
		$place_Name = $row['place_Name'];		// 지점명
		$place_Icon = $row['place_Icon'];			// 지점대표아이콘
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
		$memo = $row['memo'];					// 상세설명
		$smemo = $row['smemo'];					// 
		$tel = $row['tel'];								// 연락처
		$otime_Day = $row['otime_Day'];			// 영업시간(평일)
		if($otime_Day != ''){							// 영업시간(평일) 등록여부
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
			$img_Cnt = "0";
			$img = array('');
		}else{
			$m_file = $_SERVER["DOCUMENT_ROOT"].'/contents/place_img/'.$img;
			if(is_dir($m_file)){
				// 핸들 획득
				$handle  = opendir($m_file);
				$filename = readdir($handle);
				$files = array();
				// 디렉터리에 포함된 파일을 저장한다.
				while (false !== ($filename = readdir($handle))) {
					if($filename == "." || $filename == ".."){
						continue;
					}
					// 파일인 경우만 목록에 추가한다.
					$f_dir = $m_file . "/" . $filename;
					if(is_file($f_dir)){
						$files[] = $filename;
					}
				}
				// 핸들 해제 
				closedir($handle);
				// 정렬, 역순으로 정렬하려면 rsort 사용
				rsort($files);
				// 파일명을 출력한다.
				$idx = 0;
				foreach ($files as $f) {
					$img_FileName = "https://places.gachita.kr/contents/place_img/".$img."/".$f;
					array_push($img_File, $img_FileName);
					$idx++;
					if($idx > 1){
						break;
					}
				} 
				$img_Cnt = count($files);
				$img = $img_File;
			}else{
				$img_Cnt = "0";
				$img = array('');
			}
		}
		$like_Cnt = $row['like_Cnt'];						// 지점좋아요수
		$share_Cnt = $row['share_Cnt'];					// 지점공유수
		$coupon_Cnt = $row['coupon_Cnt'];				// 쿠폰보유수
		if($coupon_Cnt == "" || $coupon_Cnt == "0"){
			$coupon_Cnt = "0";
		}
		$coupon_Bit = $row['coupon_Bit'];				// 쿠폰사용여부
		$reserv_Bit = $row['reserv_Bit'];					// 예약가능여부
		if($mem_Id != 'GUEST'){
			$like_query = "
				SELECT *
				FROM TB_MEMBERS_LIKE
				WHERE mem_Id = :mem_Id
					AND place_Idx = :place_Idx
					AND member_Idx = :member_Idx
					AND use_Bit = 'Y'
				ORDER BY reg_Date DESC;
				";
			$like_stmt = $DB_con->prepare($like_query);
			$like_stmt->bindParam(":mem_Id", $mem_Id);
			$like_stmt->bindParam(":member_Idx", $mIdx);
			$like_stmt->bindParam(":place_Idx", $place_Idx);
			$like_stmt->execute();
			$like_num = $like_stmt->rowCount();
			if($like_num < 1){
				$like_Bit = 'N';
			}else{
				$like_Bit = 'Y';
			}
		}else{
			$like_Bit = 'N';
		}
		$addr = $row['addr'];									// 지점주소
		$lng = $row['lng'];									// 경도
		$lat = $row['lat'];										// 위도
		$reg_Id = $row['reg_Id'];								// 지점등록자
		$member_Img = memImgInfo($reg_Id);				// 지점등록회원이미지
		if($member_Img == ''){
			$member_Img = "";
		}
		$reg_Nname = memNickInfo($reg_Id);				// 회원닉네임
		if($reg_Nname == ''){
			$reg_Nname = "";
		}
		$reg_date = $row['reg_date'];						// 지점등록일
	}
	if($img_Cnt == "0"){
		$result = array("result" => "success", "category" => $category, "code_Name" => $code_Name, "code_Img" => $code_Img, "place_Name" => $place_Name, "place_Icon" => $place_Icon, "code_Color" => $code_Color, "memo" => $memo, "tel" => $tel, "day_Bit" => $day_Bit, "otime_Day" => $otime_Day, "week_Bit" => $week_Bit, "otime_Week" => $otime_Week, "img_Cnt" => (string)$img_Cnt, "like_Cnt" => (string)$like_Cnt, "like_Bit" => $like_Bit, "share_Cnt" => (string)$share_Cnt, "coupon_Cnt" => (string)$coupon_Cnt, "coupon_Bit" => $coupon_Bit, "reserv_Bit" => $reserv_Bit, "addr" => $addr, "lng" => $lng, "lat" => $lat, "reg_Id" => $reg_Id, "nick_Name" => $reg_Nname, "member_Img" => $member_Img, "reg_date" => $reg_date);
	}else{
		$result = array("result" => "success", "category" => $category, "code_Name" => $code_Name, "code_Img" => $code_Img, "place_Name" => $place_Name, "place_Icon" => $place_Icon, "code_Color" => $code_Color, "memo" => $memo, "tel" => $tel, "day_Bit" => $day_Bit, "otime_Day" => $otime_Day, "week_Bit" => $week_Bit, "otime_Week" => $otime_Week, "img_Cnt" => (string)$img_Cnt, "img" => $img, "like_Cnt" => (string)$like_Cnt, "like_Bit" => $like_Bit, "share_Cnt" => (string)$share_Cnt, "coupon_Cnt" => (string)$coupon_Cnt, "coupon_Bit" => $coupon_Bit, "reserv_Bit" => $reserv_Bit, "addr" => $addr, "lng" => $lng, "lat" => $lat, "reg_Id" => $reg_Id, "nick_Name" => $reg_Nname, "member_Img" => $member_Img, "reg_date" => $reg_date);
	}
    dbClose($DB_con);
    $stmt = null;
} else { //빈값일 경우
	$result = array("result" => "error", "errorMsg" => "지점고유번호 오류");
}
	echo json_encode($result, JSON_UNESCAPED_UNICODE); 
?>



