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
$org_cod_Idx = trim($con_Idx);							// 지도고유번호 ( 선택한 지도 고유번호 )
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
		SELECT con_Idx, category, place_Name, place_Icon, memo, smemo, tel, otime_Day, otime_Week, img, like_Cnt, share_Cnt, comment_Cnt, coupon_Cnt, reserv_Bit, coupon_Bit, addr, lng, lat, open_Bit, delete_Bit, reg_Id, reg_date, mod_Date
		FROM TB_PLACE 
		WHERE idx = :idx;
		";
	$stmt = $DB_con->prepare($query);
	$stmt->bindParam(":idx", $place_Idx);
	$stmt->execute();
	$img_File = [];
	while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
		$con_Idx = $row['con_Idx'];					// 지도고유번호
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
		// 담아온 지점 여부 확인
		$share_query = "
			SELECT *
			FROM TB_MEMBERS_SHARE 
			WHERE member_Idx = :member_Idx
				AND place_Idx = :place_Idx
				AND con_Idx = :con_Idx
				AND use_Bit = 'Y'
			LIMIT 1;
		";
		$share_stmt = $DB_con->prepare($share_query);
		$share_stmt->bindParam(":member_Idx", $mIdx);
		$share_stmt->bindParam(":place_Idx", $place_Idx);
		$share_stmt->bindParam(":con_Idx", $org_cod_Idx);
		$share_stmt->execute();
		$share_num = $share_stmt->rowCount();
		if($share_num > 0){
			$share_Bit = 'Y';										// 담아온 지점임
		}else{
			$share_Bit = 'N';										// 담아온 지점이 아님
		}
		$delete_Bit = $row['delete_Bit'];						// 삭제여부
		$mod_Date = substr($row['mod_Date'], 2, 2).".".substr($row['mod_Date'], 5, 2).".".substr($row['mod_Date'], 8, 2);				// 최근 수정 시간
		$open_Bit = $row['open_Bit'];							// 공개여부 
		$addr = $row['addr'];										// 지점주소
		$lng = $row['lng'];										// 경도
		$lat = $row['lat'];											// 위도
		$reg_Id = $row['reg_Id'];									// 지점등록자
		$member_Img = memImgInfo($reg_Id);				// 지점등록회원이미지
		if($member_Img == ''){
			$member_Img = "";
		}
		$reg_Nname = memNickInfo($reg_Id);				// 회원닉네임
		if($reg_Nname == ''){
			$reg_Nname = "";
		}
		$reg_date = $row['reg_date'];							// 지점등록일
		$con_Lv_query = "
			SELECT con_Lv
			FROM TB_CONTENTS
			WHERE idx = :con_Idx
				AND delete_Bit = '0'
			;
			";
		$con_Lv_stmt = $DB_con->prepare($con_Lv_query);
		$con_Lv_stmt->bindParam(":con_Idx", $con_Idx);
		$con_Lv_stmt->execute();
		$con_Lv_row=$con_Lv_stmt->fetch(PDO::FETCH_ASSOC);
		$con_Lv = $con_Lv_row['con_Lv'];
		// 확인용
		$tCnt == "";
		// 테스트용이므로 현재시간을 10월 24일 23시로 고정
		$time_Chk = "0";
		//$nowDate = date("Y-m-d H:i:s", time());
		$now_Date = strtotime("2019-10-24 23:59:59");
		$nowDate = date("Y-m-d H:i:s", $now_Date);
		$time1 = (int)$time_Chk;
		$time2 = (int)$time_Chk + 1;
		$timestamp = strtotime($nowDate."-".$time1."hours");
		$timestamp2 = strtotime($nowDate."-".$time2."hours");
		$to_date = date("Y-m-d H:i:s", $timestamp);
		$fr_date = date("Y-m-d H:i:s", $timestamp2);
		$sql_search ="  AND reg_Date = (SELECT MAX(reg_Date) FROM TB_CONGESTION WHERE (DATE_FORMAT(reg_Date,'%Y-%m-%d %H: %i: %s') >= '".$fr_date."' AND DATE_FORMAT(reg_Date,'%Y-%m-%d %H: %i: %s') <= '".$to_date."'))";
		$query = "
			SELECT count(idx) as tCnt, SUM(tot_Cnt) as pCnt, SUM(cong_Rate) as cCnt, SUM(male_Cnt) as mCnt, SUM(female_Cnt) as fCnt
			FROM TB_CONGESTION
			WHERE ( 6371 * acos( cos( radians(:lat) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(:lng) ) + sin( radians(:lat) ) * sin( radians( lat ) ) ) ) < 0.1
				{$sql_search};
		";
		$stmt = $DB_con->prepare($query);
		$stmt->bindParam(":lng", $lng);
		$stmt->bindParam(":lat", $lat);
		$stmt->execute();
		while($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
			$tCnt = $row['tCnt'];								// 총행수
			$pCnt = $row['pCnt'];							// 총인구수
			$mCnt = $row['mCnt'];							// 남자인구수
			$fCnt = $row['fCnt'];								// 여자인구수
			$cCnt = $row['cCnt'];								// 총혼잡정도점수
			if($pCnt != ""){
				$mRate = (($mCnt / $pCnt) * 100);
				$fRate =  (($fCnt / $pCnt) * 100);
			}else{
				$mRate = 0;
				$fRate =  0;
			}
			if($tCnt != "" && $cCnt != ""){
				$congCnt = ((int)$cCnt / (int)$tCnt);			// 평균혼잡정도"area_Name" => $area_Code, 
			}else{
				$congCnt = 1;			// 평균혼잡정도"area_Name" => $area_Code, 
			}
			$cong_Cnt = round($congCnt);
			if($cong_Cnt > 5){
				$cong_Cnt = "5";
			}else if($cong_Cnt < 0){
				$cong_Cnt = "1";
			}else{
				$cong_Cnt = $cong_Cnt;
			}
			/*
				$mresult = ["people_Cnt" => (string)$pCnt, "male_Rate" => (string)(round($mRate)), "female_Rate" => (string)(round($fRate)),"cong_Cnt" => (string)$cong_Cnt];
				 array_push($mdata[$i], $mresult);
			 */
		}
		if(is_nan($mRate)){
			$mRate = "0";
		}
		if(is_nan($fRate)){
			$fRate = "0";
		}
		if(is_nan($cong_Cnt)){
			$cong_Cnt = "";
		}
	}
	if($img_Cnt == "0"){
		$result = array("result" => "success", "category" => $category, "code_Name" => $code_Name, "code_Img" => $code_Img, "place_Name" => $place_Name, "place_Icon" => $place_Icon, "code_Color" => $code_Color, "memo" => $memo, "tel" => $tel, "day_Bit" => $day_Bit, "otime_Day" => $otime_Day, "week_Bit" => $week_Bit, "otime_Week" => $otime_Week, "img_Cnt" => (string)$img_Cnt, "like_Cnt" => (string)$like_Cnt, "like_Bit" => $like_Bit, "share_Cnt" => (string)$share_Cnt, "coupon_Cnt" => (string)$coupon_Cnt, "coupon_Bit" => $coupon_Bit, "reserv_Bit" => $reserv_Bit, "addr" => $addr, "lng" => $lng, "lat" => $lat, "open_Bit" => $open_Bit, "male_Rate" => (string)(round($mRate)), "female_Rate" => (string)(round($fRate)),"cong_Cnt" => (string)$cong_Cnt, "reg_Id" => $reg_Id, "nick_Name" => $reg_Nname, "reg_share_Bit" => $share_Bit, "member_Img" => $member_Img, "reg_date" => $reg_date, "mod_Date" => $mod_Date, "delete_Bit" => $delete_Bit);
	}else{
		$result = array("result" => "success", "category" => $category, "code_Name" => $code_Name, "code_Img" => $code_Img, "place_Name" => $place_Name, "place_Icon" => $place_Icon, "code_Color" => $code_Color, "memo" => $memo, "tel" => $tel, "day_Bit" => $day_Bit, "otime_Day" => $otime_Day, "week_Bit" => $week_Bit, "otime_Week" => $otime_Week, "img_Cnt" => (string)$img_Cnt, "img" => $img, "like_Cnt" => (string)$like_Cnt, "like_Bit" => $like_Bit, "share_Cnt" => (string)$share_Cnt, "coupon_Cnt" => (string)$coupon_Cnt, "coupon_Bit" => $coupon_Bit, "reserv_Bit" => $reserv_Bit, "addr" => $addr, "lng" => $lng, "lat" => $lat, "open_Bit" => $open_Bit, "male_Rate" => (string)(round($mRate)), "female_Rate" => (string)(round($fRate)),"cong_Cnt" => (string)$cong_Cnt, "reg_Id" => $reg_Id, "nick_Name" => $reg_Nname, "reg_share_Bit" => $share_Bit, "member_Img" => $member_Img, "reg_date" => $reg_date, "mod_Date" => $mod_Date, "delete_Bit" => $delete_Bit);
	}
    dbClose($DB_con);
    $stmt = null;
} else { //빈값일 경우
	$result = array("result" => "error", "errorMsg" => "지점고유번호 오류");
}
	echo json_encode($result, JSON_UNESCAPED_UNICODE); 
?>



