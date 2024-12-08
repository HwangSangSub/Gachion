<?
	include "../lib/common.php";
	include "../lib/functionDB.php";  //공통 db함수
	
	$mem_Id = trim($memId);				//아이디
	$mem_SId = memSIdInfo($mem_Id);   //회원 주아이디
	//$mem_Id = "shut7720@hanmail.net";
	//$memHaddr = "테스트 집 주소";
	//$memOaddr = "테스트 사무실 주소";


	$mem_Haddr = trim($memHaddr);						 //집 주소
	$mem_HaddrNm = trim($memHaddrNm);          //집 주소명
	$mem_Oaddr = trim($memOaddr);					    //사무실 주소
	$mem_OaddrNm = trim($memOaddrNm);		    //사무실 주소명


	if($ie) { //익슬플로러일경우
	   $mem_Haddr = iconv('euc-kr', 'utf-8', $mem_Haddr);
	   $mem_HaddrNm = iconv('euc-kr', 'utf-8', $mem_HaddrNm);
	   $mem_Oaddr = iconv('euc-kr', 'utf-8', $mem_Oaddr);
	   $mem_OaddrNm = iconv('euc-kr', 'utf-8', $mem_OaddrNm);
	}

   $mem_Haddr = str_replace("null","",$mem_Haddr);
   $mem_HaddrNm = str_replace("null","",$mem_HaddrNm);
   $mem_Oaddr = str_replace("null","",$mem_Oaddr);
   $mem_OaddrNm = str_replace("null","",$mem_OaddrNm);

	if ($mem_Id != "" ) {  //아이디가 있을 경우

			$mem_Hdong = trim($memHdong);					 //집 주소 동명
			$mem_HLat = trim($memHLat);				 //집 구글 위도
			$mem_HLng = trim($memHLng);          //집 구글 경도
			$mem_Odong = trim($memOdong);					 //사무실 주소 동명
			$mem_OLat = trim($memOLat);				 //사무실 구글 위도
			$mem_OLng = trim($memOLng);          //사무실 구글 경도


			if($ie) { //익슬플로러일경우
			   $mem_Hdong = iconv('euc-kr', 'utf-8', $mem_Hdong);
			   $mem_Odong = iconv('euc-kr', 'utf-8', $mem_Odong);
			}

		   $mem_Hdong = str_replace("null","",$mem_Hdong);
		   $mem_Odong = str_replace("null","",$mem_Odong);


			$DB_con = db1();

			$memQuery = "SELECT mem_HaddrNm, mem_Haddr, mem_Hdong, mem_HLat, mem_HLng, mem_OaddrNm, mem_Oaddr, mem_Odong, mem_OLat, mem_OLng from TB_MEMBERS_MAP WHERE mem_SId = :mem_SId AND mem_Id = :mem_Id " ;
			$stmt = $DB_con->prepare($memQuery);
			$stmt->bindparam(":mem_Id",$mem_Id);
			$stmt->bindparam(":mem_SId",$mem_SId);
			$stmt->execute();
			$num = $stmt->rowCount();
			
			if($num < 1)  { //아닐경우
				$result = array("result" => "error");
			} else {

				while($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
				    
					$mHaddrNm = $row['mem_HaddrNm'];	         
					$mHaddr = $row['mem_Haddr'];	         
					$mHdong = $row['mem_Hdong'];	    
					$mHLat = $row['mem_HLat'];	    
					$mHLng = $row['mem_HLng'];	    
					$mOaddrNm = $row['mem_OaddrNm'];	         
					$mOaddr = $row['mem_Oaddr'];	         
					$mOdong = $row['mem_Odong'];	         
					$mOLat = $row['mem_OLat'];	    
					$mOLng = $row['mem_OLng'];	    
				}


				if ( $mem_Haddr != ""  || $mem_HaddrNm != "" ) { //집주소, 집주소명이 있을 경우
					$mem_Haddr =  $mem_Haddr;
					$mem_HaddrNm =  $mem_HaddrNm;
					$mem_Hdong = $mem_Hdong;					 //집 주소 동명
					$mem_HLat = $mem_HLat;				 //집 구글 위도
					$mem_HLng = $mem_HLng;          //집 구글 경도
				} else { //없을 경우
					$mem_Haddr =  $mHaddr;
					$mem_HaddrNm =  $mHaddrNm;
					$mem_Hdong = $mHdong;					 //집 주소 동명
					$mem_HLat = $mHLat;				 //집 구글 위도
					$mem_HLng = $mHLng;          //집 구글 경도
				}

				if ( $mem_Oaddr != ""  || $mem_OaddrNm != "" ) { //회사주소, 회사주소명이 있을 경우
					$mem_Oaddr =  $mem_Oaddr;
					$mem_OaddrNm =  $mem_OaddrNm;
					$mem_Odong = $mem_Odong;					 //회사 주소 동명
					$mem_OLat = $mem_OLat;				 //회사 구글 위도
					$mem_OLng = $mem_OLng;          //회사 구글 경도
				} else { //없을 경우
					$mem_Oaddr =  $mOaddr;
					$mem_OaddrNm =  $mOaddrNm;
					$mem_Odong = $mOdong;					 //회사 주소 동명
					$mem_OLat = $mOLat;				 //회사 구글 위도
					$mem_OLng = $mOLng;          //회사 구글 경도
				}


				$upQquery = "UPDATE TB_MEMBERS_MAP SET mem_HaddrNm = :mem_HaddrNm, mem_Haddr = :mem_Haddr, mem_Hdong = :mem_Hdong, mem_HLat = :mem_HLat, mem_HLng = :mem_HLng, mem_OaddrNm = :mem_OaddrNm,
                 mem_Oaddr = :mem_Oaddr, mem_Odong = :mem_Odong, mem_OLat = :mem_OLat, mem_OLng = :mem_OLng WHERE  mem_Id = :mem_Id AND mem_SId = :mem_SId LIMIT 1";
				$upStmt = $DB_con->prepare($upQquery);
				$upStmt->bindparam(":mem_HaddrNm",$mem_HaddrNm);
				$upStmt->bindparam(":mem_Haddr",$mem_Haddr);
				$upStmt->bindparam(":mem_Hdong",$mem_Hdong);
				$upStmt->bindParam("mem_HLat", $mem_HLat);
				$upStmt->bindParam("mem_HLng", $mem_HLng);
				$upStmt->bindparam(":mem_OaddrNm",$mem_OaddrNm);
				$upStmt->bindparam(":mem_Oaddr",$mem_Oaddr);
				$upStmt->bindparam(":mem_Odong",$mem_Odong);
				$upStmt->bindParam("mem_OLat", $mem_OLat);
				$upStmt->bindParam("mem_OLng", $mem_OLng);
				$upStmt->bindparam(":mem_Id",$mem_Id);
				$upStmt->bindparam(":mem_SId",$mem_SId);
				$upStmt->execute();

				$result = array("result" => "success" );

			}

		dbClose($DB_con);
		$stmt = null;
		$upStmt = null;

	} else {
		$result = array("result" => "error");

	}
		echo json_encode($result); 


?>