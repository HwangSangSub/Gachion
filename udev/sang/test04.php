<?php
	include "../lib/common.php";
	include "../lib/functionDB.php";  //공통 db함수
	include "../lib/functionCoupon.php";    //쿠폰관련 함수
if(!isset($_SESSION)) { 
    session_start(); 
} 
if(!empty($_SESSION['userID'])){
    echo "<script>window.top.location.replace('/index.php');</script>";
    exit;
}

$fname="xxx_code7";  // 저장할 파일명
// 파일의 저장형식이 utf-8일 경우 한글파일 이름은 깨지므로 euc-kr로 변환해준다.
//$fname = iconv("UTF-8", "EUC-KR", "한글이름");

$DB_con = db1();

/** Include PHPExcel */
require_once "./PHPExcel-1.8/Classes/PHPExcel.php";
require_once "./PHPExcel-1.8/Classes/PHPExcel/IOFactory.php"; 

$objPHPExcel = new PHPExcel();
$objPHPExcel->setActiveSheetIndex(0); //set first sheet as active

$objSheet = $objPHPExcel->getActiveSheet();
$objSheet->setTitle('코드');
$objSheet->getCell('A1')->setValue('코드');
$objSheet->getDefaultStyle('A1')->getFont()->setName('맑은 고딕');
$objSheet->mergeCells('A1:V1'); // 셀 병합
$objSheet->getStyle('A1')->getFont()->setSize(16); // 셀의 textsize

$objPHPExcel->getDefaultStyle()->getFont()->setSize(12); // 폰트 사이즈
/*
$objSheet->mergeCells('A2:A3'); // 셀 병합
$objSheet->SetCellValue('B2', "부서");
$objSheet->mergeCells('B2:E2');
$objSheet->SetCellValue('F2', "순서");
$objSheet->mergeCells('F2:I2');
$objSheet->SetCellValue('J2', "코드");
$objSheet->mergeCells('J2:M2');
*/
$objSheet->SetCellValue('A2', "예정일");
$objSheet->SetCellValue('B2', "시간");
$objSheet->SetCellValue('C2', "상태");
$objSheet->SetCellValue('D2', "담당자");
$objSheet->SetCellValue('E2', "처리일");
$objSheet->SetCellValue('F2', "처리자");
$objSheet->SetCellValue('G2', "시간");
$objSheet->SetCellValue('H2', "계약번호");
$objSheet->SetCellValue('I2', "고객명");
$objSheet->SetCellValue('J2', "차량번호");
$objSheet->SetCellValue('K2', "상품명");
$objSheet->SetCellValue('L2', "본수");
$objSheet->SetCellValue('M2', "주행거리");
$objSheet->SetCellValue('N2', "주행거리(계약)");
$objSheet->SetCellValue('O2', "장착일자");
$objSheet->SetCellValue('P2', "핸드폰");
$objSheet->SetCellValue('Q2', "전화번호");
$objSheet->SetCellValue('R2', "우편번호");
$objSheet->SetCellValue('S2', "주소");
$objSheet->SetCellValue('T2', "구주소▼");
$objSheet->SetCellValue('U2', "상세주소");
$objSheet->SetCellValue('V2', "전월연체여부");

cellColor('A2:V2', 'F28A8C'); // 헤더 배경색 지정

$i=3; // 값을 기록할 셀의 시작위치

$sql="select e_Date, e_Time, e_State, Manager, p_Date, p_Manager, p_Time, c_No, c_Name, car_No, p_Name, b_No, d_Distance, c_Distance, r_Date, p_Tel, h_Tel, z_Code, n_Addr, p_Addr, d_Addr, Lng, Lat, o_State from TB_EXCELDATA";
$stmt = $DB_con->prepare($sql);
$stmt->execute();	
while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    $objSheet->SetCellValue('A'.$i, $row['e_Date']);
    $objSheet->SetCellValue('B'.$i, $row['e_Time']);
    $objSheet->SetCellValue('C'.$i, $row['e_State']);
    $objSheet->SetCellValue('D'.$i, $row['Manager']);
    $objSheet->SetCellValue('E'.$i, $row['p_Date']);
    $objSheet->SetCellValue('F'.$i, $row['p_Manager']);
    $objSheet->SetCellValue('G'.$i, $row['p_Time']);
    $objSheet->SetCellValue('H'.$i, $row['c_No']);
    $objSheet->SetCellValue('I'.$i, $row['c_Name']);
    $objSheet->SetCellValue('J'.$i, $row['car_No']);
    $objSheet->SetCellValue('k'.$i, $row['p_Name']);
    $objSheet->SetCellValue('L'.$i, $row['b_No']);
    $objSheet->SetCellValue('M'.$i, $row['d_Distance']);
    $objSheet->SetCellValue('N'.$i, $row['c_Distance']);
    $objSheet->SetCellValue('O'.$i, $row['r_Date']);
    $objSheet->SetCellValue('P'.$i, $row['p_Tel']);
    $objSheet->SetCellValue('Q'.$i, $row['h_Tel']);
    $objSheet->SetCellValue('R'.$i, $row['z_Code']);
    $objSheet->SetCellValue('S'.$i, $row['n_Addr']);
    $objSheet->SetCellValue('T'.$i, $row['p_Addr']);
    $objSheet->SetCellValue('U'.$i, $row['d_Addr']);
    $objSheet->SetCellValue('V'.$i, $row['o_State']);

    $i++;
    $rowCount++;
}

// 표 그리기
$i--;
$objSheet->getStyle('A2:V'.$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

// 헤더 칼럼 가운데 정렬
$objSheet->getStyle('A2:V'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

// 셀 높이
$objSheet->getRowDimension(1)->setRowHeight(20);

// 칼럼 사이즈 자동 조정
$objSheet->getColumnDimension('A:V')->setAutoSize(true);
/*
$objSheet->getColumnDimension('B')->setWidth(18);  // 칼럼 크기 직접 지정
$objSheet->getColumnDimension('C')->setWidth(18);
$objSheet->getColumnDimension('D')->setWidth(18);
$objSheet->getColumnDimension('E')->setWidth(18);
*/
// 파일 PC로 다운로드
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename='.$fname.'.xlsx');
header('Cache-Control: max-age=0');
/*
// 임시파일 저장 후 로드
$tmpfile = './excel_file/'.$fname.'.xlsx';
file_put_contents($tmpfile, $html);
$reader = new PHPExcel_Reader_HTML; 
$content = $reader->load($tmpfile); 
unlink( $tmpfile );
*/
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
$filePath = './excel_file/' .$fname. ".xlsx";
$objWriter->save($filePath);
$result = array("result" => "success","link" => "http://roadian.gachita.co.kr/excel/excel_file/" .$fname. ".xlsx");
echo json_encode($result); 
exit;

/*
function SaveViaTempFile($objWriter){
    $filePath = './excel_file/' .$fname. ".xlsx";
    $objWriter->save($filePath);
    readfile($filePath);
    unlink($filePath);
}
*/
// 엑셀의 셀 배경색 지정
function cellColor($cells,$color){
    global $objSheet;

    $objSheet->getStyle($cells)->getFill()->applyFromArray(array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'startcolor' => array(
             'rgb' => $color
        )
    ));
}

	dbClose($DB_con);
?>