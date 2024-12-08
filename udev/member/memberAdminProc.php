<?

	include "../../lib/common.php"; 
	include "../../lib/alertLib.php";
	include "../../lib/thumbnail.lib.php";   //썸네일


	if ($memPwd =="") {
		$mem_Pwd = $mem_Pwd;
	} else {
		$mem_Pwd = password_hash($memPwd, PASSWORD_DEFAULT);  // 비밀번호 암호화 
	}

	$DB_con = db1();
	
	$mbImgUrl = $_SERVER["DOCUMENT_ROOT"]."/data/member"; // 이미지 경로(삭제시 필요)
	
	$memQuery = "SELECT idx, mem_ImgFile FROM TB_MEMBERS WHERE mem_Id = :mem_Id AND b_Disply = 'N'" ;
	$stmt = $DB_con->prepare($memQuery);
	$stmt->bindparam(":mem_Id",$mem_Id);
	$stmt->execute();
	$num = $stmt->rowCount();
	
	while($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
	  $idx = $row['idx'];      // idx
	  $mem_ImgFile = $row['mem_ImgFile'];     // 이미지 파일
	}
	

	// 회원 이미지 경로
	$mb_dir = DU_DATA_PATH.'/member';
	//$mb_id = substr($mem_Id,0,2)."_".$idx;
	$mb_id = $mem_Id;
	
	//이미지가 있을 경우 이미지 삭제
	if ($del_mb_img == TRUE) {
	    @unlink("$mbImgUrl/$mem_ImgFile");
	    $mem_ImgFile = "";
	} else {
	    $mem_ImgFile = $mem_ImgFile;
	}
	
	
	// 회원 프로필 이미지 업로드
	$image_regex = "/(\.(gif|jpe?g|png))$/i";

	$cf_img_width = "100";
	$cf_img_height = "100";

	if (isset($_FILES['mb_img']) && is_uploaded_file($_FILES['mb_img']['tmp_name'])) {  //이미지 업로드 성공일 경우

		@unlink($mb_dir.'/'.$mb_id.'.gif');

		if (preg_match($image_regex, $_FILES['mb_img']['name'])) {

			@mkdir($mb_dir, 0755);
			//@chmod($mb_dir, 0644);
			//파일명
			$mb_img = $mb_id.'.gif';

			$dest_path = $mb_dir.'/'.$mb_img;

			//echo $_FILES['mb_img']['tmp_name']."<BR>";			
			//echo $dest_path."<BR>";
			move_uploaded_file($_FILES['mb_img']['tmp_name'], $dest_path);
		
			if (file_exists($dest_path)) {
				$size = @getimagesize($dest_path);

				if (!($size[2] === 1 || $size[2] === 2 || $size[2] === 3)) { // gif jpg png 파일이 아니면 올라간 이미지를 삭제한다.
					@unlink($dest_path);
				} else if ($size[0] > $cf_img_width || $size[1] > $cf_img_height) {
					$thumb = null;
					if($size[2] === 2 || $size[2] === 3) {
						//jpg 또는 png 파일 적용
						$thumb = thumbnail($mb_img, $mb_dir, $mb_dir, $cf_img_width, $cf_img_height, true, true);

						if($thumb) {
							@unlink($dest_path);
							rename($mb_dir.'/'.$thumb, $dest_path);
						}
					}
					if( !$thumb ){
						// 아이콘의 폭 또는 높이가 설정값 보다 크다면 이미 업로드 된 아이콘 삭제
						@unlink($dest_path);
					}
				}
				//=================================================================\
			}

			$mem_ImgFile = $mb_img;	

		}

	}
		
	//이미지경로 출력
	$member_img = $mbImgUrl.'/'.$mb_img;
	

	//파일저장방법 변경 _blob -------------------------------------------------------- 2019.02.14
	if(file_exists($member_img))
	{
		//첨부파일 -> 썸네일 이미지로 변경 및 저장된 경로
		$filename = $member_img;
		$handle = fopen($filename,"rb");
		$size =	GetImageSize($filename);
		$width = $size[0];
		$height = $size[1];
		$imageblob = addslashes(fread($handle, filesize($filename)));
		$filesize = filesize($filename);
		$mine = $size[mime];
		fclose($handle);
		
		// 입력 및 업데이트 일자를 time() 함수를 이용한 int값으로 저장
		// 추후 저장된 파일과 변경여부 확인을 위한 값
		$now_time = time();		

		$chkQuery = " SELECT count(*) as num FROM TB_MEMBER_PHOTO WHERE mem_id = :id ";
		$cntStmt = $DB_con->prepare($chkQuery);
		$cntStmt->bindparam(":id",$mem_Id);
		$cntStmt->execute();
		$row = $cntStmt->fetch(PDO::FETCH_ASSOC);
		$num = $row['num'];
		
		// 회원 프로필 이미지(BLOB) 저장 
		// 기존 프로필 이미지가 존재할 경우, update
		// 기존 프로필 이미지가 존재하지 않을  경우, insert
		if($num > 0)
		{
			// 기존파일이 있을 경우,
			// 기존파일 삭제
			//----------------------------------------------------------------------------------------------------S
			$chkQuery = " SELECT mem_profile_update FROM TB_MEMBER_PHOTO WHERE mem_id = :id ";
			$cntStmt = $DB_con->prepare($chkQuery);
			$cntStmt->bindparam(":id",$mem_Id);
			$cntStmt->execute();
			$row = $cntStmt->fetch(PDO::FETCH_ASSOC);
			$mem_profile_update = $row['mem_profile_update'];
			$update_file = $mbImgUrl.'/'.$mem_profile_update;
			if (file_exists($update_file)) {
				@unlink($update_file);
			} 
			//----------------------------------------------------------------------------------------------------E

			$insQuery = "
				update TB_MEMBER_PHOTO 
				set 
					mem_profile='".$imageblob."', 
					mem_profile_update ='".$now_time."' 
				where 
					mem_Id ='".$mem_Id."' 
			";
		}
		else
		{		
			$insQuery = "
				INSERT INTO 
					TB_MEMBER_PHOTO (mem_id, mem_profile, mem_profile_update) 
				values 
					('".$mem_Id."', '".$imageblob."', '".$now_time."')
			";
		}		
		$DB_con->exec($insQuery);

		// 파일로 blob형태 이미지 저장----------S
		// 새로 생성되는 파일명(전체경로 포함) : $m_file
		$img_txt = $now_time;
		$m_file = $mbImgUrl.'/'.$img_txt;
		$is_file_exist = file_exists($m_file);

		if ($is_file_exist) {
			//echo 'Found it';
		} else {
			//echo 'Not found.';
			$file = fopen($m_file , "w");
			fwrite($file, $imageblob);
			fclose($file);
			chmod($m_file, 0755);

			//등록한 썸네일이미지 삭제
			@unlink($member_img);
		}
		// 파일로 blob형태 이미지 저장----------E

	}
	//파일저장방법 변경 _blob --------------------------------------------------------


	
	if ($mode == "reg") {		// 추가일 경우
		
		$rand_num = sprintf('%03d',rand(000,999));
        //회원 주 아이디 생성 (랜덤  : 년도 + 랜덤수 + 일자 + max값(db))
        $nowYear = date("Y");
        $nowMonth = date("m");
        $nowDay = date("d");
        
        $memSId = $nowYear.getRandID($nowYear, $nowMonth, $nowDay, 9).$nowMonth.$nowDay.$rand_num;

        
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
            
            $mem_Lv = 1;													 // 등급 - 관리자추가로 level 1
            $b_Disply = "N";												 //탈퇴여부(N:가입/Y:탈퇴)
            $reg_Date = date("Y-m-d H:i:s");										 //등록일

            
            //회원 기본테이블 저장
            $insQuery = "
				INSERT INTO TB_MEMBERS (mem_SId, mem_Id, mem_NickNm, mem_Tel, mem_Lv, b_Disply, mem_Code, reg_date ) 
				VALUES ('".$memSId."', '".$mem_Id."', '".$mem_NickNm."', '".$mem_Tel."', '".$mem_Lv."', '".$b_Disply."','".$mem_Code."', '".$reg_Date."' )";
            $DB_con->exec($insQuery);
			$mIdx = $DB_con->lastInsertId();  //저장된 idx 값

            
            if($stmt->rowCount() > 0 ) { //삽입 성공
                
                //회원 정보테이블 저장
                $insInFoQuery = "
					INSERT INTO TB_MEMBERS_INFO (mem_Idx, mem_SId, mem_Id, mem_Email ) 
					VALUES ('".$mIdx."', '".$memSId."', '".$mem_Id."', '".$mem_Email."' )";
				$DB_con->exec($insInFoQuery);
                
                //회원 기타테이블 저장
                $insEtcQuery = "
					INSERT INTO TB_MEMBERS_ETC (mem_Idx, mem_SId, mem_Id) 
					VALUES ('".$mIdx."', '".$memSId."', '".$mem_Id."' )";
				$DB_con->exec($insEtcQuery);
                
                //회원 주소테이블 저장
                $insMapQuery = "	
					INSERT INTO TB_MEMBERS_MAP (mem_Idx, mem_SId, mem_Id) 
					VALUES ('".$mIdx."', '".$memSId."', '".$mem_Id."' )";
				$DB_con->exec($insMapQuery);
                
            } else { //등록시 에러
                $result = array("result" => "error");
            }
            
        }
		
		$preUrl = "memberAdminList.php?page=$page&$qstr";
		$message = "reg";
		proc_msg($message, $preUrl);



	} else if ($mode == "mod") { //수정일경우
	    
        //회원 기본 수정
		$upQquery = "UPDATE TB_MEMBERS SET mem_NickNm = :mem_NickNm, mem_Pwd = :mem_Pwd,  mem_Lv = :mem_Lv, mem_Tel = :mem_Tel, mem_Birth = :mem_Birth, mem_NPush = :mem_NPush WHERE mem_Id = :mem_Id LIMIT 1";
		$upStmt = $DB_con->prepare($upQquery);
		$upStmt->bindparam(":mem_NickNm",$mem_NickNm);
		$upStmt->bindparam(":mem_Pwd",$mem_Pwd);
		$upStmt->bindparam(":mem_Lv",$mem_Lv);
		$upStmt->bindParam(":mem_Tel", $mem_Tel);
		$upStmt->bindParam(":mem_Birth", $mem_Birth);
		$upStmt->bindParam(":mem_NPush", $mem_NPush);
		$upStmt->bindParam(":mem_Id", $mem_Id);
		$upStmt->execute();

		//회원 기타 정보 수정
		$upQquery2 = "UPDATE TB_MEMBERS_INFO SET mem_Sex = :mem_Sex, mem_Seat = :mem_Seat, mem_Memo = :mem_Memo WHERE mem_Id = :mem_Id LIMIT 1";
		$upStmt2 = $DB_con->prepare($upQquery2);
		$upStmt2->bindParam(":mem_Sex", $mem_Sex);
		$upStmt2->bindParam(":mem_Seat", $mem_Seat);
		$upStmt2->bindParam(":mem_Memo", $mem_Memo);
		$upStmt2->bindParam(":mem_Id", $mem_Id);
		$upStmt2->execute();
		
		$preUrl = "memberAdminList.php?page=$page&$qstr";
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


					//회원 이미지 삭제
					@unlink($mb_dir.'/'.$memImgFile);

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