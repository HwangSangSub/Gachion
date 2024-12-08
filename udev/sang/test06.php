 <?php 
 $i=1;  
 // php가 안전모드가 아니라면 25초로 늘립니다.  
 ini_set('max_execution_time', 1);  
  
 echo ini_get('max_execution_time'); // 결과: 120 
 if( !ini_get('safe_mode') ){  
	set_time_limit(1);
	 while(true){ 
		echo "<br />\n안전모드 아니다";
	 }
 }else{ 
	 echo "안전모드 다";
	 while(true){ 
		  echo "$i <br />\n"; 
		  $i++; 
	 }
 }
 ?>