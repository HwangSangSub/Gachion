<?
	include "../lib/common.php"; 
	include "../../lib/alertLib.php";
	include "../../lib/thumbnail.lib.php";   //썸네일

	$content_MaxCnt = trim($content_MaxCnt);
	$place_MaxCnt = trim($place_MaxCnt);
	$list_PlaceCnt = trim($list_PlaceCnt);
	$total_LikeCnt = trim($total_LikeCnt);
	$DB_con = db1();
	
	if ($mode ==''){
		$mode = "mode";
	}
	if ($mode == "reg") {

		$insQuery = "INSERT INTO TB_CONFIG SET content_MaxCnt = :content_MaxCnt, place_MaxCnt = :place_MaxCnt, list_PlaceCnt = :list_PlaceCnt, total_LikeCnt = :total_LikeCnt";
		$stmt = $DB_con->prepare($insQuery);
		$stmt->bindParam(":content_MaxCnt", $content_MaxCnt);
		$stmt->bindParam(":place_MaxCnt", $place_MaxCnt);
		$stmt->bindParam(":list_PlaceCnt", $list_PlaceCnt);
		$stmt->bindParam(":total_LikeCnt", $total_LikeCnt);
		$stmt->execute();
		$DB_con->lastInsertId();

		$preUrl = "configReg.php";
		$message = "reg";
		proc_msg($message, $preUrl);


	} else if ($mode == "mod") { //수정일경우
/*
		$query = "";
		$query = "SELECT con_PopupUrl FROM TB_CONFIG" ;
		$stmt1 = $DB_con->prepare($query);
		//$idx = trim($idx);
		$stmt1->execute();
        $row1 = $stmt1->fetch(PDO::FETCH_ASSOC);
        $pop_ImgFile = trim($row1['con_PopupUrl']);
		// 배너 이미지 경로
		// 배너 이미지 경로
		$file_dir = DU_DATA_PATH.'/popup';

		//기존배너파일 full경로
		$org_pop_ImgFile = $file_dir.'/'.$pop_ImgFile;

		// 파일삭제
		if ($del_pop_Img) {
			$file_img1 = $file_dir.'/'.$pop_ImgFile;
			@unlink($file_img1);
			del_thumbnail(dirname($file_img1), basename($file_img1));
			$pop_Img = '';
		} else {
			$pop_Img = "$pop_Img";
		}


		// 이미지 업로드 
		$image_regex = "/(\.(gif|jpe?g|png))$/i";

		$cf_img_width = "600";
		$cf_img_height = "720";

		if (isset($_FILES['pop_Img']) && is_uploaded_file($_FILES['pop_Img']['tmp_name'])) {  //이미지 업로드 성공일 경우


			if (preg_match($image_regex, $_FILES['pop_Img']['name'])) {

				@mkdir($file_dir, 0755);
				//@chmod($file_dir, 0644);

				$filename = $_FILES['pop_Img']['name'];

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

				move_uploaded_file($_FILES['pop_Img']['tmp_name'], $dest_path);
			
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
							
				$pop_ImgFile = $fileName;	
			}
		}
		else
		{
			//이미지 변경없이 수정이 될 경우 대비
			$no_img = "Y";
		}		
		
		
		if ($pop_ImgFile != "") {
			$pop_ImgFile = $pop_ImgFile;
		} else {
			$pop_ImgFile = $pop_Img;
		}

		//새로운 팝업 이미지경로 출력
		$member_img = $file_dir.'/'.$pop_ImgFile;

		//파일저장방법 변경 _blob -------------------------------------------------------- 2019.02.19
		if($no_img != "Y")
		{
			if($member_img)
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
				$now_time = time();	
				$insQuery = "
					update TB_CONFIG 
					set 
						con_PopupUrl ='".$now_time."' 
					where 
						idx ='".$idx."' 
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
				@unlink($org_pop_ImgFile);
				//신규 업로드 팝업 이미지 삭제
				@unlink($member_img);
				// 파일로 blob형태 이미지 저장----------E

			}
		}

	*/
		//$upQquery = "UPDATE TB_CONFIG SET  con_SharingD = :con_SharingD, con_SharingS = :con_SharingS, con_ETime = :con_ETime, con_FTime = :con_FTime, con_Distance = :con_Distance, con_MaxDc = :con_MaxDc, con_Sec = :con_Sec, con_GpsTime = :con_GpsTime, con_GpsPTime = :con_GpsPTime, con_GpsYTime = :con_GpsYTime, con_GpsRegTime = :con_GpsRegTime, con_BtnTime = :con_BtnTime, con_mTxt = :con_mTxt, con_PopupChk = :con_PopupChk, con_PopupUrl = :con_PopupUrl, conComp1_Max_D = :conComp1_Max_D, conComp1_H = :conComp1_H, conComp2_Min_D = :conComp2_Min_D, conComp2_Max_D = :conComp2_Max_D, conComp2_H = :conComp2_H, conComp3_Min_D = :conComp3_Min_D, conComp3_Max_D = :conComp3_Max_D, conComp3_H = :conComp3_H, conComp4_Min_D = :conComp4_Min_D, conComp4_H = :conComp4_H, conRecom_RC = :conRecom_RC, conRecom_RP = :conRecom_RP, conRecom_BRC = :conRecom_BRC, conRecom_BRP = :conRecom_BRP WHERE idx = :idx  LIMIT 1";

		$upQuery = "UPDATE TB_CONFIG SET content_MaxCnt = :content_MaxCnt, place_MaxCnt = :place_MaxCnt, list_PlaceCnt = :list_PlaceCnt, total_LikeCnt = :total_LikeCnt WHERE idx = :idx LIMIT 1";

		$upStmt = $DB_con->prepare($upQuery);
		$upStmt->bindParam(":content_MaxCnt", $content_MaxCnt);
		$upStmt->bindParam(":place_MaxCnt", $place_MaxCnt);
		$upStmt->bindParam(":list_PlaceCnt", $list_PlaceCnt);
		$upStmt->bindParam(":total_LikeCnt", $total_LikeCnt);
		$upStmt->bindParam(":idx", $idx);
		$upStmt->execute();

		$preUrl = "configReg.php";
		$message = "mod";
		proc_msg($message, $preUrl);

	}


	dbClose($DB_con);
	$stmt = null;
	$stmt1 = null;
	$upStmt = null;
