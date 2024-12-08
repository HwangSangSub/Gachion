<?

	include "../../lib/common.php"; 
	include "../../lib/alertLib.php";
	include "../../lib/thumbnail.lib.php";   //썸네일

	$mem_Id =  trim($mem_Id);
	$memPwd = $mem_Pwd;
	$memNm = trim($mem_Nm);
	$memTel = trim($mem_Tel);
	$memEmail = trim($mem_Email);
	$modDate =  DU_TIME_YMDHIS;
	$use_Date =  trim($use_Date);
	$add_use_Date =  trim($add_use_Date);

	if ($memPwd =="") {
		$mem_Pwd = $mem_Pwd;
	} else {
		$mem_Pwd = password_hash($memPwd, PASSWORD_DEFAULT);  // 비밀번호 암호화 
	}

	$DB_con = db1();


	if($add_use_Date == '30'){
		$timestamp = strtotime("+30 days", strtotime($use_Date));
	}else if($add_use_Date == '60'){
		$timestamp = strtotime("+60 days", strtotime($use_Date));
	}else if($add_use_Date == '90'){
		$timestamp = strtotime("+90 days", strtotime($use_Date));
	}
	//사용일! 입력하기
	$useDate = date("Y-m-d", $timestamp);	
	if ($mode == "reg") {

	} else if ($mode == "mod") { //수정일경우
        //회원 기본 수정
		$upQquery = "UPDATE TB_MEMBERS SET mem_Email = :mem_Email, mem_Pwd = :mem_Pwd, mem_Tel = :mem_Tel, use_Date = :use_Date, mod_Date = :mod_Date WHERE mem_Id = :mem_Id LIMIT 1";
		$upStmt = $DB_con->prepare($upQquery);
		$upStmt->bindparam(":mem_Email",$mem_Email);
		$upStmt->bindparam(":mem_Pwd",$mem_Pwd);
		$upStmt->bindParam(":mem_Tel", $memTel);
		$upStmt->bindParam(":use_Date", $useDate);
		$upStmt->bindParam(":mod_Date", $modDate);
		$upStmt->bindParam(":mem_Id", $mem_Id);
		$upStmt->execute();

		$preUrl = "memberList.php?page=$page&$qstr";
		$message = "mod";
		proc_msg($message, $preUrl);


	} else {  //삭제일경우

		$array = explode('/', $chk);

			foreach($array as $k=>$v) {
				$chkIdx = $v;

				//회원 아이디 검색
				$chkQuery = "";
				$chkQuery = " SELECT mem_Id, mem_ImgFile FROM TB_MEMBERS WHERE idx = :idx ";
				//echo $chkQuery."<BR>";
				//exit;
				$chkStmt = $DB_con->prepare($chkQuery);
				$chkStmt->bindparam(":idx",$chkIdx);
				$chkStmt->execute();
				$chkNum = $chkStmt->rowCount();
				//echo $chkNum."<BR>";
				//exit;
				
				if($chkNum < 1) { //매칭값이 맞지 않을 경우
				} else {  // 취소가능
				    
				    while($row=$chkStmt->fetch(PDO::FETCH_ASSOC)) {
				        $memImgFile = $row['mem_ImgFile'];
				    }
				}

				//매칭생성 진행 중인지 체크 (매칭중, 메칭요청, 만남중, 만남완료, 이동중)
				$chkCntQuery = "SELECT count(taxi_MemId)  AS num from TB_STAXISHARING WHERE taxi_MemId = :taxi_MemId AND taxi_State NOT IN ('9', '10') " ;
				$stmt = $DB_con->prepare($chkCntQuery);
				$stmt->bindparam(":taxi_MemId",$memId);
				$stmt->execute();
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				$num = $row['num'];


				//매칭자 수락한 자가 있는 지 체크 상태값이 2가 맞음
				$cntQuery = "SELECT count(taxi_MemId) AS num from TB_RTAXISHARING WHERE  taxi_RIdx = :taxiRIdx AND taxi_MemId = :taxiMemId AND ( taxi_RState = '0' or taxi_RState = '1' or taxi_RState = '3' or taxi_RState = '4' or taxi_RState = '5' ) " ;
				$cntStmt = $DB_con->prepare($cntQuery);
				$cntStmt->bindparam(":taxiRIdx",$taxiRIdx);
				$cntStmt->bindparam(":taxiMemId",$memId);
				$cntStmt->execute();
				$cntRow = $cntStmt->fetch(PDO::FETCH_ASSOC);
				$cntNum = $cntRow['num'];

				if ($num == "0" && $cntNum == "0" ) { // 매칭신청 진행중인 회원이 없을 경우는 탈퇴처리

					$mem_Tel = ""; //전화번호
					$mem_Birth = ""; //생년월일
					$mem_Sex = ""; //성별
					$mem_ImgFile = ""; //회원이미지
					$mem_Haddr = ""; //회원주소
					$mem_Oaddr = ""; //사무실주소
					$mem_Point = "0";  //캐시
					$mem_Coupon = "0"; //쿠폰
					$mem_MatCnt = "0";  //매칭카운트 성공 횟수
					$b_Disply = "Y"; //탈퇴
					$reg_Date = DU_TIME_YMDHIS;		   //탈퇴일

					$upQquery = "UPDATE TB_MEMBERS SET mem_Tel = :mem_Tel, mem_Birth = :mem_Birth, mem_Sex = :mem_Sex, mem_ImgFile = :mem_ImgFile, mem_Haddr = :mem_Haddr, mem_Oaddr = :mem_Oaddr, mem_Point = :mem_Point, mem_Coupon = :mem_Coupon, mem_MatCnt = :mem_MatCnt, b_Disply = :b_Disply, leaved_Date = :leaved_Date  WHERE idx = :idx LIMIT 1";
					$upStmt = $DB_con->prepare($upQquery);
					$upStmt->bindparam(":mem_Tel",$mem_Tel);
					$upStmt->bindparam(":mem_Birth",$mem_Birth);
					$upStmt->bindparam(":mem_Sex",$mem_Sex);
					$upStmt->bindparam(":mem_ImgFile",$mem_ImgFile);
					$upStmt->bindParam(":mem_Haddr", $mem_Haddr);
					$upStmt->bindParam(":mem_Oaddr", $mem_Oaddr);
					$upStmt->bindparam(":mem_Point",$mem_Point);
					$upStmt->bindParam(":mem_Coupon", $mem_Coupon);
					$upStmt->bindParam(":mem_MatCnt", $mem_MatCnt);
					$upStmt->bindParam(":b_Disply", $b_Disply);
					$upStmt->bindParam(":leaved_Date", $reg_Date);
					$upStmt->bindParam(":idx", $chkIdx);
					$upStmt->execute();

					echo "success";


				} else { //매칭 진행중인 회원이 있을 경우
					echo "fail";  

				}

			}

	}

	
	dbClose($DB_con);
	$stmt = null;
	$upStmt = null;
	$upStmt2 = null;
	$chkStmt = null;
	$cntStmt = null;
	$upStmt = null;

	?>