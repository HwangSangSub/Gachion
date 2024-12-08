<?
include "../../../../lib/common.php";
$DB_con = db1();
$code = $_GET['id'];
// 장소정보
$query = "
	SELECT idx, code, code_ios_Img
	FROM TB_CONFIG_CODE 
	WHERE code = :code
		AND code_Div = 'placeicon';
";
$stmt = $DB_con->prepare($query);
$stmt->bindParam(":code", $code);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$code_ios_Img = $row['code_ios_Img'];	
$m_file = $_SERVER["DOCUMENT_ROOT"].'/udev/admin/data/code_img/ios/'.$code_ios_Img;

$is_file_exist = file_exists($m_file);
if ($is_file_exist) {	
	$handle = fopen($m_file, "rb");
    $contents = fread($handle, filesize($m_file));
    fclose($handle);
	Header("Content-type:  image/png");
    print stripslashes($contents);
} else {
	echo "no file";
}

dbClose($DB_con);
?>