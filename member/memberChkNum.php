<?
	include "../lib/common.php";

	$mem_Id = trim($memId);				//아이디
	$chkNum = trim($chkNum);				//추천코드
	//$mem_Id = "shut7720@hanmail.net";

	if ($mem_Id != "" && $chkNum != "" ) {  //아이디가 있을 경우

		$DB_con = db1();
			//완료변수설정 값 조회	
			$conQuery = "";
			$conQuery .= "SELECT conRecom_RC, conRecom_RP, conRecom_BRC, conRecom_BRP  from TB_CONFIG ;";
			$conStmt = $DB_con->prepare($conQuery);
			$conStmt->execute();
			while($conrow = $conStmt->fetch(PDO::FETCH_ASSOC)) {
				$conRecom_RC = $conrow['conRecom_RC'];				// 추천 받을 시 캐시적립
				$conRecom_RP = $conrow['conRecom_RP'];				// 추천 받을 시 회원등급점수
				$conRecom_BRC = $conrow['conRecom_BRC'];			// 추천 시 캐시적립
				$conRecom_BRP = $conrow['conRecom_BRP'];			// 추천 시 회원등급점수
			}
			// 추천코드 조회
			$memQuery = "SELECT mem_Id, mem_SId from TB_MEMBERS  WHERE mem_Code = :mem_Code AND b_Disply = 'N' " ;
			$stmt = $DB_con->prepare($memQuery);
			$stmt->bindparam(":mem_Code",$chkNum);
			$stmt->execute();
			$num = $stmt->rowCount();

			if($num < 1)  { //아닐경우
				$result = array("result" => "error","errorMsg" => "잘못된 추천코드 입니다. 다시 확인후 입력해 주세요." );
			} else {

				while($row=$stmt->fetch(PDO::FETCH_ASSOC)) {

						$nmemId = $row['mem_Id'];	  //아이디(추천 한 회원)
						$nmemSId = $row['mem_SId'];   //회원고유아이디

					    //회원 기타 정보 
					    $mEtcQuery = "";
					    $mEtcQuery = "SELECT mem_Point from TB_MEMBERS_ETC  WHERE mem_Id = :mem_Id  AND mem_SId = :mem_SId LIMIT 1"; 
					    $mEtcStmt = $DB_con->prepare($mEtcQuery);
					    $mEtcStmt->bindparam(":mem_Id",$nmemId);
					    $mEtcStmt->bindparam(":mem_SId",$nmemSId);
					    $mEtcStmt->execute();
					    $etcNum = $mEtcStmt->rowCount();
					    //echo $etcNum."<BR>";
					    //exit;

						if($etcNum < 1)  { //아닐경우
						} else {
							while($etcRow=$mEtcStmt->fetch(PDO::FETCH_ASSOC)) {
								$mem_Point = trim($etcRow['mem_Point']);							//포인트

								if ($mem_Point  == "") {
									$memPoint 	= 0;							
								} else {
									$memPoint 	= $mem_Point;
								}

							}
						}

				}

				$result = array("result" => "success","successMsg" => "추천코드가 확인 되었습니다." );

			}


		dbClose($DB_con);
		$stmt = null;	
		$meInfoStmt = null;	
		$mEtcStmt = null;	
		$mMapStmt = null;	
	} else {
		$result = array("result" => "error","errorMsg" => "필수값이 입력 되지 않았습니다." );
	}
		echo json_encode($result, JSON_UNESCAPED_UNICODE); 

?>