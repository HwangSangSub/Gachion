<?
//https://places.gachita.kr/node/exec.php?conIdx=340
//https://places.gachita.kr/node/exec.php?conIdx=340
//rm -rf /hd/webFolder/places/contents/map_img/contents/340.jpg
$conIdx=$_GET["conIdx"];

//@ulink("./data/screenshot.png");
$cmd="sh ./exec.sh $conIdx";
echo $cmd;

shell_exec($cmd);





?>
