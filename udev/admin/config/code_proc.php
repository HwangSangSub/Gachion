<?
	include "../../lib/common.php";
	include "../../../lib/alertLib.php"; 
	include "../../../lib/thumbnail.lib.php";   //썸네일
	
	$mode = trim($mode);
	$page =  trim($page);
	$qstr = trim($qstr);
	$idx  = trim($idx);
	$code_Div = trim($code_Div);
	$code_Sub_Div = trim($code_Sub_Div);
	$code_Name = trim($code_Name);
	$code_on_ImgFile = trim($code_On_ImgFile);
	// echo "11111";
	// print_r($code_on_ImgFile);
	// echo "22222";
    // print_r( $_FILES[ 'code_On_ImgFile' ] );
    // $file = $_FILES["code_On_ImgFile"];
    // $tmp_name = $file["tmp_name"];
	// echo $tmp_name;
	// echo "33333";
	// print_r($_POST);
	// echo "44444";
    // print_r( $_FILES[ 'code_On_Img' ] );
	// echo "55555";
	// exit;
	$code_off_ImgFile = trim($code_off_ImgFile);
	$code_sel_Color = trim($code_sel_Color);	// 새로 선택한 색상
	$code_Color = trim($code_Color);				// 등록된 색상
	if($code_sel_Color != ''){
		$code_Color = $code_sel_Color;
	}else{
		$code_Color = $code_Color;
	}
	//$code_ImgFile = trim($code_ImgFile);
	$use_Bit = trim($use_Bit);
	$reg_Date = DU_TIME_YMDHIS;
	
	$DB_con = db1();
	//아이콘별 색상지정
	if($code_Div == 'category'){
		$cf_img_width = "200";
		$cf_img_height = "210";
	}else if($code_Div == 'minicategory'){
		$cf_img_width = "24";
		$cf_img_height = "24";
	}else if($code_Div == 'placeicon'){
		$cf_img_width = "80";
		$cf_img_height = "80";
	}else{
		$cf_img_width = "100";
		$cf_img_height = "100";
	}
    $chk_code_on_Img = "";
    $chk_code_off_Img = "";
    $chk_code_and_Img = "";
    $chk_code_ios_Img = "";
	// 배너 이미지 경로
	$file_dir = $_SERVER["DOCUMENT_ROOT"].'/udev/admin/data/code_img';
	$file_dir_and = $_SERVER["DOCUMENT_ROOT"].'/udev/admin/data/code_img/and';
	$file_dir_ios = $_SERVER["DOCUMENT_ROOT"].'/udev/admin/data/code_img/ios';
	if ($mode == "reg") {  //등록일 경우
			$code_query = "";
			$code_query = "SELECT code FROM TB_CONFIG_CODE WHERE code_Div = :code_Div ORDER BY idx DESC LIMIT 1;" ;
			$code_stmt = $DB_con->prepare($code_query);
			$code_stmt->bindParam(":code_Div",$code_Div);		
			$code_stmt->execute();
			$code_Num = $code_stmt->rowCount();
			
			if($code_Num < 1){ //아닐경우
				$ins_code = "1";
			}else{
				$code_row = $code_stmt->fetch(PDO::FETCH_ASSOC);
				$code = $code_row['code'];	
				$ins_code = (int)$code + 1;
			}
			if($code_Sub_Div != ''){
				$insQuery = "INSERT INTO TB_CONFIG_CODE ( code_Div, code_Sub_Div, code, code_Name, code_Color, use_Bit, reg_Date ) VALUES ( :code_Div, :code_Sub_Div, :code, :code_Name, :code_Color, :use_Bit, :reg_Date )";
				$stmt = $DB_con->prepare($insQuery);
				$stmt->bindParam(":code_Div",$code_Div);		
				$stmt->bindParam(":code_Sub_Div",$code_Sub_Div);		
				$stmt->bindParam(":code", $ins_code);
				$stmt->bindParam(":code_Name", $code_Name);		
				$stmt->bindParam(":code_Color", $code_Color);		
				$stmt->bindParam(":use_Bit", $use_Bit);		
				$stmt->bindParam(":reg_Date", $reg_Date);
				$stmt->execute();
				$mIdx = $DB_con->lastInsertId();
			}else{
				$insQuery = "INSERT INTO TB_CONFIG_CODE ( code_Div, code, code_Name, use_Bit, reg_Date ) VALUES ( :code_Div, :code, :code_Name, :use_Bit, :reg_Date )";
				$stmt = $DB_con->prepare($insQuery);
				$stmt->bindParam(":code_Div",$code_Div);		
				$stmt->bindParam(":code", $ins_code);
				$stmt->bindParam(":code_Name", $code_Name);		
				$stmt->bindParam(":use_Bit", $use_Bit);		
				$stmt->bindParam(":reg_Date", $reg_Date);
				$stmt->execute();
				$mIdx = $DB_con->lastInsertId();
			}

// ON이미지 ------------------------------------------------------------------
	$org_chk_code_Img = $file_dir.'/'.$chk_code_on_Img;

	// 파일삭제
	if ($del_code_on_ImgFile) {
	    $file_img = $file_dir.'/'.$chk_code_on_Img;
		@unlink($file_img);
		del_thumbnail(dirname($file_img), basename($file_img));
		$code_on_ImgFile = '';
	} else {
	    $code_on_ImgFile = "$code_on_ImgFile";
	}


	// 이미지 업로드 
	$image_regex = "/(\.(gif|jpe?g|png))$/i";
/*
	$cf_img_width = "100";
	$cf_img_height = "100";
*/
	if (isset($_FILES['code_On_ImgFile']) && is_uploaded_file($_FILES['code_On_ImgFile']['tmp_name'])) {  //이미지 업로드 성공일 경우


		if (preg_match($image_regex, $_FILES['code_On_ImgFile']['name'])) {

			@mkdir($file_dir, 0755);
			//@chmod($file_dir, 0644);

			$filename = $_FILES['code_On_ImgFile']['name'];

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
			if(is_file($file_dir.'/'.$filename)) {
				for($i=0; $i<20; $i++) {
					$prepend = str_replace('.', '_', microtime(true)).'_';

					if(is_file($file_dir.'/'.$prepend.$filename)) {
						usleep(mt_rand(100, 10000));
						continue;
					} else {
						break;
					}
				}
			}

			$fileName = $prepend.$filename;
			$dest_path = $file_dir.'/'.$fileName;

			move_uploaded_file($_FILES['code_On_ImgFile']['tmp_name'], $dest_path);
		
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
						
			$chk_code_Img = $fileName;	
		}
	}
	
	
	if ($chk_code_on_Img != "") {
	    $chk_code_on_Img = $chk_code_on_Img;
	} else {
	    $chk_code_on_Img = $code_on_ImgFile;
	}

	//새로운 팝업 이미지경로 출력
	$member_on_img = $file_dir.'/'.$fileName;


	//파일저장방법 변경 _blob -------------------------------------------------------- 2019.02.19
	
	if(file_exists($member_on_img) && $fileName != "")
	{
		$now_time = time()."_on";	

		//첨부파일 -> 썸네일 이미지로 변경 및 저장된 경로
		$filename = $member_on_img;
		$handle = fopen($filename,"rb");
		$size =	GetImageSize($filename);
		$width = $size[0];
		$height = $size[1];
		$imageblob = addslashes(fread($handle, filesize($filename)));
		$filesize = filesize($filename);
		$mine = $size[mime];
		fclose($handle);		

		
		$insQuery = "
			update TB_CONFIG_CODE 
			set 
				code_on_Img ='".$now_time."' 
			where 
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
		@unlink($org_chk_code_on_Img);
		//신규 업로드 팝업 이미지 삭제
		@unlink($member_on_img);
		// 파일로 blob형태 이미지 저장----------E

	}

// OFF 이미지------------------------------------------------------------------
	$org_chk_code_off_Img = $file_dir.'/'.$chk_code_off_Img;

	// 파일삭제
	if ($del_code_off_ImgFile) {
	    $file_off_img = $file_dir.'/'.$chk_code_off_Img;
		@unlink($file_off_img);
		del_thumbnail(dirname($file_off_img), basename($file_off_img));
		$code_off_ImgFile = '';
	} else {
	    $code_off_ImgFile = "$code_off_ImgFile";
	}


	// 이미지 업로드 
	$image_regex = "/(\.(gif|jpe?g|png))$/i";
/*
	$cf_img_width = "100";
	$cf_img_height = "100";
*/
	if (isset($_FILES['code_Off_ImgFile']) && is_uploaded_file($_FILES['code_Off_ImgFile']['tmp_name'])) {  //이미지 업로드 성공일 경우


		if (preg_match($image_regex, $_FILES['code_Off_ImgFile']['name'])) {

			@mkdir($file_dir, 0755);
			//@chmod($file_dir, 0644);

			$filename_off = $_FILES['code_Off_ImgFile']['name'];

			//php파일도 getimagesize 에서 Image Type Flag 를 속일수 있다
			if (!preg_match('/\.(gif|jpe?g|png)$/i', $filename_off)) {
				return '';
			}

			$pattern = "/[#\&\+\-%@=\/\\:;,'\"\^`~\|\!\?\*\$#<>\(\)\[\]\{\}]/";
			$filename_off = preg_replace("/\s+/", "", $filename_off);
			$filename_off = preg_replace( $pattern, "", $filename_off);

			$filename_off = preg_replace_callback(
								  "/[가-힣]+/",
								  create_function('$matches', 'return base64_encode($matches[0]);'),
								  $filename_off);

			$filename_off = preg_replace( $pattern, "", $filename_off);

			// 동일한 이름의 파일이 있으면 파일명 변경
			if(is_file($dir.'/'.$filename_off)) {
				for($i=0; $i<20; $i++) {
					$prepend_off = str_replace('.', '_', microtime(true)).'_';

					if(is_file($dir.'/'.$prepend_off.$filename_off)) {
						usleep(mt_rand(100, 10000));
						continue;
					} else {
						break;
					}
				}
			}

			$fileName_off = $prepend_off.$filename_off;
			$dest_path_off = $file_dir.'/'.$fileName_off;

			move_uploaded_file($_FILES['code_Off_ImgFile']['tmp_name'], $dest_path_off);
		
			if (file_exists($dest_path_off)) {
				$size = @getimagesize($dest_path_off);

				if (!($size[2] === 1 || $size[2] === 2 || $size[2] === 3)) { // gif jpg png 파일이 아니면 올라간 이미지를 삭제한다.
					@unlink($dest_path_off);
				} else if ($size[0] > $cf_img_width || $size[1] > $cf_img_height) {
					$thumb_off = null;
					if($size[2] === 2 || $size[2] === 3) {
						//jpg 또는 png 파일 적용
						$thumb_off = thumbnail($fileName_off, $file_dir, $file_dir, $cf_img_width, $cf_img_height, true, true);

						if($thumb_off) {
							@unlink($dest_path_off);
							rename($file_dir.'/'.$thumb_off, $dest_path_off);
						}
					}
					if( !$thumb_off ){
						// 아이콘의 폭 또는 높이가 설정값 보다 크다면 이미 업로드 된 아이콘 삭제
						@unlink($dest_path_off);
					}
				}
				//=================================================================\
			}
						
			$chk_code_off_Img = $fileName_off;	
		}
	}
	
	
	if ($chk_code_off_Img != "") {
	    $chk_code_off_Img = $chk_code_off_Img;
	} else {
	    $chk_code_off_Img = $code_off_ImgFile;
	}

	//새로운 팝업 이미지경로 출력
	$member_off_img = $file_dir.'/'.$fileName_off;


	//파일저장방법 변경 _blob -------------------------------------------------------- 2019.02.19
	
	if(file_exists($member_off_img) && $fileName_off != "")
	{
		$now_time = time()."_off";	

		//첨부파일 -> 썸네일 이미지로 변경 및 저장된 경로
		$filename_off = $member_off_img;
		$handle_off = fopen($filename_off,"rb");
		$size_off =	GetImageSize($filename_off);
		$width = $size_off[0];
		$height = $size_off[1];
		$imageblob_off = addslashes(fread($handle_off, filesize($filename_off)));
		$filesize = filesize($filename_off);
		$mine = $size_off[mime];
		fclose($handle_off);		

		
		$insQuery = "
			update TB_CONFIG_CODE 
			set 
				code_off_Img ='".$now_time."' 
			where 
				idx =	".$mIdx." 
		";		
		$DB_con->exec($insQuery);


		// 파일로 blob형태 이미지 저장----------S
		// 새로 생성되는 파일명(전체경로 포함) : $m_file
		$img_txt_off = $now_time;
		$m_file_off = $file_dir.'/'.$img_txt_off;
		$is_file_exist_off = file_exists($m_file_off);

		if ($is_file_exist_off) {
			//echo 'Found it';
		} else {
			//echo 'Not found.';
			$file_off = fopen($m_file_off , "w");
			fwrite($file_off, $imageblob_off);
			fclose($file_off);
			chmod($m_file_off, 0755);
		}

		//기존 파일 삭제
		@unlink($org_chk_code_off_Img);
		//신규 업로드 팝업 이미지 삭제
		@unlink($member_off_img);
		// 파일로 blob형태 이미지 저장----------E

	}	
	//파일저장방법 변경 _blob --------------------------------------------------------
// 안드로이드이미지 이미지------------------------------------------------------------------
// 안드로이드이미지 이미지------------------------------------------------------------------
// 안드로이드이미지 이미지------------------------------------------------------------------
// 안드로이드이미지 이미지------------------------------------------------------------------
// 안드로이드이미지 이미지------------------------------------------------------------------
	$org_chk_code_and_Img = $file_dir_and.'/'.$chk_code_and_Img;

	// 파일삭제
	if ($del_code_and_ImgFile) {
	    $file_and_img = $file_dir_and.'/'.$chk_code_and_Img;
		@unlink($file_and_img);
		del_thumbnail(dirname($file_and_img), basename($file_and_img));
		$code_and_ImgFile = '';
	} else {
	    $code_and_ImgFile = "$code_and_ImgFile";
	}


	// 이미지 업로드 
	$image_regex = "/(\.(gif|jpe?g|png))$/i";
/*
	$cf_img_width = "100";
	$cf_img_height = "100";
*/
	if (isset($_FILES['code_And_ImgFile']) && is_uploaded_file($_FILES['code_And_ImgFile']['tmp_name'])) {  //이미지 업로드 성공일 경우


		if (preg_match($image_regex, $_FILES['code_And_ImgFile']['name'])) {

			@mkdir($file_dir_and, 0755);
			//@chmod($file_dir_and, 0644);

			$filename_and = $_FILES['code_And_ImgFile']['name'];

			//php파일도 getimagesize 에서 Image Type Flag 를 속일수 있다
			if (!preg_match('/\.(gif|jpe?g|png)$/i', $filename_and)) {
				return '';
			}

			$pattern = "/[#\&\+\-%@=\/\\:;,'\"\^`~\|\!\?\*\$#<>\(\)\[\]\{\}]/";
			$filename_and = preg_replace("/\s+/", "", $filename_and);
			$filename_and = preg_replace( $pattern, "", $filename_and);

			$filename_and = preg_replace_callback(
								  "/[가-힣]+/",
								  create_function('$matches', 'return base64_encode($matches[0]);'),
								  $filename_and);

			$filename_and = preg_replace( $pattern, "", $filename_and);

			// 동일한 이름의 파일이 있으면 파일명 변경
			if(is_file($dir.'/'.$filename_and)) {
				for($i=0; $i<20; $i++) {
					$prepend_and = str_replace('.', '_', microtime(true)).'_';

					if(is_file($dir.'/'.$prepend_and.$filename_and)) {
						usleep(mt_rand(100, 10000));
						continue;
					} else {
						break;
					}
				}
			}

			$fileName_and = $prepend_and.$filename_and;
			$dest_path_and = $file_dir_and.'/'.$fileName_and;

			move_uploaded_file($_FILES['code_And_ImgFile']['tmp_name'], $dest_path_and);
		
			if (file_exists($dest_path_and)) {
				$size = @getimagesize($dest_path_and);

				if (!($size[2] === 1 || $size[2] === 2 || $size[2] === 3)) { // gif jpg png 파일이 아니면 올라간 이미지를 삭제한다.
					@unlink($dest_path_and);
				} else if ($size[0] > $cf_img_width || $size[1] > $cf_img_height) {
					$thumb_and = null;
					if($size[2] === 2 || $size[2] === 3) {
						//jpg 또는 png 파일 적용
						$thumb_and = thumbnail($fileName_and, $file_dir_and, $file_dir_and, $cf_img_width, $cf_img_height, true, true);

						if($thumb_and) {
							@unlink($dest_path_and);
							rename($file_dir_and.'/'.$thumb_and, $dest_path_and);
						}
					}
					if( !$thumb_and ){
						// 아이콘의 폭 또는 높이가 설정값 보다 크다면 이미 업로드 된 아이콘 삭제
						@unlink($dest_path_and);
					}
				}
				//=================================================================\
			}
						
			$chk_code_and_Img = $fileName_and;	
		}
	}
	
	
	if ($chk_code_and_Img != "") {
	    $chk_code_and_Img = $chk_code_and_Img;
	} else {
	    $chk_code_and_Img = $code_and_ImgFile;
	}

	//새로운 팝업 이미지경로 출력
	$member_and_img = $file_dir_and.'/'.$fileName_and;


	//파일저장방법 변경 _blob -------------------------------------------------------- 2019.02.19
	
	if(file_exists($member_and_img) && $fileName_and != "")
	{
		$now_time = time()."_and";	

		//첨부파일 -> 썸네일 이미지로 변경 및 저장된 경로
		$filename_and = $member_and_img;
		$handle_and = fopen($filename_and,"rb");
		$size_and =	GetImageSize($filename_and);
		$width = $size_and[0];
		$height = $size_and[1];
		$imageblob_and = addslashes(fread($handle_and, filesize($filename_and)));
		$filesize = filesize($filename_and);
		$mine = $size_and[mime];
		fclose($handle_and);		

		
		$insQuery = "
			update TB_CONFIG_CODE 
			set 
				code_and_Img ='".$now_time."' 
			where 
				idx =	".$mIdx." 
		";		
		$DB_con->exec($insQuery);


		// 파일로 blob형태 이미지 저장----------S
		// 새로 생성되는 파일명(전체경로 포함) : $m_file
		$img_txt_and = $now_time;
		$m_file_and = $file_dir_and.'/'.$img_txt_and;
		$is_file_exist_and = file_exists($m_file_and);

		if ($is_file_exist_and) {
			//echo 'Found it';
		} else {
			//echo 'Not found.';
			$file_and = fopen($m_file_and , "w");
			fwrite($file_and, $imageblob_and);
			fclose($file_and);
			chmod($m_file_and, 0755);
		}

		//기존 파일 삭제
		@unlink($org_chk_code_and_Img);
		//신규 업로드 팝업 이미지 삭제
		@unlink($member_and_img);
		// 파일로 blob형태 이미지 저장----------E

	}	
// 아이폰이미지 이미지------------------------------------------------------------------
// 아이폰이미지 이미지------------------------------------------------------------------
// 아이폰이미지 이미지------------------------------------------------------------------
// 아이폰이미지 이미지------------------------------------------------------------------
	$org_chk_code_ios_Img = $file_dir_ios.'/'.$chk_code_ios_Img;

	// 파일삭제
	if ($del_code_ios_ImgFile) {
	    $file_ios_img = $file_dir_ios.'/'.$chk_code_ios_Img;
		@unlink($file_ios_img);
		del_thumbnail(dirname($file_ios_img), basename($file_ios_img));
		$code_ios_ImgFile = '';
	} else {
	    $code_ios_ImgFile = "$code_ios_ImgFile";
	}


	// 이미지 업로드 
	$image_regex = "/(\.(gif|jpe?g|png))$/i";
/*
	$cf_img_width = "100";
	$cf_img_height = "100";
*/
	if (isset($_FILES['code_Ios_ImgFile']) && is_uploaded_file($_FILES['code_Ios_ImgFile']['tmp_name'])) {  //이미지 업로드 성공일 경우


		if (preg_match($image_regex, $_FILES['code_Ios_ImgFile']['name'])) {

			@mkdir($file_dir_ios, 0755);
			//@chmod($file_dir_ios, 0644);

			$filename_ios = $_FILES['code_Ios_ImgFile']['name'];

			//php파일도 getimagesize 에서 Image Type Flag 를 속일수 있다
			if (!preg_match('/\.(gif|jpe?g|png)$/i', $filename_ios)) {
				return '';
			}

			$pattern = "/[#\&\+\-%@=\/\\:;,'\"\^`~\|\!\?\*\$#<>\(\)\[\]\{\}]/";
			$filename_ios = preg_replace("/\s+/", "", $filename_ios);
			$filename_ios = preg_replace( $pattern, "", $filename_ios);

			$filename_ios = preg_replace_callback(
								  "/[가-힣]+/",
								  create_function('$matches', 'return base64_encode($matches[0]);'),
								  $filename_ios);

			$filename_ios = preg_replace( $pattern, "", $filename_ios);

			// 동일한 이름의 파일이 있으면 파일명 변경
			if(is_file($dir.'/'.$filename_ios)) {
				for($i=0; $i<20; $i++) {
					$prepend_ios = str_replace('.', '_', microtime(true)).'_';

					if(is_file($dir.'/'.$prepend_ios.$filename_ios)) {
						usleep(mt_rand(100, 10000));
						continue;
					} else {
						break;
					}
				}
			}

			$fileName_ios = $prepend_ios.$filename_ios;
			$dest_path_ios = $file_dir_ios.'/'.$fileName_ios;

			move_uploaded_file($_FILES['code_Ios_ImgFile']['tmp_name'], $dest_path_ios);
		
			if (file_exists($dest_path_ios)) {
				$size = @getimagesize($dest_path_ios);

				if (!($size[2] === 1 || $size[2] === 2 || $size[2] === 3)) { // gif jpg png 파일이 아니면 올라간 이미지를 삭제한다.
					@unlink($dest_path_ios);
				} else if ($size[0] > $cf_img_width || $size[1] > $cf_img_height) {
					$thumb_ios = null;
					if($size[2] === 2 || $size[2] === 3) {
						//jpg 또는 png 파일 적용
						$thumb_ios = thumbnail($fileName_ios, $file_dir_ios, $file_dir_ios, $cf_img_width, $cf_img_height, true, true);

						if($thumb_ios) {
							@unlink($dest_path_ios);
							rename($file_dir_ios.'/'.$thumb_ios, $dest_path_ios);
						}
					}
					if( !$thumb_ios ){
						// 아이콘의 폭 또는 높이가 설정값 보다 크다면 이미 업로드 된 아이콘 삭제
						@unlink($dest_path_ios);
					}
				}
				//=================================================================\
			}
						
			$chk_code_ios_Img = $fileName_ios;	
		}
	}
	
	
	if ($chk_code_ios_Img != "") {
	    $chk_code_ios_Img = $chk_code_ios_Img;
	} else {
	    $chk_code_ios_Img = $code_ios_ImgFile;
	}

	//새로운 팝업 이미지경로 출력
	$member_ios_img = $file_dir_ios.'/'.$fileName_ios;


	//파일저장방법 변경 _blob -------------------------------------------------------- 2019.02.19
	
	if(file_exists($member_ios_img) && $fileName_ios != "")
	{
		$now_time = time()."_ios";	

		//첨부파일 -> 썸네일 이미지로 변경 및 저장된 경로
		$filename_ios = $member_ios_img;
		$handle_ios = fopen($filename_ios,"rb");
		$size_ios =	GetImageSize($filename_ios);
		$width = $size_ios[0];
		$height = $size_ios[1];
		$imageblob_ios = addslashes(fread($handle_ios, filesize($filename_ios)));
		$filesize = filesize($filename_ios);
		$mine = $size_ios[mime];
		fclose($handle_ios);		

		
		$insQuery = "
			update TB_CONFIG_CODE 
			set 
				code_ios_Img ='".$now_time."' 
			where 
				idx =	".$mIdx." 
		";		
		$DB_con->exec($insQuery);


		// 파일로 blob형태 이미지 저장----------S
		// 새로 생성되는 파일명(전체경로 포함) : $m_file
		$img_txt_ios = $now_time;
		$m_file_ios = $file_dir_ios.'/'.$img_txt_ios;
		$is_file_exist_ios = file_exists($m_file_ios);

		if ($is_file_exist_ios) {
			//echo 'Found it';
		} else {
			//echo 'Not found.';
			$file_ios = fopen($m_file_ios , "w");
			fwrite($file_ios, $imageblob_ios);
			fclose($file_ios);
			chmod($m_file_ios, 0755);
		}

		//기존 파일 삭제
		@unlink($org_chk_code_ios_Img);
		//신규 업로드 팝업 이미지 삭제
		@unlink($member_ios_img);
		// 파일로 blob형태 이미지 저장----------E

	}	
			$preUrl = "code_list.php?page=$page&$qstr";
			$message = "reg";
			proc_msg($message, $preUrl);
	} else if ($mode == "mod") { //수정일경우

		$query = "";
		$query = "SELECT code_on_Img, code_off_Img  FROM TB_CONFIG_CODE WHERE idx = :idx" ;
		$stmt1 = $DB_con->prepare($query);			
		$stmt1->bindParam(":idx", $idx);		
		//$idx = trim($idx);
		$stmt1->execute();
        $row1 = $stmt1->fetch(PDO::FETCH_ASSOC);
        $chk_code_on_Img = trim($row1['code_on_Img']);
        $chk_code_off_Img = trim($row1['code_off_Img']);

	// ON이미지 ------------------------------------------------------------------
	$org_chk_code_Img = $file_dir.'/'.$chk_code_on_Img;

	// 파일삭제
	if ($del_code_on_ImgFile) {
	    $file_img = $file_dir.'/'.$chk_code_on_Img;
		@unlink($file_img);
		del_thumbnail(dirname($file_img), basename($file_img));
		$code_on_ImgFile = '';
	} else {
	    $code_on_ImgFile = "$code_on_ImgFile";
	}


	// 이미지 업로드 
	$image_regex = "/(\.(gif|jpe?g|png))$/i";
/*
	$cf_img_width = "100";
	$cf_img_height = "100";
*/
	if (isset($_FILES['code_On_ImgFile']) && is_uploaded_file($_FILES['code_On_ImgFile']['tmp_name'])) {  //이미지 업로드 성공일 경우


		if (preg_match($image_regex, $_FILES['code_On_ImgFile']['name'])) {
			@mkdir($file_dir, 0755);
			//@chmod($file_dir, 0644);

			$filename = $_FILES['code_On_ImgFile']['name'];

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

			move_uploaded_file($_FILES['code_On_ImgFile']['tmp_name'], $dest_path);
		
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
				
			$chk_code_Img = $fileName;	
		}
	}
	
	
	if ($chk_code_on_Img != "") {
	    $chk_code_on_Img = $chk_code_on_Img;
	} else {
	    $chk_code_on_Img = $code_on_ImgFile;
	}

	//새로운 팝업 이미지경로 출력
	$member_on_img = $file_dir.'/'.$fileName;


	echo $_FILES['code_On_ImgFile']['tmp_name']."<BR>";
	//파일저장방법 변경 _blob -------------------------------------------------------- 2019.02.19
	
	echo $member_on_img;
	if(file_exists($member_on_img) && $fileName != "")
	{
		$now_time = time()."_on";	

		echo $now_time;
		//첨부파일 -> 썸네일 이미지로 변경 및 저장된 경로
		$filename = $member_on_img;
		$handle = fopen($filename,"rb");
		$size =	GetImageSize($filename);
		$width = $size[0];
		$height = $size[1];
		$imageblob = addslashes(fread($handle, filesize($filename)));
		$filesize = filesize($filename);
		$mine = $size['mime'];
		fclose($handle);		

		
		$insQuery = "
			update TB_CONFIG_CODE 
			set 
				code_on_Img ='".$now_time."' 
			where 
				idx =	".$idx." 
		";		
		$DB_con->exec($insQuery);


		// 파일로 blob형태 이미지 저장----------S
		// 새로 생성되는 파일명(전체경로 포함) : $m_file
		$img_txt = $now_time;
		$m_file = $file_dir.'/'.$img_txt;
		$is_file_exist = file_exists($m_file);

		if ($is_file_exist) {
			echo 'Found it';
		} else {
			echo 'Not found.';
			$file = fopen($m_file , "w");
			fwrite($file, $imageblob);
			fclose($file);
			chmod($m_file, 0755);
		}

		//기존 파일 삭제
		@unlink($org_chk_code_on_Img);
		//신규 업로드 팝업 이미지 삭제
		@unlink($member_on_img);
		// 파일로 blob형태 이미지 저장----------E

	}

	// OFF 이미지------------------------------------------------------------------
	$org_chk_code_off_Img = $file_dir.'/'.$chk_code_off_Img;

	// 파일삭제
	if ($del_code_off_ImgFile) {
	    $file_off_img = $file_dir.'/'.$chk_code_off_Img;
		@unlink($file_off_img);
		del_thumbnail(dirname($file_off_img), basename($file_off_img));
		$code_off_ImgFile = '';
	} else {
	    $code_off_ImgFile = "$code_off_ImgFile";
	}


	// 이미지 업로드 
	$image_regex = "/(\.(gif|jpe?g|png))$/i";
/*
	$cf_img_width = "100";
	$cf_img_height = "100";
*/
	if (isset($_FILES['code_Off_ImgFile']) && is_uploaded_file($_FILES['code_Off_ImgFile']['tmp_name'])) {  //이미지 업로드 성공일 경우


		if (preg_match($image_regex, $_FILES['code_Off_ImgFile']['name'])) {

			@mkdir($file_dir, 0755);
			//@chmod($file_dir, 0644);

			$filename_off = $_FILES['code_Off_ImgFile']['name'];

			//php파일도 getimagesize 에서 Image Type Flag 를 속일수 있다
			if (!preg_match('/\.(gif|jpe?g|png)$/i', $filename_off)) {
				return '';
			}

			$pattern = "/[#\&\+\-%@=\/\\:;,'\"\^`~\|\!\?\*\$#<>\(\)\[\]\{\}]/";
			$filename_off = preg_replace("/\s+/", "", $filename_off);
			$filename_off = preg_replace( $pattern, "", $filename_off);

			$filename_off = preg_replace_callback(
								  "/[가-힣]+/",
								  create_function('$matches', 'return base64_encode($matches[0]);'),
								  $filename_off);

			$filename_off = preg_replace( $pattern, "", $filename_off);

			// 동일한 이름의 파일이 있으면 파일명 변경
			if(is_file($dir.'/'.$filename_off)) {
				for($i=0; $i<20; $i++) {
					$prepend_off = str_replace('.', '_', microtime(true)).'_';

					if(is_file($dir.'/'.$prepend_off.$filename_off)) {
						usleep(mt_rand(100, 10000));
						continue;
					} else {
						break;
					}
				}
			}

			$fileName_off = $prepend_off.$filename_off;
			$dest_path_off = $file_dir.'/'.$fileName_off;

			move_uploaded_file($_FILES['code_Off_ImgFile']['tmp_name'], $dest_path_off);
		
			if (file_exists($dest_path_off)) {
				$size = @getimagesize($dest_path_off);

				if (!($size[2] === 1 || $size[2] === 2 || $size[2] === 3)) { // gif jpg png 파일이 아니면 올라간 이미지를 삭제한다.
					@unlink($dest_path_off);
				} else if ($size[0] > $cf_img_width || $size[1] > $cf_img_height) {
					$thumb_off = null;
					if($size[2] === 2 || $size[2] === 3) {
						//jpg 또는 png 파일 적용
						$thumb_off = thumbnail($fileName_off, $file_dir, $file_dir, $cf_img_width, $cf_img_height, true, true);

						if($thumb_off) {
							@unlink($dest_path_off);
							rename($file_dir.'/'.$thumb_off, $dest_path_off);
						}
					}
					if( !$thumb_off ){
						// 아이콘의 폭 또는 높이가 설정값 보다 크다면 이미 업로드 된 아이콘 삭제
						@unlink($dest_path_off);
					}
				}
				//=================================================================\
			}
						
			$chk_code_off_Img = $fileName_off;	
		}
	}
	
	
	if ($chk_code_off_Img != "") {
	    $chk_code_off_Img = $chk_code_off_Img;
	} else {
	    $chk_code_off_Img = $code_off_ImgFile;
	}

	//새로운 팝업 이미지경로 출력
	$member_off_img = $file_dir.'/'.$fileName_off;


	//파일저장방법 변경 _blob -------------------------------------------------------- 2019.02.19
	
	if(file_exists($member_off_img) && $fileName_off != "")
	{
		$now_time = time()."_off";	

		//첨부파일 -> 썸네일 이미지로 변경 및 저장된 경로
		$filename_off = $member_off_img;
		$handle_off = fopen($filename_off,"rb");
		$size_off =	GetImageSize($filename_off);
		$width = $size_off[0];
		$height = $size_off[1];
		$imageblob_off = addslashes(fread($handle_off, filesize($filename_off)));
		$filesize = filesize($filename_off);
		$mine = $size_off[mime];
		fclose($handle_off);		

		
		$insQuery = "
			update TB_CONFIG_CODE 
			set 
				code_off_Img ='".$now_time."' 
			where 
				idx =	".$idx." 
		";		
		$DB_con->exec($insQuery);


		// 파일로 blob형태 이미지 저장----------S
		// 새로 생성되는 파일명(전체경로 포함) : $m_file
		$img_txt_off = $now_time;
		$m_file_off = $file_dir.'/'.$img_txt_off;
		$is_file_exist_off = file_exists($m_file_off);

		if ($is_file_exist_off) {
			//echo 'Found it';
		} else {
			//echo 'Not found.';
			$file_off = fopen($m_file_off , "w");
			fwrite($file_off, $imageblob_off);
			fclose($file_off);
			chmod($m_file_off, 0755);
		}

		//기존 파일 삭제
		@unlink($org_chk_code_off_Img);
		//신규 업로드 팝업 이미지 삭제
		@unlink($member_off_img);
		// 파일로 blob형태 이미지 저장----------E

	}	
	//파일저장방법 변경 _blob --------------------------------------------------------

// 안드로이드이미지 이미지------------------------------------------------------------------
// 안드로이드이미지 이미지------------------------------------------------------------------
// 안드로이드이미지 이미지------------------------------------------------------------------
// 안드로이드이미지 이미지------------------------------------------------------------------
// 안드로이드이미지 이미지------------------------------------------------------------------
	$org_chk_code_and_Img = $file_dir_and.'/'.$chk_code_and_Img;

	// 파일삭제
	if ($del_code_and_ImgFile) {
	    $file_and_img = $file_dir_and.'/'.$chk_code_and_Img;
		@unlink($file_and_img);
		del_thumbnail(dirname($file_and_img), basename($file_and_img));
		$code_and_ImgFile = '';
	} else {
	    $code_and_ImgFile = "$code_and_ImgFile";
	}


	// 이미지 업로드 
	$image_regex = "/(\.(gif|jpe?g|png))$/i";
/*
	$cf_img_width = "100";
	$cf_img_height = "100";
*/
	if (isset($_FILES['code_And_ImgFile']) && is_uploaded_file($_FILES['code_And_ImgFile']['tmp_name'])) {  //이미지 업로드 성공일 경우


		if (preg_match($image_regex, $_FILES['code_And_ImgFile']['name'])) {

			@mkdir($file_dir_and, 0755);
			//@chmod($file_dir_and, 0644);

			$filename_and = $_FILES['code_And_ImgFile']['name'];

			//php파일도 getimagesize 에서 Image Type Flag 를 속일수 있다
			if (!preg_match('/\.(gif|jpe?g|png)$/i', $filename_and)) {
				return '';
			}

			$pattern = "/[#\&\+\-%@=\/\\:;,'\"\^`~\|\!\?\*\$#<>\(\)\[\]\{\}]/";
			$filename_and = preg_replace("/\s+/", "", $filename_and);
			$filename_and = preg_replace( $pattern, "", $filename_and);

			$filename_and = preg_replace_callback(
								  "/[가-힣]+/",
								  create_function('$matches', 'return base64_encode($matches[0]);'),
								  $filename_and);

			$filename_and = preg_replace( $pattern, "", $filename_and);

			// 동일한 이름의 파일이 있으면 파일명 변경
			if(is_file($dir.'/'.$filename_and)) {
				for($i=0; $i<20; $i++) {
					$prepend_and = str_replace('.', '_', microtime(true)).'_';

					if(is_file($dir.'/'.$prepend_and.$filename_and)) {
						usleep(mt_rand(100, 10000));
						continue;
					} else {
						break;
					}
				}
			}

			$fileName_and = $prepend_and.$filename_and;
			$dest_path_and = $file_dir_and.'/'.$fileName_and;

			move_uploaded_file($_FILES['code_And_ImgFile']['tmp_name'], $dest_path_and);
		
			if (file_exists($dest_path_and)) {
				$size = @getimagesize($dest_path_and);

				if (!($size[2] === 1 || $size[2] === 2 || $size[2] === 3)) { // gif jpg png 파일이 아니면 올라간 이미지를 삭제한다.
					@unlink($dest_path_and);
				} else if ($size[0] > $cf_img_width || $size[1] > $cf_img_height) {
					$thumb_and = null;
					if($size[2] === 2 || $size[2] === 3) {
						//jpg 또는 png 파일 적용
						$thumb_and = thumbnail($fileName_and, $file_dir_and, $file_dir_and, $cf_img_width, $cf_img_height, true, true);

						if($thumb_and) {
							@unlink($dest_path_and);
							rename($file_dir_and.'/'.$thumb_and, $dest_path_and);
						}
					}
					if( !$thumb_and ){
						// 아이콘의 폭 또는 높이가 설정값 보다 크다면 이미 업로드 된 아이콘 삭제
						@unlink($dest_path_and);
					}
				}
				//=================================================================\
			}
						
			$chk_code_and_Img = $fileName_and;	
		}
	}
	
	
	if ($chk_code_and_Img != "") {
	    $chk_code_and_Img = $chk_code_and_Img;
	} else {
	    $chk_code_and_Img = $code_and_ImgFile;
	}

	//새로운 팝업 이미지경로 출력
	$member_and_img = $file_dir_and.'/'.$fileName_and;


	//파일저장방법 변경 _blob -------------------------------------------------------- 2019.02.19
	
	if(file_exists($member_and_img) && $fileName_and != "")
	{
		$now_time = time()."_and";	

		//첨부파일 -> 썸네일 이미지로 변경 및 저장된 경로
		$filename_and = $member_and_img;
		$handle_and = fopen($filename_and,"rb");
		$size_and =	GetImageSize($filename_and);
		$width = $size_and[0];
		$height = $size_and[1];
		$imageblob_and = addslashes(fread($handle_and, filesize($filename_and)));
		$filesize = filesize($filename_and);
		$mine = $size_and[mime];
		fclose($handle_and);		

		
		$insQuery = "
			update TB_CONFIG_CODE 
			set 
				code_and_Img ='".$now_time."' 
			where 
				idx =	".$idx." 
		";		
		$DB_con->exec($insQuery);


		// 파일로 blob형태 이미지 저장----------S
		// 새로 생성되는 파일명(전체경로 포함) : $m_file
		$img_txt_and = $now_time;
		$m_file_and = $file_dir_and.'/'.$img_txt_and;
		$is_file_exist_and = file_exists($m_file_and);

		if ($is_file_exist_and) {
			//echo 'Found it';
		} else {
			//echo 'Not found.';
			$file_and = fopen($m_file_and , "w");
			fwrite($file_and, $imageblob_and);
			fclose($file_and);
			chmod($m_file_and, 0755);
		}

		//기존 파일 삭제
		@unlink($org_chk_code_and_Img);
		//신규 업로드 팝업 이미지 삭제
		@unlink($member_and_img);
		// 파일로 blob형태 이미지 저장----------E

	}	
// 아이폰이미지 이미지------------------------------------------------------------------
// 아이폰이미지 이미지------------------------------------------------------------------
// 아이폰이미지 이미지------------------------------------------------------------------
// 아이폰이미지 이미지------------------------------------------------------------------
	$org_chk_code_ios_Img = $file_dir_ios.'/'.$chk_code_ios_Img;

	// 파일삭제
	if ($del_code_ios_ImgFile) {
	    $file_ios_img = $file_dir_ios.'/'.$chk_code_ios_Img;
		@unlink($file_ios_img);
		del_thumbnail(dirname($file_ios_img), basename($file_ios_img));
		$code_ios_ImgFile = '';
	} else {
	    $code_ios_ImgFile = "$code_ios_ImgFile";
	}


	// 이미지 업로드 
	$image_regex = "/(\.(gif|jpe?g|png))$/i";
/*
	$cf_img_width = "100";
	$cf_img_height = "100";
*/
	if (isset($_FILES['code_Ios_ImgFile']) && is_uploaded_file($_FILES['code_Ios_ImgFile']['tmp_name'])) {  //이미지 업로드 성공일 경우


		if (preg_match($image_regex, $_FILES['code_Ios_ImgFile']['name'])) {

			@mkdir($file_dir_ios, 0755);
			//@chmod($file_dir_ios, 0644);

			$filename_ios = $_FILES['code_Ios_ImgFile']['name'];

			//php파일도 getimagesize 에서 Image Type Flag 를 속일수 있다
			if (!preg_match('/\.(gif|jpe?g|png)$/i', $filename_ios)) {
				return '';
			}

			$pattern = "/[#\&\+\-%@=\/\\:;,'\"\^`~\|\!\?\*\$#<>\(\)\[\]\{\}]/";
			$filename_ios = preg_replace("/\s+/", "", $filename_ios);
			$filename_ios = preg_replace( $pattern, "", $filename_ios);

			$filename_ios = preg_replace_callback(
								  "/[가-힣]+/",
								  create_function('$matches', 'return base64_encode($matches[0]);'),
								  $filename_ios);

			$filename_ios = preg_replace( $pattern, "", $filename_ios);

			// 동일한 이름의 파일이 있으면 파일명 변경
			if(is_file($dir.'/'.$filename_ios)) {
				for($i=0; $i<20; $i++) {
					$prepend_ios = str_replace('.', '_', microtime(true)).'_';

					if(is_file($dir.'/'.$prepend_ios.$filename_ios)) {
						usleep(mt_rand(100, 10000));
						continue;
					} else {
						break;
					}
				}
			}

			$fileName_ios = $prepend_ios.$filename_ios;
			$dest_path_ios = $file_dir_ios.'/'.$fileName_ios;

			move_uploaded_file($_FILES['code_Ios_ImgFile']['tmp_name'], $dest_path_ios);
		
			if (file_exists($dest_path_ios)) {
				$size = @getimagesize($dest_path_ios);

				if (!($size[2] === 1 || $size[2] === 2 || $size[2] === 3)) { // gif jpg png 파일이 아니면 올라간 이미지를 삭제한다.
					@unlink($dest_path_ios);
				} else if ($size[0] > $cf_img_width || $size[1] > $cf_img_height) {
					$thumb_ios = null;
					if($size[2] === 2 || $size[2] === 3) {
						//jpg 또는 png 파일 적용
						$thumb_ios = thumbnail($fileName_ios, $file_dir_ios, $file_dir_ios, $cf_img_width, $cf_img_height, true, true);

						if($thumb_ios) {
							@unlink($dest_path_ios);
							rename($file_dir_ios.'/'.$thumb_ios, $dest_path_ios);
						}
					}
					if( !$thumb_ios ){
						// 아이콘의 폭 또는 높이가 설정값 보다 크다면 이미 업로드 된 아이콘 삭제
						@unlink($dest_path_ios);
					}
				}
				//=================================================================\
			}
						
			$chk_code_ios_Img = $fileName_ios;	
		}
	}
	
	
	if ($chk_code_ios_Img != "") {
	    $chk_code_ios_Img = $chk_code_ios_Img;
	} else {
	    $chk_code_ios_Img = $code_ios_ImgFile;
	}

	//새로운 팝업 이미지경로 출력
	$member_ios_img = $file_dir_ios.'/'.$fileName_ios;


	//파일저장방법 변경 _blob -------------------------------------------------------- 2019.02.19
	
	if(file_exists($member_ios_img) && $fileName_ios != "")
	{
		$now_time = time()."_ios";	

		//첨부파일 -> 썸네일 이미지로 변경 및 저장된 경로
		$filename_ios = $member_ios_img;
		$handle_ios = fopen($filename_ios,"rb");
		$size_ios =	GetImageSize($filename_ios);
		$width = $size_ios[0];
		$height = $size_ios[1];
		$imageblob_ios = addslashes(fread($handle_ios, filesize($filename_ios)));
		$filesize = filesize($filename_ios);
		$mine = $size_ios[mime];
		fclose($handle_ios);		

		
		$insQuery = "
			update TB_CONFIG_CODE 
			set 
				code_ios_Img ='".$now_time."' 
			where 
				idx =	".$idx." 
		";		
		$DB_con->exec($insQuery);


		// 파일로 blob형태 이미지 저장----------S
		// 새로 생성되는 파일명(전체경로 포함) : $m_file
		$img_txt_ios = $now_time;
		$m_file_ios = $file_dir_ios.'/'.$img_txt_ios;
		$is_file_exist_ios = file_exists($m_file_ios);

		if ($is_file_exist_ios) {
			//echo 'Found it';
		} else {
			//echo 'Not found.';
			$file_ios = fopen($m_file_ios , "w");
			fwrite($file_ios, $imageblob_ios);
			fclose($file_ios);
			chmod($m_file_ios, 0755);
		}

		//기존 파일 삭제
		@unlink($org_chk_code_ios_Img);
		//신규 업로드 팝업 이미지 삭제
		@unlink($member_ios_img);
		// 파일로 blob형태 이미지 저장----------E

	}	
			$upQquery = "UPDATE TB_CONFIG_CODE SET code_Div = :code_Div, code_Name = :code_Name, use_Bit = :use_Bit, code_Color = :code_Color WHERE idx = :idx LIMIT 1";
			$upStmt = $DB_con->prepare($upQquery);
			$upStmt->bindparam(":code_Div",$code_Div);
			$upStmt->bindparam(":code_Name",$code_Name);
			$upStmt->bindParam(":code_Color", $code_Color);	
			$upStmt->bindParam(":use_Bit", $use_Bit);
			$upStmt->bindParam(":idx", $idx);
			$upStmt->execute();

			$preUrl = "code_list.php?page=$page&$qstr";
			$message = "mod";
			proc_msg($message, $preUrl);

   	} else {  //삭제일경우

			$Qquery = "SELECT code_On_Img, code_Off_Img, code_And_Img, code_Ios_Img FROM TB_CONFIG_CODE WHERE idx = :idx LIMIT 1;";
			$Stmt = $DB_con->prepare($Qquery);
			$Stmt->bindParam(":idx", $idx);
			$Stmt->execute();
			$Row = $Stmt->fetch(PDO::FETCH_ASSOC);
			$code_On_Img = $Row['code_On_Img'];
			if($code_On_Img != ""){
				$file_img = $file_dir.'/'.$code_On_Img;
				@unlink($file_img);
				del_thumbnail(dirname($file_img), basename($file_img));
			}
			$code_Off_Img = $Row['code_Off_Img'];
			if($code_Off_Img != ""){
				$file_img = $file_dir.'/'.$code_Off_Img;
				@unlink($file_img);
				del_thumbnail(dirname($file_img), basename($file_img));
			}
			$code_And_Img = $Row['code_And_Img'];
			if($code_And_Img != ""){
				$file_img = $file_dir.'/'.$code_And_Img;
				@unlink($file_img);
				del_thumbnail(dirname($file_img), basename($file_img));
			}
			$code_Ios_Img = $Row['code_Ios_Img'];
			if($code_Ios_Img != ""){
				$file_img = $file_dir.'/'.$code_Ios_Img;
				@unlink($file_img);
				del_thumbnail(dirname($file_img), basename($file_img));
			}

			$delQquery = "DELETE FROM TB_CONFIG_CODE WHERE idx = :idx LIMIT 1;";

			$delStmt = $DB_con->prepare($delQquery);
			$delStmt->bindParam(":idx", $idx);
			$delStmt->execute();

			$preUrl = "code_list.php?page=$page&$qstr";
			$message = "del";
			proc_msg($message, $preUrl);
	}
	
	dbClose($DB_con);
	$code_stmt = null;
	$stmt = null;
	$upStmt = null;	
	$delStmt = null;
