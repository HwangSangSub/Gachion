<?

	include "../../lib/common.php"; 
	include "../../lib/alertLib.php";
	include "../../lib/thumbnail.lib.php";   //썸네일

	$con_GuideUrl1 = trim($con_GuideUrl1);
	$con_GuideUrl2 = trim($con_GuideUrl2);
	$con_GuideUrl3 = trim($con_GuideUrl3);
	$con_GuideUrl4 = trim($con_GuideUrl4);
	$con_GuideUrl5 = trim($con_GuideUrl5);
	$DB_con = db1();
	
	if ($mode ==''){
		$mode = "mode";
	}
	if ($mode == "reg") {

		$insQuery = "INSERT INTO TB_CONFIG_GUIDE (con_GuideUrl1, con_GuideUrl2, con_GuideUrl3, con_GuideUrl4, con_GuideUrl5) VALUES (:con_GuideUrl1, :con_GuideUrl2, :con_GuideUrl3, :con_GuideUrl4, :con_GuideUrl5)";
		$stmt = $DB_con->prepare($insQuery);
		$stmt->bindParam(":con_GuideUrl1", $con_GuideUrl1);
		$stmt->bindParam(":con_GuideUrl2", $con_GuideUrl2);
		$stmt->bindParam(":con_GuideUrl3", $con_GuideUrl3);
		$stmt->bindParam(":con_GuideUrl4", $con_GuideUrl4);
		$stmt->bindParam(":con_GuideUrl5", $con_GuideUrl5);
		$stmt->execute();
		$DB_con->lastInsertId();

		$preUrl = "configGuideReg.php";
		$message = "reg";
		proc_msg($message, $preUrl);


	} else if ($mode == "mod") { //수정일경우

		$query = "";
		$query = "SELECT con_GuideUrl1, con_GuideUrl2, con_GuideUrl3, con_GuideUrl4, con_GuideUrl5 FROM TB_CONFIG_GUIDE" ;
		$stmt1 = $DB_con->prepare($query);
		//$idx = trim($idx);
		$stmt1->execute();
        $row1 = $stmt1->fetch(PDO::FETCH_ASSOC);
        $guide_ImgFile1 = trim($row1['con_GuideUrl1']);
        $guide_ImgFile2 = trim($row1['con_GuideUrl2']);
        $guide_ImgFile3 = trim($row1['con_GuideUrl3']);
        $guide_ImgFile4 = trim($row1['con_GuideUrl4']);
        $guide_ImgFile5 = trim($row1['con_GuideUrl5']);
		// 배너 이미지 경로
	// 배너 이미지 경로
	$file_dir = DU_DATA_PATH.'/guide';


	// 가이드 이미지 1 ------------------------------------------------------------------2019.02.19
	$org_guide_ImgFile1 = $file_dir.'/'.$guide_ImgFile1;

	// 파일삭제
	if ($del_guide_Img1) {
	    $file_img1 = $file_dir.'/'.$guide_ImgFile1;
		@unlink($file_img1);
		del_thumbnail(dirname($file_img1), basename($file_img1));
		$guide_Img1 = '';
	} else {
	    $guide_Img1 = "$guide_Img1";
	}


	// 이미지 업로드 
	$image_regex = "/(\.(gif|jpe?g|png))$/i";

	$cf_img_width = "720";
	$cf_img_height = "5000";

	if (isset($_FILES['guide_Img1']) && is_uploaded_file($_FILES['guide_Img1']['tmp_name'])) {  //이미지 업로드 성공일 경우


		if (preg_match($image_regex, $_FILES['guide_Img1']['name'])) {

			@mkdir($file_dir, 0755);
			//@chmod($file_dir, 0644);

			$filename = $_FILES['guide_Img1']['name'];

			//php파일도 getimagesize 에서 Image Type Flag 를 속일수 있다
			if (!preg_match('/\.(gif|jpe?g|png)$/i', $filename)) {
				return '';
			}

			$pattern = "/[#\&\+\-%@=\/\\:;,'\"\^`~\|\!\?\*\$#<>\(\)\[\]\{\}]/";
			$filename = preg_replace("/\s+/", "", $filename);
			$filename = preg_replace( $pattern, "", $filename);

			$filename = preg_replace_callback(
								  "/[가-힣]+/",
								  create_function('$matches', 'return base64_encode($matches[0]);'),
								  $filename);

			$filename = preg_replace( $pattern, "", $filename);

			// 동일한 이름의 파일이 있으면 파일명 변경
			if(is_file($dir.'/'.$filename)) {
				for($i=0; $i<20; $i++) {
					$prepend = str_replace('.', '_', microtime(true)).'_';

					if(is_file($dir.'/'.$prepend.$filename)) {
						usleep(mt_rand(100, 10000));
						continue;
					} else {
						break;
					}
				}
			}

			$fileName = $prepend.$filename;
			$dest_path = $file_dir.'/'.$fileName;

			move_uploaded_file($_FILES['guide_Img1']['tmp_name'], $dest_path);
		
			if (file_exists($dest_path)) {
				$size = @getimagesize($dest_path);

				if (!($size[2] === 1 || $size[2] === 2 || $size[2] === 3)) { // gif jpg png 파일이 아니면 올라간 이미지를 삭제한다.
					@unlink($dest_path);
				} else if ($size[0] > $cf_img_width || $size[1] > $cf_img_height) {
					$thumb = null;
					if($size[2] === 2 || $size[2] === 3) {
						//jpg 또는 png 파일 적용
						$thumb = thumbnail($fileName, $file_dir, $file_dir, $cf_img_width, $cf_img_height, true, true);

						if($thumb) {
							@unlink($dest_path);
							rename($file_dir.'/'.$thumb, $dest_path);
						}
					}
					if( !$thumb ){
						// 아이콘의 폭 또는 높이가 설정값 보다 크다면 이미 업로드 된 아이콘 삭제
						@unlink($dest_path);
					}
				}
				//=================================================================\
			}
						
			$guide_ImgFile1 = $fileName;	
		}
	}
	
	
	if ($guide_ImgFile1 != "") {
	    $guide_ImgFile1 = $guide_ImgFile1;
	} else {
	    $guide_ImgFile1 = $guide_Img1;
	}

	//새로운 팝업 이미지경로 출력
	$member_img = $file_dir.'/'.$fileName;


	//파일저장방법 변경 _blob -------------------------------------------------------- 2019.02.19
	
	if(file_exists($member_img) && $fileName != "")
	{
		$now_time = time();	

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

		
		$insQuery = "
			update TB_CONFIG_GUIDE 
			set 
				con_GuideUrl1 ='".$now_time."' 
			where 
				idx ='1' 
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
		@unlink($org_guide_ImgFile1);
		//신규 업로드 팝업 이미지 삭제
		@unlink($member_img);
		// 파일로 blob형태 이미지 저장----------E

	}
	
	//파일저장방법 변경 _blob --------------------------------------------------------





	// 가이드 이미지 2 ------------------------------------------------------------------2019.02.19
	$org_guide_ImgFile2 = $file_dir.'/'.$guide_ImgFile2;

	if ($del_guide_Img2) {
	    $file_img2 = $file_dir.'/'.$guide_ImgFile2;
		@unlink($file_img2);
		del_thumbnail(dirname($file_img2), basename($file_img2));
		$guide_Img2 = '';
	} else {
	    $guide_Img2 = "$guide_Img2";
	}


	// 이미지 업로드 
	$image_regex = "/(\.(gif|jpe?g|png))$/i";

	$cf_img_width = "720";
	$cf_img_height = "5000";

	if (isset($_FILES['guide_Img2']) && is_uploaded_file($_FILES['guide_Img2']['tmp_name'])) {  //이미지 업로드 성공일 경우


		if (preg_match($image_regex, $_FILES['guide_Img2']['name'])) {

			@mkdir($file_dir, 0755);
			//@chmod($file_dir, 0644);

			$filename = $_FILES['guide_Img2']['name'];

			//php파일도 getimagesize 에서 Image Type Flag 를 속일수 있다
			if (!preg_match('/\.(gif|jpe?g|png)$/i', $filename)) {
				return '';
			}

			$pattern = "/[#\&\+\-%@=\/\\:;,'\"\^`~\|\!\?\*\$#<>\(\)\[\]\{\}]/";
			$filename = preg_replace("/\s+/", "", $filename);
			$filename = preg_replace( $pattern, "", $filename);

			$filename = preg_replace_callback(
								  "/[가-힣]+/",
								  create_function('$matches', 'return base64_encode($matches[0]);'),
								  $filename);

			$filename = preg_replace( $pattern, "", $filename);

			// 동일한 이름의 파일이 있으면 파일명 변경
			if(is_file($dir.'/'.$filename)) {
				for($i=0; $i<20; $i++) {
					$prepend = str_replace('.', '_', microtime(true)).'_';

					if(is_file($dir.'/'.$prepend.$filename)) {
						usleep(mt_rand(100, 10000));
						continue;
					} else {
						break;
					}
				}
			}

			$fileName = $prepend.$filename;
			$dest_path = $file_dir.'/'.$fileName;

			move_uploaded_file($_FILES['guide_Img2']['tmp_name'], $dest_path);
		
			if (file_exists($dest_path)) {
				$size = @getimagesize($dest_path);

				if (!($size[2] === 1 || $size[2] === 2 || $size[2] === 3)) { // gif jpg png 파일이 아니면 올라간 이미지를 삭제한다.
					@unlink($dest_path);
				} else if ($size[0] > $cf_img_width || $size[1] > $cf_img_height) {
					$thumb = null;
					if($size[2] === 2 || $size[2] === 3) {
						//jpg 또는 png 파일 적용
						$thumb = thumbnail($fileName, $file_dir, $file_dir, $cf_img_width, $cf_img_height, true, true);

						if($thumb) {
							@unlink($dest_path);
							rename($file_dir.'/'.$thumb, $dest_path);
						}
					}
					if( !$thumb ){
						// 아이콘의 폭 또는 높이가 설정값 보다 크다면 이미 업로드 된 아이콘 삭제
						@unlink($dest_path);
					}
				}
				//=================================================================\
			}
						
			$guide_ImgFile2 = $fileName;	
		}
	}
	
	
	if ($guide_ImgFile2 != "") {
	    $guide_ImgFile2 = $guide_ImgFile2;
	} else {
	    $guide_ImgFile2 = $guide_Img2;
	}

	//새로운 팝업 이미지경로 출력
	$member_img = $file_dir.'/'.$fileName;

	//파일저장방법 변경 _blob -------------------------------------------------------- 2019.02.19
	
	if(file_exists($member_img) && $fileName != "")
	{
		$now_time = time()+2;	

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

		
		$insQuery = "
			update TB_CONFIG_GUIDE 
			set 
				con_GuideUrl2 ='".$now_time."' 
			where 
				idx ='1' 
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
		@unlink($org_guide_ImgFile2);
		//신규 업로드 팝업 이미지 삭제
		@unlink($member_img);
		// 파일로 blob형태 이미지 저장----------E

	}
	
	//파일저장방법 변경 _blob --------------------------------------------------------





	// 가이드 이미지 3 ------------------------------------------------------------------2019.02.19
	$org_guide_ImgFile3 = $file_dir.'/'.$guide_ImgFile3;

	// 파일삭제
	if ($del_guide_Img3) {
	    $file_img3 = $file_dir.'/'.$guide_ImgFile3;
		@unlink($file_img3);
		del_thumbnail(dirname($file_img3), basename($file_img3));
		$guide_Img1 = '';
	} else {
	    $guide_Img1 = "$guide_Img3";
	}


	// 이미지 업로드 
	$image_regex = "/(\.(gif|jpe?g|png))$/i";

	$cf_img_width = "720";
	$cf_img_height = "5000";

	if (isset($_FILES['guide_Img3']) && is_uploaded_file($_FILES['guide_Img3']['tmp_name'])) {  //이미지 업로드 성공일 경우


		if (preg_match($image_regex, $_FILES['guide_Img3']['name'])) {

			@mkdir($file_dir, 0755);
			//@chmod($file_dir, 0644);

			$filename = $_FILES['guide_Img3']['name'];

			//php파일도 getimagesize 에서 Image Type Flag 를 속일수 있다
			if (!preg_match('/\.(gif|jpe?g|png)$/i', $filename)) {
				return '';
			}

			$pattern = "/[#\&\+\-%@=\/\\:;,'\"\^`~\|\!\?\*\$#<>\(\)\[\]\{\}]/";
			$filename = preg_replace("/\s+/", "", $filename);
			$filename = preg_replace( $pattern, "", $filename);

			$filename = preg_replace_callback(
								  "/[가-힣]+/",
								  create_function('$matches', 'return base64_encode($matches[0]);'),
								  $filename);

			$filename = preg_replace( $pattern, "", $filename);

			// 동일한 이름의 파일이 있으면 파일명 변경
			if(is_file($dir.'/'.$filename)) {
				for($i=0; $i<20; $i++) {
					$prepend = str_replace('.', '_', microtime(true)).'_';

					if(is_file($dir.'/'.$prepend.$filename)) {
						usleep(mt_rand(100, 10000));
						continue;
					} else {
						break;
					}
				}
			}

			$fileName = $prepend.$filename;
			$dest_path = $file_dir.'/'.$fileName;

			move_uploaded_file($_FILES['guide_Img3']['tmp_name'], $dest_path);
		
			if (file_exists($dest_path)) {
				$size = @getimagesize($dest_path);

				if (!($size[2] === 1 || $size[2] === 2 || $size[2] === 3)) { // gif jpg png 파일이 아니면 올라간 이미지를 삭제한다.
					@unlink($dest_path);
				} else if ($size[0] > $cf_img_width || $size[1] > $cf_img_height) {
					$thumb = null;
					if($size[2] === 2 || $size[2] === 3) {
						//jpg 또는 png 파일 적용
						$thumb = thumbnail($fileName, $file_dir, $file_dir, $cf_img_width, $cf_img_height, true, true);

						if($thumb) {
							@unlink($dest_path);
							rename($file_dir.'/'.$thumb, $dest_path);
						}
					}
					if( !$thumb ){
						// 아이콘의 폭 또는 높이가 설정값 보다 크다면 이미 업로드 된 아이콘 삭제
						@unlink($dest_path);
					}
				}
				//=================================================================\
			}
						
			$guide_ImgFile3 = $fileName;	
		}
	}
	
	
	if ($guide_ImgFile3 != "") {
	    $guide_ImgFile3 = $guide_ImgFile3;
	} else {
	    $guide_ImgFile3 = $guide_Img3;
	}


	//새로운 팝업 이미지경로 출력
	$member_img = $file_dir.'/'.$fileName;

	//파일저장방법 변경 _blob -------------------------------------------------------- 2019.02.19
	
	if(file_exists($member_img) && $fileName != "")
	{
		$now_time = time()+3;	

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

		
		$insQuery = "
			update TB_CONFIG_GUIDE 
			set 
				con_GuideUrl3 ='".$now_time."' 
			where 
				idx ='1' 
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
		@unlink($org_guide_ImgFile3);
		//신규 업로드 팝업 이미지 삭제
		@unlink($member_img);
		// 파일로 blob형태 이미지 저장----------E

	}
	
	//파일저장방법 변경 _blob --------------------------------------------------------





	// 가이드 이미지 4 ------------------------------------------------------------------2019.02.19
	$org_guide_ImgFile4 = $file_dir.'/'.$guide_ImgFile4;

	// 파일삭제
	if ($del_guide_Img4) {
	    $file_img4 = $file_dir.'/'.$guide_ImgFile4;
		@unlink($file_img4);
		del_thumbnail(dirname($file_img4), basename($file_img4));
		$guide_Img4 = '';
	} else {
	    $guide_Img4 = "$guide_Img4";
	}


	// 이미지 업로드 
	$image_regex = "/(\.(gif|jpe?g|png))$/i";

	$cf_img_width = "720";
	$cf_img_height = "5000";

	if (isset($_FILES['guide_Img4']) && is_uploaded_file($_FILES['guide_Img4']['tmp_name'])) {  //이미지 업로드 성공일 경우


		if (preg_match($image_regex, $_FILES['guide_Img4']['name'])) {

			@mkdir($file_dir, 0755);
			//@chmod($file_dir, 0644);

			$filename = $_FILES['guide_Img4']['name'];

			//php파일도 getimagesize 에서 Image Type Flag 를 속일수 있다
			if (!preg_match('/\.(gif|jpe?g|png)$/i', $filename)) {
				return '';
			}

			$pattern = "/[#\&\+\-%@=\/\\:;,'\"\^`~\|\!\?\*\$#<>\(\)\[\]\{\}]/";
			$filename = preg_replace("/\s+/", "", $filename);
			$filename = preg_replace( $pattern, "", $filename);

			$filename = preg_replace_callback(
								  "/[가-힣]+/",
								  create_function('$matches', 'return base64_encode($matches[0]);'),
								  $filename);

			$filename = preg_replace( $pattern, "", $filename);

			// 동일한 이름의 파일이 있으면 파일명 변경
			if(is_file($dir.'/'.$filename)) {
				for($i=0; $i<20; $i++) {
					$prepend = str_replace('.', '_', microtime(true)).'_';

					if(is_file($dir.'/'.$prepend.$filename)) {
						usleep(mt_rand(100, 10000));
						continue;
					} else {
						break;
					}
				}
			}

			$fileName = $prepend.$filename;
			$dest_path = $file_dir.'/'.$fileName;

			move_uploaded_file($_FILES['guide_Img4']['tmp_name'], $dest_path);
		
			if (file_exists($dest_path)) {
				$size = @getimagesize($dest_path);

				if (!($size[2] === 1 || $size[2] === 2 || $size[2] === 3)) { // gif jpg png 파일이 아니면 올라간 이미지를 삭제한다.
					@unlink($dest_path);
				} else if ($size[0] > $cf_img_width || $size[1] > $cf_img_height) {
					$thumb = null;
					if($size[2] === 2 || $size[2] === 3) {
						//jpg 또는 png 파일 적용
						$thumb = thumbnail($fileName, $file_dir, $file_dir, $cf_img_width, $cf_img_height, true, true);

						if($thumb) {
							@unlink($dest_path);
							rename($file_dir.'/'.$thumb, $dest_path);
						}
					}
					if( !$thumb ){
						// 아이콘의 폭 또는 높이가 설정값 보다 크다면 이미 업로드 된 아이콘 삭제
						@unlink($dest_path);
					}
				}
				//=================================================================\
			}
						
			$guide_ImgFile4 = $fileName;	
		}
	}
	
	
	if ($guide_ImgFile4 != "") {
	    $guide_ImgFile4 = $guide_ImgFile4;
	} else {
	    $guide_ImgFile4 = $guide_Img4;
	}


	//새로운 팝업 이미지경로 출력
	$member_img = $file_dir.'/'.$fileName;

	//파일저장방법 변경 _blob -------------------------------------------------------- 2019.02.19
	
	if(file_exists($member_img) && $fileName != "")
	{
		$now_time = time()+4;	

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

		
		$insQuery = "
			update TB_CONFIG_GUIDE 
			set 
				con_GuideUrl4 ='".$now_time."' 
			where 
				idx ='1' 
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
		@unlink($org_guide_ImgFile4);
		//신규 업로드 팝업 이미지 삭제
		@unlink($member_img);
		// 파일로 blob형태 이미지 저장----------E

	}
	
	//파일저장방법 변경 _blob --------------------------------------------------------





	// 가이드 이미지 5 ------------------------------------------------------------------2019.02.19
	$org_guide_ImgFile5 = $file_dir.'/'.$guide_ImgFile5;
	
	// 파일삭제
	if ($del_guide_Img5) {
	    $file_img5 = $file_dir.'/'.$guide_ImgFile5;
		@unlink($file_img5);
		del_thumbnail(dirname($file_img5), basename($file_img5));
		$guide_Img5 = '';
	} else {
	    $guide_Img5 = "$guide_Img5";
	}


	// 이미지 업로드 
	$image_regex = "/(\.(gif|jpe?g|png))$/i";

	$cf_img_width = "720";
	$cf_img_height = "5000";

	if (isset($_FILES['guide_Img5']) && is_uploaded_file($_FILES['guide_Img5']['tmp_name'])) {  //이미지 업로드 성공일 경우


		if (preg_match($image_regex, $_FILES['guide_Img5']['name'])) {

			@mkdir($file_dir, 0755);
			//@chmod($file_dir, 0644);

			$filename = $_FILES['guide_Img5']['name'];

			//php파일도 getimagesize 에서 Image Type Flag 를 속일수 있다
			if (!preg_match('/\.(gif|jpe?g|png)$/i', $filename)) {
				return '';
			}

			$pattern = "/[#\&\+\-%@=\/\\:;,'\"\^`~\|\!\?\*\$#<>\(\)\[\]\{\}]/";
			$filename = preg_replace("/\s+/", "", $filename);
			$filename = preg_replace( $pattern, "", $filename);

			$filename = preg_replace_callback(
								  "/[가-힣]+/",
								  create_function('$matches', 'return base64_encode($matches[0]);'),
								  $filename);

			$filename = preg_replace( $pattern, "", $filename);

			// 동일한 이름의 파일이 있으면 파일명 변경
			if(is_file($dir.'/'.$filename)) {
				for($i=0; $i<20; $i++) {
					$prepend = str_replace('.', '_', microtime(true)).'_';

					if(is_file($dir.'/'.$prepend.$filename)) {
						usleep(mt_rand(100, 10000));
						continue;
					} else {
						break;
					}
				}
			}

			$fileName = $prepend.$filename;
			$dest_path = $file_dir.'/'.$fileName;

			move_uploaded_file($_FILES['guide_Img5']['tmp_name'], $dest_path);
		
			if (file_exists($dest_path)) {
				$size = @getimagesize($dest_path);

				if (!($size[2] === 1 || $size[2] === 2 || $size[2] === 3)) { // gif jpg png 파일이 아니면 올라간 이미지를 삭제한다.
					@unlink($dest_path);
				} else if ($size[0] > $cf_img_width || $size[1] > $cf_img_height) {
					$thumb = null;
					if($size[2] === 2 || $size[2] === 3) {
						//jpg 또는 png 파일 적용
						$thumb = thumbnail($fileName, $file_dir, $file_dir, $cf_img_width, $cf_img_height, true, true);

						if($thumb) {
							@unlink($dest_path);
							rename($file_dir.'/'.$thumb, $dest_path);
						}
					}
					if( !$thumb ){
						// 아이콘의 폭 또는 높이가 설정값 보다 크다면 이미 업로드 된 아이콘 삭제
						@unlink($dest_path);
					}
				}
				//=================================================================\
			}
						
			$guide_ImgFile5 = $fileName;	
		}
	}
	
	
	if ($guide_ImgFile5 != "") {
	    $guide_ImgFile5 = $guide_ImgFile5;
	} else {
	    $guide_ImgFile5 = $guide_Img5;
	}

	//새로운 팝업 이미지경로 출력
	$member_img = $file_dir.'/'.$fileName;

	//파일저장방법 변경 _blob -------------------------------------------------------- 2019.02.19
	
	if(file_exists($member_img) && $fileName != "")
	{
		$now_time = time()+5;	

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

		
		$insQuery = "
			update TB_CONFIG_GUIDE 
			set 
				con_GuideUrl5 ='".$now_time."' 
			where 
				idx ='1' 
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
		@unlink($org_guide_ImgFile5);
		//신규 업로드 팝업 이미지 삭제
		@unlink($member_img);
		// 파일로 blob형태 이미지 저장----------E

	}
	
	//파일저장방법 변경 _blob --------------------------------------------------------


				
	$ban_Type = "0";   //외부 url주소

		/*
		$upQquery = "UPDATE TB_CONFIG_GUIDE SET con_GuideUrl1 = :con_GuideUrl1, con_GuideUrl2 = :con_GuideUrl2, con_GuideUrl3 = :con_GuideUrl3, con_GuideUrl4 = :con_GuideUrl4, con_GuideUrl5 = :con_GuideUrl5 WHERE idx = 1  LIMIT 1";

		$upStmt = $DB_con->prepare($upQquery);
		$upStmt->bindParam(":con_GuideUrl1", $guide_ImgFile1);
		$upStmt->bindParam(":con_GuideUrl2", $guide_ImgFile2);
		$upStmt->bindParam(":con_GuideUrl3", $guide_ImgFile3);
		$upStmt->bindParam(":con_GuideUrl4", $guide_ImgFile4);
		$upStmt->bindParam(":con_GuideUrl5", $guide_ImgFile5);
		$upStmt->execute();
		*/

		$preUrl = "configGuideReg.php";
		$message = "mod";
		proc_msg($message, $preUrl);

	}


	dbClose($DB_con);
	$stmt = null;
	$stmt1 = null;
	$upStmt = null;


	?>