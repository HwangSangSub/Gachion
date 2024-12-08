<?
	include "../lib/common.php";
	include "../lib/functionDB.php";  //공통 db함수
	include "../lib/functionCoupon.php";    //쿠폰관련 함수
	

	
	$mem_Id = trim($memId);				//아이디
	$file_idx = trim($idx);						//고유번호
	$file_name = trim($filename);			//파일이름
	$e_date = trim($eDate);					//예정일	=> 예 : 2100-01-01
	$p_date = trim($procdate);				//처리일	=> 예 : 2100-01-01
	$n_Addr = trim($nAddr);				//주소
	$d_Addr = trim($dAddr);				//상세주소
	$car_No = trim($carNo);					// 차량번호 => 예 : 52조3740
	$memo_info = trim($memo);			// 메모
	$update_date = DU_TIME_YMDHIS;	//수정일

	//티맵api
	function common_Form($addr){
		$url = 'https://api2.sktelecom.com/tmap/pois?version=1&searchKeyword='.$addr.'&appKey=AIzaSyBAm_wDUkAZwtSDHp8OX3HM8h85tKnSR7c';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);//헤더 정보를 보내도록 함(*필수)
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0'); 
		$contents = curl_exec($ch); 
		$contents_json = json_decode($contents, true); // 결과값을 파싱
		curl_close($ch);
		return $contents_json;	// 최종 위치정보만 가져오기
	}
	//네이버api
	function common_Form2($addr){
		$url = 'https://naveropenapi.apigw.ntruss.com/map-geocode/v2/geocode?query='.$addr.'&X-NCP-APIGW-API-KEY-ID=hiznu965vx&X-NCP-APIGW-API-KEY=2adILXyRAWoc3jpyonesul3e2BDv1A5vHEQPCzwX';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);//헤더 정보를 보내도록 함(*필수)
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0'); 
		$contents = curl_exec($ch); 
		$contents_json = json_decode($contents, true); // 결과값을 파싱
		curl_close($ch);
//		return $contents_json['searchPoiInfo']['pois']['poi']['0'];
		return $contents_json;
	}

	$DB_con = db1();

	//예정일이 빈값이 아닌 경우 예정일을 수정한다.
	$chkQuery ="
		SELECT idx, e_date, p_date, n_Addr, d_Addr, car_No
		FROM TB_EXCEL_DATA
		WHERE idx = :idx AND mem_Id = :mem_Id AND f_Name = :f_Name LIMIT 1
		";
	$chkStmt = $DB_con->prepare($chkQuery);
	$chkStmt->bindparam(":idx",$file_idx);
	$chkStmt->bindparam(":mem_Id",$mem_Id);
	$chkStmt->bindparam(":f_Name",$file_name);
	$chkStmt->execute();
	$chkrow = $chkStmt->fetch(PDO::FETCH_ASSOC);
	$chk_idx = $chkrow['idx'];
	$chk_e_date = $chkrow['e_date'];
	$chk_p_date = $chkrow['p_date'];
	$chk_n_Addr = $chkrow['n_Addr'];
	$chk_d_Addr = $chkrow['d_Addr'];
	$chk_car_No = $chkrow['car_No'];
	if($p_date != ""){
		if($p_date != $chk_p_date){
			$chk_pdate = $p_date;
		}else{
			$chk_pdate = $chk_p_date;
		}
		$upQuery = "UPDATE TB_EXCEL_DATA SET p_date = :p_date, update_date = :update_date WHERE idx = :idx AND mem_Id = :mem_Id AND f_Name = :f_Name LIMIT 1";
		$upStmt = $DB_con->prepare($upQuery);
		$upStmt->bindparam(":p_date",$p_date);
		$upStmt->bindparam(":update_date",$update_date);
		$upStmt->bindparam(":idx",$file_idx);
		$upStmt->bindparam(":mem_Id",$mem_Id);
		$upStmt->bindparam(":f_Name",$file_name);
		$upStmt->execute();
		$result = array("result" => "success", "Msg" => "처리일 수정 완료");
	}else if($e_date != ""){		
		if($e_date != $chk_e_date){
			$chk_edate = $e_date;
		}else{
			$chk_edate = $chk_e_date;
		}
		$upQuery = "UPDATE TB_EXCEL_DATA SET e_date = :e_date, update_date = :update_date WHERE idx = :idx AND mem_Id = :mem_Id AND f_Name = :f_Name LIMIT 1";
		$upStmt = $DB_con->prepare($upQuery);
		$upStmt->bindparam(":e_date",$e_date);
		$upStmt->bindparam(":update_date",$update_date);
		$upStmt->bindparam(":idx",$file_idx);
		$upStmt->bindparam(":mem_Id",$mem_Id);
		$upStmt->bindparam(":f_Name",$file_name);
		$upStmt->execute();
		$result = array("result" => "success", "Msg" => "예정일 수정 완료");
	//주소와 상세주소가 빈값이 아닌 경우 주소와 상세주소를 수정한다.
	}else if($n_Addr != "" || $d_Addr != ""){
		if($n_Addr != $chk_n_Addr){
			$chk_nAddr = $n_Addr;
		}else{
			$chk_nAddr = $chk_n_Addr;
		}
		if($d_Addr != $chk_d_Addr){
			$chk_dAddr = $d_Addr;
		}else{
			$chk_dAddr = $chk_d_Addr;
		}

		$address = $chk_nAddr; 
		$addr = urlencode($address);

		$res = common_Form($addr);
		if(is_array($res['error']) == 1 || $res == '') {						//주소 값이 에러인 경우
			$res2 = common_Form2($addr);
			if($res2['status'] == 'OK'){
				$fLng = $res2['addresses']['0']['x'];		//경도
				if($fLng ==''){
					$Lng = '';
				}else{
					$Lng = $fLng;
				}
				$fLat = $res2['addresses']['0']['y'];		//위도
				if($fLat ==''){
					$Lat = '';
				}else{
					$Lat = $fLat;
				}
			}else{
				$Lng = '0.000000000000000';
				$Lat = '0.000000000000000';
			}
		} else {													//주소 값이 정상인 경우
			$fLng = $res['searchPoiInfo']['pois']['poi']['0']['frontLon'];		//경도
			if($fLng ==''){
				$Lng = '';
			}else{
				$Lng = $fLng;
			}
			$fLat = $res['searchPoiInfo']['pois']['poi']['0']['frontLat'];		//위도
			if($fLat ==''){
				$Lat = '';
			}else{
				$Lat = $fLat;
			}
		}
		$upQuery = "UPDATE TB_EXCEL_DATA SET n_Addr = :n_Addr, d_Addr = :d_Addr, Lng = :Lng, Lat = :Lat, update_date = :update_date WHERE idx = :idx AND mem_Id = :mem_Id AND f_Name = :f_Name LIMIT 1";
		$upStmt = $DB_con->prepare($upQuery);
		$upStmt->bindparam(":n_Addr",$chk_nAddr);
		$upStmt->bindparam(":d_Addr",$chk_dAddr);
		$upStmt->bindparam(":Lng",$Lng);		//경도
		$upStmt->bindparam(":Lat",$Lat);		//위도
		$upStmt->bindparam(":update_date",$update_date);
		$upStmt->bindparam(":idx",$file_idx);
		$upStmt->bindparam(":mem_Id",$mem_Id);
		$upStmt->bindparam(":f_Name",$file_name);
		$upStmt->execute();
		$result = array("result" => "success", "Msg" => "주소 수정 완료");
	}else if($car_No != ''){
		if($car_No != $chk_car_No){
			$chk_carNo = $car_No;
		}else{
			$chk_carNo = $chk_car_No;
		}
		$upQuery = "UPDATE TB_EXCEL_DATA SET car_No = :car_No, update_date = :update_date WHERE idx = :idx AND mem_Id = :mem_Id AND f_Name = :f_Name LIMIT 1";
		$upStmt = $DB_con->prepare($upQuery);
		$upStmt->bindparam(":car_No",$chk_carNo);
		$upStmt->bindparam(":update_date",$update_date);
		$upStmt->bindparam(":idx",$file_idx);
		$upStmt->bindparam(":mem_Id",$mem_Id);
		$upStmt->bindparam(":f_Name",$file_name);
		$upStmt->execute();
		$result = array("result" => "success", "Msg" => "차량번호 수정 완료");
	}else if($memo_info != ''){
		$chkquery2 = "
				SELECT idx
				FROM TB_EXCEL_ETC
				WHERE mem_Id = :mem_Id
					AND f_Name = :f_Name
					AND excel_Idx = :excel_Idx;" ;
		$chkstmt2 = $DB_con->prepare($chkquery2);
		$chkstmt2->bindparam(":mem_Id",$mem_Id);
		$chkstmt2->bindparam(":f_Name",$file_name);
		$chkstmt2->bindparam(":excel_Idx",$chk_idx);
		$chkstmt2->execute();	
		$chknum2 = $chkstmt2->rowCount();
		if($chknum2 < 1){
			$insQuery = "INSERT INTO TB_EXCEL_ETC(mem_Id, excel_Idx, f_Name, memo, reg_Date, update_Date) VALUES(:mem_Id, :excel_Idx, :f_Name, :memo, :reg_Date, :update_Date)";
			$insStmt = $DB_con->prepare($insQuery);
			$insStmt->bindparam(":memo",$memo_info);
			$insStmt->bindparam(":reg_Date",$update_date);
			$insStmt->bindparam(":update_Date",$update_date);
			$insStmt->bindparam(":excel_Idx",$chk_idx);
			$insStmt->bindparam(":mem_Id",$mem_Id);
			$insStmt->bindparam(":f_Name",$file_name);
			$insStmt->execute();
			$result = array("result" => "success", "Msg" => "메모 입력 완료");
		}else{
			$chkrow2 = $chkstmt2->fetch(PDO::FETCH_ASSOC);
			$excel_idx = $chkrow2['idx'];
			$upQuery = "UPDATE TB_EXCEL_ETC SET memo = :memo, update_Date = :update_Date WHERE idx = :idx AND mem_Id = :mem_Id AND f_Name = :f_Name LIMIT 1";
			$upStmt = $DB_con->prepare($upQuery);
			$upStmt->bindparam(":memo",$memo_info);
			$upStmt->bindparam(":idx",$excel_idx);
			$upStmt->bindparam(":mem_Id",$mem_Id);
			$upStmt->bindparam(":f_Name",$file_name);
			$upStmt->bindparam(":update_Date",$update_date);
			$upStmt->execute();
			$result = array("result" => "success", "Msg" => "메모 수정 완료");
		}
	}else if($memo_info == ''){
		$chkquery2 = "
				SELECT idx
				FROM TB_EXCEL_ETC
				WHERE mem_Id = :mem_Id
					AND f_Name = :f_Name
					AND excel_Idx = :excel_Idx;" ;
		$chkstmt2 = $DB_con->prepare($chkquery2);
		$chkstmt2->bindparam(":mem_Id",$mem_Id);
		$chkstmt2->bindparam(":f_Name",$file_name);
		$chkstmt2->bindparam(":excel_Idx",$chk_idx);
		$chkstmt2->execute();	
		$chknum2 = $chkstmt2->rowCount();
		if($chknum2 < 1){
			$insQuery = "INSERT INTO TB_EXCEL_ETC(mem_Id, excel_Idx, f_Name, memo, reg_Date, update_Date) VALUES(:mem_Id, :excel_Idx, :f_Name, '', :reg_Date, :update_Date)";
			$insStmt = $DB_con->prepare($insQuery);
			$insStmt->bindparam(":reg_Date",$update_date);
			$insStmt->bindparam(":update_Date",$update_date);
			$insStmt->bindparam(":excel_Idx",$chk_idx);
			$insStmt->bindparam(":mem_Id",$mem_Id);
			$insStmt->bindparam(":f_Name",$file_name);
			$insStmt->execute();
			$result = array("result" => "success", "Msg" => "메모 입력 완료");
		}else{
			$chkrow2 = $chkstmt2->fetch(PDO::FETCH_ASSOC);
			$excel_idx = $chkrow2['idx'];
			$upQuery = "UPDATE TB_EXCEL_ETC SET memo = '', update_Date = :update_Date WHERE idx = :idx AND mem_Id = :mem_Id AND f_Name = :f_Name LIMIT 1";
			$upStmt = $DB_con->prepare($upQuery);
			$upStmt->bindparam(":idx",$excel_idx);
			$upStmt->bindparam(":mem_Id",$mem_Id);
			$upStmt->bindparam(":f_Name",$file_name);
			$upStmt->bindparam(":update_Date",$update_date);
			$upStmt->execute();
			$result = array("result" => "success", "Msg" => "메모 수정 완료");
		}
	}else{
	//해당 조건 값이 없을 경우
		$result = array("result" => "error", "errorMsg" => "수정사항이 없습니다.");
	}
	

	dbClose($DB_con);
	$upStmt = null;
	$chkStmt = null;
	$chkstmt2 = null;
	$insStmt = null;
	echo json_encode($result); 

?>