<?
	include "../../lib/common.php";
	include "../../../lib/alertLib.php"; 
	include "../../../lib/thumbnail.lib.php";   //썸네일
	
	$mode = trim($mode);
	$page =  trim($page);
	$qstr = trim($qstr);
	$idx  = trim($idx);
	$code_Div = trim($code_Div);
	$code_Sub_Div = trim($code_Sub_Div);
	$code_Name = trim($code_Name);
	//$code_ImgFile = trim($code_ImgFile);
	$use_Bit = trim($use_Bit);
	$reg_Date = DU_TIME_YMDHIS;
	
	$DB_con = db1();
	if ($mode == "reg") {  //등록일 경우
		$code_query = "";
		$code_query = "SELECT code FROM TB_CONFIG_CODE WHERE code_Div = 'report' ORDER BY code DESC LIMIT 1;" ;
		$code_stmt = $DB_con->prepare($code_query);
		$code_stmt->execute();
		$code_Num = $code_stmt->rowCount();
		
		if($code_Num < 1){ //아닐경우
			$ins_code = "01";
		}else{
			$code_row = $code_stmt->fetch(PDO::FETCH_ASSOC);
			$code = $code_row['code'];	
			$inscode = (int)$code + 1;
			if(strlen($inscode) == 1){
				$ins_code = "0".$inscode;
			}else{
				$ins_code = $inscode;
			}
		}
		if($code_Sub_Div != ''){
			$insQuery = "INSERT INTO TB_CONFIG_CODE ( code_Div, code, code_Name, use_Bit, reg_Date ) VALUES ( 'report', :code, :code_Name, :use_Bit, :reg_Date )";
			$stmt = $DB_con->prepare($insQuery);
			$stmt->bindParam(":code", $ins_code);
			$stmt->bindParam(":code_Name", $code_Name);		
			$stmt->bindParam(":use_Bit", $use_Bit);		
			$stmt->bindParam(":reg_Date", $reg_Date);
			$stmt->execute();
			$mIdx = $DB_con->lastInsertId();
		}else{
			$insQuery = "INSERT INTO TB_CONFIG_CODE ( code_Div, code, code_Name, use_Bit, reg_Date ) VALUES ( 'report', :code, :code_Name, :use_Bit, :reg_Date )";
			$stmt = $DB_con->prepare($insQuery);
			$stmt->bindParam(":code", $ins_code);
			$stmt->bindParam(":code_Name", $code_Name);		
			$stmt->bindParam(":use_Bit", $use_Bit);		
			$stmt->bindParam(":reg_Date", $reg_Date);
			$stmt->execute();
			$mIdx = $DB_con->lastInsertId();
		}

		$preUrl = "report_list.php?page=$page&$qstr";
		$message = "reg";
		proc_msg($message, $preUrl);
	} else if ($mode == "mod") { //수정일경우

		$upQquery = "UPDATE TB_CONFIG_CODE code_Name = :code_Name, use_Bit = :use_Bit WHERE idx = :idx LIMIT 1";
		$upStmt = $DB_con->prepare($upQquery);
		$upStmt->bindparam(":code_Name",$code_Name);
		$upStmt->bindParam(":use_Bit", $use_Bit);
		$upStmt->bindParam(":idx", $idx);
		$upStmt->execute();

		$preUrl = "report_list.php?page=$page&$qstr";
		$message = "mod";
		proc_msg($message, $preUrl);

   	} else {  //삭제일경우

		$delQquery = "DELETE FROM TB_CONFIG_CODE WHERE idx = :idx LIMIT 1;";

		$delStmt = $DB_con->prepare($delQquery);
		$delStmt->bindParam(":idx", $idx);
		$delStmt->execute();

		$preUrl = "report_list.php?page=$page&$qstr";
		$message = "del";
		proc_msg($message, $preUrl);
	}
	
	dbClose($DB_con);
	$code_stmt = null;
	$stmt = null;
	$upStmt = null;	
	$delStmt = null;
	
?>