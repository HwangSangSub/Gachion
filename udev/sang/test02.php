<?
set_time_limit(3);  
function get_time() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}
$start = get_time();
$now_Date =  date('Y-m-d H:i:s', time());
$use_Date = '2019-08-25 00:00:00';
(int)$time = strtotime($use_Date) - strtotime($now_Date) ;
$time_min = ceil($time / (60)) ;
$time_day = ceil($time / (86400)) ;
$time_chk = 10080;		// (7일 * 24시간 * 60분)
echo "time : ".$time."<br><br>";
echo "time_min : ".$time_min."<br><br>";
echo "time_chk : ".$time_chk."<br><br>";
echo "time_day : ".$time_day."<br><br>";
if((int)$time_min < (int)$time_chk){
	echo "7일보다 적게 남았어요";

$end = get_time();
$time = $end - $start;
echo '<br/>'.$time.'초 걸림';
}else{
	echo "7일보다 많이 남았어요";

$end = get_time();
$time = $end - $start;
echo '<br/>'.$time.'초 걸림';
}
​?>