<?

$DB_con = db1();

// 기본 설정값 확인
$query = "
	SELECT content_MaxCnt, place_MaxCnt, list_PlaceCnt, total_LikeCnt
	FROM TB_CONFIG;
	";
$stmt = $DB_con->prepare($query);
$stmt->execute();
while($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
	$content_MaxCnt = $row['content_MaxCnt'];			// 지도생성제한
	$place_MaxCnt = $row['place_MaxCnt'];					// 지점생성제한
	$list_PlaceCnt = $row['list_PlaceCnt'];						// 메인지도 노출조건 (최소 지점 보유 수)
	$total_LikeCnt = $row['total_LikeCnt'];					// 통합좋아요 노출조건 (최소 좋아요 수)
}

// 기타 설정값 확인
$etc_query = "
	SELECT con_ImgUp, con_TxtFilter, con_Agree, con_Privacy
	FROM TB_CONFIG_ETC ;
	";
$etc_stmt = $DB_con->prepare($etc_query);
$etc_stmt->execute();
while($etc_row=$etc_stmt->fetch(PDO::FETCH_ASSOC)) {
	$con_ImgUp = $etc_row['con_ImgUp'];						// 이미지 업로드 확장자
	$con_TxtFilter = $etc_row['con_TxtFilter'];					// 단어 필터링
	$con_Agree = $etc_row['con_Agree'];							// 회원가입약관
	$con_Privacy = $etc_row['con_Privacy'];						// 개인정보취급방침
}
/*
$result = array("result" => "success", "content_MaxCnt" => $content_MaxCnt, "place_MaxCnt" => $place_MaxCnt, "list_PlaceCnt" => $list_PlaceCnt, "total_LikeCnt" => $total_LikeCnt, "con_ImgUp" => $con_ImgUp, "con_TxtFilter" => $con_TxtFilter, "con_Agree" => $con_Agree, "con_Privacy" => $con_Privacy);
echo json_encode($result, JSON_UNESCAPED_UNICODE); 
*/
dbClose($DB_con);
$stmt = null;	
$etc_stmt = null;	

?>


