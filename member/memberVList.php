<?
header('Content-Type: application/json; charset=UTF-8');
include "../lib/common.php";
include "../lib/functionDB.php";

$mem_Id = trim($memId);						// 로그인한 회원 아이디
if(memChk($mem_Id) == "0"){
	$mem_Id = "GUEST";							//회원아이디가 없는 경우 비회원으로 처리
}else{
	$mIdx = memIdxInfo($mem_Id);	// 회원고유번호
}
$reg_Id = trim($regId);							// 프로필조회할 아이디
$memberIdx = memIdxInfo($reg_Id);	// 회원고유번호
//$reg_Date = DU_TIME_YMDHIS;		//등록일

$DB_con = db1();
if ($reg_Id != "") {
	$query ="
		SELECT mem_Id, mem_NickNm, mem_ImgFile, mem_Memo
		FROM TB_MEMBERS
		WHERE mem_Id = :mem_Id
			AND b_Disply = 'N'
		LIMIT 1;
	";
	$stmt = $DB_con->prepare($query);
	$stmt->bindParam(":mem_Id", $reg_Id);
	$stmt->execute();
	$num = $stmt->rowCount();
	if($num < 1){
		$result = array("result" => "error", "errorMsg" => "가입하지 않은 계정이거나 탈퇴된 계정입니다.");
		echo json_encode($result, JSON_UNESCAPED_UNICODE); 
	}else{
		$row=$stmt->fetch(PDO::FETCH_ASSOC);
		$mem_NickNm = $row['mem_NickNm'];
		$member_Img = memImgInfo($reg_Id);											// 회원이미지
		if($member_Img == ""){
			$member_Img = "";
		}
		$mem_Memo = $row['mem_Memo'];
		if($mem_Memo == ''){
			$mem_Memo = '';
		}
		// 관심을 준 수
		$inte_give_query = "
			SELECT count(idx) as cnt
			FROM TB_MEMBERS_INTEREST
			WHERE reg_Id = :mem_Id
				AND member_Idx = :member_Idx
				AND use_Bit = 'Y';
		";
		$inte_give_stmt = $DB_con->prepare($inte_give_query);
		$inte_give_stmt->bindParam(":mem_Id", $reg_Id);
		$inte_give_stmt->bindParam(":member_Idx", $memberIdx);
		$inte_give_stmt->execute();
		$inte_give_row=$inte_give_stmt->fetch(PDO::FETCH_ASSOC);
		$give_cnt = $inte_give_row['cnt'];
		// 관심을 받은 수
		$inte_receive_query = "
			SELECT count(a.idx) as cnt
			FROM TB_MEMBERS_INTEREST as a
			WHERE a.mem_Id = :mem_Id
				AND a.member_Idx = (SELECT idx FROM TB_MEMBERS WHERE mem_Id = a.reg_Id AND b_Disply = 'N' LIMIT 1)
				AND a.use_Bit = 'Y';
		";
		$inte_receive_stmt = $DB_con->prepare($inte_receive_query);
		$inte_receive_stmt->bindParam(":mem_Id", $reg_Id);
		$inte_receive_stmt->execute();
		$inte_receive_row=$inte_receive_stmt->fetch(PDO::FETCH_ASSOC);
		$receive_cnt = $inte_receive_row['cnt'];
		if($mem_Id != $reg_Id){
			$inte_chk_query = "
				SELECT count(a.idx) as cnt
				FROM TB_MEMBERS_INTEREST as a
				WHERE a.reg_Id = :reg_Id
					AND a.member_Idx = (SELECT idx FROM TB_MEMBERS WHERE mem_Id = a.reg_Id AND b_Disply = 'N' LIMIT 1)
					AND a.use_Bit = 'Y'
					AND a.mem_Id = :mem_Id;
			";
			$inte_chk_stmt = $DB_con->prepare($inte_chk_query);
			$inte_chk_stmt->bindParam(":mem_Id", $reg_Id);
			$inte_chk_stmt->bindParam(":reg_Id", $mem_Id);
			$inte_chk_stmt->execute();
			$inte_chk_row=$inte_chk_stmt->fetch(PDO::FETCH_ASSOC);
			$interest_cnt = $inte_chk_row['cnt'];
			if($interest_cnt == '0'){
				$inte_bit = 'N';
			}else{
				$inte_bit = 'Y';
			}
		}else{
			$inte_bit = 'N';
		}
		$result = array("result" => "success", "mem_Id" => $reg_Id, "mem_NickNm" => $mem_NickNm, "mem_ImgFile" => $member_Img, "mem_Memo" => $mem_Memo, "receive_Cnt" => (string)$receive_cnt, "give_Cnt" => (string)$give_cnt, "inte_bit" => $inte_bit);
		echo json_encode($result, JSON_UNESCAPED_UNICODE); 
	}
} else { //빈값일 경우
	$result = array("result" => "error", "errorMsg" => "아이디 빈값오류");
	echo json_encode($result, JSON_UNESCAPED_UNICODE); 
}
dbClose($DB_con);
?>



