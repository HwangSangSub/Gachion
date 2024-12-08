<?
	include "../../../lib/common.php";
	include "../../../lib/functionDB.php";  //공통 db함수

	//$mem_Id = "shut7720@hanmail.net";
	$reg_Date = DU_TIME_YMDHIS;		//등록일
	$mode = trim($mode);					//모드
	$DB_con = db1();
	if($mode == 'mirpay'){
		$userId = trim($userId);				//아이디
		$login_Id = trim($loginId);				//접속한 개발자 아이디
		$pay = trim($pay);				//접속한 개발자 아이디
		$deve_Locat = "미르페이전환";			//사용위치
		$deve_Memo = "관리자 (".$login_Id.")가 회원 (".$userId.")에게 미르페이포인트 ".$pay." 전환";		//메모
		$develop_log_query = "
			INSERT INTO TB_DEVELOP_LOG(login_Id, deve_Locat, deve_Memo, reg_Date)
			VALUES (:login_Id, :deve_Locat, :deve_Memo, :reg_Date);";
		$develop_Stmt = $DB_con->prepare($develop_log_query);
		$develop_Stmt->bindparam(":login_Id",$login_Id);
		$develop_Stmt->bindparam(":deve_Locat",$deve_Locat);
		$develop_Stmt->bindparam(":deve_Memo",$deve_Memo);
		$develop_Stmt->bindparam(":reg_Date",$reg_Date);
		$develop_Stmt->execute();
		$result = array("result" => "success","Msg" => "미르페이전환");
	}
	dbClose($DB_con);
	$develop_Stmt = null;
	echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>