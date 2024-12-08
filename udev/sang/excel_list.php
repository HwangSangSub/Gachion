<?
	include "../lib/common.php";
	include "../lib/functionDB.php";  //공통 db함수
	include "../lib/functionCoupon.php";    //쿠폰관련 함수
	
	 
	$mem_Id = trim($memId);				// 아이디
	$datebit = trim($datebit);				// 검색기간 (1: 최근 1주일, 2: 최근 1달, 3: 최근 3달)
	$nowDate =  date("Y-m-d");			// 현재날짜 조회
	 
	if($datebit == ''){
		$date_bit = '1';
	}else{
		$date_bit = $datebit;
	}

	if($date_bit == '1'){
		$timestamp = strtotime("-7 days", strtotime($nowDate));
	}else if($date_bit == '2'){
		$timestamp = strtotime("-30 days", strtotime($nowDate));
	}else if($date_bit == '3'){
		$timestamp = strtotime("-90 days", strtotime($nowDate));
	}

	//사용일! 입력하기
	$seachDate = date("Y-m-d", $timestamp);	

	$dateQuery = "";

	$DB_con = db1();
	//업로드 된 파일을 우선 확인한다.
	$chkquery = "
			SELECT idx, mem_Id, mem_Idx, f_Name, u_Date, s_Date, reg_Date
			FROM TB_EXCEL_LIST
			WHERE mem_Id = :mem_Id
				AND (DATE_FORMAT(reg_Date,'%Y-%m-%d') >= :seachDate AND DATE_FORMAT(reg_Date,'%Y-%m-%d') <= :nowDate)
			ORDER BY reg_Date DESC, idx DESC;" ;
	$chkstmt = $DB_con->prepare($chkquery);
	$chkstmt->bindparam(":mem_Id",$mem_Id);
	$chkstmt->bindparam(":seachDate",$seachDate);
	$chkstmt->bindparam(":nowDate",$nowDate);
	$chkstmt->execute();	
	$chknum = $chkstmt->rowCount();
	if($chknum > 1){
		$data  = [];
		while($chkrow = $chkstmt->fetch(PDO::FETCH_ASSOC)){
			$idx = $chkrow['idx'];											// 고유번호
			$mem_Id = $chkrow['mem_Id'];									// 회원아이디
			$mem_Idx = $chkrow['mem_Idx'];							// 회원고유번호
			$f_Name = $chkrow['f_Name'];							// 회원고유번호
			$u_Date = $chkrow['u_Date'];									// 업로드일
			if($u_Date == ''){
				$u_Date = "-";
			}else{
				$u_Date = date("Y-m-d H:i:s", strtotime($u_Date));
			}
			$s_Date = $chkrow['s_Date'];									// 다운로드일
			if($s_Date == ''){
				$s_Date = "-";
			}else{
				$s_Date = date("Y-m-d H:i:s", strtotime($s_Date));
			}
			$result = array("f_Name" => $f_Name, "u_Date" => $u_Date, "s_Date" => $s_Date);
			array_push($data, $result);
		}
		$result = array("result" => "success", "data" => $data);
	} else { 
		$result = array("result" => "error", "errorMsg" => "등록된 파일이 없습니다.");
	} 

	dbClose($DB_con);
	$chkstmt = null;
	echo json_encode($result); 

?>