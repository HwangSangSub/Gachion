<?
	include "../lib/common.php";
	include "../lib/functionDB.php";  //공통 db함수
	include "../lib/functionCoupon.php";    //쿠폰관련 함수
	
	
	$mem_Id = trim($memId);				//아이디
	$file_name = trim($filename);			//파일이름

	$DB_con = db1();
	//업로드 된 파일을 우선 확인한다.
	$query = "
			SELECT idx
			FROM TB_EXCELDATA_LIST  
			WHERE mem_Id = :mem_Id 
				AND f_Name = :f_Name; " ;
	$stmt = $DB_con->prepare($query);
	$stmt->bindparam(":mem_Id",$mem_Id);
	$stmt->bindparam(":f_Name",$file_name);
	$stmt->execute();
	$num = $stmt->rowCount();

	if($num < 1)  { //업로드 된 파일이 없는 경우
		$result = array("result" => "error");
	} else {	//업로드 된 파일이 있는 경우
		$chkquery = "
				SELECT idx, e_Date, e_State, p_Date, c_No, c_Name, car_No, p_Name, b_No, d_Distance, c_Distance, r_Date, p_Tel, n_Addr, Lng, Lat, o_State
				FROM TB_EXCELDATA
				WHERE mem_Id = :mem_Id
					AND f_Name = :f_Name
				ORDER BY e_Date;" ;
		$chkstmt = $DB_con->prepare($chkquery);
		$chkstmt->bindparam(":mem_Id",$mem_Id);
		$chkstmt->bindparam(":f_Name",$file_name);
		$chkstmt->execute();	
		$chknum = $chkstmt->rowCount();
		if($chknum > 1){
			$data  = [];
			while($chkrow = $chkstmt->fetch(PDO::FETCH_ASSOC)){
				$idx = $chkrow['idx'];											// 고유번호
				$e_Date = $chkrow['e_Date'];									// 예정일
				$e_State = $chkrow['e_State'];									// 상태
				$p_Date = $chkrow['p_Date'];									// 처리일
				if($p_Date == ''){
					$p_Date = "";
				}else{
					$p_Date = date("Y-m-d", strtotime($p_Date));
				}
				$c_No = $chkrow['c_No'];										// 계약번호
				$c_Name = $chkrow['c_Name'];								// 고객명
				$car_No = $chkrow['car_No'];									// 차량번호
				$p_Name = $chkrow['p_Name'];								// 상품명
				$b_No = $chkrow['b_No'];										// 본수
				$d_Distance = $chkrow['d_Distance'];						// 주행거리
				if($d_Distance == ''){
					$d_Distance = "";
				}
				$c_Distance = $chkrow['c_Distance'];							// 주행거리(계약)
				$r_Date = $chkrow['r_Date'];									// 장착일자
				$p_Tel = $chkrow['p_Tel'];										// 핸드폰
				$n_Addr = $chkrow['n_Addr'];									// 주소
				$Lng = $chkrow['Lng'];											// 경도
				$Lat = $chkrow['Lat'];											// 위도
				$o_State = $chkrow['o_State'];									// 전월전체여부
				$result = array("idx" => $idx, "e_Date" => $e_Date, "e_State" => $e_State, "p_Date" => $p_Date, "c_Name" => $c_Name, "c_Name" => $c_Name, "car_No" => $car_No, "p_Name" => $p_Name, "b_No" => $b_No, "d_Distance" => $d_Distance, "c_Distance" => $c_Distance, "r_Date" => $r_Date, "p_Tel" => $p_Tel, "n_Addr" => $n_Addr, "Lng" => $Lng, "Lat" => $Lat, "o_State" => $o_State);
				array_push($data, $result);
			}        
			$result = array("result" => "success", "data" => $data);
		} else { 
			$result = array("result" => "error", "errorMsg" => "해당 파일 없습니다. 확인 후 다시 시도해주세요.");
		} 
	}

	dbClose($DB_con);
	$stmt = null;
	$chkstmt = null;
	$upStmt2 = null;
	echo json_encode($result); 

?>