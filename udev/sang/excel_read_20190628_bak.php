<?php
include "../lib/common.php";
include "../lib/functionDB.php";  //공통 db함수
include "../lib/functionCoupon.php";    //쿠폰관련 함수
require_once "./PHPExcel-1.8/Classes/PHPExcel.php";

$objPHPExcel = new PHPExcel();
require_once "./PHPExcel-1.8/Classes/PHPExcel/IOFactory.php"; 
// 엑셀 데이터를 담을 배열을 선언한다.
$allData = array();

// 파일의 저장형식이 utf-8일 경우 한글파일 이름은 깨지므로 euc-kr로 변환해준다.
//$filename = iconv("UTF-8", "EUC-KR", $_FILES['excelFile']['name']);

$mem_Id = trim($memId);
$file_name = trim($filename);
$reg_Date = DU_TIME_YMDHIS;

//공통 폼 (맵좌표 확인)
function common_Form($addr){
	https://api2.sktelecom.com/tmap/pois?version={$version}&searchKeyword={$searchKeyword}&appKey={$tmap_appKey}
	$url = 'https://api2.sktelecom.com/tmap/pois?version=1&searchKeyword='.$addr.'&appKey=AIzaSyBAm_wDUkAZwtSDHp8OX3HM8h85tKnSR7c';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);//헤더 정보를 보내도록 함(*필수)
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0'); 
	$contents = curl_exec($ch); 
	$contents_json = json_decode($contents, true); // 결과값을 파싱
	curl_close($ch);
	return $contents_json['searchPoiInfo']['pois']['poi']['0'];	// 최종 위치정보만 가져오기
}	

$DB_con = db1();
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
		  $cnt = 1;
          for($row = 1; $row <= $highestRow; $row++) {
			if($row == 1){
				continue;
			}
            // $rowData가 한줄의 데이터를 셀별로 배열처리 된다.
			// $rowData = $activesheet -> rangeToArray("A" . $row . ":" . $highestColumn . $row, 빈값이면 표현하는 부분, TRUE, FALSE);			
			//$daXte = PHPExcel_Style_NumberFormat::toFormattedString($날짜변수, PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2);

            $rowData = $activesheet -> rangeToArray("A" . $row . ":" . $highestColumn . $row, "", TRUE, FALSE);
			if($rowData[0][2] == "초기배정"){
				$e_State = "0";
			}else{
				$e_State = "1";
			}
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
			if($chknum < 1){
				$result = array("result" => "error");
			}else{
				$mIdx = $chkrow['idx'];
				if($row == 2){
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
				}
				$address =$rowData[0][18]; 
				$addr = urlencode($address);

				$res = common_Form($addr);
				$fLng = $res['frontLon'];		//경도
				if($fLng ==''){
					$Lng = '';
				}else{
					$Lng = $fLng;
				}
				$fLat = $res['frontLat'];		//위도
				if($fLat ==''){
					$Lat = '';
				}else{
					$Lat = $fLat;
				}

				$rdate = $rowData[0][14];
				$r_date = (string)$rdate;
				$query = "
						INSERT INTO TB_EXCEL_DATA (mem_Id, mem_Idx, e_Date, e_Time, e_State, Manager, p_Date, p_Manager, p_Time, c_No, c_Name, car_No, p_Name, b_No, d_Distance, c_Distance, r_Date, p_Tel, h_Tel, z_Code, n_Addr, p_Addr, d_Addr, Lng, Lat, o_State, f_Name, reg_Date)
						VALUES (:mem_Id, :mem_Idx, :e_Date, :e_Time, :e_State, :Manager, :p_Date, :p_Manager, :p_Time, :c_No, :c_Name, :car_No, :p_Name, :b_No, :d_Distance, :c_Distance, :r_Date, :p_Tel, :h_Tel, :z_Code, :n_Addr, :p_Addr, :d_Addr, :Lng, :Lat, :o_State, :f_Name, :reg_Date)" ;
				$stmt = $DB_con->prepare($query);
				$stmt->bindparam(":mem_Id",$mem_Id);
				$stmt->bindparam(":mem_Idx",$mIdx);
				$stmt->bindparam(":e_Date",$rowData[0][0]);
				$stmt->bindparam(":e_Time",$rowData[0][1]);
				$stmt->bindparam(":e_State",$e_State);
				$stmt->bindparam(":Manager",$rowData[0][3]);
				$stmt->bindparam(":p_Date",$rowData[0][4]);
				$stmt->bindparam(":p_Manager",$rowData[0][5]);
				$stmt->bindparam(":p_Time",$rowData[0][6]);
				$stmt->bindparam(":c_No",$rowData[0][7]);
				$stmt->bindparam(":c_Name",$rowData[0][8]);
				$stmt->bindparam(":car_No",$rowData[0][9]);
				$stmt->bindparam(":p_Name",$rowData[0][10]);
				$stmt->bindparam(":b_No",$rowData[0][11]);
				$stmt->bindparam(":d_Distance",$rowData[0][12]);
				$stmt->bindparam(":c_Distance",$rowData[0][13]);
				$stmt->bindparam(":r_Date",$r_date);
				$stmt->bindparam(":p_Tel",$rowData[0][15]);
				$stmt->bindparam(":h_Tel",$rowData[0][16]);
				$stmt->bindparam(":z_Code",$rowData[0][17]);
				$stmt->bindparam(":n_Addr",$address);
				$stmt->bindparam(":p_Addr",$rowData[0][19]);
				$stmt->bindparam(":d_Addr",$rowData[0][20]);
				$stmt->bindparam(":Lng",$Lng);		//경도
				$stmt->bindparam(":Lat",$Lat);		//위도
				$stmt->bindparam(":o_State",$rowData[0][21]);
				$stmt->bindparam(":f_Name",$file_name);
				$stmt->bindparam(":reg_Date",$reg_Date);
				$stmt->execute();
				$result = array("result" => "success", "time" =>$time);//, "data" =>$allData
			  }
            // $rowData에 들어가는 값은 계속 초기화 되기때문에 값을 담을 새로운 배열을 선안하고 담는다.
            $allData[$row] = $rowData[0];
          }
    }
} catch(exception $exception) {
    echo $exception;
}

echo json_encode($result);
dbClose($DB_con);
$chkstmt = null;
$stmt = null;
?>