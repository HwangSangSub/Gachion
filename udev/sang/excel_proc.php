<?
	include "../lib/common.php";
	include "../lib/functionDB.php";  //공통 db함수
	include "../lib/functionCoupon.php";    //쿠폰관련 함수
	
	
	$mem_Id = trim($memId);				// 아이디
	$file_idx = trim($idx);						// 고유번호
	$file_name = trim($filename);			// 파일이름
	$proc_date = date("Y-m-d");			// 처리일	=> 예 : 그날로 처리하기
	//procdate		//처리일		==> 버튼 클릭시 처리하게 수정 ==>>( 처리완료시 처리일을 입력하게 함으로 필요한 파라미터값)
	$p_time = date("H:i");					// 처리시간 (완료처리요청 시간)
	$type = trim($type);						// 취소인 경우에만 "cancel" 전송
	$proc_Date2 = DU_TIME_YMDHIS;		// 처리일

	$DB_con = db1();
	//업로드 된 파일을 우선 확인한다.
	$query = "
			SELECT *
			FROM TB_EXCEL_DATA  
			WHERE idx = :idx 
				AND mem_Id = :mem_Id 
				AND f_Name = :f_Name; " ;
	$stmt = $DB_con->prepare($query);
	$stmt->bindparam(":idx",$file_idx);
	$stmt->bindparam(":mem_Id",$mem_Id);
	$stmt->bindparam(":f_Name",$file_name);
	$stmt->execute();
	$num = $stmt->rowCount();

	if($num < 1)  { //업로드 된 파일이 없는 경우
		$result = array("result" => "error");
	} else {	//업로드 된 파일이 있는 경우
		if($type == "cancel"){
			// 승인 취소 처리
			$upQquery = "UPDATE TB_EXCEL_DATA SET e_State = '0', p_Date = '', p_Manager = '', p_Time = '', pc_Date = :pc_Date WHERE idx = :idx AND mem_Id = :mem_Id AND f_Name = :f_Name LIMIT 1";
			$upStmt = $DB_con->prepare($upQquery);
			$upStmt->bindparam(":idx",$file_idx);
			$upStmt->bindparam(":mem_Id",$mem_Id);
			$upStmt->bindparam(":f_Name",$file_name);
			$upStmt->bindparam(":pc_Date",$proc_Date2);
			$upStmt->execute();
			$result = array("result" => "success");
		}else{
			$nmquery = "
				SELECT mem_Nm
				FROM TB_MEMBERS
				WHERE mem_Id = :mem_Id
					AND b_Disply = 'N' ;" ;
			$nmstmt = $DB_con->prepare($nmquery);
			$nmstmt->bindparam(":mem_Id",$mem_Id);
			$nmstmt->execute();	
			$nmrow = $nmstmt->fetch(PDO::FETCH_ASSOC);
			$mem_Nm = $nmrow['mem_Nm'];

			// 승인 완료 처리
			$upQquery = "UPDATE TB_EXCEL_DATA SET e_State = '1', p_Date = :p_Date, p_Manager = :p_Manager, p_Time = :p_Time WHERE idx = :idx AND mem_Id = :mem_Id AND f_Name = :f_Name LIMIT 1";
			$upStmt = $DB_con->prepare($upQquery);
			$upStmt->bindparam(":p_Date",$proc_date);
			$upStmt->bindparam(":p_Manager",$mem_Nm);
			$upStmt->bindparam(":p_Time",$p_time);
			$upStmt->bindparam(":idx",$file_idx);
			$upStmt->bindparam(":mem_Id",$mem_Id);
			$upStmt->bindparam(":f_Name",$file_name);
			$upStmt->execute();
			$result = array("result" => "success");
		}
	}

	dbClose($DB_con);
	$stmt = null;
	$nmstmt = null;
	$upStmt = null;
	echo json_encode($result); 

?>