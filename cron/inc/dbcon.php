<?php 
// DB 이전으로 인하여 hostname 수정
//주DB연결
function db1() {
	$mysql_hostname = 'db-d9cfm.cdb.ntruss.com';
	$mysql_username = 'americano';
	$mysql_password = 'Gct2022^^';
	$mysql_database = 'places';
	$mysql_port = '3306';
	$mysql_charset = 'utf8';
    
    // PDO
    try {
        $DB_con = 'mysql:host='.$mysql_hostname.';dbname='.$mysql_database.';port='.$mysql_port.';charset='.$mysql_charset;
        $DB_con = new PDO( $DB_con, $mysql_username, $mysql_password );
        $DB_con->exec("SET CHARACTER SET utf8");
        
        //$DB_con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);  // 에러 출력하지 않음
        $DB_con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  // 에러 출력
        //$DB_con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);  // Warning만 출력
    } catch(PDOException $e) {  // 실 서버에서는 오류 내용을 보여주면 안된다. : ' . $e->getMessage() . '
        echo 'Connect failed';
        return false;
    }
    
    return $DB_con;
}

// 주 DB연결 해제
function dbClose($DB_con = '') {
    $DB_con = null;
}



?>