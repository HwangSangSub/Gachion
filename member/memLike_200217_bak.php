<?
header('Content-Type: application/json; charset=UTF-8');
	include "../lib/common.php";
	include "../lib/functionDB.php";  //공통 db함수
	
	$mem_Id = trim($memId);					// 아이디
	$mIdx = memIdxInfo($mem_Id);			// 회원고유번호
	$place_Idx = trim($placeIdx);				// 좋아요 할 지점고유번호
	$con_Idx = conIdxInfo($place_Idx);		// 좋아요 할 지점의 지도고유번호
	if($con_Idx == ""){
		$con_Idx = "";
	}
	$mode = trim($mode);						// 좋아요취소인 경우 'del' 보내기
	//$mem_Id = "shut7720@hanmail.net";
	$reg_Date = DU_TIME_YMDHIS;
	$DB_con = db1();
	if($mode == "del"){		//담기 해지인 경우
		if($mem_Id != "" ) {  //아이디가 있을 경우
			$memQuery = "SELECT idx from TB_MEMBERS  WHERE mem_Id = :mem_Id AND idx = :idx AND b_Disply = 'N' " ;
			$stmt = $DB_con->prepare($memQuery);
			$stmt->bindparam(":mem_Id",$mem_Id);
			$stmt->bindparam(":idx",$mIdx);
			$stmt->execute();
			$num = $stmt->rowCount();
			if($num < 1){
				$result = array("result" => "error", "errorMsg" => "로그인아이디오류");
			}else{
				$chk_Query = "SELECT idx FROM TB_MEMBERS_LIKE WHERE member_Idx = :member_Idx AND mem_Id = :mem_Id AND con_Idx = :con_Idx AND place_Idx = :place_Idx AND use_Bit = 'Y'; " ;
				$chk_stmt = $DB_con->prepare($chk_Query);
				$chk_stmt->bindParam(":member_Idx", $mIdx);
				$chk_stmt->bindparam(":mem_Id",$mem_Id);
				$chk_stmt->bindparam(":con_Idx",$con_Idx);
				$chk_stmt->bindparam(":place_Idx",$place_Idx);
				$chk_stmt->execute();
				$chk_num = $chk_stmt->rowCount();
				if($chk_num < 1){
					$result = array("result" => "error", "errorMsg" => "좋아요 중이 아닙니다.");
				}else{
					//담기취소하기
					$insquery ="UPDATE TB_MEMBERS_LIKE SET use_Bit = 'N', cancle_Date = NOW() WHERE place_Idx = :place_Idx AND mem_Id = :mem_Id AND use_Bit = 'Y' AND member_Idx = :member_Idx LIMIT 1;";
					$insstmt = $DB_con->prepare($insquery);
					$insstmt->bindParam(":member_Idx", $mIdx);
					$insstmt->bindparam(":place_Idx",$place_Idx);
					$insstmt->bindParam(":mem_Id", $mem_Id);
					$insstmt->execute();
					//$mIdx = $DB_con->lastInsertId();  //저장된 idx 값
					if($place_Idx != ""){
						$chk_p_query = "
							SELECT idx, member_Idx, like_Cnt
							FROM TB_PLACE
							WHERE idx = :idx;
						";
						$chk_p_stmt = $DB_con->prepare($chk_p_query);
						$chk_p_stmt->bindparam(":idx",$place_Idx);
						$chk_p_stmt->execute();
						$chk_p_row=$chk_p_stmt->fetch(PDO::FETCH_ASSOC);
						$like_t_Cnt = $chk_p_row['like_Cnt'];
						$member_Idx = $chk_p_row['member_Idx'];
						$like_Cnt = (int)$like_t_Cnt - 1;
						//담긴횟수 업데이트
						$insquery ="UPDATE TB_PLACE SET like_Cnt = :like_Cnt, mod_Date = NOW() WHERE member_Idx = :member_Idx AND idx = :place_Idx LIMIT 1;";
						$insstmt = $DB_con->prepare($insquery);
						$insstmt->bindParam(":member_Idx", $member_Idx);
						$insstmt->bindparam(":place_Idx",$place_Idx);
						$insstmt->bindParam(":like_Cnt", $like_Cnt);
						$insstmt->execute();
					}

					$his_Query = "SELECT idx FROM TB_HISTORY WHERE con_Idx = :con_Idx AND place_Idx = :place_Idx AND history = '좋아요' AND reg_Id = :reg_Id; " ;
					$his_stmt = $DB_con->prepare($his_Query);
					$his_stmt->bindparam(":con_Idx",$con_Idx);
					$his_stmt->bindparam(":place_Idx",$place_Idx);
					$his_stmt->bindparam(":reg_Id",$mem_Id);
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
		if($mem_Id != "" ) {  //아이디가 있을 경우
			$memQuery = "SELECT idx from TB_MEMBERS  WHERE mem_Id = :mem_Id AND idx = :idx AND b_Disply = 'N' " ;
			$stmt = $DB_con->prepare($memQuery);
			$stmt->bindparam(":mem_Id",$mem_Id);
			$stmt->bindparam(":idx",$mIdx);
			$stmt->execute();
			$num = $stmt->rowCount();
			if($num < 1){
				$result = array("result" => "error", "errorMsg" => "로그인아이디오류");
			}else{
				$chk_Query = "
					SELECT *
					FROM TB_MEMBERS_LIKE
					WHERE mem_Id = :mem_Id
						AND use_Bit = 'Y'
						AND place_Idx = :place_Idx
						AND member_Idx = :member_Idx;";
				$chk_stmt = $DB_con->prepare($chk_Query);
				$chk_stmt->bindParam(":member_Idx", $mIdx);
				$chk_stmt->bindparam(":mem_Id",$mem_Id);
				$chk_stmt->bindparam(":place_Idx",$place_Idx);
				$chk_stmt->execute();
				$chk_num = $chk_stmt->rowCount();
				if($chk_num > 0){
					$result = array("result" => "error", "errorMsg" => "이미 좋아요 한 지점입니다.");
				}else{
					//지점담가기
					$insquery = "INSERT INTO TB_MEMBERS_LIKE (member_Idx, mem_Id, con_Idx, place_Idx, reg_date) VALUES (:member_Idx, :mem_Id, :con_Idx, :place_Idx, :reg_Date);";
					$insstmt = $DB_con->prepare($insquery);
					$insstmt->bindParam(":member_Idx", $mIdx);
					$insstmt->bindParam(":mem_Id", $mem_Id);
					$insstmt->bindParam(":con_Idx", $con_Idx);
					$insstmt->bindParam(":place_Idx", $place_Idx);
					$insstmt->bindParam(":reg_Date", $reg_Date);
					$insstmt->execute();
					$sIdx = $DB_con->lastInsertId();  //저장된 idx 값
				
					if($sIdx != ""){
						$chk_p_query = "
							SELECT idx, member_Idx, like_Cnt
							FROM TB_PLACE
							WHERE idx = :idx;
						";
						$chk_p_stmt = $DB_con->prepare($chk_p_query);
						$chk_p_stmt->bindparam(":idx",$place_Idx);
						$chk_p_stmt->execute();
						$chk_p_row=$chk_p_stmt->fetch(PDO::FETCH_ASSOC);
						$like_t_Cnt = $chk_p_row['like_Cnt'];
						$member_Idx = $chk_p_row['member_Idx'];
						$like_Cnt = (int)$like_t_Cnt + 1;
						//담긴횟수 업데이트
						$insquery ="UPDATE TB_PLACE SET like_Cnt = :like_Cnt, mod_Date = NOW() WHERE member_Idx = :member_Idx AND idx = :place_Idx LIMIT 1;";
						$insstmt = $DB_con->prepare($insquery);
						$insstmt->bindParam(":member_Idx", $member_Idx);
						$insstmt->bindparam(":place_Idx",$place_Idx);
						$insstmt->bindParam(":like_Cnt", $like_Cnt);
						$insstmt->execute();
					}
					$history = "좋아요";
					$con_Id = contentsIdInfo($con_Idx);	//지도를 등록한 아이디
					$his_query ="INSERT INTO TB_HISTORY (member_Idx, mem_Id, con_Idx, place_Idx, history, reg_Id, reg_date) VALUES (:member_Idx, :mem_Id, :con_Idx, :place_Idx, :history, :reg_Id, :reg_Date)";
					$his_stmt = $DB_con->prepare($his_query);
					$his_stmt->bindParam(":member_Idx", $mIdx);
					$his_stmt->bindParam(":mem_Id", $con_Id);
					$his_stmt->bindParam(":history", $history);
					$his_stmt->bindParam(":con_Idx", $con_Idx);
					$his_stmt->bindParam(":place_Idx", $place_Idx);
					$his_stmt->bindParam(":reg_Id", $mem_Id);
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