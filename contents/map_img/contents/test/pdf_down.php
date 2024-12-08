<?
header('Content-Type: text/html; charset=UTF-8');
$url = "http://www.naver.com";
$down_name = "test";

//tmp폴더의 test.pdf 저장
exec('/usr/local/bin/wkhtmltopdf -L 10mm -R 10mm -T 10mm -B 10mm "'.$url.'" "tmp/'.$down_name.'.pdf" 2>&1');

//tmp폴더의 test.pdf를 클라이언트가 다운받을 수 있도록
$sFilePath = 'tmp/'.$down_name.'.pdf';
$sFileName = $down_name.'.pdf';

header("Content-Disposition: attachment; filename=\"".$sFileName."\"");
header("Content-Transfer-Encoding: binary");
header("Content-Length: ".strval(filesize($sFilePath)));
header("Cache-Control: cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

echo file_get_contents($sFilePath);
flush();
unlink($sFilePath);//tmp폴더의 test.pdf 파일을 삭제

?>