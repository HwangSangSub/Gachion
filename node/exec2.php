<?
//https://places.gachita.kr/node/exec.php?conIdx=340
//https://places.gachita.kr/node/exec.php?conIdx=340
//rm -rf /hd/webFolder/places/contents/map_img/contents/340.jpg
$conIdx=$_GET["conIdx"];

$file_dir = $_SERVER["DOCUMENT_ROOT"].'/contents/map_img/contents/'.$conIdx.'.jpg';  
if (is_file($file_dir)) {
	unlink($file_dir);
}
$cmd="sh ./exec2.sh $conIdx";
//echo $cmd;
if($conIdx != ""){
	echo "success";
	shell_exec($cmd);
}else{
	echo "error";
}
?>
