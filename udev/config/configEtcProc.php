<?

	include "../lib/common.php"; 
	include "../../lib/alertLib.php";

	$con_ImgUp =  trim($conImgUp);
	$con_TxtFilter = trim($conTxtFilter);
	$con_Agree = trim($conAgree);
	$con_Privacy = trim($conPrivacy);

	$DB_con = db1();
	
	if ($mode == "reg") {

		$insQuery = "INSERT INTO TB_CONFIG_ETC (con_ImgUp, con_TxtFilter, con_Agree, con_Privacy ) VALUES (:con_ImgUp, :con_TxtFilter, :con_Agree, :con_Privacy)";
		$stmt = $DB_con->prepare($insQuery);
		$stmt->bindParam("con_ImgUp", $con_ImgUp);
		$stmt->bindParam("con_TxtFilter", $con_TxtFilter);
		$stmt->bindParam("con_Agree", $con_Agree);
		$stmt->bindParam("con_Privacy", $con_Privacy);
		$stmt->execute();
		$DB_con->lastInsertId();

		$preUrl = "configEtcReg.php";
		$message = "reg";
		proc_msg($message, $preUrl);


	} else if ($mode == "mod") { //수정일경우

		$upQuery = "UPDATE TB_CONFIG_ETC SET  con_ImgUp = :con_ImgUp, con_TxtFilter = :con_TxtFilter, con_Agree = :con_Agree,  con_Privacy = :con_Privacy  WHERE  idx = :idx  LIMIT 1";
		$upStmt = $DB_con->prepare($upQuery);
		$upStmt->bindParam("con_ImgUp", $con_ImgUp);
		$upStmt->bindParam("con_TxtFilter", $con_TxtFilter);
		$upStmt->bindParam("con_Agree", $con_Agree);
		$upStmt->bindParam("con_Privacy", $con_Privacy);
		$upStmt->bindParam(":idx", $idx);
		$upStmt->execute();

		$preUrl = "configEtcReg.php";
		$message = "mod";
		proc_msg($message, $preUrl);


	}
	dbClose($DB_con);
	$stmt = null;
	$upStmt = null;
