<?
/*
* 프로그램				: 등록된 지점의 지역순 조회시 시구분 조회
* 페이지 설명			: 등록된 지점 목록을 보여줌(지도에 표시하기 위함)
* 파일명					: last_info.php
* 관련DB					: TB_CONTENTS, TB_PLACE, TB_BOARD
*/
include "../lib/common.php";
include "../lib/functionDB.php";

$mem_Id = trim($memId);						// 회원아이디
$memberIdx = memIdxInfo($mem_Id);					// 회원고유번호
$DB_con = db1();
// 게시판 정보 조회하기
$board_query = "
	SELECT idx, b_Idx, reg_Date
	FROM TB_BOARD
	ORDER BY idx DESC LIMIT 1;
";
$board_stmt = $DB_con->prepare($board_query);
$board_stmt->execute();
$board_idx = "";
$board_reg_Date = "";
while($board_row=$board_stmt->fetch(PDO::FETCH_ASSOC)) {
	$board_idx = $board_row['b_Idx'];											// 게시글 고유번호
	$board_reg_Date = $board_row['reg_Date'];								// 게시글 등록일
}
// 최신구독지도 정보보기
$cnt_sub_query = "
	SELECT idx, con_Idx
	FROM TB_MEMBERS_SUBSCRIBE
	WHERE mem_Id = :mem_Id AND use_Bit = 'Y' AND member_Idx = :member_Idx
	ORDER BY reg_Date DESC;";
$cnt_sub_Stmt = $DB_con->prepare($cnt_sub_query);
$cnt_sub_Stmt->bindParam(":mem_Id", $mem_Id);
$cnt_sub_Stmt->bindParam(":member_Idx", $memberIdx);
$cnt_sub_Stmt->execute();
$sub_Cnt = $cnt_sub_Stmt->rowCount();
if($sub_Cnt < 1){	//구독지도가 없음.
	$subs_idx = "";							// 구독지도고유번호
	$subs_mod_Date = "";				// 구독지도의 수정일
}else{
	if($sub_Cnt == 1){
		$sub_row=$cnt_sub_Stmt->fetch(PDO::FETCH_ASSOC);
		$sub_Idx = $sub_row['con_Idx'];
		$my_con_idx = "idx in ( ".$sub_Idx.")";
	}else{
		$sub_Idx = [];
		while($sub_row=$cnt_sub_Stmt->fetch(PDO::FETCH_ASSOC)){
			$sub_con_Idx = $sub_row['con_Idx'];
			 array_push($sub_Idx, $sub_con_Idx);
		}
		$myconidx = implode( ', ', $sub_Idx );
		$my_con_idx = "idx in ( ".$myconidx.")";
	}
	//지도 기본테이블 조회
	$sub_query = "
		SELECT idx, mod_Date
		FROM TB_CONTENTS
		WHERE ".$my_con_idx."
			AND delete_Bit = '0'
		ORDER BY mod_Date DESC
		LIMIT 1;
		";
	$sub_stmt = $DB_con->prepare($sub_query);
	$sub_stmt->execute();
	$sub_Num = $sub_stmt->rowCount();
	if($sub_Num < 1)  { //아닐경우
		$subs_idx = "";							// 구독지도고유번호
		$subs_mod_Date = "";				// 구독지도의 수정일
	} else {
		while($sub_row=$sub_stmt->fetch(PDO::FETCH_ASSOC)) {
			$subs_idx = $sub_row['idx'];								// 구독지도고유번호
			$subs_mod_Date = $sub_row['mod_Date'];				// 구독지도의 수정일
		}
	}
}
// echo $like_Id;

// 관심유저 아이디 조회하기
$like_query = "
	SELECT a.mem_Id
	FROM TB_MEMBERS_INTEREST as a
	WHERE a.reg_Id = :reg_Id
		AND a.member_Idx = (SELECT idx FROM TB_MEMBERS WHERE mem_Id = a.reg_Id AND b_Disply = 'N' LIMIT 1)
		AND a.use_Bit = 'Y';
	";

$like_stmt = $DB_con->prepare($like_query);
$like_stmt->bindParam(":reg_Id", $mem_Id);
$like_stmt->execute();
$like_Id = "";
$int = 1;
while($like_row=$like_stmt->fetch(PDO::FETCH_ASSOC)){
	if($int == 1){
		$like_Id = "'".$like_row['mem_Id']."'";
	}else{
		$like_Id = $like_Id.", '".$like_row['mem_Id']."'";
	}
	$int++;
}
if($like_Id != ""){
	$his_query = "
		SELECT idx, con_Idx, place_Idx, mem_Id, history, reg_Id, reg_Date
		FROM TB_HISTORY 
		WHERE reg_Id = :reg_Id
			AND history NOT IN ('신고', '지점공개여부' ,'지점삭제')
			OR (reg_Id IN (".$like_Id.") AND history NOT IN ('신고', '지점공개여부' ,'지점삭제'))
			OR (place_Idx IN (SELECT idx FROM TB_PLACE WHERE reg_Id = :reg_Id) AND history NOT IN ('신고', '지점공개여부' ,'지점삭제'))
		ORDER BY reg_Date DESC
		LIMIT 1;
		";
	$his_stmt = $DB_con->prepare($his_query);
	$his_stmt->bindParam(":reg_Id", $mem_Id);
	$his_stmt->execute();
}else{
	$his_query = "
		SELECT idx, con_Idx, place_Idx, mem_Id, history, reg_Id, reg_Date
		FROM TB_HISTORY 
		WHERE reg_Id = :reg_Id
			AND history NOT IN ('신고', '지점공개여부' ,'지점삭제')
			OR (place_Idx IN (SELECT idx FROM TB_PLACE WHERE reg_Id = :reg_Id) AND history NOT IN ('신고', '지점공개여부' ,'지점삭제'))
		ORDER BY reg_Date DESC
		LIMIT 1;
		";
	
	$his_stmt = $DB_con->prepare($his_query);
	$his_stmt->bindParam(":reg_Id", $mem_Id);
	$his_stmt->execute();
}
$chknum = $his_stmt->rowCount();
if($chknum < 1)  { // 등록된 히스토리가 없는 경우
	$his_idx = "";								// 히스토리 고유번호
	$his_reg_Date = "";				// 히스토리 등록일
} else {  //등록된 히스토리가 있을 경우
	while($his_row=$his_stmt->fetch(PDO::FETCH_ASSOC)){
		$his_idx = $his_row['idx'];								// 히스토리 고유번호
		$his_reg_Date = $his_row['reg_Date'];				// 히스토리 등록일
	}
}

$result = array("result" => "success", "board_idx" => $board_idx, "subs_mod_Date" => $subs_mod_Date, "his_idx" => $his_idx);
echo json_encode($result, JSON_UNESCAPED_UNICODE); 
dbClose($DB_con);
$stmt = null;
?>



