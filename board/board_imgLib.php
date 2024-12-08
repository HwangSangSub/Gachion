<?
	include "../lib/thumbnail.lib.php";   //썸네일


	//게시판 환경설정
	$bquery = "";
	$bquery = "SELECT b_Upload, b_UploadFChk  FROM  TB_BOARD_SET WHERE b_Idx = :b_Idx LIMIT 1";
	$bStmt = $DB_con->prepare($bquery);
	$bStmt->bindparam(":b_Idx",$b_Idx);
	$bStmt->execute();
	$bNum = $bStmt->rowCount();

	if($bNum < 1)  { //아닐경우
	} else {
		while($bsRow=$bStmt->fetch(PDO::FETCH_ASSOC)) {
			$b_Upload = trim($bsRow['b_Upload']);			
			$b_UploadFChk = trim($bsRow['b_UploadFChk']);			
		}
	}
	
	/* 파일 첨부관련 */
	$dirRoot = DU_DATA_PATH;
	$uploadFolder =  trim($b_Upload);  //게시판 업로드폴더
	$upload_dir   =  $dirRoot."/".$uploadFolder."/";
	$maxsize = 10;   //용량 10M제한
	$maxfilesize = $maxsize * 1024 * 1024;	// 바이트로 계산한다. 1MB = 1024KB = 1048576Byte
	$uploadFile	= explode(",", $b_UploadFChk);

	//이미지 업로드 시작.
	if (isset($_FILES["files"])) {
		
		if(is_array($_FILES['files']['name'])){
	
			// 파일 사이즈 구하기
			$filesize = 0;
			foreach($_FILES['files']['size'] as $key => $val){
				if($_FILES['files']['size'][$key] > 0){
					$filesize += $_FILES['files']['size'][$key];
				}
			}
	
			//  파일 용량 비교
			if((int)$filesize > (int)$maxfilesize){
				$message = "업로드  파일용량(10M) 이상은 업로드 하실수 없습니다.";
				proc_msg3($message);
			} else {
				foreach($_FILES['files']['name'] as $key => $val){
					if($_FILES['files']['size'][$key] > 0){

						if($_FILES['files']['error'][$key] === UPLOAD_ERR_OK) {

							$allow_ext = $uploadFile;
							$fname = explode(".", $_FILES['files']['name'][$key]);
							$fileExt = strtolower($fname[count($fname)-1]);   //확장자 구하는것
								
							if(@preg_match($fileExt, "php|php3|php4|htm|inc|htmlcgi|pl")){
								$message = "허용 할수 없는 파일(확장자)입니다.";
								proc_msg3($message);
							}
								
							$chkArray =  in_array(strval($fileExt), $uploadFile) ? "true" : "false";  // 확장자 비교
							if($chkArray == false) {
								$message = "허용 할수 없는 파일(확장자)입니다.";
								proc_msg3($message);
							}

							// remove_all($dirRoot."/data/");   //폴더명이 다를 경우 전체 삭제 (나중에 폴더 전체 삭제할경우)
							$chkUpload = $dirRoot;   //data폴더 권한 변경때문에
							$chkUpload = "'".$chkUpload."'";
							$chkUpload2 =  "'".$upload_dir."'";  //생성하는 게시판 폴더 권한 때문에

							if(!is_dir(trim($chkUpload))){   //업로드 상위 폴더권한 먼저 생성시킴.
								@chmod($dirRoot, 0755, true);//게시판별로 권한 생성
							}
								
							if(!is_dir($chkUpload2)){
								@mkdir($dirRoot."/".$uploadFolder, 0755, true);  //게시판별 폴더 생성.
								@chmod($dirRoot."/".$uploadFolder, 0755, true);  //게시판별로 폴더 권한 생성.
							}

								if(is_uploaded_file($_FILES['files']['tmp_name'][$key])){
								//$filename = md5("habony_".$_FILES['files']['name'][$key]);
								$filename = $_FILES['files']['name'][$key];
								$nfilename = md5(date("YmdHis"))."-1.".$fileExt;  //새로운파일명
								$tmpfilename = $_FILES['files']['tmp_name'][$key];

								$fileSize =	 $_FILES['files']['size'][$key];  //파일 사이즈

								//echo "@@@2=".$dirRoot."/".$uploadFolder."/".$filename.".".$fileExt;
								//exit;
								$FileName = GetUniqFileName($nfilename, $upload_dir); // 같은 화일 이름이 있는지 검사
								$chkFileNm = $dirRoot."/".$uploadFolder."/".$FileName;      //저장 위치 파일

								//echo "@@@=".$chkFileNm."<BR>";
								//exit;

								$img2 = "/data/".$uploadFolder."/".$FileName;      //저장 위치 파일

								//echo $img2;
								//exit;

								//move_uploaded_file($tmpfilename,$chkFileNm); // 화일을 업로드 위치에 저장

								//수정일 경우 첨부파일 유무 확인

								if ($mode == "mod") { 

									 $chkCnt = $key + 1;  //파일 첨부 번호 의미
									 $chkFQuery = "";
									// $chkFQuery = " SELECT idx, b_Idx, b_NIdx, b_FIdx, b_FName, b_FSize FROM TB_BOARD_FILE  WHERE b_Idx = :b_Idx AND b_NIdx = :b_NIdx AND b_FIdx = :b_FIdx ";
									 $chkFQuery = " SELECT idx, b_Idx, b_NIdx, b_FIdx, b_FName, b_FSize FROM TB_BOARD_FILE  WHERE b_Idx = $b_Idx AND b_NIdx = $idx AND b_FIdx = $chkCnt ";
									 $chkFQuery .= " ORDER BY b_FIdx DESC";
									 //echo $chkFQuery;
									 //exit;
									 $chkFStmt = $DB_con->prepare($chkFQuery);
									 $chkFStmt->bindparam(":b_Idx",$b_Idx);
									 $chkFStmt->bindparam(":b_NIdx",$idx);
									 $chkFStmt->bindparam(":b_FIdx",$chkCnt);
									 $chkFStmt->execute();
									 $chkFNum = $chkFStmt->rowCount();

									 //!$chkFcounts == 0
									 if ($chkFNum < 1)  {
									 } else {  //첨부파일이 있을 경우에만 출력

										while($chkFRow=$chkFStmt->fetch(PDO::FETCH_ASSOC)) {

											$upfile = addslashes($_FILES["files"]["name"][$key]);  //수정시 새로첨부된 파일 추가
											$dFName = trim($chkFRow['b_FName']);		
											$dFIdx = trim($chkFRow['b_FIdx']);		

											if  ( $upfile != $dFName ) { //파일 비교후 같지 않을 경우 삭제함.
												$dfileName   =  $dirRoot."/".$uploadFolder."/".$dFName;

												//파일 삭제
												if(file_exists($dfileName)){
												   unlink($dfileName);
												 }
											}

										}

									 }

								}

								$result = move_uploaded_file($tmpfilename,$chkFileNm); // 화일을 업로드 위치에 저장
								//$result = "1";

								if ($result == "1") {  //저장성공
									//chmod($chkFileNm, 0705);
									
									$img = $chkFileNm;
									$img2 = $img2;

									//echo $img."<BR>";
									//echo $img2."<BR>";
									//exit;
									if (is_file($img)) {


										/*
										$img_info = getimagesize($img);
										//print_r($img_info)."<BR>";
										//exit;
										$width = $img_info[0]; //입력받은 파일의 가로크기
										$height = $img_info[1]; //입력받은 파일의 세로크기
										
										if($width > 700)  {    //  이미지의 가로길이가 900보다 크다면 작게만든다
											$chkimg = thumbnail($img, 700, '', $img2);
										} else {
											$chkimg = $FileName;
										}

										*/
                $cf_img_width = "700";
                $cf_img_height = "700";

				$size = @getimagesize($img);

				if (!($size[2] === 1 || $size[2] === 2 || $size[2] === 3)) { // gif jpg png 파일이 아니면 올라간 이미지를 삭제한다.
					@unlink($img);
				} else if ($size[0] > $cf_img_width || $size[1] > $cf_img_height) {
					$thumb = null;
					if($size[2] === 2 || $size[2] === 3) {
						//jpg 또는 png 파일 적용
						$thumb = thumbnail($FileName, $upload_dir, $upload_dir, $cf_img_width, $cf_img_height, true, true);

						if($thumb) {
							@unlink($img);
							rename($upload_dir.'/'.$thumb, $img);
						}
					}
					if( !$thumb ){
						// 아이콘의 폭 또는 높이가 설정값 보다 크다면 이미 업로드 된 아이콘 삭제
						@unlink($img);
					}
				}


			$chkimg = $FileName;	




										
									}

									
									if ($mode == "reg") {

										// 파일첨부 고유번호값 가져오기
										$bNFileQuery = "";
										$bNFileQuery = "SELECT MAX(b_FIdx) AS b_FIdx FROM TB_BOARD_FILE WHERE b_Idx = :b_Idx AND b_NIdx = :b_NIdx LIMIT 1";
										$bNFileSmt = $DB_con->prepare($bNFileQuery);
										$bNFileSmt->bindparam(":b_Idx",$b_Idx);
										$bNFileSmt->bindparam(":b_NIdx",$b_NIdx);
										$bNFileSmt->execute();
										$bNFileRow = $bNFileSmt->fetch(PDO::FETCH_ASSOC);
										$b_NFIdx = $bNFileRow['b_FIdx'] + 1;

										//파일첨부 저장
										$finsQuery = "INSERT INTO TB_BOARD_FILE (b_Idx, b_NIdx, b_FIdx, b_FName, b_OFName, b_FSize) VALUES (:b_Idx, :b_NIdx, :b_FIdx, :b_FName, :b_OFName, :b_FSize)";
										//$finsQuery = "INSERT INTO TB_BOARD_FILE (b_Idx, b_NIdx, b_FIdx, b_FName, b_OFName, b_FSize) VALUES ($b_Idx, $b_NIdx, $b_NFIdx, $chkimg, $filename, $fileSize)";
										//echo $finsQuery."<BR>";
										//exit;
										$fstmt = $DB_con->prepare($finsQuery);
										$fstmt->bindParam("b_Idx", $b_Idx, PDO::PARAM_INT);
										$fstmt->bindParam("b_NIdx", $b_NIdx, PDO::PARAM_INT);
										$fstmt->bindParam("b_FIdx", $b_NFIdx, PDO::PARAM_INT);
										$fstmt->bindParam("b_FName", $chkimg, PDO::PARAM_STR);
										$fstmt->bindParam("b_OFName", $filename, PDO::PARAM_STR);
										$fstmt->bindParam("b_FSize", $fileSize, PDO::PARAM_INT);
										$fstmt->execute();
										$DB_con->lastInsertId();

									} elseif ($mode == "mod") { 

										echo "FF".$chkFNum."<BR>";

										if ($chkFNum < 1)  { 


											// 파일첨부 고유번호값 가져오기
											$bMFileQuery = "";
											$bMFileQuery = "SELECT MAX(b_FIdx) AS b_FIdx FROM TB_BOARD_FILE WHERE b_Idx = :b_Idx AND b_NIdx = :b_NIdx LIMIT 1";
											$bMFileSmt = $DB_con->prepare($bMFileQuery);
											$bMFileSmt->bindparam(":b_Idx",$b_Idx);
											$bMFileSmt->bindparam(":b_NIdx",$idx);
											$bMFileSmt->execute();
											$bMFileRow = $bMFileSmt->fetch(PDO::FETCH_ASSOC);
											$bMFileNum = $bMFileRow['b_FIdx'];
											$b_MFIdx = $bMFileRow['b_FIdx'] + 1;

											//파일첨부 저장
											$fMinsQuery = "INSERT INTO TB_BOARD_FILE (b_Idx, b_NIdx, b_FIdx, b_FName, b_OFName, b_FSize) VALUES (:b_Idx, :b_NIdx, :b_FIdx, :b_FName, :b_OFName, :b_FSize)";
											//$fMinsQuery = "INSERT INTO TB_BOARD_FILE (b_Idx, b_NIdx, b_FIdx, b_FName, b_OFName, b_FSize) VALUES ($b_Idx, $idx, $b_MFIdx, $chkimg, $filename, $fileSize)";

											//echo $fMinsQuery."<BR>";
											//exit;
											$fMstmt = $DB_con->prepare($fMinsQuery);
											$fMstmt->bindParam("b_Idx", $b_Idx);
											$fMstmt->bindParam("b_NIdx", $idx);
											$fMstmt->bindParam("b_FIdx", $b_MFIdx);
											$fMstmt->bindParam("b_FName", $chkimg);
											$fMstmt->bindParam("b_OFName", $filename);
											$fMstmt->bindParam("b_FSize", $fileSize);
											$fMstmt->execute();
											$DB_con->lastInsertId();


										} else { //첨부파일이 있을 경우에만 출력

											while($chkFRow=$fMstmt->fetch(PDO::FETCH_ASSOC)) {

												$cFIdx = trim($chkFRow['b_FIdx']);		

												$upfileQuery = "UPDATE TB_BOARD_FILE SET b_FName = :b_FName, b_OFName = :b_OFName, b_FSize = :b_FSize WHERE b_Idx = :b_Idx AND b_NIdx = b_NIdx AND b_FIdx = b_FIdx LIMIT 1";
												$upStmt = $DB_con->prepare($upfileQuery);
												$upStmt->bindparam(":b_FName",$chkimg);
												$upStmt->bindparam(":b_OFName",$filename);
												$upStmt->bindparam(":b_FSize",$b_Name);
												$upStmt->bindparam(":b_Idx",$b_Idx);
												$upStmt->bindparam(":b_NIdx",$idx);
												$upStmt->bindparam(":b_FIdx",$cFIdx);
												$upStmt->execute();

											}
										}


											
									}


								}
							}

						}
					}
				}
			}
		}
	}


?>