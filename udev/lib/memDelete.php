<?
	include "../../lib/common.php";
	include "../../lib/functionDB.php";  //공통 db함수

	//$mem_Id = "shut7720@hanmail.net";
	$mem_Id = trim($memId);				//아이디
	$login_Id = trim($loginId);				//접속한 개발자 아이디
	$reg_Date = DU_TIME_YMDHIS;		//등록일

	$DB_con = db1();
	$chkQuery = "
		SELECT idx FROM TB_MEMBERS WHERE mem_Id = :mem_Id;
	";
	$chkStmt = $DB_con->prepare($chkQuery);
	$chkStmt->bindparam(":mem_Id",$mem_Id);
	$chkStmt->execute();
	$chknum = $chkStmt->rowCount();
	if($chknum < 1){
		//사용기록 남기기!
		$deve_Locat = "회원정보삭제 (테스트서버)";			//사용위치
		$deve_Memo = "관리자 (".$login_Id.")가 회원 (".$mem_Id.")을 삭제처리시도 하였지만 실패함";		//메모
		$develop_log_query = "
			INSERT INTO TB_DEVELOP_LOG(login_Id, deve_Locat, deve_Memo, reg_Date)
			VALUES (:login_Id, :deve_Locat, :deve_Memo, :reg_Date);";
		$develop_Stmt = $DB_con->prepare($develop_log_query);
		$develop_Stmt->bindparam(":login_Id",$login_Id);
		$develop_Stmt->bindparam(":deve_Locat",$deve_Locat);
		$develop_Stmt->bindparam(":deve_Memo",$deve_Memo);
		$develop_Stmt->bindparam(":reg_Date",$reg_Date);
		$develop_Stmt->execute();

		$result = array("result" => "error", "errorMsg" => "회원정보가 없습니다. - 삭제 실패");
	}else{
		//사용기록 남기기!
		$deve_Locat = "회원정보삭제 (테스트서버)";			//사용위치
		$deve_Memo = "관리자 (".$login_Id.")가 회원 (".$mem_Id.")을 삭제처리 함";		//메모
		$develop_log_query = "
			INSERT INTO TB_DEVELOP_LOG(login_Id, deve_Locat, deve_Memo, reg_Date)
			VALUES (:login_Id, :deve_Locat, :deve_Memo, :reg_Date)";
		$develop_Stmt = $DB_con->prepare($develop_log_query);
		$develop_Stmt->bindparam(":login_Id",$login_Id);
		$develop_Stmt->bindparam(":deve_Locat",$deve_Locat);
		$develop_Stmt->bindparam(":deve_Memo",$deve_Memo);
		$develop_Stmt->bindparam(":reg_Date",$reg_Date);
		$develop_Stmt->execute();
		$Query = "
			DELETE FROM TB_MEMBERS WHERE mem_Id = :mem_Id;
		";
		$Stmt = $DB_con->prepare($Query);
		$Stmt->bindparam(":mem_Id",$mem_Id);
		$Stmt->execute();

		$result = array("result" => "success", "Msg" => "노선정보가 없어 노선관련 정보는 삭제 실패");
	}

	dbClose($DB_con);
	$Stmt = null;	
	$Stmt2 = null;
	$develop_Stmt = null;
	echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>