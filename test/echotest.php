<?php
// Web JSON 파일 읽어오기
//$url = 'http://ip주소/getFileList.php';
//$json_string = file_get_contents($url);

// Local JSON 파일 읽어오기
$json_string = file_get_contents('AURA.json');
// 다차원 배열 반복처리
$R = new RecursiveIteratorIterator(
    new RecursiveArrayIterator(json_decode($json_string, TRUE)),
    RecursiveIteratorIterator::SELF_FIRST);
// $R : array data
// json_decode : JSON 문자열을 PHP 배열로 바꾼다
// json_decode 함수의 두번째 인자를 true 로 설정하면 무조건 array로 변환된다.
foreach ($R as $key => $val) {
	echo $key." : ".$val."<BR>";
	//echo $R['features'];
	/*
    if(is_array($val)) { // val 이 배열이면
        echo "$key:<br/>";
        //echo $key.' (key), value : (array)<br />';
    } else { // 배열이 아니면
        echo "$key => $val <br />";
    }
	*/
}
?>