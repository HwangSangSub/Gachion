<?
include "../lib/common.php";
include "../lib/functionDB.php";

$mem_Id = trim($memId);						// 로그인한 회원 아이디
if($mem_Id == ''){
	$mem_Id = 'GUEST';							// 비회원 인 경우 GUEST
}
//$reg_Date = DU_TIME_YMDHIS;		//등록일

$DB_con = db1();
if ($mem_Id != "") {
	$query ="
		SELECT mem_Id, mem_NickNm, mem_ImgFile, mem_Memo
		FROM TB_MEMBERS
		WHERE mem_Id = :mem_Id
			AND b_Disply = 'N'
		LIMIT 1;
	";
	$stmt = $DB_con->prepare($query);
	$stmt->bindParam(":mem_Id", $mem_Id);
	$stmt->execute();
	$num = $stmt->rowCount();
	if($num < 1){
		$result = array("result" => "error", "errorMsg" => "가입하지 않은 계정이거나 탈퇴된 계정입니다.");
		echo json_encode($result, JSON_UNESCAPED_UNICODE); 
	}else{
		$row=$stmt->fetch(PDO::FETCH_ASSOC);
		$mem_NickNm = $row['mem_NickNm'];
		$mem_ImgFile = $row['mem_ImgFile'];
		if($mem_ImgFile == ""){
			$mImgFile = "";
		}else{
			$mImgFile = "https://places.gachita.kr/member/member_img/".$mem_ImgFile;
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
				AND use_Bit = 'Y';
		";
		$inte_give_stmt = $DB_con->prepare($inte_give_query);
		$inte_give_stmt->bindParam(":mem_Id", $mem_Id);
		$inte_give_stmt->execute();
		$inte_give_row=$inte_give_stmt->fetch(PDO::FETCH_ASSOC);
		$give_cnt = $inte_give_row['cnt'];
		// 관심을 받은 수
		$inte_receive_query = "
			SELECT count(idx) as cnt
			FROM TB_MEMBERS_INTEREST
			WHERE mem_Id = :mem_Id
				AND use_Bit = 'Y';
		";
		$inte_receive_stmt = $DB_con->prepare($inte_receive_query);
		$inte_receive_stmt->bindParam(":mem_Id", $mem_Id);
		$inte_receive_stmt->execute();
		$inte_receive_row=$inte_receive_stmt->fetch(PDO::FETCH_ASSOC);
		$receive_cnt = $inte_receive_row['cnt'];
		$result = array("result" => "success", "mem_Id" => $mem_Id,"mem_NickNm" => $mem_NickNm, "mem_ImgFile" => $mImgFile, "mem_Memo" => $mem_Memo, "receive_Cnt" => (string)$receive_cnt, "give_Cnt" => (string)$give_cnt);
		echo json_encode($result, JSON_UNESCAPED_UNICODE); 
	}
} else { //빈값일 경우
	$result = array("result" => "error", "errorMsg" => "아이디 빈값오류");
	echo json_encode($result, JSON_UNESCAPED_UNICODE); 
}
dbClose($DB_con);
?>



