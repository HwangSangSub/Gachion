<?
ignore_user_abort(true);
set_time_limit(0);
ini_set('memory_limit','2045M');
include "../lib/common.php";
include "../lib/functionDB.php";  //공통 db함수
require_once "./PHPExcel-1.8/Classes/PHPExcel.php";
require_once "./PHPExcel-1.8/Classes/PHPExcel/IOFactory.php"; 

if(isset($_FILES['upload'])){ 
	$target = "./excel_data/"); 
	echo $target;
	exit;
	if(move_uploaded_file($_FILES['upload']['tmp_name'],$target)) {
		$objPHPExcel = new PHPExcel();
		// 엑셀 데이터를 담을 배열을 선언한다.
		$allData = array();
		$mem_Id = trim($memId);
		$file_name = trim(basename($_FILES['upload']['name']));
		$reg_Date = DU_TIME_YMDHIS;
		$DB_con = db1();

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
		if((int)$maxRow != 0){
		}
		for ($i = 1 ; $i <= $maxRow; $i++) {
			if($i == 1){
				continue;
			}
			$name = $objWorksheet->getCell('B' . $i)->getValue();
			if($name ==''){
				$name = '';
			}else{
				$name = $name;
			}
			$jumin = $objWorksheet->getCell('C' . $i)->getValue();
			if($jumin ==''){
				$jumin = '';
			}else{
				$jumin = $jumin;
			}
			$tel = $objWorksheet->getCell('D' . $i)->getValue();
			if($tel ==''){
				$tel= '';
			}else{
				$tel = $tel;
			}
			$group = $objWorksheet->getCell('E' . $i)->getValue();
			if($group ==''){
				$group = '';
			}else{
				$group = $group;
			}
			$cnt = $objWorksheet->getCell('F' . $i)->getValue();
			if($cnt ==''){
				$cnt = '';
			}else{
				$cnt = $cnt;
			}
			$addr = $objWorksheet->getCell('G' . $i)->getValue();
			if($addr ==''){
				$addr = '';
			}else{
				$addr = $addr;
			}
			// 엑셀값을 배열로 만들기
			$r_data = array("name" => $name, "jumin" => $jumin,"tel" => $tel,"group" => $group,"cnt" => $cnt,"addr" => $addr);
	
			array_push($e_data, $r_data);
			//, "data" =>$allData
			// $rowData에 들어가는 값은 계속 초기화 되기때문에 값을 담을 새로운 배열을 선안하고 담는다.
		  }
		  print_r($e_data);
		  exit;
		  $r_cnt = count($e_data);
		 for($j = 1 ; $j <= $r_cnt; $j++){
			$jj = (int)$j -1;
			$query = "
					INSERT INTO TB_EXEC_LIST (name, jumin, tel, group, cnt, addr)
					VALUES (:name, :jumin, :tel, :group, :cnt, :addr)" ;
			$stmt = $DB_con->prepare($query);
			$stmt->bindparam(":name",$e_data[$jj]["name"]);
			$stmt->bindparam(":jumin",$e_data[$jj]["jumin"]);
			$stmt->bindparam(":tel",$e_data[$jj]["tel"]);
			$stmt->bindparam(":group",$e_data[$jj]["group"]);
			$stmt->bindparam(":cnt",$e_data[$jj]["cnt"]);
			$stmt->bindparam(":addr",$e_data[$jj]["addr"]);
			$stmt->execute();
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