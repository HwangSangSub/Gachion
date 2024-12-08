<?
echo "Start:".date(':Y-m-d h:i:s'); 
fastcgi_finish_request(); //일단 화면 출력 후 이후 코드는 백그라운드에서 동작 
sleep(10); //오래 걸리는 프로세스
?>

