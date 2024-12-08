<?
/*
* 프로그램				: 핀을 등록하는 기능
* 페이지 설명			: 핀을 등록하는 기능
* 파일명					: reg_place.php
* 관련DB					: TB_CONTENTS, TB_PLACE
*/
include "../lib/common.php";
include "../lib/functionDB.php";  //공통 db함수

$con_Idx = trim($con_Idx);						//콘텐츠그룹
$category = trim($category);					//카테고리 
$place_Name = trim($place_Name);			//장소명
$place_Icon = trim($place_Icon);				//장소대표아이콘
$memo = trim($memo);							//상세설명
$smemo = trim($smemo);						//한줄설명
$tel = trim($tel);									//연락처
$otime_Day = trim($otime_Day);				//영업시간(평일)
$otime_Week = trim($otime_Week);			//영업시간(주말)
$address = trim($addr);							//주소
$zaddr = trim($zaddr);							//상세주소
$taddr = $address." ".$zaddr;					//전체 주소
/*
if(isset($_FILES['img'])){
	$img_f_name = $_FILES['img']['name'];
	$img_fname = iconv("UTF-8", "EUC-KR", $img_f_name);
	$img_target = "./place_img/".$img_fname ; 
	move_uploaded_file($_FILES['img']['tmp_name'],$img_target);
	$img = trim($_FILES['img']['name']);				//썸네일이미지
}else{
	$img = "";												//썸네일이미지
}
*/


$now_time = time();										// 추후 파일 디렉토리가 될 예정
/*
// 이미지 경로
$file_dir = $_SERVER["DOCUMENT_ROOT"].'/contents/place_img/'.$now_time;
if(!is_dir($file_dir)){
	mkdir($file_dir, 0777, true);
}
// 이미지 ------------------------------------------------------------------
$org_chk_code_Img = $file_dir.'/'.$chk_code_Img;
foreach ($_FILES['img']['name'] as $f => $name) {   

    $name = $_FILES['img']['name'][$f];
    $uploadName = explode('.', $name);

    // $fileSize = $_FILES['upload']['size'][$f];
    // $fileType = $_FILES['upload']['type'][$f];
    $uploadname = time().$f.'.'.$uploadName[1];
    $uploadFile = $file_dir."/".$uploadname;

    if(move_uploaded_file($_FILES['img']['tmp_name'][$f], $uploadFile)){	
		$upload_Bit = "1";
    }else{
		$upload_Bit = "0";
    }
}
if($upload_Bit == "1"){
	$insQuery = "
		update TB_CONFIG_CODE 
		set 
			code_on_Img ='".$now_time."' 
		where 
			idx =	".$mIdx." 
	";		
	$DB_con->exec($insQuery);  
}

*/
/* 이미지 파일 업로드 시작 */
/*
$chk_code_Img = "";
$now_time = time();										// 추후 파일 디렉토리가 될 예정
// 이미지 경로
$file_dir = $_SERVER["DOCUMENT_ROOT"].'/contents/place_img/'.$now_time;
// 이미지 ------------------------------------------------------------------
$org_chk_code_Img = $file_dir.'/'.$chk_code_Img;
foreach ($_FILES['img']['name'] as $f => $name) {   

    $name = $_FILES['img']['name'][$f];
    $uploadName = explode('.', $name);

    // $fileSize = $_FILES['upload']['size'][$f];
    // $fileType = $_FILES['upload']['type'][$f];
    $uploadname = time().$f.'.'.$uploadName[1];
    $uploadFile = $file_dir.$uploadname;

    if(move_uploaded_file($_FILES['img']['tmp_name'][$f], $uploadFile)){
        echo 'success';
    }else{
        echo 'error';
    }
}  

// 파일삭제
// if ($del_code_ImgFile) {
// 	$file_img = $file_dir.'/'.$chk_code_Img;
// 	@unlink($file_img);
// 	del_thumbnail(dirname($file_img), basename($file_img));
// 	$code_ImgFile = '';
// } else {
// 	$code_ImgFile = "$code_ImgFile";
// }


// 이미지 업로드 
$image_regex = "/(\.(gif|jpe?g|png))$/i";
if (isset($_FILES['img']) && is_uploaded_file($_FILES['img']['tmp_name'])) {  //이미지 업로드 성공일 경우


	if (preg_match($image_regex, $_FILES['img']['name'])) {

		@mkdir($file_dir, 0755);
		//@chmod($file_dir, 0644);

		$filename = $_FILES['img']['name'];

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

		move_uploaded_file($_FILES['img']['tmp_name'], $dest_path);
	
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


if ($chk_code_Img != "") {
	$chk_code_Img = $chk_code_Img;
} else {
	$chk_code_Img = $code_ImgFile;
}

//새로운 팝업 이미지경로 출력
$member_on_img = $file_dir.'/'.$fileName;


//파일저장방법 변경 _blob -------------------------------------------------------- 2019.02.19

if(file_exists($member_on_img) && $fileName != "")
{

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
	//@unlink($org_chk_code_Img);
	//신규 업로드 팝업 이미지 삭제
	@unlink($member_img);
	// 파일로 blob형태 이미지 저장----------E
}
*/
//공통 폼 (맵좌표 확인)
function common_Form($addr){
	$url = 'https://api2.sktelecom.com/tmap/pois?version=1&searchKeyword='.$addr.'&appKey=AIzaSyBAm_wDUkAZwtSDHp8OX3HM8h85tKnSR7c';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);//헤더 정보를 보내도록 함(*필수)
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0'); 
	$contents = curl_exec($ch); 
	$contents_json = json_decode($contents, true); // 결과값을 파싱
	curl_close($ch);
//	return $contents_json['searchPoiInfo']['pois']['poi']['0'];	// 최종 위치정보만 가져오기
	return $contents_json;
}
$addr = urlencode($address);
$res = common_Form($addr);
$rLng = $res['searchPoiInfo']['pois']['poi']['0']['frontLon'];		//경도
if($rLng ==''){
	$lng = '0.000000000000000';
}else{
	$lng = $rLng;
}
$rLat = $res['searchPoiInfo']['pois']['poi']['0']['frontLat'];		//위도
if($rLat ==''){
	$lat = '0.000000000000000';
}else{
	$lat = $rLat;
}
$reg_Id = trim($reg_Id);							//등록자
$mIdx = memIdxInfo($reg_Id);
$reg_Date = DU_TIME_YMDHIS;				//등록일

if ($reg_Id != "") {
    
    $DB_con = db1();
	if($con_Idx != ""){
		//지점 등록
		$query = "INSERT INTO TB_PLACE (member_Idx, con_Idx, category, place_Name, place_Icon, memo, smemo, tel, img, otime_Day, otime_Week, addr, lng, lat, reg_Id, reg_date) VALUES (:member_Idx, :con_Idx, :category, :place_Name, :place_Icon, :memo, :smemo, :tel, '', :otime_Day, :otime_Week, :addr, :lng, :lat, :reg_Id, :reg_Date)";
		
		$stmt = $DB_con->prepare($query);
		$stmt->bindParam(":member_Idx", $mIdx);
		$stmt->bindParam(":con_Idx", $con_Idx);
		$stmt->bindParam(":category", $category);
		$stmt->bindParam(":place_Name", $place_Name);
		$stmt->bindParam(":place_Icon", $place_Icon);
		$stmt->bindParam(":memo", $memo);
		$stmt->bindParam(":smemo", $smemo);
		$stmt->bindParam(":tel", $tel);
		$stmt->bindParam(":otime_Day", $otime_Day);
		$stmt->bindParam(":otime_Week", $otime_Week);
		$stmt->bindParam(":addr", $taddr);
		$stmt->bindParam(":lng", $lng);
		$stmt->bindParam(":lat", $lat);
		$stmt->bindParam(":reg_Id", $reg_Id);
		$stmt->bindParam(":reg_Date", $reg_Date);
		$stmt->execute();
		$pIdx = $DB_con->lastInsertId();  //저장된 idx 값

		$con_query ="
			UPDATE TB_CONTENTS
			SET mod_Date = NOW()
			WHERE idx = :idx
				AND member_Idx = :member_Idx";
		$con_stmt = $DB_con->prepare($con_query);
		$con_stmt->bindParam(":member_Idx", $mIdx);
		$con_stmt->bindParam(":idx", $con_Idx);
		$con_stmt->execute();
	}else{
		//핀 등록
		$query = "INSERT INTO TB_PLACE (member_Idx, category, place_Name, place_Icon, memo, smemo, tel, img, otime_Day, otime_Week, addr, lng, lat, reg_Id, reg_date) VALUES (:member_Idx, :category, :place_Name, :place_Icon, :memo, :smemo, :tel, '', :otime_Day, :otime_Week, :addr, :lng, :lat, :reg_Id, :reg_Date)";
		
		$stmt = $DB_con->prepare($query);
		$stmt->bindParam(":member_Idx", $mIdx);
		$stmt->bindParam(":category", $category);
		$stmt->bindParam(":place_Name", $place_Name);
		$stmt->bindParam(":place_Icon", $place_Icon);
		$stmt->bindParam(":memo", $memo);
		$stmt->bindParam(":smemo", $smemo);
		$stmt->bindParam(":tel", $tel);
		$stmt->bindParam(":otime_Day", $otime_Day);
		$stmt->bindParam(":otime_Week", $otime_Week);
		$stmt->bindParam(":addr", $taddr);
		$stmt->bindParam(":lng", $lng);
		$stmt->bindParam(":lat", $lat);
		$stmt->bindParam(":reg_Id", $reg_Id);
		$stmt->bindParam(":reg_Date", $reg_Date);
		$stmt->execute();
		$pIdx = $DB_con->lastInsertId();  //저장된 idx 값
	}
// 이미지가 있는 경우 없는 경우는 없음
if(isset($_FILES['img'])){
	$now_time = time();										// 추후 파일 디렉토리가 될 예정
	// 이미지 경로
	$file_dir = $_SERVER["DOCUMENT_ROOT"].'/contents/place_img/'.$now_time;
	if(!is_dir($file_dir)){
		mkdir($file_dir, 0777, true);
	}
	// 이미지 ------------------------------------------------------------------
	$org_chk_code_Img = $file_dir.'/'.$chk_code_Img;
	foreach ($_FILES['img']['name'] as $f => $name) {   

		$name = $_FILES['img']['name'][$f];
		$uploadName = explode('.', $name);

		// $fileSize = $_FILES['upload']['size'][$f];
		// $fileType = $_FILES['upload']['type'][$f];
		$uploadname = time().$f.'.'.$uploadName[1];
		$uploadFile = $file_dir."/".$uploadname;

		if(move_uploaded_file($_FILES['img']['tmp_name'][$f], $uploadFile)){	
			$upload_Bit = "1";
		}else{
			$upload_Bit = "0";
		}
	}
	if($upload_Bit == "1"){
		$insQuery = "
			update TB_PLACE 
			set 
				img ='".$now_time."' 
			where 
				idx =	".$pIdx." 
		";		
		$DB_con->exec($insQuery);  
	}
}else{
	$insQuery = "
		update TB_PLACE 
		set 
			img ='' 
		where 
			idx =	".$pIdx." 
	";		
	$DB_con->exec($insQuery);  
}
	$history = "지점등록";
	$his_query ="INSERT INTO TB_HISTORY (member_Idx, mem_Id, place_Idx, history, reg_Id, reg_date) VALUES (:member_Idx, :mem_Id, :place_Idx, :history, :reg_Id, :reg_Date)";
	$his_stmt = $DB_con->prepare($his_query);
	$his_stmt->bindParam(":member_Idx", $mIdx);
	$his_stmt->bindParam(":mem_Id", $reg_Id);
	$his_stmt->bindParam(":history", $history);
	$his_stmt->bindParam(":place_Idx", $pIdx);
	$his_stmt->bindParam(":reg_Id", $reg_Id);
	$his_stmt->bindParam(":reg_Date", $reg_Date);
	$his_stmt->execute();


    dbClose($DB_con);
    $stmt = null;
	$chkStmt = null;
	$his_stmt = null;
	$result = array("result" => "success");
} else { //빈값일 경우
	$result = array("result" => "error", "errorMsg" => "지점등록실패");
}

	echo json_encode($result);
?>



