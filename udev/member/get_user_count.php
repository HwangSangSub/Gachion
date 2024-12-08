<?
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Include
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
include "../../lib/common.php"; 
include "../../lib/alertLib.php";
include "../../lib/thumbnail.lib.php";   //썸네일

$DB_con = db1();
$memLv = $_POST["memLv"];

if($memLv)
{
	$search_query= " and  mem_Lv='".$memLv."' ";
}
$query = "select * from TB_MEMBERS where 1=1 {$search_query} " ;
$stmt = $DB_con->prepare($query);
$stmt->execute();
$num = $stmt->rowCount();

echo $num;
?>

