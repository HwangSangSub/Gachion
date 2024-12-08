<?

	include "../../lib/common.php"; 
	include "../../lib/alertLib.php";
	include "../../lib/thumbnail.lib.php";   //썸네일

	$con_Distance = trim($conDistance);
	$con_MaxDc = trim($conMaxDc);
	$con_mTxt = trim($con_mTxt);
	$con_PopupChk = trim($con_PopupChk);
	$con_PopupUrl = trim($con_PopupUrl);
    $con_Sec = trim($conSec);
	$con_GpsTime = trim($con_GpsTime);			//GPS신호 재 탐색 시간(분단위)
	$con_GpsPTime = trim($con_GpsPTime);		//GPS신호 재 탐색시간(분단위)
	$con_GpsYTime = trim($con_GpsYTime);		//GPS동일위치 경고 이후 상대방알림시간(분단위)
	$con_GpsRegTime = trim($conGpsRegTime);		//GPS 위치기록시간 (분단위)
	$con_BtnTime = trim($con_BtnTime);			//바로양도 제한시간(%단위)
	$con_SharingD = trim($conSharingD);
	$con_SharingS = trim($conSharingS); 
	$con_ETime = trim($conETime); 
	$con_Comp = trim($conComp);
	$conComp1_Max_D = trim($conComp1_Max_D);	//조건 1 : 최대거리
	$conComp1_H = trim($conComp1_H);			//조건 1 : 제한시간
	$conComp2_Min_D = trim($conComp2_Min_D);	//조건 2 : 최소거리
	$conComp2_Max_D = trim($conComp2_Max_D);	//조건 2 : 최대거리
	$conComp2_H = trim($conComp2_H);			//조건 2 : 제한시간
	$conComp3_Min_D = trim($conComp3_Min_D);	//조건 3 : 최소거리
	$conComp3_Max_D = trim($conComp3_Max_D);	//조건 3 : 최대거리
	$conComp3_H = trim($conComp3_H);			//조건 3 : 제한시간
	$conComp4_Min_D = trim($conComp4_Min_D);	//조건 4 : 최소거리
	$conComp4_H = trim($conComp4_H);			//조건 4 : 제한시간
	$conRecom_RC = trim($conRecom_RC);			//추천 받을 시 캐시적립
	$conRecom_RP = trim($conRecom_RP);			//추천 받을 시 회원등급점수
	$conRecom_BRC = trim($conRecom_BRC);		//추천 할 시 캐시적립
	$conRecom_BRP = trim($conRecom_BRP);		//추천 받을 시 회원등급점수
	$DB_con = db1();
	
	if ($mode ==''){
		$mode = "mode";
	}
	if ($mode == "reg") {

		$insQuery = "INSERT INTO TB_CONFIG (con_SharingD, con_SharingS, con_ETime, con_Distance, con_MaxDc, con_Sec, con_GpsTime, con_GpsPTime, con_GpsYTime, con_GpsRegTime, con_BtnTime, con_mTxt, con_PopupChk, con_PopupUrl, conComp1_Max_D, conComp1_H, conComp2_Min_D, conComp2_Max_D, conComp2_H, conComp3_Min_D, conComp3_Max_D, conComp3_H, conComp4_Min_D, conComp4_H, conRecom_RC, conRecom_RP, conRecom_BRC, conRecom_BRP ) VALUES (:con_SharingD, :con_SharingS, :con_ETime, :con_Distance, :con_MaxDc, :con_Sec, :con_GpsTime, :con_GpsPTime, :con_GpsYTime, :con_GpsRegTime, :con_BtnTime, :con_mTxt, :con_PopupChk, :con_PopupUrl, :conComp1_Max_D, :conComp1_H, :conComp2_Min_D, :conComp2_Max_D, :conComp2_H, :conComp3_Min_D, :conComp3_Max_D, :conComp3_H, :conComp4_Min_D, :conComp4_H, :conRecom_RC, :conRecom_RP, :conRecom_BRC, :conRecom_BRP )";
		$stmt = $DB_con->prepare($insQuery);
		$stmt->bindParam(":con_SharingD", $con_SharingD);
		$stmt->bindParam(":con_SharingS", $con_SharingS);
		$stmt->bindParam(":con_ETime", $con_ETime);
		$stmt->bindParam(":con_Distance", $con_Distance);
		$stmt->bindParam(":con_MaxDc", $con_MaxDc);
		$stmt->bindParam(":con_Sec", $con_Sec);
		$stmt->bindParam(":con_GpsTime", $con_GpsTime);
		$stmt->bindParam(":con_GpsPTime", $con_GpsPTime);
		$stmt->bindParam(":con_GpsYTime", $con_GpsYTime);
		$stmt->bindParam(":con_GpsRegTime", $con_GpsRegTime);
		$stmt->bindParam(":con_BtnTime", $con_BtnTime);
		$stmt->bindParam(":con_mTxt", $con_mTxt);
		$stmt->bindParam(":con_PopupChk", $con_PopupChk);
		$stmt->bindParam(":con_PopupUrl", $con_PopupUrl);
		$stmt->bindParam(":conComp1_Max_D", $conComp1_Max_D);
		$stmt->bindParam(":conComp1_H", $conComp1_H);
		$stmt->bindParam(":conComp2_Min_D", $conComp2_Min_D);
		$stmt->bindParam(":conComp2_Max_D", $conComp2_Max_D);
		$stmt->bindParam(":conComp2_H", $conComp2_H);
		$stmt->bindParam(":conComp3_Min_D", $conComp3_Min_D);
		$stmt->bindParam(":conComp3_Max_D", $conComp3_Max_D);
		$stmt->bindParam(":conComp3_H", $conComp3_H);
		$stmt->bindParam(":conComp4_Min_D", $conComp4_Min_D);
		$stmt->bindParam(":conComp4_H", $conComp4_H);
		$stmt->bindParam(":conRecom_RC", $conRecom_RC);
		$stmt->bindParam(":conRecom_RP", $conRecom_RP);
		$stmt->bindParam(":conRecom_BRC", $conRecom_BRC);
		$stmt->bindParam(":conRecom_BRP", $conRecom_BRP);
		$stmt->execute();
		$DB_con->lastInsertId();

		$preUrl = "configReg.php";
		$message = "reg";
		proc_msg($message, $preUrl);


	} else if ($mode == "mod") { //수정일경우

		$query = "";
		$query = "SELECT con_PopupUrl FROM TB_CONFIG" ;
		$stmt1 = $DB_con->prepare($query);
		//$idx = trim($idx);
		$stmt1->execute();
        $row1 = $stmt1->fetch(PDO::FETCH_ASSOC);
        $pop_ImgFile = trim($row1['con_PopupUrl']);
	
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
		
		
		if ($pop_ImgFile != "") {
			$pop_ImgFile = $pop_ImgFile;
		} else {
			$pop_ImgFile = $pop_Img;
		}
		

		//새로운 팝업 이미지경로 출력
		$member_img = $file_dir.'/'.$pop_ImgFile;

		//파일저장방법 변경 _blob -------------------------------------------------------- 2019.02.19
		
		if(file_exists($member_img) && $fileName != "")
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
		
		//파일저장방법 변경 _blob --------------------------------------------------------

		$ban_Type = "0";   //외부 url주소

		//$upQquery = "UPDATE TB_CONFIG SET  con_SharingD = :con_SharingD, con_SharingS = :con_SharingS, con_ETime = :con_ETime, con_Distance = :con_Distance, con_MaxDc = :con_MaxDc, con_Sec = :con_Sec, con_GpsTime = :con_GpsTime, con_GpsPTime = :con_GpsPTime, con_GpsYTime = :con_GpsYTime, con_GpsRegTime = :con_GpsRegTime, con_BtnTime = :con_BtnTime, con_mTxt = :con_mTxt, con_PopupChk = :con_PopupChk, con_PopupUrl = :con_PopupUrl, conComp1_Max_D = :conComp1_Max_D, conComp1_H = :conComp1_H, conComp2_Min_D = :conComp2_Min_D, conComp2_Max_D = :conComp2_Max_D, conComp2_H = :conComp2_H, conComp3_Min_D = :conComp3_Min_D, conComp3_Max_D = :conComp3_Max_D, conComp3_H = :conComp3_H, conComp4_Min_D = :conComp4_Min_D, conComp4_H = :conComp4_H, conRecom_RC = :conRecom_RC, conRecom_RP = :conRecom_RP, conRecom_BRC = :conRecom_BRC, conRecom_BRP = :conRecom_BRP WHERE idx = :idx  LIMIT 1";

		$upQquery = "
			UPDATE TB_CONFIG 
			SET 
				con_SharingD = :con_SharingD, 
				con_SharingS = :con_SharingS, 
				con_ETime = :con_ETime, 
				con_Distance = :con_Distance, 
				con_MaxDc = :con_MaxDc, 
				con_Sec = :con_Sec, 
				con_GpsTime = :con_GpsTime, 
				con_GpsPTime = :con_GpsPTime, 
				con_GpsYTime = :con_GpsYTime, 
				con_GpsRegTime = :con_GpsRegTime, 
				con_BtnTime = :con_BtnTime, 
				con_mTxt = :con_mTxt, 
				con_PopupChk = :con_PopupChk, 
				conComp1_Max_D = :conComp1_Max_D, 
				conComp1_H = :conComp1_H, 
				conComp2_Min_D = :conComp2_Min_D, 
				conComp2_Max_D = :conComp2_Max_D, 
				conComp2_H = :conComp2_H, 
				conComp3_Min_D = :conComp3_Min_D, 
				conComp3_Max_D = :conComp3_Max_D, 
				conComp3_H = :conComp3_H, 
				conComp4_Min_D = :conComp4_Min_D, 
				conComp4_H = :conComp4_H, 
				conRecom_RC = :conRecom_RC, 
				conRecom_RP = :conRecom_RP, 
				conRecom_BRC = :conRecom_BRC, 
				conRecom_BRP = :conRecom_BRP 
			WHERE 
				idx = :idx  
			LIMIT 1";

		$upStmt = $DB_con->prepare($upQquery);
		$upStmt->bindParam(":con_SharingD", $con_SharingD);
		$upStmt->bindParam(":con_SharingS", $con_SharingS);
		$upStmt->bindParam(":con_ETime", $con_ETime);
		$upStmt->bindParam(":con_Distance", $con_Distance);
		$upStmt->bindParam(":con_MaxDc", $con_MaxDc);
		$upStmt->bindParam(":con_Sec", $con_Sec);
		$upStmt->bindParam(":con_GpsTime", $con_GpsTime);
		$upStmt->bindParam(":con_GpsPTime", $con_GpsPTime);
		$upStmt->bindParam(":con_GpsYTime", $con_GpsYTime);
		$upStmt->bindParam(":con_GpsRegTime", $con_GpsRegTime);
		$upStmt->bindParam(":con_BtnTime", $con_BtnTime);
		$upStmt->bindParam(":con_mTxt", $con_mTxt);
		$upStmt->bindParam(":con_PopupChk", $con_PopupChk);
//		$upStmt->bindParam(":con_PopupUrl", $pop_ImgFile);
		$upStmt->bindParam(":idx", $idx);
		$upStmt->bindParam(":conComp1_Max_D", $conComp1_Max_D);
		$upStmt->bindParam(":conComp1_H", $conComp1_H);
		$upStmt->bindParam(":conComp2_Min_D", $conComp2_Min_D);
		$upStmt->bindParam(":conComp2_Max_D", $conComp2_Max_D);
		$upStmt->bindParam(":conComp2_H", $conComp2_H);
		$upStmt->bindParam(":conComp3_Min_D", $conComp3_Min_D);
		$upStmt->bindParam(":conComp3_Max_D", $conComp3_Max_D);
		$upStmt->bindParam(":conComp3_H", $conComp3_H);
		$upStmt->bindParam(":conComp4_Min_D", $conComp4_Min_D);
		$upStmt->bindParam(":conComp4_H", $conComp4_H);
		$upStmt->bindParam(":conRecom_RC", $conRecom_RC);
		$upStmt->bindParam(":conRecom_RP", $conRecom_RP);
		$upStmt->bindParam(":conRecom_BRC", $conRecom_BRC);
		$upStmt->bindParam(":conRecom_BRP", $conRecom_BRP);
		$upStmt->execute();

		$preUrl = "configReg.php";
		$message = "mod";
		proc_msg($message, $preUrl);

	}


	dbClose($DB_con);
	$stmt = null;
	$stmt1 = null;
	$upStmt = null;


	?>