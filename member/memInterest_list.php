<?
header('Content-Type: application/json; charset=UTF-8');
	include "../lib/common.php";
	include "../lib/functionDB.php";  //공통 db함수
	
	$mem_Id = trim($memId);			// 회원아이디
	$mode = trim($mode);				// 모드(receive: 관심받은, give: 관심준)
	$mIdx = memIdxInfo($mem_Id);	// 회원고유번호
	
	//$mem_Id = "shut7720@hanmail.net";
	$reg_Date = DU_TIME_YMDHIS;
	
	$DB_con = db1();
	if($mode == "receive"){		//관심받은 리스트
		if ($mem_Id != "" ) {  //아이디가 있을 경우
			$memQuery = "SELECT idx FROM TB_MEMBERS WHERE mem_Id = :mem_Id AND b_Disply = 'N' " ;
			$memstmt = $DB_con->prepare($memQuery);
			$memstmt->bindparam(":mem_Id",$mem_Id);
			$memstmt->execute();
			$memnum = $memstmt->rowCount();
			if($memnum < 1){
				$result = array("result" => "error", "errorMsg" => "로그인아이디오류");
				echo json_encode($result); 
			}else{
				$query = "
					SELECT a.reg_Id, a.reg_Date
					FROM TB_MEMBERS_INTEREST as a
					WHERE a.mem_Id = :mem_Id
						AND a.member_Idx = (SELECT idx FROM TB_MEMBERS WHERE mem_Id = a.reg_Id AND b_Disply = 'N' LIMIT 1)
						AND a.use_Bit = 'Y'
					ORDER BY a.reg_Date DESC;
				";
				$stmt = $DB_con->prepare($query);
				$stmt->bindparam(":mem_Id",$mem_Id);
				$stmt->execute();
				$num = $stmt->rowCount();
				if($num < 1){
					$result = array("result" => "error", "errorMsg" => "관심받은 내역이 없습니다.");
					echo json_encode($result); 
				}else{
					$data = [];
					while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
						$reg_Id = $row['reg_Id'];
						$reg_Date = $row['reg_Date'];
						$reg_Nname = memNickInfo($reg_Id);											// 회원닉네임
						$member_Img = memImgInfo($reg_Id);											// 회원이미지
						$rIdx = memIdxInfo($reg_Id);	// 회원고유번호
						$login_chk_query = "
							SELECT login_Date
							FROM TB_MEMBERS
							WHERE mem_Id = :mem_Id
								AND idx = :idx
								AND b_Disply = 'N'
							ORDER BY idx DESC
							LIMIT 1;
						";
						$login_chk_stmt = $DB_con->prepare($login_chk_query);
						$login_chk_stmt->bindparam(":mem_Id",$reg_Id);
						$login_chk_stmt->bindParam(":idx", $rIdx);
						$login_chk_stmt->execute();
						$login_chk_row=$login_chk_stmt->fetch(PDO::FETCH_ASSOC);
						$last_login_date = $login_chk_row['login_Date'];
						if($last_login_date == ""){
							$last_login_date = "";
						}

						$result = ["reg_Id" => $reg_Id, "reg_Nname" => $reg_Nname, "member_Img" => $member_Img, "reg_Date" => $reg_Date,  "login_Date" => $last_login_date];
						 array_push($data, $result);
					}
					$chkData = [];
					$chkData["result"] = "success";
					$chkData['lists'] = $data;
					$output = str_replace('\\\/', '/', json_encode($chkData, JSON_UNESCAPED_UNICODE));
					echo  urldecode($output);
				}
			}
		}else{
			$result = array("result" => "error");
			echo json_encode($result); 
		}
	}else if($mode == "give"){
		if ($mem_Id != "" ) {  //아이디가 있을 경우
			$memQuery = "SELECT idx FROM TB_MEMBERS WHERE mem_Id = :mem_Id AND b_Disply = 'N' " ;
			$memstmt = $DB_con->prepare($memQuery);
			$memstmt->bindparam(":mem_Id",$mem_Id);
			$memstmt->execute();
			$memnum = $memstmt->rowCount();
			if($memnum < 1){
				$result = array("result" => "error", "errorMsg" => "로그인아이디오류");
			}else{
				$query = "
					SELECT mem_Id, reg_Date
					FROM TB_MEMBERS_INTEREST
					WHERE reg_Id = :reg_Id
						AND member_Idx = :member_Idx
						AND use_Bit = 'Y'
					ORDER BY reg_Date DESC;
				";
				$stmt = $DB_con->prepare($query);
				$stmt->bindparam(":reg_Id",$mem_Id);
				$stmt->bindparam(":member_Idx",$mIdx);
				$stmt->execute();
				$num = $stmt->rowCount();
				if($num < 1){
					$result = array("result" => "error", "errorMsg" => "관심 준 내역이 없습니다.");
					 echo json_encode($result); 
				}else{
					$data = [];
					while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
						$reg_Id = $row['mem_Id'];
						$reg_Date = $row['reg_Date'];
						$reg_Nname = memNickInfo($reg_Id);											// 회원닉네임
						$member_Img = memImgInfo($reg_Id);											// 회원이미지
						$login_chk_query = "
							SELECT login_Date
							FROM TB_MEMBERS
							WHERE mem_Id = :mem_Id
								AND b_Disply = 'N'
							ORDER BY idx DESC
							LIMIT 1;
						";
						$login_chk_stmt = $DB_con->prepare($login_chk_query);
						$login_chk_stmt->bindparam(":mem_Id",$reg_Id);
						$login_chk_stmt->execute();
						$login_chk_row=$login_chk_stmt->fetch(PDO::FETCH_ASSOC);
						$last_login_date = $login_chk_row['login_Date'];
						if($last_login_date == ""){
							$last_login_date = "";
						}

						$result = ["reg_Id" => $reg_Id, "reg_Nname" => $reg_Nname, "member_Img" => $member_Img, "reg_Date" => $reg_Date,  "login_Date" => $last_login_date];
						 array_push($data, $result);
					}
					$chkData = [];
					$chkData["result"] = "success";
					$chkData['lists'] = $data;
					$output = str_replace('\\\/', '/', json_encode($chkData, JSON_UNESCAPED_UNICODE));
					echo  urldecode($output);
				}
			}
		}else{
			$result = array("result" => "error");
			echo json_encode($result); 
		}
	}else{
		$result = array("result" => "error");
		echo json_encode($result); 
	}

	dbClose($DB_con);
	$stmt = null;
	$insstmt = null;
	$his_stmt = null;

?>