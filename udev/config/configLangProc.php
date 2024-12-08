<?

	include "../lib/common.php"; 
	include "../lib/alertLib.php";

	$idx =  trim($idx);
	$korea = trim($korea);
	$english = trim($english);
	$DB_con = db1();
	
	if ($mode == "reg") {

		$insQuery = "INSERT INTO TB_LANGUAGE (korea, english, reg_Date) VALUES (:korea, :english, NOW());";
		$stmt = $DB_con->prepare($insQuery);
		$stmt->bindParam(":korea", $korea);
		$stmt->bindParam(":english", $english);
		$stmt->execute();
		$pIdx = $DB_con->lastInsertId();

		$preUrl = "configLangReg.php?idx=".$pIdx;
		$message = "reg";
		proc_msg($message, $preUrl);


	} else if ($mode == "mod") {					// 수정일경우

		$upQquery = "UPDATE TB_LANGUAGE SET  korea = :korea, english = :english WHERE  idx = :idx  LIMIT 1;";
		$upStmt = $DB_con->prepare($upQquery);
		$upStmt->bindParam(":korea", $korea);
		$upStmt->bindParam(":english", $english);
		$upStmt->execute();

		$preUrl = "configLangReg.php?idx=".$idx;
		$message = "mod";
		proc_msg($message, $preUrl);

	} else if ($mode == "del") {					// 삭제일경우

		$upQquery = "DELETE FROM TB_LANGUAGE WHERE  idx = :idx  LIMIT 1;";
		$upStmt = $DB_con->prepare($upQquery);
		$upStmt->bindParam(":idx", $idx);
		$upStmt->execute();

		$preUrl = "configLangReg.php?idx=".$idx;
		$message = "del";
		proc_msg($message, $preUrl);
	}
	dbClose($DB_con);
	$stmt = null;
	$upStmt = null;



?>