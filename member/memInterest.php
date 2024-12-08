<?
header('Content-Type: application/json; charset=UTF-8');
	include "../lib/common.php";
	include "../lib/functionDB.php";  //공통 db함수
	
	$reg_Id = trim($regId);			// 아이디
	$mem_Id = trim($memId);		// 관심유저아이디
	$mIdx = memIdxInfo($reg_Id);	// 회원고유번호
	$mode = trim($mode);			// 관심유저해지할 경우 del 보내기
	
	//$mem_Id = "shut7720@hanmail.net";
	$reg_Date = DU_TIME_YMDHIS;
	
	$DB_con = db1();
	if($mode == "del"){		//관심유저 해지인 경우
		if ($reg_Id != "" ) {  //아이디가 있을 경우
			$memQuery = "SELECT idx FROM TB_MEMBERS WHERE mem_Id = :mem_Id AND idx = :idx AND b_Disply = 'N' " ;
			$stmt = $DB_con->prepare($memQuery);
			$stmt->bindparam(":mem_Id",$reg_Id);
			$stmt->bindparam(":idx",$mIdx);
			$stmt->execute();
			$num = $stmt->rowCount();
			if($num < 1){
				$result = array("result" => "error", "errorMsg" => "로그인아이디오류");
			}else{
				$chk_Query = "SELECT idx FROM TB_MEMBERS_INTEREST WHERE member_Idx = :member_Idx AND mem_Id = :mem_Id AND reg_Id = :reg_Id AND use_Bit = 'Y'; " ;
				$chk_stmt = $DB_con->prepare($chk_Query);
				$chk_stmt->bindparam(":member_Idx", $mIdx);
				$chk_stmt->bindparam(":mem_Id",$mem_Id);
				$chk_stmt->bindparam(":reg_Id",$reg_Id);
				$chk_stmt->execute();
				$chk_num = $chk_stmt->rowCount();
				if($chk_num < 1){
					$result = array("result" => "error", "errorMsg" => "관심유저가 아닙니다.");
				}else{
					//관심유저취소하기
					$insquery ="UPDATE TB_MEMBERS_INTEREST SET use_Bit = 'N', cancle_Date = NOW() WHERE mem_Id = :mem_Id AND reg_Id = :reg_Id AND use_Bit = 'Y' AND member_Idx = :member_Idx LIMIT 1;";
					$insstmt = $DB_con->prepare($insquery);
					$insstmt->bindParam(":member_Idx", $mIdx);
					$insstmt->bindParam(":mem_Id", $mem_Id);
					$insstmt->bindParam(":reg_Id", $reg_Id);
					$insstmt->execute();
					//$mIdx = $DB_con->lastInsertId();  //저장된 idx 값

					$his_Query = "SELECT idx FROM TB_HISTORY WHERE mem_Id = :mem_Id AND history = '관심등록' AND reg_Id = :reg_Id; " ;
					$his_stmt = $DB_con->prepare($his_Query);
					$his_stmt->bindparam(":mem_Id",$mem_Id);
					$his_stmt->bindparam(":reg_Id",$reg_Id);
					$his_stmt->execute();
					$his_row=$his_stmt->fetch(PDO::FETCH_ASSOC);
					$his_idx = $his_row['idx'];
					$his_del_query ="DELETE FROM TB_HISTORY WHERE idx = :idx LIMIT 1;";
					$his_del_stmt = $DB_con->prepare($his_del_query);
					$his_del_stmt->bindParam(":idx", $his_idx);
					$his_del_stmt->execute();

					$result = array("result" => "success");
				}
			}
		}else{
			$result = array("result" => "error");
		}
	}else{
		if ($reg_Id != "" ) {  //아이디가 있을 경우
			$memQuery = "SELECT idx FROM TB_MEMBERS WHERE mem_Id = :mem_Id AND idx = :idx AND b_Disply = 'N' " ;
			$stmt = $DB_con->prepare($memQuery);
			$stmt->bindparam(":mem_Id",$reg_Id);
			$stmt->bindparam(":idx",$mIdx);
			$stmt->execute();
			$num = $stmt->rowCount();
			if($num < 1){
				$result = array("result" => "error", "errorMsg" => "로그인아이디오류");
			}else{
				$chk_Query = "
					SELECT *
					FROM TB_MEMBERS_INTEREST
					WHERE reg_Id = :reg_Id
						AND use_Bit = 'Y'
						AND mem_Id = :mem_Id
						AND member_Idx = :member_Idx
					LIMIT 1;";
				$chk_stmt = $DB_con->prepare($chk_Query);
				$chk_stmt->bindparam(":member_Idx", $mIdx);
				$chk_stmt->bindparam(":reg_Id",$reg_Id);
				$chk_stmt->bindparam(":mem_Id",$mem_Id);
				$chk_stmt->execute();
				$chk_num = $chk_stmt->rowCount();
				if($chk_num > 0){
					$result = array("result" => "error", "errorMsg" => "이미 관심유저 중입니다.");
				}else{
					//관심유저등록하기
					$insquery ="INSERT INTO TB_MEMBERS_INTEREST (member_Idx, mem_Id, reg_Id, reg_date) VALUES (:member_Idx, :mem_Id, :reg_Id, :reg_Date)";
					$insstmt = $DB_con->prepare($insquery);
					$insstmt->bindParam(":member_Idx", $mIdx);
					$insstmt->bindParam(":mem_Id", $mem_Id);
					$insstmt->bindParam(":reg_Id", $reg_Id);
					$insstmt->bindParam(":reg_Date", $reg_Date);
					$insstmt->execute();
					//$mIdx = $DB_con->lastInsertId();  //저장된 idx 값

					$history = "관심등록";
					$his_query ="INSERT INTO TB_HISTORY (member_Idx, mem_Id, history, reg_Id, reg_date) VALUES (:member_Idx, :mem_Id, :history, :reg_Id, :reg_Date)";
					$his_stmt = $DB_con->prepare($his_query);
					$his_stmt->bindParam(":member_Idx", $mIdx);
					$his_stmt->bindParam(":mem_Id", $mem_Id);
					$his_stmt->bindParam(":history", $history);
					$his_stmt->bindParam(":reg_Id", $reg_Id);
					$his_stmt->bindParam(":reg_Date", $reg_Date);
					$his_stmt->execute();
					$result = array("result" => "success");
				}
			}
		}else{
			$result = array("result" => "error");
		}
	}

	dbClose($DB_con);
	$stmt = null;
	$insstmt = null;
	$his_stmt = null;
  echo json_encode($result); 

?>