<?
	include "../lib/common.php";
	include "../lib/functionDB.php";  //공통 db함수

	$mem_Id = trim($memId);					//아이디
	$mIdx = memIdxInfo($mem_Id);			//회원 고유번호
	$mem_Token = trim($token);				//토큰값
	$mem_Os = trim($os);						//os구분  (0 : 안드로이드, 1: 아이폰)

	if ($mem_Id != "" & $mem_Token != "")  {  //아이디가 있을 경우

			$DB_con = db1();
			
			$memQuery = "SELECT mem_Token, mem_Os from TB_MEMBERS WHERE idx = :idx AND mem_Id = :mem_Id AND b_Disply = 'N'" ;
			$stmt = $DB_con->prepare($memQuery);
			$stmt->bindparam(":mem_Id",$mem_Id);
			$stmt->bindparam(":idx",$mIdx);
			$stmt->execute();
			$num = $stmt->rowCount();
			
			
			if($num < 1)  { //아닐경우
				$result = array("result" => "error");
			} else {
			    
				while($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
				    $memToken = $row['mem_Token'];	         
				    $memOs = $row['mem_Os'];
				}

			    if ($mem_Token != "") {
			        $mem_Token = $mem_Token;
			    } else {
			        $mem_Token = $memToken;
			    }
				   
			    
			    // 빈값이면 0: 안드로이드로 고정 아니면 받은 값으로 수정 작업일 : 2019-03-19 
			    if ($mem_Os == "") {
			        $mem_Os = "0";
			    } else {
			        $mem_Os = $mem_Os;
			    }

				$upQquery = "UPDATE TB_MEMBERS SET mem_Token = :mem_Token, mem_Os = :mem_Os WHERE mem_Id = :mem_Id AND idx = :idx LIMIT 1";
				$upStmt = $DB_con->prepare($upQquery);
				$upStmt->bindparam(":mem_Token",$mem_Token);
				$upStmt->bindparam(":mem_Os",$mem_Os);
				$upStmt->bindparam(":mem_Id",$mem_Id);
				$upStmt->bindparam(":idx",$mIdx);
				$upStmt->execute();

				$result = array("result" => "success" );

			}

		dbClose($DB_con);
		$stmt = null;
		$upStmt = null;

	} else {
		$result = array("result" => "error");

	}
	
	echo json_encode($result); 


?>