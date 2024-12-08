<?
/*
* 프로그램				: 지도정보를 수정하는 기능
* 페이지 설명			: 카테고리, 지도명
* 파일명					: mod_contents.php
* 관련DB					: TB_CONTENTS
*/
include "../lib/common.php";
include "../lib/functionDB.php";  //공통 db함수

$con_Idx = trim($conIdx);									//지도고유번호
$reg_Id = contentsIdInfo($con_Idx);						//지도등록자 아이디
$con_Name = trim($conName);							//지도명
if($con_Name == ""){
	$con_Name = "";
}
$category = trim($category);								//지도카테고리
if($category == ""){
	$category = "";
}
$memo = trim($memo);										//지도소개글
if($memo == ""){
	$memo = "";
}
$tag = trim($tag);												//지도태그
if($tag == ""){	
	$tag = "";
}
$open_Bit = trim($openBit);								//지도공개설정여부
if($open_Bit == ""){
	$open_Bit = "";
}
$del_Img_Bit = trim($delimg_Bit);							//썸네일 이미지 삭제여부
if($del_Img_Bit == ""){
	$del_Img_Bit = "0";										//디폴트 삭제안함
}
$now_time = time();											// 추후 파일 디렉토리가 될 예정
$mode = trim($mode);										// 모드(del: 지도삭제)
$DB_con = db1();

function delete_all($file_dir) {
	$d = @dir($file_dir);
	while ($entry = $d->read()) {
		if ($entry == "." || $entry == "..") continue;
		if (is_dir($entry)) delete_all($entry);
		else unlink($file_dir."/".$entry);
	}
 
	// 해당디렉토리도 삭제할 경우에는 아래 주석처리를 해제합니다.
	//unlink($file_dir);
	rmdir($file_dir);
}

// 지도고유번호를 확인한다.
$chk_query = "
		SELECT count(idx) as cnt
		FROM TB_CONTENTS
		WHERE idx = :con_Idx
			AND delete_Bit = '0';
	";
//			
$chk_stmt = $DB_con->prepare($chk_query);
$chk_stmt->bindParam(":con_Idx", $con_Idx);
$chk_stmt->execute();
$chk_row = $chk_stmt->fetch(PDO::FETCH_ASSOC);
$chk_Cnt = $chk_row['cnt'];
if($chk_Cnt > 0){				//지도가 있는 경우
	if($mode == "del"){
		$con_chk_query = "
			SELECT count(*) as cnt
			FROM TB_CONTENTS
			WHERE reg_Id = :reg_Id
		";
		$con_chk_stmt = $DB_con->prepare($con_chk_query);
		$con_chk_stmt->bindParam(":reg_Id", $reg_Id);
		$con_chk_stmt->execute();
		$con_chk_row=$con_chk_stmt->fetch(PDO::FETCH_ASSOC);
		$con_chk_cnt = $con_chk_row['cnt'];
		if($con_chk_cnt < 2){
			$result = array("result" => "error", "errorMsg" => "지도가 1개이하일 경우 삭제할 수 없습니다.");
		}else{
			//지도삭제시작
			$query = "
				SELECT idx, member_Idx, img, reg_Id
				FROM TB_CONTENTS
				WHERE idx = :con_Idx
					AND delete_Bit = '0';
			";
			$stmt = $DB_con->prepare($query);
			$stmt->bindParam(":con_Idx", $con_Idx);
			$stmt->execute();
			while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
				$org_del_img = $row['img'];
				$member_Idx = $row['member_Idx'];
				$reg_Id = $row['reg_Id'];	
				$file_img = $file_dir.'/'.$org_del_img;
				@unlink($file_img);
				del_thumbnail(dirname($file_img), basename($file_img));
// 지도관련 히스토리 삭제
				$history_con_del_query = "
					SELECT idx
					FROM TB_HISTORY
					WHERE con_Idx = :con_Idx
						AND history IN ('지도생성', '구독')
				";
				$history_con_del_stmt = $DB_con->prepare($history_con_del_query);
				$history_con_del_stmt->bindParam(":con_Idx", $con_Idx);
				$history_con_del_stmt->execute();
				$history_con_del_num = $history_con_del_stmt->rowCount();
				if($history_con_del_num > 0){
					while($history_con_del_row = $history_con_del_stmt->fetch(PDO::FETCH_ASSOC)){
						$idx = $history_con_del_row['idx'];						// 지점고유번호
						$del_history_query = "
							DELETE FROM TB_HISTORY
							WHERE idx = :idx
							LIMIT 1;
						";
						$del_history_stmt = $DB_con->prepare($del_history_query);
						$del_history_stmt->bindParam(":idx", $idx);
						$del_history_stmt->execute();
					}
				}
// 지도구독 삭제
				$subs_del_query = "
					SELECT idx
					FROM TB_MEMBERS_SUBSCRIBE
					WHERE con_Idx = :con_Idx;
				";
				$subs_del_stmt = $DB_con->prepare($subs_del_query);
				$subs_del_stmt->bindParam(":con_Idx", $con_Idx);
				$subs_del_stmt->execute();
				$subs_del_num = $subs_del_stmt->rowCount();
				if($subs_del_num > 0){
					while($subs_del_row = $subs_del_stmt->fetch(PDO::FETCH_ASSOC)){
						$subs_idx = $subs_del_row['idx'];						// 지점고유번호
						$del_subs_query = "
							DELETE FROM TB_MEMBERS_SUBSCRIBE
							WHERE idx = :idx
							LIMIT 1;
						";
						$del_subs_stmt = $DB_con->prepare($del_subs_query);
						$del_subs_stmt->bindParam(":idx", $subs_idx);
						$del_subs_stmt->execute();
					}
				}

				$p_query = "
					SELECT idx, con_Idx, member_Idx, img, reg_Id
					FROM TB_PLACE
					WHERE con_Idx = :con_Idx;
				";
				$p_stmt = $DB_con->prepare($p_query);
				$p_stmt->bindParam(":con_Idx", $con_Idx);
				$p_stmt->execute();
				while($p_row = $p_stmt->fetch(PDO::FETCH_ASSOC)){
					$placeIdx = $p_row['idx'];						// 지점고유번호
					$conIdx = $p_row['con_Idx'];					// 지도고유번호
					$memberIdx = $p_row['member_Idx'];		// 회원고유번호
					$placeImg = $p_row['img'];						// 지점사진폴더
					$reg_Id = $p_row['reg_Id'];						// 지점사진폴더
					// 삭제처리 => 좋아요, 통합좋아요, 담기, 댓글, 지점이미지폴더삭제, 히스토리(좋아요, 담기, 댓글, 사진업데이트, 지점등록)
					// 6. 히스토리 내역 삭제
					$history_del_query = "
						SELECT idx
						FROM TB_HISTORY
						WHERE place_Idx = :place_Idx
							AND history IN ('지점등록', '지도생성', '사진업데이트', '지점공개여부')
							OR idx IN (member_Idx = :member_Idx AND place_Idx = :place_Idx AND history = '댓글등록');
					";
					$history_del_stmt = $DB_con->prepare($history_del_query);
					$history_del_stmt->bindParam(":place_Idx", $placeIdx);
					$history_del_stmt->bindParam(":member_Idx", $memberIdx);
					$history_del_stmt->execute();
					$history_del_num = $history_del_stmt->rowCount();
					if($history_del_num > 0){
						while($history_del_row = $history_del_stmt->fetch(PDO::FETCH_ASSOC)){
							$idx = $history_del_row['idx'];						// 지점고유번호
							$del_history_query = "
								DELETE FROM TB_HISTORY
								WHERE idx = :idx
								LIMIT 1;
							";
							$del_history_stmt = $DB_con->prepare($del_history_query);
							$del_history_stmt->bindParam(":idx", $idx);
							$del_history_stmt->execute();
						}
					}
					// 7. 지점삭제
					$share_chk_query = "
						SELECT count(idx) as cnt
						FROM TB_MEMBERS_SHARE
						WHERE place_Idx = :place_Idx
							AND use_Bit = 'Y';
					";
					$share_chk_stmt = $DB_con->prepare($share_chk_query);
					$share_chk_stmt->bindParam(":place_Idx", $placeIdx);
					$share_chk_stmt->execute();
					$share_chk_row = $share_chk_stmt->fetch(PDO::FETCH_ASSOC);
					$share_chk_num = $share_chk_row['cnt'];						// 담긴수
					if($share_chk_num > 0){		// 다른사람이 담기를 한 경우는 본인 댓글, 지점관련히스토리, 지점DB삭제가아닌 BIT값 수정
						$del_place_query = "
							UPDATE TB_PLACE
							SET delete_Bit = '1'
							WHERE idx = :idx
								AND member_Idx = :member_Idx
							LIMIT 1;
						";
						$del_place_stmt = $DB_con->prepare($del_place_query);
						$del_place_stmt->bindParam(":idx", $placeIdx);
						$del_place_stmt->bindParam(":member_Idx", $memberIdx);
						$del_place_stmt->execute();
						// 4. 댓글 내역 삭제
						$comment_del_query = "
							SELECT idx
							FROM TB_MEMBERS_COMMENT
							WHERE con_Idx = :con_Idx
								AND place_Idx = :place_Idx
								AND member_Idx = :member_Idx;
						";
						$comment_del_stmt = $DB_con->prepare($comment_del_query);
						$comment_del_stmt->bindParam(":con_Idx", $conIdx);
						$comment_del_stmt->bindParam(":place_Idx", $placeIdx);
						$comment_del_stmt->bindParam(":member_Idx", $memberIdx);
						$comment_del_stmt->execute();
						$comment_del_num = $comment_del_stmt->rowCount();
						if($comment_del_num > 0){
							while($comment_del_row = $comment_del_stmt->fetch(PDO::FETCH_ASSOC)){
								$idx = $comment_del_row['idx'];						// 지점고유번호
								$del_comment_query = "
									DELETE FROM TB_MEMBERS_COMMENT
									WHERE idx = :idx
									LIMIT 1;
								";
								$del_comment_stmt = $DB_con->prepare($del_comment_query);
								$del_comment_stmt->bindParam(":idx", $idx);
								$del_comment_stmt->execute();
							}
						}
	/*
						$history = "지점삭제";
						$his_query ="INSERT INTO TB_HISTORY (member_Idx, mem_Id, con_Idx, place_Idx, history, reg_Id, reg_date) VALUES (:member_Idx, :mem_Id, :con_Idx, :place_Idx, :history, :reg_Id, NOW())";
						$his_stmt = $DB_con->prepare($his_query);
						$his_stmt->bindParam(":member_Idx", $memberIdx);
						$his_stmt->bindParam(":mem_Id", $reg_Id);
						$his_stmt->bindParam(":history", $history);
						$his_stmt->bindParam(":con_Idx", $conIdx);
						$his_stmt->bindParam(":place_Idx", $placeIdx);
						$his_stmt->bindParam(":reg_Id", $reg_Id);
						$his_stmt->execute();
	*/
					}else{		// 다른사람이 담기를 안한 경우는 전부 삭제
						// 1. 좋아요 내역 삭제
						$like_del_query = "
							SELECT idx
							FROM TB_MEMBERS_LIKE
							WHERE con_Idx = :con_Idx
								AND place_Idx = :place_Idx;
						";
						$like_del_stmt = $DB_con->prepare($like_del_query);
						$like_del_stmt->bindParam(":con_Idx", $conIdx);
						$like_del_stmt->bindParam(":place_Idx", $placeIdx);
						$like_del_stmt->execute();
						$like_del_num = $like_del_stmt->rowCount();
						if($like_del_num > 0){
							while($like_del_row = $like_del_stmt->fetch(PDO::FETCH_ASSOC)){
								$idx = $like_del_row['idx'];						// 좋아요 고유번호
								$del_like_query = "
									DELETE FROM TB_MEMBERS_LIKE
									WHERE idx = :idx
									LIMIT 1;
								";
								$del_like_stmt = $DB_con->prepare($del_like_query);
								$del_like_stmt->bindParam(":idx", $idx);
								$del_like_stmt->execute();
							}
						}
						// 2. 통합좋아요 내역 삭제
						$tlike_del_query = "
							SELECT idx
							FROM TB_TOTAL_LIKE
							WHERE con_Idx = :con_Idx
								AND place_Idx = :place_Idx;
						";
						$tlike_del_stmt = $DB_con->prepare($tlike_del_query);
						$tlike_del_stmt->bindParam(":con_Idx", $conIdx);
						$tlike_del_stmt->bindParam(":place_Idx", $placeIdx);
						$tlike_del_stmt->execute();
						$tlike_del_num = $tlike_del_stmt->rowCount();
						if($tlike_del_num > 0){
							while($tlike_del_row = $tlike_del_stmt->fetch(PDO::FETCH_ASSOC)){
								$idx = $tlike_del_row['idx'];						// 통합좋아요 고유번호
								$del_tlike_query = "
									DELETE FROM TB_TOTAL_LIKE
									WHERE idx = :idx
									LIMIT 1;
								";
								$del_tlike_stmt = $DB_con->prepare($del_tlike_query);
								$del_tlike_stmt->bindParam(":idx", $idx);
								$del_tlike_stmt->execute();
							}
						}
						// 4. 댓글 내역 삭제
						$comment_del_query = "
							SELECT idx
							FROM TB_MEMBERS_COMMENT
							WHERE con_Idx = :con_Idx
								AND place_Idx = :place_Idx;
						";
						$comment_del_stmt = $DB_con->prepare($comment_del_query);
						$comment_del_stmt->bindParam(":con_Idx", $conIdx);
						$comment_del_stmt->bindParam(":place_Idx", $placeIdx);
						$comment_del_stmt->execute();
						$comment_del_num = $comment_del_stmt->rowCount();
						if($comment_del_num > 0){
							while($comment_del_row = $comment_del_stmt->fetch(PDO::FETCH_ASSOC)){
								$idx = $comment_del_row['idx'];						// 지점고유번호
								$del_comment_query = "
									DELETE FROM TB_MEMBERS_COMMENT
									WHERE idx = :idx
									LIMIT 1;
								";
								$del_comment_stmt = $DB_con->prepare($del_comment_query);
								$del_comment_stmt->bindParam(":idx", $idx);
								$del_comment_stmt->execute();
							}
						}
						$del_place_query = "
							DELETE FROM TB_PLACE
							WHERE idx = :idx
								AND member_Idx = :member_Idx
							LIMIT 1;
						";
						$del_place_stmt = $DB_con->prepare($del_place_query);
						$del_place_stmt->bindParam(":idx", $placeIdx);
						$del_place_stmt->bindParam(":member_Idx", $memberIdx);
						$del_place_stmt->execute();
						// 5. 지점이미지폴더삭제
						if($placeImg != ""){
							$file_dir = $_SERVER["DOCUMENT_ROOT"].'/contents/place_img/'.$placeImg;
							if(is_dir($file_dir)){
								delete_all($file_dir);
							}
						}
						/*
						$history = "지점삭제";
						$his_query ="INSERT INTO TB_HISTORY (member_Idx, mem_Id, con_Idx, place_Idx, history, reg_Id, reg_date) VALUES (:member_Idx, :mem_Id, :con_Idx, :place_Idx, :history, :reg_Id, NOW())";
						$his_stmt = $DB_con->prepare($his_query);
						$his_stmt->bindParam(":member_Idx", $memberIdx);
						$his_stmt->bindParam(":mem_Id", $reg_Id);
						$his_stmt->bindParam(":history", $history);
						$his_stmt->bindParam(":con_Idx", $conIdx);
						$his_stmt->bindParam(":place_Idx", $placeIdx);
						$his_stmt->bindParam(":reg_Id", $reg_Id);
						$his_stmt->execute();
						*/
					}
				}
/*
				$history = "지도삭제";
				$his_query ="INSERT INTO TB_HISTORY (member_Idx, mem_Id, con_Idx, history, reg_Id, reg_date) VALUES (:member_Idx, :mem_Id, :con_Idx, :history, :reg_Id, NOW())";
				$his_stmt = $DB_con->prepare($his_query);
				$his_stmt->bindParam(":member_Idx", $member_Idx);
				$his_stmt->bindParam(":mem_Id", $reg_Id);
				$his_stmt->bindParam(":history", $history);
				$his_stmt->bindParam(":con_Idx", $con_Idx);
				$his_stmt->bindParam(":reg_Id", $reg_Id);
				$his_stmt->execute();
*/
				$del_contents_query = "
					UPDATE TB_CONTENTS
					SET delete_Bit = '1'
					WHERE idx = :con_Idx
					LIMIT 1;
				";
				$del_contents_stmt = $DB_con->prepare($del_contents_query);
				$del_contents_stmt->bindParam(":con_Idx", $con_Idx);
				$del_contents_stmt->execute();
	/*
				$history = "지도삭제";
				$his_query ="INSERT INTO TB_HISTORY (member_Idx, mem_Id, con_Idx, history, reg_Id, reg_date) VALUES (:member_Idx, :mem_Id, :con_Idx, :history, :reg_Id, NOW())";
				$his_stmt = $DB_con->prepare($his_query);
				$his_stmt->bindParam(":member_Idx", $member_Idx);
				$his_stmt->bindParam(":mem_Id", $reg_Id);
				$his_stmt->bindParam(":history", $history);
				$his_stmt->bindParam(":con_Idx", $con_Idx);
				$his_stmt->bindParam(":reg_Id", $reg_Id);
				$his_stmt->execute();

				$del_contents_query = "
					DELETE FROM TB_CONTENTS
					WHERE idx = :con_Idx
					LIMIT 1;
				";
				$del_contents_stmt = $DB_con->prepare($del_contents_query);
				$del_contents_stmt->bindParam(":con_Idx", $con_Idx);
				$del_contents_stmt->execute();
	*/
			}
			$result = array("result" => "success");
		}
	}else{
		$file_dir = $_SERVER["DOCUMENT_ROOT"].'/contents/img';
		if($del_Img_Bit == "1"){
			$img_query = "
				SELECT idx, member_Idx, img
				FROM TB_CONTENTS
				WHERE idx = :con_Idx
					AND delete_Bit = '0'
			";
			$img_stmt = $DB_con->prepare($img_query);
			$img_stmt->bindParam(":con_Idx", $con_Idx);
			$img_stmt->execute();
			while($img_row=$img_stmt->fetch(PDO::FETCH_ASSOC)){
				$org_del_img = $img_row['img'];
				$member_Idx = $img_row['member_Idx'];
				$file_img = $file_dir.'/'.$org_del_img;
				@unlink($file_img);
				del_thumbnail(dirname($file_img), basename($file_img));
				$delImg_query = "
					UPDATE TB_CONTENTS
					SET img = '',
						thumbnail_Bit = '0',
						mod_Date = NOW()
					WHERE member_Idx = :member_Idx
						AND idx = :con_Idx
					LIMIT 1;
				";
				$delImg_stmt = $DB_con->prepare($delImg_query);
				$delImg_stmt->bindParam(":member_Idx", $member_Idx);
				$delImg_stmt->bindParam(":con_Idx", $con_Idx);
				$delImg_stmt->execute();
			}
		}
		$query = "
			SELECT idx, member_Idx, con_Name, img, memo, category, tag, open_Bit
			FROM TB_CONTENTS
			WHERE idx = :con_Idx
				AND delete_Bit = '0'
		";
		$stmt = $DB_con->prepare($query);
		$stmt->bindParam(":con_Idx", $con_Idx);
		$stmt->execute();
		while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
			$idx = $row['idx'];
			$member_Idx = $row['member_Idx'];
			$org_con_Name = $row['con_Name'];
			if($con_Name == ""){
				$con_Name = $org_con_Name;
			}else{
				$con_Name = $con_Name;
			}
			$org_memo = $row['memo'];
			if($memo == ""){
				$memo = $org_memo;
			}else{
				$memo = $memo;
			}
			$org_category = $row['category'];
			if($category == ""){
				$category = $org_category;
			}else{
				$category = $category;
			}

			$org_tag = $row['tag'];
			if($tag == ""){
				$tag = $org_tag;
			}else{
				$tag = $tag;
			}
			$org_open_Bit = $row['open_Bit'];
			if($open_Bit == ""){
				$open_Bit = $org_open_Bit;
			}else{
				$open_Bit = $open_Bit;
			}
			$org_img = $row['img'];
			if($img == ""){
				$img = $org_img;
			}else{
				if($org_img != ""){
					$img_query = "
						SELECT idx, member_Idx, img
						FROM TB_CONTENTS
						WHERE idx = :con_Idx
							AND delete_Bit = '0'
					";
					$img_stmt = $DB_con->prepare($img_query);
					$img_stmt->bindParam(":con_Idx", $con_Idx);
					$img_stmt->execute();
					while($img_row=$img_stmt->fetch(PDO::FETCH_ASSOC)){
						$org_del_img = $img_row['img'];
						$member_Idx = $img_row['member_Idx'];
						$file_img = $file_dir.'/'.$org_del_img;
						@unlink($file_img);
						del_thumbnail(dirname($file_img), basename($file_img));
						$delImg_query = "
							UPDATE TB_CONTENTS
							SET img = '',
								thumbnail_Bit = '0',
								mod_Date = NOW()
							WHERE member_Idx = :member_Idx
								AND idx = :con_Idx
							LIMIT 1;
						";
						$delImg_stmt = $DB_con->prepare($delImg_query);
						$delImg_stmt->bindParam(":member_Idx", $member_Idx);
						$delImg_stmt->bindParam(":con_Idx", $con_Idx);
						$delImg_stmt->execute();
					}
					/* 이미지 파일 업로드 시작 */
					if(isset($_FILES['img'])){
						$img_f_name = $_FILES['img']['name'];
						$img_fname = iconv("UTF-8", "EUC-KR", $img_f_name);
						$file_dir = $_SERVER["DOCUMENT_ROOT"].'/contents/img';
						$img_target = $file_dir."/".$img_fname ; 
						move_uploaded_file($_FILES['img']['tmp_name'],$img_target);	
						$info_image=getimagesize($img_target);
						switch($info_image['mime']){
							case "image/gif";
								 $new_image=imagecreatefromgif($img_target);
								 break;
							case "image/jpeg";
								 $new_image=imagecreatefromjpeg($img_target);
								 break;
							case "image/png";
								 $new_image=imagecreatefrompng($img_target);
								 break;
							case "image/bmp";
								 $new_image=imagecreatefromwbmp($img_target);
								 break;
						}
						if($new_image){
							if($info_image[0] > 720){       //이미지의 가로길이가 950보다 크다면 작게만든다
								$del_filename = $img_target ;        //원본파일이름과 디렉토리이다

								//파일을 가로950에 세로축 비율로 만들어지게 했다
								  $w = 720;
								  $rate = $w / $info_image[0];
								  $h = (int)($info_image[1] * $rate);

								  // 캔버스를 엽니다 (캔버스 사이즈는 이미지의 사이즈)
								  $canvas=imagecreatetruecolor($w,$h);
								  imagecopyresampled($canvas,$new_image,0,0,0,0,$w,$h,$info_image[0],$info_image[1]);
								  $file_name = $img_fname;  
								  $rfile_name = "r_".$file_name;
								  $filename = $file_dir."/".$rfile_name;     //새로운 이름으로 파일을 만들어준다

								//switch문을 이용하여 각각의 이미지에 맞는 확장자를 만든다

								  switch($info_image['mime']){
									   case "image/gif";
										   imagegif($canvas,$filename);
										   break;
										case "image/jpeg";
										   imagejpeg($canvas,$filename);
										   break;
									   case "image/png";
										   imagepng($canvas,$filename);
										   break;
									   case "image/bmp";
										   imagewbmp($canvas,$filename);
										   break;
								  }
							 @unlink("$del_filename");     //이미지가 950보다 클때 새로파일을 만들고 원본파일을 지워준다
								$img = $rfile_name;									//썸네일이미지
							 }else{
								$img = $img_fname;				//썸네일이미지
							 }
						}
					}
					$fileName = $img;
					//새로운 팝업 이미지경로 출력
					$contents_img = $file_dir.'/'.$fileName;

					//파일저장방법 변경 _blob -------------------------------------------------------- 2019.02.19
					if(file_exists($contents_img) && $fileName != "")
					{
						$now_time = time();	

						//첨부파일 -> 썸네일 이미지로 변경 및 저장된 경로
						$filename = $contents_img;
						$handle = fopen($filename,"rb");
						$size =	GetImageSize($filename);
						$width = $size[0];
						$height = $size[1];
						$imageblob = addslashes(fread($handle, filesize($filename)));
						$filesize = filesize($filename);
						$mine = $size['mime'];
						fclose($handle);		
						
						$insQuery = "
							UPDATE TB_CONTENTS 
							SET
								img ='".$now_time."',
								thumbnail_Bit = '1'
							WHERE
								idx =	".$con_Idx." 
						";		
						$DB_con->exec($insQuery);

						// 파일로 blob형태 이미지 저장----------S
						// 새로 생성되는 파일명(전체경로 포함) : $m_file
						$img_txt = $now_time;
						$m_file = $file_dir.'/'.$img_txt;
						$is_file_exist = file_exists($m_file);

						if ($is_file_exist) {
							//echo 'Found it';
						} else {
							//echo 'Not found.';
							$file = fopen($m_file , "w");
							fwrite($file, $imageblob);
							fclose($file);
							chmod($m_file, 0755);
						}

						//기존 파일 삭제
						@unlink($img_target);
						//신규 업로드 팝업 이미지 삭제
						@unlink($contents_img);
						// 파일로 blob형태 이미지 저장----------E

					}
					$img = $img_txt;	
				}else{
					/* 이미지 파일 업로드 시작 */
					if(isset($_FILES['img'])){
						$img_f_name = $_FILES['img']['name'];
						$img_fname = iconv("UTF-8", "EUC-KR", $img_f_name);
						$file_dir = $_SERVER["DOCUMENT_ROOT"].'/contents/img';
						$img_target = $file_dir."/".$img_fname ; 
						move_uploaded_file($_FILES['img']['tmp_name'],$img_target);	
						$info_image=getimagesize($img_target);
						switch($info_image['mime']){
							case "image/gif";
								 $new_image=imagecreatefromgif($img_target);
								 break;
							case "image/jpeg";
								 $new_image=imagecreatefromjpeg($img_target);
								 break;
							case "image/png";
								 $new_image=imagecreatefrompng($img_target);
								 break;
							case "image/bmp";
								 $new_image=imagecreatefromwbmp($img_target);
								 break;
						}
						if($new_image){
							if($info_image[0] > 720){       //이미지의 가로길이가 950보다 크다면 작게만든다
								$del_filename = $img_target ;        //원본파일이름과 디렉토리이다

								//파일을 가로950에 세로축 비율로 만들어지게 했다
								  $w = 720;
								  $rate = $w / $info_image[0];
								  $h = (int)($info_image[1] * $rate);

								  // 캔버스를 엽니다 (캔버스 사이즈는 이미지의 사이즈)
								  $canvas=imagecreatetruecolor($w,$h);
								  imagecopyresampled($canvas,$new_image,0,0,0,0,$w,$h,$info_image[0],$info_image[1]);
								  $file_name = $img_fname;  
								  $rfile_name = "r_".$file_name;
								  $filename = $file_dir."/".$rfile_name;     //새로운 이름으로 파일을 만들어준다

								//switch문을 이용하여 각각의 이미지에 맞는 확장자를 만든다

								  switch($info_image['mime']){
									   case "image/gif";
										   imagegif($canvas,$filename);
										   break;
										case "image/jpeg";
										   imagejpeg($canvas,$filename);
										   break;
									   case "image/png";
										   imagepng($canvas,$filename);
										   break;
									   case "image/bmp";
										   imagewbmp($canvas,$filename);
										   break;
								  }
							 @unlink("$del_filename");     //이미지가 950보다 클때 새로파일을 만들고 원본파일을 지워준다
							$img = $rfile_name;									//썸네일이미지
							}else{
								$img = $img_fname;									//썸네일이미지
							}
						}else{
							$img = $img_fname;				//썸네일이미지
						}
					}
					$fileName = $img;
					//새로운 팝업 이미지경로 출력
					$contents_img = $file_dir.'/'.$fileName;

					//파일저장방법 변경 _blob -------------------------------------------------------- 2019.02.19
					
					if(file_exists($contents_img) && $fileName != "")
					{
						$now_time = time();	

						//첨부파일 -> 썸네일 이미지로 변경 및 저장된 경로
						$filename = $contents_img;
						$handle = fopen($filename,"rb");
						$size =	GetImageSize($filename);
						$width = $size[0];
						$height = $size[1];
						$imageblob = addslashes(fread($handle, filesize($filename)));
						$filesize = filesize($filename);
						$mine = $size['mime'];
						fclose($handle);		
						
						$insQuery = "
							UPDATE TB_CONTENTS 
							SET
								img ='".$now_time."',
								thumbnail_Bit = '1'
							WHERE
								idx =	".$con_Idx." 
						";		
						$DB_con->exec($insQuery);

						// 파일로 blob형태 이미지 저장----------S
						// 새로 생성되는 파일명(전체경로 포함) : $m_file
						$img_txt = $now_time;
						$m_file = $file_dir.'/'.$img_txt;
						$is_file_exist = file_exists($m_file);

						if ($is_file_exist) {
							//echo 'Found it';
						} else {
							//echo 'Not found.';
							$file = fopen($m_file , "w");
							fwrite($file, $imageblob);
							fclose($file);
							chmod($m_file, 0755);
						}

						//기존 파일 삭제
						@unlink($img_target);
						//신규 업로드 팝업 이미지 삭제
						@unlink($contents_img);
						// 파일로 blob형태 이미지 저장----------E

					}
					$img = $img_txt;
				}
			}
			$mod_query = "
				UPDATE TB_CONTENTS
				SET category = :category, 
					con_Name = :con_Name,
					img = :img,
					memo = :memo,
					open_Bit = :open_Bit,
					tag = :tag,
					mod_Date = NOW()
				WHERE member_Idx = :member_Idx
					AND idx = :con_Idx
				LIMIT 1;
			";
			$mod_stmt = $DB_con->prepare($mod_query);
			$mod_stmt->bindParam(":category", $category);
			$mod_stmt->bindParam(":con_Name", $con_Name);
			$mod_stmt->bindParam(":img", $img);
			$mod_stmt->bindParam(":memo", $memo);
			$mod_stmt->bindParam(":open_Bit", $open_Bit);
			$mod_stmt->bindParam(":tag", $tag);
			$mod_stmt->bindParam(":member_Idx", $member_Idx);
			$mod_stmt->bindParam(":con_Idx", $con_Idx);
			$mod_stmt->execute();
		}
		dbClose($DB_con);
		$chk_stmt = null;
		$stmt = null;
		$mod_stmt = null;
		$mod_stmt2 = null;
		$result = array("result" => "success");
	}

} else { //빈값일 경우
	$result = array("result" => "error", "errorMsg" => "삭제된 지도는 수정 및 삭제할 수 없습니다.");
}

echo json_encode($result);