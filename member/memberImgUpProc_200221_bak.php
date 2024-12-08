<?
header('Content-Type: application/json; charset=UTF-8');
include "../lib/common.php";
include "../lib/functionDB.php";  //공통 db함수
include "../lib/thumbnail.lib.php";   //썸네일

$mem_Id = trim($memId);				//아이디

if ($mem_Id != "" ) {  //아이디가 있을 경우
    $DB_con = db1();
    
    $mIdx = memIdxInfo($mem_Id);   //회원 주아이디
    
    if ($_FILES['mbImg']['name'] == "") {
        $result = array("result" => "success", "Msg" => "없을경우");
    } else {
        $mbImgUrl = $_SERVER["DOCUMENT_ROOT"]."/member/member_img"; // 이미지 경로(삭제시 필요)
        
        $memQuery = "SELECT idx, mem_ImgFile FROM TB_MEMBERS WHERE idx = :idx AND mem_Id = :mem_Id AND b_Disply = 'N'" ;
        $stmt = $DB_con->prepare($memQuery);
        $stmt->bindparam(":idx",$mIdx);
        $stmt->bindparam(":mem_Id",$mem_Id);
        $stmt->execute();
        $num = $stmt->rowCount();
        
        while($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
            $idx = $row['idx'];      // idx
            $mem_ImgFile = $row['mem_ImgFile'];     // 이미지 파일
        }
        
		$his_Query = "SELECT idx FROM TB_HISTORY WHERE mem_Id = :mem_Id AND history = '프로필사진변경' AND reg_Id = :reg_Id; " ;
		$his_stmt = $DB_con->prepare($his_Query);
		$his_stmt->bindparam(":mem_Id",$mem_Id);
		$his_stmt->bindparam(":reg_Id",$mem_Id);
		$his_stmt->execute();
		$his_row=$his_stmt->fetch(PDO::FETCH_ASSOC);
		$his_idx = $his_row['idx'];
		if($his_idx != ""){
			$his_del_query ="DELETE FROM TB_HISTORY WHERE idx = :idx LIMIT 1;";
			$his_del_stmt = $DB_con->prepare($his_del_query);
			$his_del_stmt->bindParam(":idx", $his_idx);
			$his_del_stmt->execute();
			$history = "프로필사진변경";
			$his_query ="INSERT INTO TB_HISTORY (member_Idx, mem_Id, history, reg_Id, reg_date) VALUES (:member_Idx, :mem_Id, :history, :reg_Id, NOW())";
			$his_stmt = $DB_con->prepare($his_query);
			$his_stmt->bindParam(":member_Idx", $mIdx);
			$his_stmt->bindParam(":mem_Id", $mem_Id);
			$his_stmt->bindParam(":history", $history);
			$his_stmt->bindParam(":reg_Id", $mem_Id);
			$his_stmt->execute();
		}else{
			$history = "프로필사진변경";
			$his_query ="INSERT INTO TB_HISTORY (member_Idx, mem_Id, history, reg_Id, reg_date) VALUES (:member_Idx, :mem_Id, :history, :reg_Id, NOW())";
			$his_stmt = $DB_con->prepare($his_query);
			$his_stmt->bindParam(":member_Idx", $mIdx);
			$his_stmt->bindParam(":mem_Id", $mem_Id);
			$his_stmt->bindParam(":history", $history);
			$his_stmt->bindParam(":reg_Id", $mem_Id);
			$his_stmt->execute();
		}
        // 회원 이미지 경로
        $mb_dir = $_SERVER["DOCUMENT_ROOT"].'/member/member_img';
        $mb_id = $mem_Id;
        $mbImg = $mb_id.'.gif';
        
        //이미지가 있을 경우 이미지 삭제
        if (!$mem_ImgFile == "") {
            @unlink("$mbImgUrl/$mem_ImgFile");
        } else {
			$first_img = "Y";
		}
		
        
        // 회원 프로필 이미지 업로드
        $image_regex = "/(\.(gif|jpe?g|png))$/i";
        
        $cf_img_width = "100";
        $cf_img_height = "100";
        
        
        if (isset($_FILES['mbImg']) && is_uploaded_file($_FILES['mbImg']['tmp_name'])) {  //이미지 업로드 성공일 경우
            
            if (preg_match($image_regex, $_FILES['mbImg']['name'])) {
                
                @mkdir($mb_dir, 0755);
                //@chmod($mb_dir, 0644);
                
                $dest_path = $mb_dir.'/'.$mbImg;
                //echo $_FILES['mbImg']['tmp_name']."<BR>";
                //echo $dest_path."<BR>";
                //exit;
                move_uploaded_file($_FILES['mbImg']['tmp_name'], $dest_path);
                
                if (file_exists($dest_path)) {
                    
                    
                    $size = @getimagesize($dest_path);
                    
                    if (!($size[2] === 1 || $size[2] === 2 || $size[2] === 3)) { // gif jpg png 파일이 아니면 올라간 이미지를 삭제한다.
                        @unlink($dest_path);
                    } else if ($size[0] > $cf_img_width || $size[1] > $cf_img_height) {
                        $thumb = null;
                        if($size[2] === 2 || $size[2] === 3) {
                            
                            //jpg 또는 png 파일 적용
                            $thumb = thumbnail($mbImg, $mb_dir, $mb_dir, $cf_img_width, $cf_img_height, true, true);
                            
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

				//이미지경로 출력
				$member_img = $mbImgUrl.'/'.$mbImg;
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
					$mine = $size['mime'];
					fclose($handle);
					
					// 입력 및 업데이트 일자를 time() 함수를 이용한 int값으로 저장
					// 추후 저장된 파일과 변경여부 확인을 위한 값
					$now_time = time();		

					$chkQuery = " SELECT count(*) as num FROM TB_MEMBERS_PHOTO WHERE mem_id = :id ";
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
						$chkQuery = " SELECT mem_profile_update FROM TB_MEMBERS_PHOTO WHERE mem_id = :id ";
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
							update TB_MEMBERS_PHOTO 
							set 
								mem_profile='".$imageblob."', 
								mem_profile_update ='".$now_time."' 
							where 
								mem_Id ='".$mem_Id."' 
								AND member_Idx = ".$mIdx."
						";

					}
					else
					{		
						$insQuery = "
							INSERT INTO 
								TB_MEMBERS_PHOTO (member_Idx, mem_id, mem_profile, mem_profile_update) 
							values 
								(".$mIdx.", '".$mem_Id."', '".$imageblob."', '".$now_time."')
						";
					}		
					$DB_con->exec($insQuery);

					// 파일로 blob형태 이미지 저장----------S
					// 새로 생성되는 파일명(전체경로 포함) : $m_file
					$img_txt = $mbImg;
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
				//
                
                // 이미지 업로드
                // 회원가입 이후 최초 이미지 업로드시, 이미지명값을 넣어줘야 함.
				// 기본값으로 입력 해줘야 함.
				// ___ 2019.02.27
				if($first_img == "Y") {
					$upQquery = "UPDATE TB_MEMBERS SET mem_ImgFile = :mem_ImgFile WHERE mem_Id = :mem_Id AND idx = :idx LIMIT 1";
					$upStmt = $DB_con->prepare($upQquery);
					$upStmt->bindparam(":mem_ImgFile",$mem_ImgFile);
					$upStmt->bindparam(":mem_Id",$mem_Id);
					$upStmt->bindparam(":idx",$mIdx);
					$mem_ImgFile = $mbImg;
					$mem_Id = $mem_Id;
					$upStmt->execute();
				}
                
                $result = array("result" => "success");
                
            } else { //이미지 업로드 실패일 경우
                $result = array("result" => "error");
            }
            //echo $result."<BR>";
        }
        
        
    }
    
    dbClose($DB_con);
    $stmt = null;
    $upStmt = null;
    
    
} else {  //아이디가 없을 경우
    
    $result = array("result" => "error");
}

echo json_encode($result);

?>