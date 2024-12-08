<?
/*
* 프로그램				: 지도 등록 및 수정(관리자페이지)
* 페이지 설명			: 지도를 등록하는 기능
* 파일명					: proc_contents.php
* 관련DB					: TB_CONTENTS
*/
include "../lib/common.php";
include "../../lib/functionDB.php";
include "../../lib/alertLib.php"; 
include "../../lib/thumbnail.lib.php";   //썸네일
			
$DB_con = db1();
$con_Name = trim($con_Name);									//지도이름 
$con_Lv = trim($con_Lv);
if($con_Lv == ""){
	$con_Lv = "1";
}
$category = trim($category);										//카테고리
$tag = trim($tag);														//태그 (예 : 이사아폴리스,돼지고기,양많음,적극추천 등 태그를 , 로 구분하여 넣기.
$open_Bit = trim($open_Bit);										//공개여부(0:전체공개, 1:비공개)
$memo = trim($memo);												//메모
if($open_Bit == ""){
	$open_Bit = "0";
}else{
	$open_Bit = $open_Bit;
}	
$reg_Id = trim($reg_Id);												//등록자
$memberIdx = memIdxInfo($reg_Id);							// 회원고유번호
if($mode == "mod"){
	$idx = trim($idx);													// 지도 고유번호
	$org_con_Name = trim($org_con_Name);					// 기존 지도명
	if($con_Name == $org_con_Name){
		$con_Name = $org_con_Name;
	}else{
		$con_Name = $con_Name;
	}
	$org_category = trim($org_category);						// 기존 카테고리
	if($category == $org_category){
		$category = $org_category;
	}else{
		$category = $category;
	}
	$org_reg_Id = trim($org_reg_Id);								// 기존 등록자
	$org_memberIdx = memIdxInfo($org_reg_Id);				// 기존 회원고유번호
	if($reg_Id == $org_reg_Id){
		$reg_Id = $org_reg_Id;
		$memberIdx = $org_memberIdx;
	}else{
		$reg_Id = $reg_Id;
		$memberIdx = $memberIdx;
	}
	$org_con_Lv = trim($org_con_Lv);								// 기존 지도등급
	if($con_Lv == $org_con_Lv){
		$con_Lv = $org_con_Lv;
	}else{
		$con_Lv = $con_Lv;
	}
	$org_end_Date = trim($org_end_Date);						// 기존 마감일
	if($end_Date == $org_end_Date){
		$end_Date = $org_end_Date;
	}else{
		$end_Date = $end_Date;
	}
	$org_kml_File = trim(	$org_kml_File);							// 기존 kml파일
	$del_kml_File = trim($del_kml_File);							// 기존 kml파일 삭제여부

	$up_con_query = "
		UPDATE TB_CONTENTS
		SET member_Idx = :member_Idx,
			con_Name = :con_Name,
			con_Lv = :con_Lv,
			category = :category,
			tag = :tag,
			open_Bit = :open_Bit,
			memo = :memo,
			reg_Id = :reg_Id,
			mod_Date = NOW(),
			end_Date = :end_Date
		WHERE idx = :idx
	";	
	$up_con_stmt = $DB_con->prepare($up_con_query);
	$up_con_stmt->bindParam(":idx", $idx);
	$up_con_stmt->bindParam(":member_Idx", $memberIdx);
	$up_con_stmt->bindParam(":con_Name", $con_Name);
	$up_con_stmt->bindParam(":con_Lv", $con_Lv);
	$up_con_stmt->bindParam(":category", $category);
	$up_con_stmt->bindParam(":tag", $tag);
	$up_con_stmt->bindParam(":open_Bit", $open_Bit);
	$up_con_stmt->bindParam(":memo", $memo);
	$up_con_stmt->bindParam(":reg_Id", $reg_Id);
	$up_con_stmt->bindParam(":end_Date", $end_Date);
	$up_con_stmt->execute();

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
				idx =	".$idx." 
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
	if($del_kml_File){
		$deltarget = "../../contents/kmlfile/".$org_kml_File; 
		@unlink($deltarget);
		$delQuery = "
			UPDATE TB_CONTENTS 
			SET
				kml_File ='',
				area_Code = '',
				locat_Cnt = ''
			WHERE
				idx =	".$idx." 
		";		
		$DB_con->exec($delQuery);
	}
	if(isset($_FILES['kml_File'])){
		$f_name = $_FILES['kml_File']['name'];
		$fname = iconv("UTF-8", "EUC-KR", $f_name);
		$kml_Idx = ((int)$idx).".kml";
		$target = "../../contents/kmlfile/".$kml_Idx; 
		if(move_uploaded_file($_FILES['kml_File']['tmp_name'],$target)){		
			$xml = file_get_contents($target);
			//xml파일 읽어오기
			$result_xml = simplexml_load_string($xml);
			$Placemark = $result_xml->Document->Placemark;
			$pm_cnt = count($Placemark);
			//echo $pm_cnt;
			$name = [];
			for($pm = 0; $pm < $pm_cnt; $pm++){
				$name_chk = $result_xml->Document->Placemark[$pm]->name;
				array_push($name, str_replace('SimpleXMLElement Object ( [0] => ', '',$name_chk));
			}
			$area_Code = implode(',', $name);
			//print_r($name);
			$name_cnt = count($name);
			$upQuery = "
				UPDATE TB_CONTENTS 
				SET
					kml_File ='".$kml_Idx."',
					area_Code = '".$area_Code."',
					locat_Cnt = '".$name_cnt."'
				WHERE
					idx =	".$idx." 
			";		
			$DB_con->exec($upQuery);
		}
	}
	$preUrl = "reg_contents.php?$qstr&idx=".$idx."&mode=".$mode;
	$message = "mod";
	proc_msg($message, $preUrl);
}else{
	//회원 기본테이블 저장
	$query = "INSERT INTO TB_CONTENTS (member_Idx, con_Name, con_Lv, category, img, tag, open_Bit, memo, reg_Id, reg_date, mod_Date, end_Date) VALUES (:member_Idx, :con_Name, :con_Lv, :category, '', :tag, :open_Bit, :memo, :reg_Id, NOW(), NOW(), :end_Date)";
	
	$stmt = $DB_con->prepare($query);
	$stmt->bindParam(":member_Idx", $memberIdx);
	$stmt->bindParam(":con_Name", $con_Name);
	$stmt->bindParam(":con_Lv", $con_Lv);
	$stmt->bindParam(":category", $category);
	$stmt->bindParam(":tag", $tag);
	$stmt->bindParam(":open_Bit", $open_Bit);
	$stmt->bindParam(":memo", $memo);
	$stmt->bindParam(":reg_Id", $reg_Id);
	$stmt->bindParam(":end_Date", $end_Date);
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

	$f_name = $_FILES['kml_File']['name'];
	$fname = iconv("UTF-8", "EUC-KR", $f_name);
	$idx = ((int)$mIdx).".kml";
	$target = "../../contents/kmlfile/".$idx; 
	if(move_uploaded_file($_FILES['kml_File']['tmp_name'],$target)){		
		$xml = file_get_contents($target);
		//xml파일 읽어오기
		$result_xml = simplexml_load_string($xml);
		$Placemark = $result_xml->Document->Placemark;
		$pm_cnt = count($Placemark);
		//echo $pm_cnt;
		$name = [];
		for($pm = 0; $pm < $pm_cnt; $pm++){
			$name_chk = $result_xml->Document->Placemark[$pm]->name;
			array_push($name, str_replace('SimpleXMLElement Object ( [0] => ', '',$name_chk));
		}
		$area_Code = implode(',', $name);
		//print_r($name);
		$name_cnt = count($name);
		$upQuery = "
			UPDATE TB_CONTENTS 
			SET
				kml_File ='".$idx."',
				area_Code = '".$area_Code."',
				locat_Cnt = '".$name_cnt."'
			WHERE
				idx =	".$mIdx." 
		";		
		$DB_con->exec($upQuery);
	}
	$history = "지도생성";
	$his_query ="INSERT INTO TB_HISTORY (member_Idx, mem_Id, con_Idx, history, reg_Id, reg_date) VALUES (:member_Idx, :mem_Id, :con_Idx, :history, :reg_Id, NOW())";
	$his_stmt = $DB_con->prepare($his_query);
	$his_stmt->bindParam(":member_Idx", $memberIdx);
	$his_stmt->bindParam(":mem_Id", $reg_Id);
	$his_stmt->bindParam(":history", $history);
	$his_stmt->bindParam(":con_Idx", $mIdx);
	$his_stmt->bindParam(":reg_Id", $reg_Id);
	$his_stmt->execute();
	dbClose($DB_con);
	$stmt = null;
	$preUrl = "list_contents.php?$qstr";
	$message = "reg";
	proc_msg($message, $preUrl);
}
echo json_encode($result);
?>



