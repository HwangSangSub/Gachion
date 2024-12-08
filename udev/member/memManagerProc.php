<?
	include "../../lib/common.php"; 
	include "../../lib/alertLib.php";
	include "../../lib/thumbnail.lib.php";   //썸네일
	
	
	$DB_con = db1();
	
	$mbImgUrl = $_SERVER["DOCUMENT_ROOT"]."/taxiKing/data/levIcon"; // 이미지 경로(삭제시 필요)
	
	$memQuery = "SELECT memIconFile FROM TB_MEMBER_LEVEL WHERE idx = :idx " ;
	$stmt = $DB_con->prepare($memQuery);
	$stmt->bindparam(":idx",$idx);
	$stmt->execute();
	$num = $stmt->rowCount();
	
	while($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
	    $memIconFile = $row['memIconFile'];     // 이미지 파일
	}
	
	
	// 회원 등급이미지 경로
	$mb_dir = DU_DATA_PATH.'/levIcon';
	$mb_img = "icon_vvip_".$mem_Lv.".png";
	
	//이미지가 있을 경우 이미지 삭제
	if ($del_mb_img == TRUE || $mem_ImgFile == "") {
	    @unlink("$mbImgUrl/$memIconFile");
	    $mem_ImgFile = "";
	} else {
	    $mem_ImgFile = "$memIconFile";
	}
	
	
	// 회원 프로필 이미지 업로드
	$image_regex = "/(\.(gif|jpe?g|png))$/i";
	
	$cf_img_width = "132";
	$cf_img_height = "132";
	
	if (isset($_FILES['mb_img']) && is_uploaded_file($_FILES['mb_img']['tmp_name'])) {  //이미지 업로드 성공일 경우
	    
	    @unlink($mb_dir.'/'.$mb_id.'.gif');
	    
	    if (preg_match($image_regex, $_FILES['mb_img']['name'])) {
	        
	        @mkdir($mb_dir, 0755);
	        //@chmod($mb_dir, 0644);
	        
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
	

	if ($mode == "reg") {  //등록일 경우

			$insQuery = "INSERT INTO TB_MEMBER_LEVEL ( memLv, memLv_Name, memIconFile, memMatCnt, memDc ) VALUES ( :memLv, :memLv_Name, :memIconFile, :memMatCnt, :memDc )";
			$stmt = $DB_con->prepare($insQuery);
			$stmt->bindParam(":memLv", $mem_Lv);
			$stmt->bindParam(":memLv_Name", $memLv_Name);		
			$stmt->bindParam(":memIconFile", $mem_ImgFile);
			$stmt->bindParam(":memMatCnt", $memMatCnt);
			$stmt->bindParam(":memDc", $memDc);
			$stmt->execute();
			$DB_con->lastInsertId();

			$preUrl = "memManagerList.php?page=$page&$qstr";
			$message = "reg";
			proc_msg($message, $preUrl);


	} else if ($mode == "mod") { //수정일경우

			$upQquery = "UPDATE TB_MEMBER_LEVEL SET memLv = :memLv, memLv_Name = :memLv_Name, memIconFile = :memIconFile, memMatCnt = :memMatCnt, memDc = :memDc WHERE idx =  :idx LIMIT 1";
			$upStmt = $DB_con->prepare($upQquery);
			$upStmt->bindparam(":memLv",$mem_Lv);
			$upStmt->bindParam(":memLv_Name", $memLv_Name);
			$upStmt->bindParam(":memIconFile", $mem_ImgFile);
			$upStmt->bindParam(":memMatCnt", $memMatCnt);
			$upStmt->bindParam(":memDc", $memDc);
			$upStmt->bindParam(":idx", $idx);
			$upStmt->execute();

			$preUrl = "memManagerList.php?page=$page&$qstr";
			$message = "mod";
			proc_msg($message, $preUrl);

   	} else {  //삭제일경우

		    $check = trim($chk);
			$array = explode('/', $check);

			foreach($array as $k=>$v) {
				$idx = $v;
				$delQquery = "DELETE FROM TB_MEMBER_LEVEL WHERE idx =  :idx LIMIT 1";

				$delStmt = $DB_con->prepare($delQquery);
				$delStmt->bindParam(":idx", $idx);
				$delStmt->execute();

			}

			echo "success";

	}
	
	dbClose($DB_con);
	$stmt = null;
	$upStmt = null;
	$delStmt = null;
	
	
?>