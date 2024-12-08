<?php
include "../lib/common.php";
include "../lib/functionDB.php";  //공통 db함수
include "../lib/functionCoupon.php";    //쿠폰관련 함수
require_once "./PHPExcel-1.8/Classes/PHPExcel.php";
require_once "./PHPExcel-1.8/Classes/PHPExcel/IOFactory.php"; 

//공통 폼 (맵좌표 확인)
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
//		return $contents_json['searchPoiInfo']['pois']['poi']['0'];	// 최종 위치정보만 가져오기
	return $contents_json;
}
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
$objPHPExcel = new PHPExcel();

// 엑셀 데이터를 담을 배열을 선언한다.
$allData = array();
$rData = array();

// 파일의 저장형식이 utf-8일 경우 한글파일 이름은 깨지므로 euc-kr로 변환해준다.
$filename = iconv("UTF-8", "EUC-KR", $_FILES['upload']['name']);
$mem_Id = trim($memId);
$file_name = trim(basename($_FILES['upload']['name']));
$reg_Date = DU_TIME_YMDHIS;
$DB_con = db1();
$chkquery = "
	SELECT idx
	FROM TB_MEMBERS
	WHERE mem_Id = :mem_Id
		AND b_Disply = 'N' ;" ;
$chkstmt = $DB_con->prepare($chkquery);
$chkstmt->bindparam(":mem_Id",$mem_Id);
$chkstmt->execute();	
$chkrow = $chkstmt->fetch(PDO::FETCH_ASSOC);
$chknum = $chkstmt->rowCount();
$mIdx = $chkrow['idx'];

$chklistquery = "
		SELECT idx
		FROM TB_EXCEL_LIST
		WHERE mem_Id = :mem_Id
			AND f_Name = :f_Name ;" ;
$chkliststmt = $DB_con->prepare($chklistquery);
$chkliststmt->bindparam(":mem_Id",$mem_Id);
$chkliststmt->bindparam(":f_Name",$file_name);
$chkliststmt->execute();	
$chklistnum = $chkliststmt->rowCount();
if($chklistnum < 1){
	$listquery = "
			INSERT INTO TB_EXCEL_LIST (mem_Id, mem_Idx, f_Name, u_Date, reg_Date)
			VALUES (:mem_Id, :mem_Idx, :f_Name, :u_Date, :reg_Date)" ;
	$liststmt = $DB_con->prepare($listquery);
	$liststmt->bindparam(":mem_Id",$mem_Id);
	$liststmt->bindparam(":mem_Idx",$mIdx);
	$liststmt->bindparam(":f_Name",$file_name);
	$liststmt->bindparam(":u_Date",$reg_Date);
	$liststmt->bindparam(":reg_Date",$reg_Date);
	$liststmt->execute();
	try {

		// 업로드한 PHP 파일을 읽어온다.
		$objPHPExcel = PHPExcel_IOFactory::load("./excel_data/".$file_name);
		$sheetsCount = $objPHPExcel -> getSheetCount();

		// 시트Sheet별로 읽기
		for($sheet = 0; $sheet < $sheetsCount; $sheet++) {

			  $objPHPExcel -> setActiveSheetIndex($sheet);
			  $activesheet = $objPHPExcel -> getActiveSheet();
			  $highestRow = $activesheet -> getHighestRow();             // 마지막 행
			  $highestColumn = $activesheet -> getHighestColumn();    // 마지막 컬럼

			  // 한줄읽기
			  for($row = 1; $row <= $highestRow; $row++) {
					if($row == 1){
						continue;
					}
					$i = $row-1;
				// $rowData가 한줄의 데이터를 셀별로 배열처리 된다.
				$rowData = $activesheet -> rangeToArray("A" . $row . ":" . $highestColumn . $row, NULL, TRUE, FALSE);
				$r_Date = $activesheet->getCell('O' . $row)->getValue();
				$rDate = PHPExcel_Style_NumberFormat::toFormattedString($r_Date, 'YYYY-MM-DD'); // 날짜 형태의 셀을 읽을때는 toFormattedString를 사용한다.
				$r_Data = array_push($rData, $rDate);
				// $rowData에 들어가는 값은 계속 초기화 되기때문에 값을 담을 새로운 배열을 선안하고 담는다.
				$allData[$i] = $rowData[0];
			  }
		}
		echo count($allData);
		echo count($rData);
		print_r($rData);
		$acnt = count($allData);
		for($e = 1; $e <= $acnt; $e++){
			if($allData[$e]['2'] == "초기배정"){
				$e_State = "0";
			}else{
				$e_State = "1";	//점검완료
			}
			//$address = $allData[$e]['18']; 
			//$addr = urlencode($address);
			//$res = common_Form($addr);
			if(is_array($res['error']) == 1 || $res == '') {						//주소 값이 에러인 경우
				//$res2 = common_Form2($addr);
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
			
			$query = "
					INSERT INTO TB_EXCEL_DATA (mem_Id, mem_Idx, e_Date, e_Time, e_State, Manager, p_Date, p_Manager, p_Time, c_No, c_Name, car_No, p_Name, b_No, d_Distance, c_Distance, r_Date, p_Tel, h_Tel, z_Code, n_Addr, p_Addr, d_Addr, Lng, Lat, o_State, f_Name, reg_Date)
					VALUES (:mem_Id, :mem_Idx, :e_Date, :e_Time, :e_State, :Manager, :p_Date, :p_Manager, :p_Time, :c_No, :c_Name, :car_No, :p_Name, :b_No, :d_Distance, :c_Distance, :r_Date, :p_Tel, :h_Tel, :z_Code, :n_Addr, :p_Addr, :d_Addr, :Lng, :Lat, :o_State, :f_Name, :reg_Date)" ;
			$stmt = $DB_con->prepare($query);
			$stmt->bindparam(":mem_Id",$mem_Id);
			$stmt->bindparam(":mem_Idx",$mIdx);
			$stmt->bindparam(":e_Date",$allData[$e][0]);
			$stmt->bindparam(":e_Time",$allData[$e][1]);
			$stmt->bindparam(":e_State",$e_State);
			$stmt->bindparam(":Manager",$allData[$e][3]);
			$stmt->bindparam(":p_Date",$allData[$e][4]);
			$stmt->bindparam(":p_Manager",$allData[$e][5]);
			$stmt->bindparam(":p_Time",$allData[$e][6]);
			$stmt->bindparam(":c_No",$allData[$e][7]);
			$stmt->bindparam(":c_Name",$allData[$e][8]);
			$stmt->bindparam(":car_No",$allData[$e][9]);
			$stmt->bindparam(":p_Name",$allData[$e][10]);
			$stmt->bindparam(":b_No",$allData[$e][11]);
			$stmt->bindparam(":d_Distance",$allData[$e][12]);
			$stmt->bindparam(":c_Distance",$allData[$e][13]);
			$stmt->bindparam(":r_Date",$rData[$e-1]);
			$stmt->bindparam(":p_Tel",$allData[$e][15]);
			$stmt->bindparam(":h_Tel",$allData[$e][16]);
			$stmt->bindparam(":z_Code",$allData[$e][17]);
			$stmt->bindparam(":n_Addr",$allData[$e][18]);
			$stmt->bindparam(":p_Addr",$allData[$e][19]);
			$stmt->bindparam(":d_Addr",$allData[$e][20]);
			$stmt->bindparam(":Lng",$Lng);		//경도
			$stmt->bindparam(":Lat",$Lat);		//위도
			$stmt->bindparam(":o_State",$allData[$e][21]);
			$stmt->bindparam(":f_Name",$file_name);
			$stmt->bindparam(":reg_Date",$reg_Date);
			$stmt->execute();
			
		}
		$result = array("result" => "success");

	} catch(exception $exception) {
		echo $exception;
	}
}else{
	$result = array("result" => "error", "errorMsg" => "이미 업로드한 엑셀파일입니다. 확인 후 다시 시도해주세요.");
}
echo json_encode($result);
/*
echo "<pre>";
print_r($allData);
echo "</pre>";
*/
dbClose($DB_con);
$liststmt = null;
$chkliststmt = null;
$chkstmt = null;
$stmt = null;
?>