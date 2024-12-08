<?

	include "../lib/common.php";
	include "../lib/functionDB.php";  //공통 db함수
	
	$reg_Id = trim($memId);						// 아이디
	$mIdx = memIdxInfo($reg_Id);				// 회원고유번호
	$place_Idx = trim($placeIdx);					// 신고할 지점 고유번호
	$report_Idx = trim($reportIdx);				// 신고코드번호
	$report = trim($report);							// 신고내용
	if($report == ""){
		$report = "";
	}
	$mode = trim($mode);							// 댓글삭제인 경우 'del' 보내기
	//$mem_Id = "shut7720@hanmail.net";
	$reg_Date = DU_TIME_YMDHIS;
	$DB_con = db1();
	if($comment_Idx != ""){		//댓글고유번호가 있는 경우에는 삭제처리
		if($reg_Id != "" ) {		//아이디가 있을 경우
			$memQuery = "SELECT idx from TB_MEMBERS  WHERE mem_Id = :reg_Id AND idx = :idx AND b_Disply = 'N' " ;
			$stmt = $DB_con->prepare($memQuery);
			$stmt->bindparam(":reg_Id",$reg_Id);
			$stmt->bindparam(":idx",$mIdx);
			$stmt->execute();
			$num = $stmt->rowCount();
			if($num < 1){
				$result = array("result" => "error", "errorMsg" => "로그인아이디오류");
			}else{
				$chk_Query = "SELECT idx FROM TB_MEMBERS_REPORT WHERE idx = :idx AND member_Idx = :member_Idx AND reg_Id = :reg_Id AND place_Idx = :place_Idx; " ;
				$chk_stmt = $DB_con->prepare($chk_Query);
				$chk_stmt->bindParam(":member_Idx", $mIdx);
				$chk_stmt->bindparam(":reg_Id",$reg_Id);
				$chk_stmt->bindparam(":place_Idx",$place_Idx);
				$chk_stmt->bindparam(":idx",$comment_Idx);
				$chk_stmt->execute();
				$chk_num = $chk_stmt->rowCount();
				if($chk_num < 1){
					$result = array("result" => "error", "errorMsg" => "등록된 댓글이 없습니다.");
				}else{
					//댓글삭제하기
					$insquery ="DELETE FROM TB_MEMBERS_REPORT WHERE idx = :idx AND place_Idx = :place_Idx AND reg_Id = :reg_Id AND member_Idx = :member_Idx LIMIT 1;";
					$insstmt = $DB_con->prepare($insquery);
					$insstmt->bindParam(":member_Idx", $mIdx);
					$insstmt->bindparam(":place_Idx",$place_Idx);
					$insstmt->bindParam(":reg_Id", $reg_Id);
					$insstmt->bindParam(":idx", $comment_Idx);
					$insstmt->execute();
					//$mIdx = $DB_con->lastInsertId();  //저장된 idx 값

					$his_Query = "SELECT idx FROM TB_HISTORY WHERE member_Idx = :member_Idx AND place_Idx = :place_Idx AND history = '댓글등록' AND reg_Id = :reg_Id; " ;
					$his_stmt = $DB_con->prepare($his_Query);
					$his_stmt->bindParam(":member_Idx", $mIdx);
					$his_stmt->bindparam(":place_Idx",$place_Idx);
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
		if($reg_Id != "" ) {  //아이디가 있을 경우
			$memQuery = "SELECT idx from TB_MEMBERS  WHERE mem_Id = :reg_Id AND idx = :idx AND b_Disply = 'N' " ;
			$stmt = $DB_con->prepare($memQuery);
			$stmt->bindparam(":reg_Id",$reg_Id);
			$stmt->bindparam(":idx",$mIdx);
			$stmt->execute();
			$num = $stmt->rowCount();
			if($num < 1){
				$result = array("result" => "error", "errorMsg" => "로그인아이디오류");
			}else{
				$con_Idx = conIdxInfo($place_Idx);
				if($report_Idx != "01"){
					$re_query = "
						SELECT code_Name
						FROM TB_CONFIG_CODE
						WHERE code = :report_Idx
							AND code_Div = 'report'
						LIMIT 1;
					";
					$re_stmt = $DB_con->prepare($re_query);
					$re_stmt->bindParam(":report_Idx", $report_Idx);
					$re_stmt->execute();
					$re_row = $re_stmt->fetch(PDO::FETCH_ASSOC);
					$report = $re_row['code_Name'];
				}

				if($con_Idx != ""){
					//신고등록하기
					$insquery = "INSERT INTO TB_MEMBERS_REPORT (member_Idx, reg_Id, con_Idx, place_Idx, report_Idx, report, reg_date) VALUES (:member_Idx, :reg_Id, :con_Idx, :place_Idx, :report_Idx,  :report, :reg_Date);";
					$insstmt = $DB_con->prepare($insquery);
					$insstmt->bindParam(":member_Idx", $mIdx);
					$insstmt->bindParam(":reg_Id", $reg_Id);
					$insstmt->bindParam(":con_Idx", $con_Idx);
					$insstmt->bindParam(":place_Idx", $place_Idx);
					$insstmt->bindParam(":report_Idx", $report_Idx);
					$insstmt->bindParam(":report", $report);
					$insstmt->bindParam(":reg_Date", $reg_Date);
					$insstmt->execute();
					$sIdx = $DB_con->lastInsertId();  //저장된 idx 값

					$history = "신고";
					$con_Id = contentsIdInfo($con_Idx);	//지도를 등록한 아이디
					$his_query ="INSERT INTO TB_HISTORY (member_Idx, mem_Id, con_Idx, place_Idx, history, reg_Id, reg_date) VALUES (:member_Idx, :mem_Id, :con_Idx, :place_Idx, :history, :reg_Id, :reg_Date)";
					$his_stmt = $DB_con->prepare($his_query);
					$his_stmt->bindParam(":member_Idx", $mIdx);
					$his_stmt->bindParam(":mem_Id", $con_Id);
					$his_stmt->bindParam(":con_Idx", $con_Idx);
					$his_stmt->bindParam(":place_Idx", $place_Idx);
					$his_stmt->bindParam(":history", $history);
					$his_stmt->bindParam(":reg_Id", $reg_Id);
					$his_stmt->bindParam(":reg_Date", $reg_Date);
					$his_stmt->execute();
					$result = array("result" => "success");
				}else{
					//신고등록하기
					$insquery = "INSERT INTO TB_MEMBERS_REPORT (member_Idx, reg_Id, place_Idx, report_Idx, report, reg_date) VALUES (:member_Idx, :reg_Id, :place_Idx, :report_Idx,  :report, :reg_Date);";
					$insstmt = $DB_con->prepare($insquery);
					$insstmt->bindParam(":member_Idx", $mIdx);
					$insstmt->bindParam(":reg_Id", $reg_Id);
					$insstmt->bindParam(":place_Idx", $place_Idx);
					$insstmt->bindParam(":report_Idx", $report_Idx);
					$insstmt->bindParam(":report", $report);
					$insstmt->bindParam(":reg_Date", $reg_Date);
					$insstmt->execute();
					$sIdx = $DB_con->lastInsertId();  //저장된 idx 값

					$history = "신고";
					$con_Id = contentsIdInfo($con_Idx);	//지도를 등록한 아이디
					$his_query ="INSERT INTO TB_HISTORY (member_Idx, mem_Id, place_Idx, history, reg_Id, reg_date) VALUES (:member_Idx, :mem_Id, :place_Idx, :history, :reg_Id, :reg_Date)";
					$his_stmt = $DB_con->prepare($his_query);
					$his_stmt->bindParam(":member_Idx", $mIdx);
					$his_stmt->bindParam(":mem_Id", $con_Id);
					$his_stmt->bindParam(":place_Idx", $place_Idx);
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