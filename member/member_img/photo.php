<?
include "../../lib/common.php";
include "../../lib/functionDB.php";

$id = $_GET['id'];

$DB_con = db1();
$memImg_query = "
	SELECT mem_ImgFile
	FROM TB_MEMBERS
	WHERE mem_Id = :mem_Id
		AND b_Disply = 'N'
	ORDER BY idx DESC
	LIMIT 1;
";
$stmt = $DB_con->prepare($memImg_query);
$stmt->bindparam(":mem_Id",$id);
$stmt->execute();
$row=$stmt->fetch(PDO::FETCH_ASSOC);
$mem_ImgFile = $row['mem_ImgFile'];		
$m_file = $_SERVER["DOCUMENT_ROOT"].'/member/member_img/'.$mem_ImgFile;

$is_file_exist = file_exists($m_file);
if ($is_file_exist) {	
	$handle = fopen($m_file, "rb");
    $contents = fread($handle, filesize($m_file));
    fclose($handle);
	Header("Content-type:  image/png");
    print stripslashes($contents);
}else{
	header("HTTP/1.1 404 Not Found"); 
	exit;
}
dbClose($DB_con);
$stmt = null;

?>