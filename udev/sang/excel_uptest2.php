<?
ini_set('max_execution_time', 3000);
ignore_user_abort(true);
set_time_limit(0);
ini_set('memory_limit','2045M');
include "../lib/common.php";
include "../lib/functionDB.php";  //공통 db함수
include "../lib/functionCoupon.php";    //쿠폰관련 함수
require_once "./PHPExcel-1.8/Classes/PHPExcel.php";
require_once "./PHPExcel-1.8/Classes/PHPExcel/IOFactory.php"; 

if(isset($_FILES['upload'])){ 
	$target = "../excel/excel_data/".basename($_FILES['upload']['name']) ; 
	if(move_uploaded_file($_FILES['upload']['tmp_name'],$target)) {
		$objPHPExcel = new PHPExcel();
		// 엑셀 데이터를 담을 배열을 선언한다.
		$allData = array();
		$mem_Id = trim($memId);
		$file_name = trim(basename($_FILES['upload']['name']));
		$reg_Date = DU_TIME_YMDHIS;
		$DB_con = db1();

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
			/*
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
			*/
			// 업로드 된 엑셀 형식에 맞는 Reader객체를 만든다.
			$objReader = PHPExcel_IOFactory::createReaderForFile("./excel_data/".$file_name);
			// 읽기전용으로 설정
			$objReader->setReadDataOnly(true);
			// 엑셀파일을 읽는다
			$objExcel = $objReader->load("./excel_data/".$file_name);
			// 첫번째 시트를 선택
			$objExcel->setActiveSheetIndex(0);
			$objWorksheet = $objExcel->getActiveSheet();
			$rowIterator = $objWorksheet->getRowIterator();
			foreach ($rowIterator as $row) { // 모든 행에 대해서
					   $cellIterator = $row->getCellIterator();
					   $cellIterator->setIterateOnlyExistingCells(false); 
			}
			$maxRow = $objWorksheet->getHighestRow();
			//배열만들기
			$e_data = array();
			$e_data2 = "";
			if((int)$maxRow != 0){
			}
			for ($i = 1 ; $i <= $maxRow; $i++) {
				if($i == 1){
					continue;
				}
				$e_Date = $objWorksheet->getCell('A' . $i)->getValue();
				if($e_Date ==''){
					$e_Date = '';
				}else{
					$e_Date = $e_Date;
				}
				$e_Time = $objWorksheet->getCell('B' . $i)->getValue();
				if($e_Time ==''){
					$e_Time = '';
				}else{
					$e_Time = $e_Time;
				}
				$e_State = $objWorksheet->getCell('C' . $i)->getValue();
				if($e_State ==''){
					$e_State = '';
				}else{
					$e_State = $e_State;
				}
				$Manager = $objWorksheet->getCell('D' . $i)->getValue();
				if($Manager ==''){
					$Manager= '';
				}else{
					$Manager = $Manager;
				}
				$p_Date = $objWorksheet->getCell('E' . $i)->getValue();
				if($p_Date ==''){
					$p_Date = '';
				}else{
					$p_Date = $p_Date;
				}
				$p_Manager = $objWorksheet->getCell('F' . $i)->getValue();
				if($p_Manager ==''){
					$p_Manager = '';
				}else{
					$p_Manager = $p_Manager;
				}
				$p_Time = $objWorksheet->getCell('G' . $i)->getValue();
				if($p_Time ==''){
					$p_Time = '';
				}else{
					$p_Time = $p_Time;
				}
				$c_No = $objWorksheet->getCell('H' . $i)->getValue();
				if($c_No ==''){
					$c_No = '';
				}else{
					$c_No = $c_No;
				}
				$c_Name = $objWorksheet->getCell('I' . $i)->getValue();
				if($c_Name ==''){
					$c_Name = '';
				}else{
					$c_Name = $c_Name;
				}
				$car_No = $objWorksheet->getCell('J' . $i)->getValue();
				if($car_No ==''){
					$car_No = '';
				}else{
					$car_No = $car_No;
				}
				$p_Name = $objWorksheet->getCell('K' . $i)->getValue();
				if($p_Name ==''){
					$p_Name = '';
				}else{
					$p_Name = $p_Name;
				}
				$b_No = $objWorksheet->getCell('L' . $i)->getValue();
				if($b_No ==''){
					$b_No = '';
				}else{
					$b_No = $b_No;
				}
				$d_Distance = $objWorksheet->getCell('M' . $i)->getValue();
				if($d_Distance ==''){
					$d_Distance = '';
				}else{
					$d_Distance = $d_Distance;
				}
				$c_Distance = $objWorksheet->getCell('N' . $i)->getValue();
				if($c_Distance ==''){
					$c_Distance = '';
				}else{
					$c_Distance = $c_Distance;
				}
				$r_Date = $objWorksheet->getCell('O' . $i)->getValue();
				$rDate = PHPExcel_Style_NumberFormat::toFormattedString($r_Date, 'YYYY-MM-DD'); // 날짜 형태의 셀을 읽을때는 toFormattedString를 사용한다.
				$p_Tel = $objWorksheet->getCell('P' . $i)->getValue();
				if($p_Tel ==''){
					$p_Tel = '';
				}else{
					$p_Tel = $p_Tel;
				}
				$h_Tel = $objWorksheet->getCell('Q' . $i)->getValue();
				if($h_Tel ==''){
					$h_Tel = '';
				}else{
					$h_Tel = $h_Tel;
				}
				$z_Code = $objWorksheet->getCell('R' . $i)->getValue();
				if($z_Code ==''){
					$z_Code = '';
				}else{
					$z_Code = $z_Code;
				}
				$n_Addr = $objWorksheet->getCell('S' . $i)->getValue();
				if($n_Addr ==''){
					$n_Addr = '';
				}else{
					$n_Addr = $n_Addr;
				}
				$p_Addr = $objWorksheet->getCell('T' . $i)->getValue();
				if($p_Addr ==''){
					$p_Addr = '';
				}else{
					$p_Addr = $p_Addr;
				}
				$d_Addr = $objWorksheet->getCell('U' . $i)->getValue();
				if($d_Addr ==''){
					$d_Addr = '';
				}else{
					$d_Addr = $d_Addr;
				}
				$o_State = $objWorksheet->getCell('V' . $i)->getValue();
				if($o_State ==''){
					$o_State = '';
				}else{
					$o_State = $o_State;
				}
				if($e_State == "초기배정"){
					$e_State = "0";
				}else{
					$e_State = "1";	//점검완료
				}
				$address = $n_Addr; 
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
				// 엑셀값을 배열로 만들기
				$r_data = ('("'.$mem_Id.'",'. $mIdx.',"'. $e_Date.'","'. $e_Time.'","'. $e_State.'","'. $Manager.'","'. $p_Date.'","'. $p_Manager.'","'. $p_Time.'","'. $c_No.'","'. $c_Name.'","'. $car_No.'","'. $p_Name.'","'. $b_No.'","'. $d_Distance.'","'. $c_Distance.'","'. $rDate.'","'. $p_Tel.'","'. $h_Tel.'","'. $z_Code.'","'. $n_Addr.'","'. $p_Addr.'","'. $d_Addr.'",'. $Lng.','. $Lat.',"'. $o_State.'","'. $file_name.'","'. $reg_Date.'")');
		
				array_push($e_data, $r_data);
				//, "data" =>$allData
				// $rowData에 들어가는 값은 계속 초기화 되기때문에 값을 담을 새로운 배열을 선안하고 담는다.
			  }
			 // echo "<Br><br> r_data : ";
			 // print_r($r_data);
			 // echo "<Br><br> e_data : ";
			 // print_r($e_data);
			  $t_data = implode( ',', $e_data );
			 //echo "<Br><br> t_data : ".$t_data;
			  $r_cnt = count($e_data);

			  $query = "
						INSERT INTO TB_EXCEL_DATA (mem_Id, mem_Idx, e_Date, e_Time, e_State, Manager, p_Date, p_Manager, p_Time, c_No, c_Name, car_No, p_Name, b_No, d_Distance, c_Distance, r_Date, p_Tel, h_Tel, z_Code, n_Addr, p_Addr, d_Addr, Lng, Lat, o_State, f_Name, reg_Date)	
						VALUES ".$t_data.";" ;
			//echo $query;
			 $stmt = $DB_con->prepare($query);
			 $stmt->execute();

			  $result = array("result" => "success");
			  echo json_encode($result);
			 /*for($j = 1 ; $j <= $r_cnt; $j++){
				$jj = (int)$j -1;
				$query = "
						INSERT INTO TB_EXCEL_DATA (mem_Id, mem_Idx, e_Date, e_Time, e_State, Manager, p_Date, p_Manager, p_Time, c_No, c_Name, car_No, p_Name, b_No, d_Distance, c_Distance, r_Date, p_Tel, h_Tel, z_Code, n_Addr, p_Addr, d_Addr, Lng, Lat, o_State, f_Name, reg_Date)
						VALUES (:mem_Id, :mem_Idx, :e_Date, :e_Time, :e_State, :Manager, :p_Date, :p_Manager, :p_Time, :c_No, :c_Name, :car_No, :p_Name, :b_No, :d_Distance, :c_Distance, :r_Date, :p_Tel, :h_Tel, :z_Code, :n_Addr, :p_Addr, :d_Addr, :Lng, :Lat, :o_State, :f_Name, :reg_Date)" ;
				$stmt = $DB_con->prepare($query);
				$stmt->bindparam(":mem_Id",$e_data[$jj]["mem_Id"]);
				$stmt->bindparam(":mem_Idx",$e_data[$jj]["mIdx"]);
				$stmt->bindparam(":e_Date",$e_data[$jj]["e_Date"]);
				$stmt->bindparam(":e_Time",$e_data[$jj]["e_Time"]);
				$stmt->bindparam(":e_State",$e_data[$jj]["e_State"]);
				$stmt->bindparam(":Manager",$e_data[$jj]["Manager"]);
				$stmt->bindparam(":p_Date",$e_data[$jj]["p_Date"]);
				$stmt->bindparam(":p_Manager",$e_data[$jj]["p_Manager"]);
				$stmt->bindparam(":p_Time",$e_data[$jj]["p_Time"]);
				$stmt->bindparam(":c_No",$e_data[$jj]["c_No"]);
				$stmt->bindparam(":c_Name",$e_data[$jj]["c_Name"]);
				$stmt->bindparam(":car_No",$e_data[$jj]["car_No"]);
				$stmt->bindparam(":p_Name",$e_data[$jj]["p_Name"]);
				$stmt->bindparam(":b_No",$e_data[$jj]["b_No"]);
				$stmt->bindparam(":d_Distance",$e_data[$jj]["d_Distance"]);
				$stmt->bindparam(":c_Distance",$e_data[$jj]["c_Distance"]);
				$stmt->bindparam(":r_Date",$e_data[$jj]["rDate"]);
				$stmt->bindparam(":p_Tel",$e_data[$jj]["p_Tel"]);
				$stmt->bindparam(":h_Tel",$e_data[$jj]["h_Tel"]);
				$stmt->bindparam(":z_Code",$e_data[$jj]["z_Code"]);
				$stmt->bindparam(":n_Addr",$e_data[$jj]["n_Addr"]);
				$stmt->bindparam(":p_Addr",$e_data[$jj]["p_Addr"]);
				$stmt->bindparam(":d_Addr",$e_data[$jj]["d_Addr"]);
				$stmt->bindparam(":Lng",$e_data[$jj]["Lng"]);		//경도
				$stmt->bindparam(":Lat",$e_data[$jj]["Lat"]);		//위도
				$stmt->bindparam(":o_State",$e_data[$jj]["o_State"]);
				$stmt->bindparam(":f_Name",$e_data[$jj]["file_name"]);
				$stmt->bindparam(":reg_Date",$e_data[$jj]["reg_Date"]);
				$stmt->execute();
			  }*/
		}else{
			$result = array("result" => "error", "errorMsg" => "이미 업로드한 엑셀파일입니다. 확인 후 다시 시도해주세요.");
			echo json_encode($result);
		}	
	}else{
		$result = array("result" => "error1");
		echo json_encode($result);
	}
}else{ 
	$result = array("result" => "error2");
	echo json_encode($result);
} 
dbClose($DB_con);
$liststmt = null;
$chkliststmt = null;
$chkstmt = null;
$stmt = null;
?>