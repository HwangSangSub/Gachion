<?
	include "../../lib/common.php";
	include "../../lib/alertLib.php";
	include "../../ib/thumbnail.lib.php";   //썸네일

//$mem_Id = sprintf('%09d',rand(000000000,999999999));				//아이디
$login_Id = trim($login_Id);				//관리자아이디
if($login_Id == ''){
	$loginId = 'admin';
}else{
	$loginId = $login_Id;
}
$mem_Id = trim($mem_Id);				//아이디

if ($memPwd == "") {
	$mem_Pwd = $memPwd;
} else {
	$mem_Pwd = password_hash($memPwd, PASSWORD_DEFAULT);  // 비밀번호 암호화 
}

$mem_Nm = trim($mem_Nm);		//이름
$mem_Tel = trim($mem_Tel);          //연락처
$mem_Email = '';							//이메일
$mem_Sex = 0;							//성별 ( 0: 남자, 1:여자)
$mem_Os = 0;								//Os운영체제 (0: 안드로이드,1: 아이폰, 2:기타(웹만사용)  )
$mem_Lv = trim($mem_Lv);			// 등급
$use_Date = trim($use_Date);

if($mem_Lv == ''){
	$memLv = 1;
}else{
	$memLv = $mem_Lv;
}


if ($mem_Id != "") {
    
    $DB_con = db1();
    
    $memQuery = "SELECT mem_Id, b_Disply FROM TB_MEMBERS WHERE mem_Id = :mem_Id" ;
    $stmt = $DB_con->prepare($memQuery);
    $stmt->bindparam(":mem_Id",$mem_Id);
    $stmt->execute();
    $num = $stmt->rowCount();
    
    if($num < 1)  { //주 ID가 없을 경우 회원가입 시작       
		$rand_num = sprintf('%03d',rand(000,999));
        //회원 주 아이디 생성 (랜덤  : 년도 + 랜덤수 + 일자 + max값(db))
        $nowYear = date("Y");
        $nowMonth = date("m");
        $nowDay = date("d");
        
        $memSId = $nowYear.getRandID($nowYear, $nowMonth, $nowDay, 9).$nowMonth.$nowDay.$rand_num;
        
        //회원코드
        $mem_Code = get_code();
		
		$b_Disply = "N";												 //탈퇴여부
		$reg_Date = DU_TIME_YMDHIS;										 //등록일
		if($use_Date == '30'){
			$timestamp = strtotime("+30 days");
		}else if($use_Date == '60'){
			$timestamp = strtotime("+60 days");
		}else if($use_Date == '90'){
			$timestamp = strtotime("+90 days");
		}
		//사용일! 입력하기
		$useDate = date("Y-m-d", $timestamp);
		//$useDate = date("Y-m-d H:i:s", strtotime($reg_Date.$timestamp));

		//회원 기본테이블 저장
		$insQuery = "INSERT INTO TB_MEMBERS (mem_Id, mem_Nm, mem_Pwd, mem_Tel, mem_CertBit, mem_Birth, mem_Lv, b_Disply, use_Date, reg_Date ) VALUES (:mem_Id, :mem_Nm, :mem_Pwd, :mem_Tel, '1', '', :mem_Lv, :b_Disply, :use_Date, :reg_Date)";
		$stmt = $DB_con->prepare($insQuery);
		$stmt->bindParam("mem_Id", $mem_Id);
		$stmt->bindParam("mem_Nm", $mem_Nm);
		$stmt->bindParam("mem_Pwd", $mem_Pwd);
		$stmt->bindParam("mem_Tel", $mem_Tel);
		$stmt->bindParam("mem_Lv", $memLv);
		$stmt->bindParam("b_Disply", $b_Disply);
		$stmt->bindParam("use_Date", $useDate);
		$stmt->bindParam("reg_Date", $reg_Date);
		$stmt->execute();
		
		$mIdx = $DB_con->lastInsertId();  //저장된 idx 값
		
		if($stmt->rowCount() > 0 ) { //삽입 성공
			
			//사용기록 남기기!
			$deve_Locat = "회원등록";			//사용위치
			$deve_Memo = "관리자 (".$loginId.")가 신규회원 (".$mem_Id.")을 등록완료함";		//메모
			$develop_log_query = "
				INSERT INTO TB_DEVELOP_LOG(login_Id, deve_Locat, deve_Memo, reg_Date)
				VALUES (:login_Id, :deve_Locat, :deve_Memo, :reg_Date);";
			$develop_Stmt = $DB_con->prepare($develop_log_query);
			$develop_Stmt->bindparam(":login_Id",$loginId);
			$develop_Stmt->bindparam(":deve_Locat",$deve_Locat);
			$develop_Stmt->bindparam(":deve_Memo",$deve_Memo);
			$develop_Stmt->bindparam(":reg_Date",$reg_Date);
			$develop_Stmt->execute();
		} else { //등록시 에러
			//사용기록 남기기!
			$deve_Locat = "회원등록";			//사용위치
			$deve_Memo = "관리자 (".$loginId.")가 신규회원 (".$mem_Id.")을 등록시도 하였으나 실패";		//메모
			$develop_log_query = "
				INSERT INTO TB_DEVELOP_LOG(login_Id, deve_Locat, deve_Memo, reg_Date)
				VALUES (:login_Id, :deve_Locat, :deve_Memo, :reg_Date);";
			$develop_Stmt = $DB_con->prepare($develop_log_query);
			$develop_Stmt->bindparam(":login_Id",$loginId);
			$develop_Stmt->bindparam(":deve_Locat",$deve_Locat);
			$develop_Stmt->bindparam(":deve_Memo",$deve_Memo);
			$develop_Stmt->bindparam(":reg_Date",$reg_Date);
			$develop_Stmt->execute();
			echo '<script>alert("회원등록 실패");history.back();</script>';
		}
    } else {  //등록된 회원이 있을 경우
        while($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
            $cMemSId = $row['mem_SId'];	       //체크 랜덤아이디
            $bDisply = $row['b_Disply'];	       //탈퇴여부
        }

		//사용기록 남기기!
		$deve_Locat = "회원등록";			//사용위치
		$deve_Memo = "관리자 (".$loginId.")가 신규회원 (".$mem_Id.")을 등록시도 하였으나 실패 (등록되어 있는 아이디)";		//메모
		$develop_log_query = "
			INSERT INTO TB_DEVELOP_LOG(login_Id, deve_Locat, deve_Memo, reg_Date)
			VALUES (:login_Id, :deve_Locat, :deve_Memo, :reg_Date);";
		$develop_Stmt = $DB_con->prepare($develop_log_query);
		$develop_Stmt->bindparam(":login_Id",$loginId);
		$develop_Stmt->bindparam(":deve_Locat",$deve_Locat);
		$develop_Stmt->bindparam(":deve_Memo",$deve_Memo);
		$develop_Stmt->bindparam(":reg_Date",$reg_Date);
		$develop_Stmt->execute();
    }
    
        
    dbClose($DB_con);
    $stmt = null;
    $cntMStmt = null;
    $stmtInfo = null;
    $stmtEtc = null;
    $stmtMap = null;
    $upStmt = null;
    $upStmt2 = null;
	$develop_Stmt = null;
    
	echo '<script>alert("회원등록 성공");history.back();</script>';
    
} else { //빈값일 경우
	echo '<script>alert("회원등록 실패");history.back();</script>';
}

?>



