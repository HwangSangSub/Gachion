<?
/*======================================================================================================================

* 프로그램			: 회원 탈퇴 관련 함수
* 페이지 설명		: 회원 탈퇴 관련 함수

========================================================================================================================*/

/*회원 이미지 삭제 */
function memImgDel($mem_Id) {
    
    $fDB_con = db1();
    
    $chkMQuery = "";
    $chkMQuery = " SELECT mem_ImgFile FROM TB_MEMBERS WHERE mem_Id = :mem_Id ";
    //echo $chkMQuery."<BR>";
    //exit;
    $chkMStmt = $fDB_con->prepare($chkMQuery);
    $chkMStmt->bindparam(":mem_Id",$mem_Id);
    $chkMStmt->execute();
    $chkMNum = $chkMStmt->rowCount();
    //echo $chkNum."<BR>";
    //exit;
    
    if($chkMNum < 1) { //이미지가 없을 경우
    } else {  // 이미지가 있을 경우
        
        $mbImgUrl = $_SERVER["DOCUMENT_ROOT"]."/member/member_img"; // 이미지 경로(삭제시 필요)
        
        while($chkMRow=$chkMStmt->fetch(PDO::FETCH_ASSOC)) {
            $memImgFile = trim($chkMRow['mem_ImgFile']);
        }
        
        //회원 이미지 삭제
        @unlink("$mbImgUrl/$memImgFile");
    }


    dbClose($fDB_con);
    $chkMStmt = null;
}
/*회원 탈퇴*/
function memDate($mem_Id) {
    $fDB_con = db1();
	//회원상태확인
	$cntMemQuery = "
		SELECT *
		FROM TB_MEMBERS
		WHERE mem_Id = :mem_Id
			AND b_Disply = 'N'
		LIMIT 1;
	";
	$cntMemStmt = $fDB_con->prepare($cntMemQuery);
	$cntMemStmt->bindParam(":mem_Id", $mem_Id);
	$cntMemStmt->execute();
	$cntMemNum = $cntMemStmt->rowCount();
	while($cntMemRow=$cntMemStmt->fetch(PDO::FETCH_ASSOC)) {
		$memIdx = $cntMemRow['idx'];
	}
	//회원 탈퇴 기본 저장 중복 등록을 맞기 위해서 체크 함
	if ($cntMemNum > 0) {
		// member DB 탈퇴처리
		$memQuery = "
			UPDATE TB_MEMBERS
			SET b_Disply = 'Y'
				, mod_Date = NOW()
				, leaved_Date = NOW()
			WHERE mem_Id = :mem_Id
				AND idx = :memIdx
				AND b_Disply = 'N'
			LIMIT 1;
		";
		$memStmt = $fDB_con->prepare($memQuery);
		$memStmt->bindParam(":mem_Id", $mem_Id);
		$memStmt->bindParam(":memIdx", $memIdx);
		$memStmt->execute();

		// 지도 비공개처리 조회-비공개처리
		$contentCQuery = "
			SELECT idx
			FROM TB_CONTENTS
			WHERE reg_Id = :reg_Id
				AND member_Idx = :memIdx;
		";
		$contentCStmt = $fDB_con->prepare($contentCQuery);
		$contentCStmt->bindParam(":reg_Id", $mem_Id);
		$contentCStmt->bindParam(":memIdx", $memIdx);
		$contentCStmt->execute();
		
		while($contentCrow=$contentCStmt->fetch(PDO::FETCH_ASSOC)) {
			$cidx = $contentCrow['idx'];			//회원이 등록한 지도
			$contentQuery = "
				UPDATE TB_CONTENTS
				SET open_Bit = '1'
					, mod_Date = NOW()
				WHERE idx = :idx
				LIMIT 1;
			";
			$contentStmt = $fDB_con->prepare($contentQuery);
			$contentStmt->bindParam(":idx", $cidx);
			$contentStmt->execute();
		}


		dbClose($fDB_con);
		$cntMemStmt = null;
		$memStmt = null;
		$contentCStmt = null;
		$contentStmt = null;
		return "1";
	}else{
		return "0";
	}
}
?>