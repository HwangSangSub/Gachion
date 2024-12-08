<?
	include "../lib/common.php";
	include "../lib/functionDB.php";  //공통 db함수

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
		$chkQuery2 = "
			SELECT taxi_SIdx FROM TB_RTAXISHARING WHERE taxi_MemId = :mem_Id OR taxi_RMemId = :mem_Id;
		";
		$chkStmt2 = $DB_con->prepare($chkQuery2);
		$chkStmt2->bindparam(":mem_Id",$mem_Id);
		$chkStmt2->execute();
		$chknum2 = $chkStmt2->rowCount();
		if($chknum2 < 1)  { //없을 경우 기본정보만 삭제
			$Query = "
				DELETE FROM TB_MEMBERS WHERE mem_Id = :mem_Id;
				DELETE FROM TB_MEMBERS_ETC WHERE mem_Id = :mem_Id;
				DELETE FROM TB_MEMBERS_INFO WHERE mem_Id = :mem_Id;
				DELETE FROM TB_MEMBERS_MAP WHERE mem_Id = :mem_Id;
				DELETE FROM TB_MEMBER_PHOTO WHERE mem_Id = :mem_Id;
				DELETE FROM TB_MEMWITHDRAWL WHERE mem_Id = :mem_Id;
				DELETE FROM TB_ONLINE WHERE b_MemId = :mem_Id;
				DELETE FROM TB_PAYMENT_CARD WHERE card_Mem_Id = :mem_Id;
				DELETE FROM TB_POINT_EXC WHERE mem_Id = :mem_Id;
				DELETE FROM TB_SHARING_PUSH WHERE taxi_MemId = :mem_Id;
				DELETE FROM TB_SHARING_SLEEP WHERE taxi_MemId = :mem_Id;
				DELETE FROM TB_SHARING_STANDBY WHERE taxi_MemId = :mem_Id;
				DELETE FROM TB_SMATCH_STATE WHERE taxi_MemId = :mem_Id;
				DELETE FROM TB_STANDBY_PUSH WHERE taxi_MemId = :mem_Id;
				DELETE FROM TB_COUPON_HISTORY WHERE taxi_MemId = :mem_Id;
				DELETE FROM TB_COUPON_USE WHERE cou_MemId = :mem_Id;
			";
			$Stmt = $DB_con->prepare($Query);
			$Stmt->bindparam(":mem_Id",$mem_Id);
			$Stmt->execute();
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

			$result = array("result" => "success", "Msg" => "노선정보가 없어 노선관련 정보는 삭제 실패");
		}else{
			while($chkrow2=$chkStmt2->fetch(PDO::FETCH_ASSOC)) {
					$taxi_SIdx = $chkrow2['taxi_SIdx'];	       //노선생성번호
					$Query2 = "
						DELETE FROM TB_MEMBERS WHERE mem_Id = :mem_Id;
						DELETE FROM TB_MEMBERS_ETC WHERE mem_Id = :mem_Id;
						DELETE FROM TB_MEMBERS_INFO WHERE mem_Id = :mem_Id;
						DELETE FROM TB_MEMBERS_MAP WHERE mem_Id = :mem_Id;
						DELETE FROM TB_MEMBER_PHOTO WHERE mem_Id = :mem_Id;
						DELETE FROM TB_MEMWITHDRAWL WHERE mem_Id = :mem_Id;
						DELETE FROM TB_ONLINE WHERE b_MemId = :mem_Id;
						DELETE FROM TB_PAYMENT_CARD WHERE card_Mem_Id = :mem_Id;
						DELETE FROM TB_POINT_EXC WHERE mem_Id = :mem_Id;
						DELETE FROM TB_SHARING_PUSH WHERE taxi_MemId = :mem_Id;
						DELETE FROM TB_SHARING_SLEEP WHERE taxi_MemId = :mem_Id;
						DELETE FROM TB_SHARING_STANDBY WHERE taxi_MemId = :mem_Id;
						DELETE FROM TB_SMATCH_STATE WHERE taxi_MemId = :mem_Id;
						DELETE FROM TB_STANDBY_PUSH WHERE taxi_MemId = :mem_Id;
						DELETE FROM TB_COUPON_HISTORY WHERE taxi_MemId = :mem_Id;
						DELETE FROM TB_COUPON_USE WHERE cou_MemId = :mem_Id;
						DELETE FROM TB_STAXISHARING WHERE idx = :taxi_SIdx;
						DELETE FROM TB_STAXISHARING_MAP WHERE taxi_Idx = :taxi_SIdx;
						DELETE FROM TB_STAXISHARING_INFO WHERE taxi_Idx = :taxi_SIdx;
						DELETE FROM TB_RTAXISHARING WHERE taxi_SIdx = :taxi_SIdx;
						DELETE FROM TB_RTAXISHARING_INFO WHERE taxi_SIdx = :taxi_SIdx;
						DELETE FROM TB_RTAXISHARING_MAP WHERE taxi_SIdx = :taxi_SIdx;
						DELETE FROM TB_POINT_HISTORY WHERE taxi_SIdx = :taxi_SIdx OR taxi_MemId = :mem_Id;
						DELETE FROM TB_PROFIT_POINT WHERE taxi_SIdx = :taxi_SIdx;
						DELETE FROM TB_SMATCH_STATE WHERE taxi_SIdx = :taxi_SIdx;
						DELETE FROM TB_PENALTY_HISTORY WHERE taxi_SIdx = :taxi_SIdx;
						DELETE FROM TB_ORDER WHERE taxi_SIdx = :taxi_SIdx;
						DELETE FROM TB_SHARING_PUSH WHERE taxi_Idx = :taxi_SIdx;
						DELETE FROM TB_SHARING_SLEEP WHERE taxi_Idx = :taxi_SIdx;
					";
					$Stmt2 = $DB_con->prepare($Query2);
					$Stmt2->bindparam(":mem_Id",$mem_Id);
					$Stmt2->bindparam(":taxi_SIdx",$taxi_SIdx);
					$Stmt2->execute();
			}
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

			$result = array("result" => "success","Msg" => "모든정보 삭제완료");
		}
	}

	dbClose($DB_con);
	$Stmt = null;	
	$Stmt2 = null;
	$develop_Stmt = null;
	echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>