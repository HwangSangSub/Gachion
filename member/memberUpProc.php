<?
header('Content-Type: application/json; charset=UTF-8');
	include "../lib/common.php";
	include "../lib/functionDB.php";  //공통 db함수
	
	$mem_Id = trim($memId);				//아이디
	$mIdx = memIdxInfo($mem_Id);	// 회원고유번호
    $mem_NickNm= trim($nickName);	//닉네임
    if($ie) { //익슬플로러일경우
        $mem_NickNm = iconv('euc-kr', 'utf-8', $mem_NickNm);
    }
	$memo = trim($memo);					//메모
    if($ie) { //익슬플로러일경우
        $memo = iconv('euc-kr', 'utf-8', $memo);
    }

	if ($mem_Id != "" ) {  //아이디가 있을 경우

			$DB_con = db1();

			$memQuery = "
				SELECT idx, mem_Id, mem_NickNm, mem_Memo
				FROM TB_MEMBERS
				WHERE mem_Id = :mem_Id
					AND idx = :idx
					AND b_Disply = 'N'
				LIMIT 1;" ;
			$stmt = $DB_con->prepare($memQuery);
			$stmt->bindparam(":mem_Id",$mem_Id);
			$stmt->bindparam(":idx",$mIdx);
			$stmt->execute();
			$num = $stmt->rowCount();

			if($num < 1)  { //아닐경우
				$result = array("result" => "error");
			} else {
			    
			    while($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
			        $mNickNm = $row['mem_NickNm'];			//회원 닉네임
					$mmemMemo = $row['mem_Memo'];		//회원 메모
			    }
			    
			    if ($mem_NickNm == "") {
			        $mem_NickNm = $mNickNm;
			    } else {
			        $mem_NickNm = $mem_NickNm;
			    }
				if($mem_NickNm != $mNickNm){
					$his_Query = "SELECT idx FROM TB_HISTORY WHERE history = '닉네임변경' AND reg_Id = :reg_Id; " ;
					$his_stmt = $DB_con->prepare($his_Query);
					$his_stmt->bindparam(":reg_Id",$mem_Id);
					$his_stmt->execute();
					$his_row=$his_stmt->fetch(PDO::FETCH_ASSOC);
					$his_idx = $his_row['idx'];
					$his_del_query ="DELETE FROM TB_HISTORY WHERE idx = :idx LIMIT 1;";
					$his_del_stmt = $DB_con->prepare($his_del_query);
					$his_del_stmt->bindParam(":idx", $his_idx);
					$his_del_stmt->execute();

					$history = "닉네임변경";
					$his_query ="INSERT INTO TB_HISTORY (member_Idx, mem_Id, history, memo, reg_Id, reg_Date) VALUES (:member_Idx, :mem_Id, :history, :memo, :reg_Id, NOW())";
					$his_stmt = $DB_con->prepare($his_query);
					$his_stmt->bindParam(":member_Idx", $mIdx);
					$his_stmt->bindParam(":mem_Id", $mem_Id);
					$his_stmt->bindParam(":history", $history);
					$his_stmt->bindParam(":memo", $mNickNm);
					$his_stmt->bindParam(":reg_Id", $mem_Id);
					$his_stmt->execute();
				}
			    
			    // if ($memo == "") {
			    //     $memo = $mmemMemo;
			    // } else {
			    //     $memo = $memo;
			    // }			    
				if($memo != $mmemMemo){
					$his_Query = "SELECT idx FROM TB_HISTORY WHERE history = '프로필소개변경' AND reg_Id = :reg_Id; " ;
					$his_stmt = $DB_con->prepare($his_Query);
					$his_stmt->bindparam(":reg_Id",$mem_Id);
					$his_stmt->execute();
					$his_row=$his_stmt->fetch(PDO::FETCH_ASSOC);
					$his_idx = $his_row['idx'];
					$his_del_query ="DELETE FROM TB_HISTORY WHERE idx = :idx LIMIT 1;";
					$his_del_stmt = $DB_con->prepare($his_del_query);
					$his_del_stmt->bindParam(":idx", $his_idx);
					$his_del_stmt->execute();

					$history = "프로필소개변경";
					$his_query ="INSERT INTO TB_HISTORY (member_Idx, mem_Id, history, reg_Id, reg_Date) VALUES (:member_Idx, :mem_Id, :history, :reg_Id, NOW())";
					$his_stmt = $DB_con->prepare($his_query);
					$his_stmt->bindParam(":member_Idx", $mIdx);
					$his_stmt->bindParam(":mem_Id", $mem_Id);
					$his_stmt->bindParam(":history", $history);
					$his_stmt->bindParam(":reg_Id", $mem_Id);
					$his_stmt->execute();
				}
				//회원기본 테이블
				$upQquery = "UPDATE TB_MEMBERS SET  mem_NickNm = :mem_NickNm, mem_Memo = :mem_Memo, mod_Date = NOW() WHERE  mem_Id = :mem_Id AND idx = :idx  LIMIT 1";
				$upStmt = $DB_con->prepare($upQquery);
				$upStmt->bindparam(":mem_NickNm",$mem_NickNm);
				$upStmt->bindparam(":mem_Memo",$memo);
				$upStmt->bindparam(":mem_Id",$mem_Id);
				$upStmt->bindparam(":idx",$mIdx);
				$upStmt->execute();

				$result = array("result" => "success" );

			}

		dbClose($DB_con);
		$stmt = null;
		$mInfoStmt = null;
		$upStmt = null;
		$upStmt2 = null;

	} else {
		$result = array("result" => "error");

	}
		echo json_encode($result); 


?>