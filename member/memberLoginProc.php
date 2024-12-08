<?
header('Content-Type: application/json; charset=UTF-8');
	include "../lib/common.php";
	include "../lib/functionDB.php";  //공통 db함수
	
	
	$mem_Id = trim($memId);			//아이디
	$password = trim($password);		//비밀번호 (email 계정인 경우만 사용)
	$mDeviceId = trim($deviceId);		//디바이스 아이디
	//$mem_Id = "shut7720@hanmail.net";

	if ($mem_Id != "" ) {  //아이디가 있을 경우

			$DB_con = db1();
        
			//로그인횟수
			$memQuery = "SELECT idx, mem_Pwd, mem_SnsChk, login_Cnt from TB_MEMBERS  WHERE mem_Id = :mem_Id AND b_Disply = 'N' " ;
			
			$stmt = $DB_con->prepare($memQuery);
			$stmt->bindparam(":mem_Id",$mem_Id);
			$stmt->execute();
			$num = $stmt->rowCount();
			
			if($num < 1)  { //아닐경우
				$result = array("result" => "error", "errorMsg" => "아이디가 없습니다. 확인 후 다시 시도해주세요.");
			} else {
				
				while($row=$stmt->fetch(PDO::FETCH_ASSOC)) {

					$mem_SnsChk = $row['mem_SnsChk'];
					if($mem_SnsChk == "email"){
						$hash = $row['mem_Pwd'];
						if (password_verify($password, $hash)) { // 비밀번호가 일치하는지 비교합니다. 
							$mem_Id = $mem_Id;										// 아이디
							$mIdx = $row['idx'];											// 고유번호
							//$mem_Lv = $row['mem_Lv'];								// 등급
							$login_Cnt = $row['login_Cnt'];							// 로그인 횟수
							$login_Cnt = $login_Cnt + 1;
							
							# 마지막 로그인 시간을 업데이트 한다.
							$upQquery = "UPDATE TB_MEMBERS SET login_Cnt = :login_Cnt, login_Date = now(), mem_DeviceId = :mem_DeviceId WHERE idx = :idx AND mem_Id = :mem_Id LIMIT 1";
							$upStmt = $DB_con->prepare($upQquery);
							$upStmt->bindparam(":idx",$mIdx);
							$upStmt->bindparam(":mem_Id",$mem_Id);
							$upStmt->bindparam(":login_Cnt",$login_Cnt);
							$upStmt->bindparam(":mem_DeviceId",$mDeviceId);
							$upStmt->execute();
							$result = array("result" => "success");
						
						} else { 
							$result = array("result" => "error", "errorMsg" => "비밀번호가 틀렸습니다.");
						} 
					}else{
						$mem_Id = $mem_Id;										// 아이디
						$mIdx = $row['idx'];											// 고유번호
						//$mem_Lv = $row['mem_Lv'];								// 등급
						$login_Cnt = $row['login_Cnt'];							// 로그인 횟수
						$login_Cnt = $login_Cnt + 1;
						
						# 마지막 로그인 시간을 업데이트 한다.
						$upQquery = "UPDATE TB_MEMBERS SET login_Cnt = :login_Cnt, login_Date = now(), mem_DeviceId = :mem_DeviceId WHERE idx = :idx AND mem_Id = :mem_Id LIMIT 1";
						$upStmt = $DB_con->prepare($upQquery);
						$upStmt->bindparam(":idx",$mIdx);
						$upStmt->bindparam(":mem_Id",$mem_Id);
						$upStmt->bindparam(":login_Cnt",$login_Cnt);
						$upStmt->bindparam(":mem_DeviceId",$mDeviceId);
						$upStmt->execute();
						$result = array("result" => "success");
					}
				}
			}
	}else{
		$result = array("result" => "error");
	}


	dbClose($DB_con);
	$stmt = null;
	$upMStmt = null;
	$upStmt = null;
	$upStmt2 = null;
	$chktmt = null;
	$upStmt3 = null;
  echo json_encode($result); 

?>