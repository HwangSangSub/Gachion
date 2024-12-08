<?
	$menu = "1";
	$smenu = "4";

	include "../common/inc/inc_header.php";  //헤더 
	include "../common/inc/inc_gnb.php";  //헤더 
	include "../common/inc/inc_menu.php";  //메뉴 

?>

<div id="wrapper">
    <div id="container" class="">
	<h1 id="container_title">캐시파일 일괄삭제</h1>
	<div class="container_wr">


<div class="local_desc02 local_desc">
    <p>
        완료 메세지가 나오기 전에 프로그램의 실행을 중지하지 마십시오.
    </p>
</div>

    <?
    flush();

    $list_tag_st = "";
    $list_tag_end = "";
    if (!$dir=@opendir(DU_DATA_PATH.'/cache')) {
		echo '<p>캐시디렉토리를 열지못했습니다.</p>';
    } else {
        $list_tag_st = "<ul class=\"session_del\">\n<li>완료됨</li>\n";
        $list_tag_end = "</ul>\n";
    }

$cnt=0;
echo '<ul class="session_del">'.PHP_EOL;

$files = glob(DU_DATA_PATH.'/cache/latest-*');
if (is_array($files)) {
    foreach ($files as $cache_file) {
        $cnt++;
        unlink($cache_file);
        echo '<li>'.$cache_file.'</li>'.PHP_EOL;

        flush();

        if ($cnt%10==0) 
            echo PHP_EOL;
    }
}

echo '<li>완료됨</li></ul>'.PHP_EOL;
echo '<div class="local_desc01 local_desc"><p><strong>최신글 캐시파일 '.$cnt.'건 삭제 완료됐습니다.</strong><br>프로그램의 실행을 끝마치셔도 좋습니다.</p></div>'.PHP_EOL;
?>
</div>    

<? include "../common/inc/inc_footer.php";  //푸터 ?>
