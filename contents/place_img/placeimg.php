<?
$id = $_GET['id'];
$m_file = $_SERVER["DOCUMENT_ROOT"].'/contents/place_img/'.$id;
// 핸들 획득
$handle  = opendir($m_file);
$files = array();
// 디렉터리에 포함된 파일을 저장한다.
while (false !== ($filename = readdir($handle))) {
    if($filename == "." || $filename == ".."){
        continue;
    }
    // 파일인 경우만 목록에 추가한다.
	$f_dir = $m_file . "/" . $filename;
    if(is_file($f_dir)){
        $files[] = $filename;
    }
}
// 핸들 해제 
closedir($handle);
// 정렬, 역순으로 정렬하려면 rsort 사용
sort($files);
// 파일명을 출력한다.
foreach ($files as $f) {
    echo "https://places.gachita.kr/contents/place_img/".$id."/".$f;
    echo "<br />";
} 
/*
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
*/
?>