<?
/*
* 프로그램				: 콘텐츠를 등록하는 기능
* 페이지 설명			: 콘텐츠를 등록하는 기능
* 파일명					: reg_contents.php
* 관련DB					: TB_CONTENTS
*/
include "../lib/common.php";
include "../lib/functionDB.php";  //공통 db함수.
			
$DB_con = db1();
if($_FILES['kmlfile']['name'] != ''){ 

	//고유번호 확인
	$chk_query = "
		SELECT idx
		FROM TB_CONTENTS
			WHERE delete_Bit = '0'
		ORDER BY idx DESC;
	";
	$chk_stmt = $DB_con->prepare($chk_query);
	$chk_stmt->execute();
	$chk_row=$chk_stmt->fetch(PDO::FETCH_ASSOC);
	$chk_idx = $chk_row['idx'];
	$idx = ((int)$chk_idx +1).".kml";

	$f_name = $_FILES['kmlfile']['name'];
	$fname = iconv("UTF-8", "EUC-KR", $f_name);
	$target = "./kmlfile/".$idx ; 
	if(move_uploaded_file($_FILES['kmlfile']['tmp_name'],$target)) {
		$con_Name = trim($con_Name);					//콘텐츠이름 
		$con_Lv = trim($con_Lv);
		if($con_Lv == ""){
			$con_Lv = "1";
		}
		$category = trim($category);						//카테고리
		$tag = trim($tag);										//태그 (예 : 이사아폴리스,돼지고기,양많음,적극추천 등 태그를 , 로 구분하여 넣기.)
		$open_Bit = trim($open_Bit);						//공개여부(0:전체공개, 1:비공개)
		$memo = trim($memo);							//메모
		if($open_Bit == ""){
			$open_Bit = "0";
		}else{
			$open_Bit = $open_Bit;
		}
		$kml_File = trim($idx);								//KML이라는 좌표파일이 있을경우 서버에 업로드하여 파일명 안내
		$reg_Id = trim($reg_Id);								//등록자
		$memberIdx = memIdxInfo($reg_Id);					// 회원고유번호
		$reg_Date = DU_TIME_YMDHIS;					//등록일

		$chk_con_query = "
			SELECT count(*) as cnt
			FROM TB_CONTENTS
			WHERE member_Idx = :member_Idx
				AND delete_Bit = '0'
			;
		";
		$chk_con_Stmt = $DB_con->prepare($chk_con_Query);
		$chk_con_Stmt->bindparam(":member_Idx",$memberIdx);
		$chk_con_Stmt->execute();
		$chk_con_row = $chk_con_Stmt->fetch(PDO::FETCH_ASSOC);
		$cnt = $chk_con_row['cnt'];		// 지도등록수 
		$total_cnt = (int)$cnt + 1;
		$contentMaxCnt = (int)$content_MaxCnt + 1;
		if($total_cnt > 0){
//		임시로 10개이상 제한 풀음 추후 제한 걸어야함.
//		if($total_cnt < $contentMaxCnt){
			$chkQuery = "";
			$chkQuery = "SELECT mem_Lv FROM TB_MEMBERS WHERE mem_Id = :mem_Id ";
			$chkStmt = $DB_con->prepare($chkQuery);
			$chkStmt->bindparam(":mem_Id",$reg_Id);
			$chkStmt->execute();
			$chkrow = $chkStmt->fetch(PDO::FETCH_ASSOC);
			$mem_Lv = $chkrow['mem_Lv'];	//회원등급

			if ($reg_Id != "" && $con_Name != "" && $category != ""  ) {
				
				//콘텐츠등록
				$query = "INSERT INTO TB_CONTENTS (member_Idx, con_Name, con_Lv, category, img, tag, open_Bit, memo, kml_File, reg_Id, reg_date, mod_Date ) VALUES (:member_Idx, :con_Name, :con_Lv, :category, '', :tag, :open_Bit, :memo, :kml_File, :reg_Id, :reg_Date, NOW())";
				
				$stmt = $DB_con->prepare($query);
				$stmt->bindParam(":member_Idx", $memberIdx);
				$stmt->bindParam(":con_Name", $con_Name);
				$stmt->bindParam(":con_Lv", $con_Lv);
				$stmt->bindParam(":category", $category);
				$stmt->bindParam(":tag", $tag);
				$stmt->bindParam(":open_Bit", $open_Bit);
				$stmt->bindParam(":memo", $memo);
				$stmt->bindParam(":kml_File", $kml_File);
				$stmt->bindParam(":reg_Id", $reg_Id);
				$stmt->bindParam(":reg_Date", $reg_Date);
				$stmt->execute();

				$mIdx = $DB_con->lastInsertId();  //저장된 idx 값

				/* 이미지 파일 업로드 시작 */
				if(isset($_FILES['img'])){
					// 한글파일명때문에 basename() 처리해줌;
					$img_f_name = basename($_FILES['img']['name']);
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
							idx =	".$mIdx." 
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

				dbClose($DB_con);
				$stmt = null;
				$result = array("result" => "success");
			} else { //빈값일 경우
				$result = array("result" => "error", "errorMsg" => "지도등록실패");
			}	
			echo json_encode($result);
		}else{
			$result = array("result" => "error", "errorMsg" => "등록된 지도가 10개가 넘어 만들기를 할 수 없습니다.");
			echo json_encode($result);
		}
		
	}else{
		$result = array("result" => "error", "errorMsg" => "파일업로드실패");
		echo json_encode($result);
	}
	// 파일이 없는 경우 
}else{
	$con_Name = trim($con_Name);				//콘텐츠이름 
	$con_Lv = trim($con_Lv);
	if($con_Lv == ""){
		$con_Lv = "1";
	}
	$category = trim($category);					//카테고리
	$tag = trim($tag);									//태그 (예 : 이사아폴리스,돼지고기,양많음,적극추천 등 태그를 , 로 구분하여 넣기.
	$open_Bit = trim($open_Bit);					//공개여부(0:전체공개, 1:비공개)
	$memo = trim($memo);							//메모
	if($open_Bit == ""){
		$open_Bit = "0";
	}else{
		$open_Bit = $open_Bit;
	}
	$reg_Id = trim($reg_Id);							//등록자
	$memberIdx = memIdxInfo($reg_Id);				// 회원고유번호
	$reg_Date = DU_TIME_YMDHIS;				//등록일
	
	$chk_con_query = "
		SELECT count(*) as cnt
		FROM TB_CONTENTS
		WHERE member_Idx = :member_Idx
			AND delete_Bit = '0'
		;
	";
	$chk_con_Stmt = $DB_con->prepare($chk_con_query);
	$chk_con_Stmt->bindparam(":member_Idx",$memberIdx);
	$chk_con_Stmt->execute();
	$chk_con_row = $chk_con_Stmt->fetch(PDO::FETCH_ASSOC);
	$cnt = $chk_con_row['cnt'];
	$total_cnt = (int)$cnt + 1;
//$total_cnt < 11
	if($total_cnt > 0){
		$chkQuery = "";
		$chkQuery = "SELECT mem_Lv FROM TB_MEMBERS WHERE mem_Id = :mem_Id ";
		$chkStmt = $DB_con->prepare($chkQuery);
		$chkStmt->bindparam(":mem_Id",$reg_Id);
		$chkStmt->execute();
		$chkrow = $chkStmt->fetch(PDO::FETCH_ASSOC);
		$mem_Lv = $chkrow['mem_Lv'];	//회원등급
		if ($reg_Id != "" && $con_Name != "" && $category != ""  ) {
			
			$DB_con = db1();
			
			//회원 기본테이블 저장
			$query = "INSERT INTO TB_CONTENTS (member_Idx, con_Name, con_Lv, category, img, tag, open_Bit, memo, reg_Id, reg_date, mod_Date ) VALUES (:member_Idx, :con_Name, :con_Lv, :category, '', :tag, :open_Bit, :memo, :reg_Id, :reg_Date, NOW())";

			
			$stmt = $DB_con->prepare($query);
			$stmt->bindParam(":member_Idx", $memberIdx);
			$stmt->bindParam(":con_Name", $con_Name);
			$stmt->bindParam(":con_Lv", $con_Lv);
			$stmt->bindParam(":category", $category);
			$stmt->bindParam(":tag", $tag);
			$stmt->bindParam(":open_Bit", $open_Bit);
			$stmt->bindParam(":memo", $memo);
			$stmt->bindParam(":reg_Id", $reg_Id);
			$stmt->bindParam(":reg_Date", $reg_Date);
			$stmt->execute();

			$mIdx = $DB_con->lastInsertId();  //저장된 idx 값


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
						idx =	".$mIdx." 
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

			$history = "지도생성";
			$his_query ="INSERT INTO TB_HISTORY (member_Idx, mem_Id, con_Idx, history, reg_Id, reg_date) VALUES (:member_Idx, :mem_Id, :con_Idx, :history, :reg_Id, :reg_Date)";
			$his_stmt = $DB_con->prepare($his_query);
			$his_stmt->bindParam(":member_Idx", $memberIdx);
			$his_stmt->bindParam(":mem_Id", $reg_Id);
			$his_stmt->bindParam(":history", $history);
			$his_stmt->bindParam(":con_Idx", $mIdx);
			$his_stmt->bindParam(":reg_Id", $reg_Id);
			$his_stmt->bindParam(":reg_Date", $reg_Date);
			$his_stmt->execute();
			dbClose($DB_con);
			$stmt = null;
			$result = array("result" => "success");
		} else { //빈값일 경우
			$result = array("result" => "error", "errorMsg" => "지도등록실패");
		}
		echo json_encode($result);
		
	}else{
		$result = array("result" => "error", "errorMsg" => "등록된 지도가 10개가 넘어 만들기를 할 수 없습니다.");
		echo json_encode($result);
	}
}
?>



