<?

	include "../../lib/common.php"; 
	include "../../lib/alertLib.php";

	$con_Price1 =  trim($conPrice1);
	$con_Price2 = trim($conPrice2);
	$con_Price3 = trim($conPrice3);
	$con_Tax = trim($conTax);

	$DB_con = db1();
	
	if ($mode == "reg") {

		$insQuery = "INSERT INTO TB_CONFIG_EXC (con_Price1, con_Price2, con_Price3, con_Tax ) VALUES (:con_Price1, :con_Price2, :con_Price3, :con_Tax)";
		$stmt = $DB_con->prepare($insQuery);
		$stmt->bindParam("con_Price1", $con_Price1);
		$stmt->bindParam("con_Price2", $con_Price2);
		$stmt->bindParam("con_Price3", $con_Price3);
		$stmt->bindParam("con_Tax", $con_Tax);
		$stmt->execute();
		$DB_con->lastInsertId();

		$preUrl = "configExcReg.php";
		$message = "reg";
		proc_msg($message, $preUrl);


	} else if ($mode == "mod") { //수정일경우

		$upQquery = "UPDATE TB_CONFIG_EXC SET  con_Price1 = :con_Price1, con_Price2 = :con_Price2, con_Price3 = :con_Price3,  con_Tax = :con_Tax  WHERE  idx = :idx  LIMIT 1";
		$upStmt = $DB_con->prepare($upQquery);
		$upStmt->bindParam("con_Price1", $con_Price1);
		$upStmt->bindParam("con_Price2", $con_Price2);
		$upStmt->bindParam("con_Price3", $con_Price3);
		$upStmt->bindParam("con_Tax", $con_Tax);
		$upStmt->bindParam(":idx", $idx);
		$upStmt->execute();

		$preUrl = "configExcReg.php";
		$message = "mod";
		proc_msg($message, $preUrl);


	}


	dbClose($DB_con);
	$stmt = null;
	$upStmt = null;



?>