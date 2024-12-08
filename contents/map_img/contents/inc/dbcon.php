<?php
/*
	NCF이동으로 인한 DB호스트 재설정 작업일 : 2019-01-04 작업자 : 황상섭 대리
	$mysql_hostname = '10.41.28.245'; 
*/
//주DB연결
function db1() {
	$mysql_hostname = 'db-d9cfm.cdb.ntruss.com';
	$mysql_username = 'americano';
	$mysql_password = 'Gct2022^^';
	$mysql_database = 'places';
	$mysql_port = '3306';
	$mysql_charset = 'utf8mb4';

	  // PDO
	  try {
		  $DB_con = 'mysql:host='.$mysql_hostname.';dbname='.$mysql_database.';port='.$mysql_port.';charset='.$mysql_charset;
		  $DB_con = new PDO( $DB_con, $mysql_username, $mysql_password );
		  $DB_con->exec("SET CHARACTER SET utf8mb4");

		 //$DB_con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);  // 에러 출력하지 않음
		  $DB_con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  // 에러 출력
		  //$DB_con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);  // Warning만 출력
	  } catch(PDOException $e) {  // 실 서버에서는 오류 내용을 보여주면 안된다.: ' . $e->getMessage() . '
		echo 'Connect failed';
		return false;
	  }

	  return $DB_con;
  }

// 주 DB연결 해제
 function dbClose($DB_con = '') {
	 $DB_con = null; 
 }  

/*
	해당DB는 NCF에 없으므로 기존과 동일하게 유지
*/

//테스트 DB연결
function db2() {
	$mysql_hostname = 'db-d9cfm.cdb.ntruss.com';
	$mysql_username = 'americano';
	$mysql_password = 'Gct2022^^';
	$mysql_database = 'places';
	$mysql_port = '3306';
	$mysql_charset = 'utf8';

	  // PDO
	  try {
		  $dbh = 'mysql:host='.$mysql_hostname.';dbname='.$mysql_database.';port='.$mysql_port.';charset='.$mysql_charset;
		  $dbh = new PDO( $dbh, $mysql_username, $mysql_password );
		  $dbh->exec("SET CHARACTER SET utf8mb4");

		 //$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);  // 에러 출력하지 않음
		  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  // 에러 출력
		  //$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);  // Warning만 출력
	  } catch(PDOException $e) {  // 실 서버에서는 오류 내용을 보여주면 안된다.
		echo 'Connect failed : ' . $e->getMessage() . '';
		return false;
	  }

	  return $dbh;
  }




?>
