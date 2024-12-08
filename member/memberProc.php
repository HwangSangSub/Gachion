<?
include "../lib/common.php";

$mem_Id = trim($memId);					//아이디
$memPwd = trim($memPwd);				//비밀번호 (email 계정인 경우만 사용)
$mem_Pwd = password_hash($memPwd, PASSWORD_DEFAULT);  // 비밀번호 암호화 
$mem_NickNm = ($nickname);		    //닉네임
$mem_SnsChk = trim($snsChk);			//SNS체크여부 (kakao, google, email)
$mem_Tel = trim($memTel);				//연락처
if($mem_SnsChk == "email"){				//sns여부가 email인 경우 아이디가 메일로 지정. => 추후변경가능
	$mem_Email = $mem_Id;				//이메일
}else{
	$mem_Email = trim($memEmail);		//이메일
}
$mem_Sex = trim($memSex);				//성별 ( 0: 남자, 1:여자)
$mem_Os = trim($memOs);				//Os운영체제 (0: 안드로이드,1: 아이폰, 2:기타(웹만사용)  )
$mDeviceId = trim($deviceId);				//디바이스 아이디

if($ie) { //익슬플로러일경우
    $mem_NickNm = iconv('euc-kr', 'utf-8', $mem_NickNm);
} else {
    $mem_NickNm = $mem_NickNm;
}

// print_r($_REQUEST);

if ($mem_Id != "" && $mem_NickNm != "" && $mem_SnsChk != ""  ) {
    $DB_con = db1();
    
    $memQuery = "SELECT idx, b_Disply FROM TB_MEMBERS WHERE mem_Id = :mem_Id" ;
    $chkstmt = $DB_con->prepare($memQuery);
    $chkstmt->bindparam(":mem_Id",$mem_Id);
    $chkstmt->execute();
    $chknum = $chkstmt->rowCount();
    
    if($chknum < 1)  { //주 ID가 없을 경우 회원가입 시작
    } else {  //등록된 회원이 있을 경우
        while($chkrow=$chkstmt->fetch(PDO::FETCH_ASSOC)) {
            $cidx = $chkrow['idx'];						// 체크 고유번호
            $bDisply = $chkrow['b_Disply'];			//회원 상태 여부 (N: 가입, Y: 탈퇴, D: 휴먼회원)
        }
    }
    
    if ($bDisply == "N") {//회원 가입이 되어 있을 경우
        
        //로그인횟수
        $memQuery = "SELECT idx, mem_NickNm, mem_Lv, login_Cnt from TB_MEMBERS  WHERE mem_Id = :mem_Id AND b_Disply = 'N' " ;
        
        $login_stmt = $DB_con->prepare($memQuery);
        $login_stmt->bindparam(":mem_Id",$mem_Id);
        $login_stmt->execute();
        $login_num = $login_stmt->rowCount();
        
        if($login_num < 1)  { //아닐경우
        } else {
            
            while($login_row=$login_stmt->fetch(PDO::FETCH_ASSOC)) {
                
                $mem_Id = $mem_Id;										// 아이디
                $mIdx = $login_row['idx'];											// 고유번호
                $mem_NickNm = $login_row['mem_NickNm'];				// 닉네임
                //$mem_Lv = $row['mem_Lv'];								// 등급
                $login_Cnt = $login_row['login_Cnt'];							// 로그인 횟수
                $login_Cnt = $login_Cnt + 1;
                
                # 마지막 로그인 시간을 업데이트 한다.
                $upQquery = "UPDATE TB_MEMBERS SET login_Cnt = :login_Cnt, login_Date = now(), mem_DeviceId = :mem_DeviceId WHERE idx = :idx AND mem_Id = :mem_Id LIMIT 1";
                $upStmt = $DB_con->prepare($upQquery);
                $upStmt->bindparam(":idx",$mIdx);
                $upStmt->bindparam(":mem_Id",$mem_Id);
                $upStmt->bindparam(":login_Cnt",$login_Cnt);
                $upStmt->bindparam(":mem_DeviceId",$mDeviceId);
                $upStmt->execute();
				$result = array("result" => "success");
            }
            
        }
        
        
        
    } else { //회원탈퇴 및 신규회원 가입
                
        //회원코드
        $mem_Code = get_code();
        
        $cntQuery = "";
        $cntQuery = "SELECT count(idx)  AS num FROM TB_MEMBERS WHERE mem_Code = :mem_Code ";
        $cntStmt = $DB_con->prepare($cntQuery);
        $cntStmt->bindparam(":mem_Code",$mem_Code);
        $cntStmt->execute();
        $row = $cntStmt->fetch(PDO::FETCH_ASSOC);
        $vnum = $row['num'];
        
        if($vnum > 1)  { //있을 경우
        } else {
            
            $mem_Lv = 1;													 //회원등급(1: 일반회원, 2: 제휴회원)
            $b_Disply = "N";												 //탈퇴여부
            $reg_Date = DU_TIME_YMDHIS;							 //등록일

			// 이메일로 가입시 비밀번호 입력.
            if($mem_SnsChk == "email"){
				//회원 기본테이블 저장
				$insQuery = "INSERT INTO TB_MEMBERS (mem_Id, mem_Pwd, mem_NickNm, mem_Tel, mem_Sex, mem_Email, mem_DeviceId, b_Disply, mem_Os, mem_Lv, mem_SnsChk, mem_Code, reg_date ) VALUES (:mem_Id, :mem_Pwd, :mem_NickNm, :mem_Tel, :mem_Sex, :mem_Email, :mem_DeviceId, :b_Disply, :mem_Os, :mem_Lv, :mem_SnsChk, :mem_Code, :reg_Date)";
				$stmt = $DB_con->prepare($insQuery);
				$stmt->bindParam("mem_Id", $mem_Id);
				$stmt->bindParam("mem_Pwd", $mem_Pwd);
				$stmt->bindParam("mem_NickNm", $mem_NickNm);
				$stmt->bindParam("mem_Tel", $mem_Tel);
				$stmt->bindParam("mem_Sex", $mem_Sex);
				$stmt->bindParam("mem_Email", $mem_Email);
				$stmt->bindParam("mem_DeviceId", $mDeviceId);
				$stmt->bindParam("b_Disply", $b_Disply);
				$stmt->bindParam("mem_Os", $mem_Os);
				$stmt->bindParam("mem_Lv", $mem_Lv);
				$stmt->bindParam("mem_SnsChk", $mem_SnsChk);
				$stmt->bindParam("mem_Code", $mem_Code);
				$stmt->bindParam("reg_Date", $reg_Date);
				$stmt->execute();
				$mIdx = $DB_con->lastInsertId();  //저장된 idx 값
			// 그 외는 비밀번호는 입력하지 않음.
			}else{
				//회원 기본테이블 저장
				$insQuery = "INSERT INTO TB_MEMBERS (mem_Id, mem_NickNm, mem_Tel, mem_Sex, mem_Email, mem_DeviceId, b_Disply, mem_Os, mem_Lv, mem_SnsChk, mem_Code, reg_date ) VALUES (:mem_Id, :mem_NickNm, :mem_Tel, :mem_Sex, :mem_Email, :mem_DeviceId, :b_Disply, :mem_Os, :mem_Lv, :mem_SnsChk, :mem_Code, :reg_Date)";
				$stmt = $DB_con->prepare($insQuery);
				$stmt->bindParam("mem_Id", $mem_Id);
				$stmt->bindParam("mem_NickNm", $mem_NickNm);
				$stmt->bindParam("mem_Tel", $mem_Tel);
				$stmt->bindParam("mem_Sex", $mem_Sex);
				$stmt->bindParam("mem_Email", $mem_Email);
				$stmt->bindParam("mem_DeviceId", $mDeviceId);
				$stmt->bindParam("b_Disply", $b_Disply);
				$stmt->bindParam("mem_Os", $mem_Os);
				$stmt->bindParam("mem_Lv", $mem_Lv);
				$stmt->bindParam("mem_SnsChk", $mem_SnsChk);
				$stmt->bindParam("mem_Code", $mem_Code);
				$stmt->bindParam("reg_Date", $reg_Date);
				$stmt->execute();
				$mIdx = $DB_con->lastInsertId();  //저장된 idx 값
			}
			// 기본설정값 등록하기
			$con_ins_Query = "INSERT INTO TB_MEMBERS_CONFIG (member_Idx, mem_Id, reg_Date ) VALUES (:member_Idx, :mem_Id, :reg_Date)";
			$con_ins_stmt = $DB_con->prepare($con_ins_Query);
			$con_ins_stmt->bindParam("member_Idx", $mIdx);
			$con_ins_stmt->bindParam("mem_Id", $mem_Id);
			$con_ins_stmt->bindParam("reg_Date", $reg_Date);
			$con_ins_stmt->execute();

			$con_Name = $mem_NickNm."님의 지도";		// 섭입니다님의 지도
			$category = '1';										// 카테고리는 음식카테고리로 지정
			$open_Bit = '0';										// 기본은 전체공개
			$memo = '나만의 지도입니다.';

			// 최초 지도 등록하기			
			$con_query = "INSERT INTO TB_CONTENTS (member_Idx, con_Name, category, memo, open_Bit, reg_Id, reg_Date, mod_Date ) VALUES (:member_Idx, :con_Name, :category, :memo, :open_Bit, :reg_Id, :reg_Date, NOW())";
			
			$con_stmt = $DB_con->prepare($con_query);
			$con_stmt->bindParam(":member_Idx", $mIdx);
			$con_stmt->bindParam(":con_Name", $con_Name);
			$con_stmt->bindParam(":category", $category);
			$con_stmt->bindParam(":memo", $memo);
			$con_stmt->bindParam(":open_Bit", $open_Bit);
			$con_stmt->bindParam(":reg_Id", $mem_Id);
			$con_stmt->bindParam(":reg_Date", $reg_Date);
			$con_stmt->execute();
			$con_Idx = $DB_con->lastInsertId();  //저장된 idx 값

			// 지도등록 히스토리남기기
			$history = "지도생성";
			$his_query ="INSERT INTO TB_HISTORY (member_Idx, mem_Id, con_Idx, history, reg_Date) VALUES (:member_Idx, :mem_Id, :con_Idx, :history, :reg_Date)";
			$his_stmt = $DB_con->prepare($his_query);
			$his_stmt->bindParam(":member_Idx", $mIdx);
			$his_stmt->bindParam(":mem_Id", $mem_Id);
			$his_stmt->bindParam(":con_Idx", $con_Idx);
			$his_stmt->bindParam(":history", $history);
			$his_stmt->bindParam(":reg_Date", $reg_Date);
			$his_stmt->execute();

			$result = array("result" => "success", "idx" => $mIdx);
        }
    }
    dbClose($DB_con);
	$chkstmt = null;
	$login_stmt = null;
	$cntStmt = null;
    $stmt = null;
    $upStmt = null;
	$con_ins_stmt = null;
    $con_stmt = null;
	$his_stmt = null;
} else { //빈값일 경우
    $result = array("result" => "error");
}

echo json_encode($result);
