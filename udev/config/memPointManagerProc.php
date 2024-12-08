<?
	include "../../lib/common.php"; 
	include "../../lib/alertLib.php";
	
	$mode = trim($mode);
	$page =  trim($page);
	$qstr = trim($qstr);
	$idx  = trim($idx);
	$point_Title = trim($point_Title);
	$point_Num = trim($point_Num);
	
	$DB_con = db1();

	if ($mode == "reg") {  //등록일 경우

			$insQuery = "INSERT INTO TB_CPOINT ( point_Title, point_Num ) VALUES ( :point_Title, :point_Num )";
			$stmt = $DB_con->prepare($insQuery);
			$stmt->bindParam(":point_Title", $point_Title);
			$stmt->bindParam(":point_Num", $point_Num);		
			$stmt->execute();
			$DB_con->lastInsertId();

			$preUrl = "memPointManagerList.php?page=$page&$qstr";
			$message = "reg";
			proc_msg($message, $preUrl);


	} else if ($mode == "mod") { //수정일경우

			$upQquery = "UPDATE TB_CPOINT SET point_Title = :point_Title, point_Num = :point_Num WHERE idx = :idx LIMIT 1";
			$upStmt = $DB_con->prepare($upQquery);
			$upStmt->bindparam(":point_Title",$point_Title);
			$upStmt->bindParam(":point_Num", $point_Num);
			$upStmt->bindParam(":idx", $idx);
			$upStmt->execute();

			$preUrl = "memPointManagerList.php?page=$page&$qstr";
			$message = "mod";
			proc_msg($message, $preUrl);

   	} else {  //삭제일경우

		    $check = trim($chk);
			$array = explode('/', $check);

			foreach($array as $k=>$v) {
				$idx = $v;
				$delQquery = "DELETE FROM TB_CPOINT WHERE idx = :idx LIMIT 1";

				$delStmt = $DB_con->prepare($delQquery);
				$delStmt->bindParam(":idx", $idx);
				$delStmt->execute();

			}

			echo "success";

	}
	
	dbClose($DB_con);
	$stmt = null;
	$upStmt = null;	
	$delStmt = null;
	
?>