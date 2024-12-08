<?
/*
회원 mem_id
echo $_GET["memId"];
*/
include "../../lib/dbcon.php";
//db연결
$DB_con = db1();
$id = $_GET['memId'];
$query= "select * from  TB_MEMBER_PHOTO  where mem_id=:id "; 
$stmt = $DB_con->prepare($query);
$stmt->bindparam(":id", $id);	
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
// 파일로 blob형태 이미지 출력
$read_img_file = $row['mem_profile_update'];
$img_txt = $read_img_file;
$m_file = $_SERVER["DOCUMENT_ROOT"].'/contents/img/'.$img_txt;
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
?>