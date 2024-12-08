<?php
 	$sFileInfo = '';
	$headers = array();
	 
	foreach($_SERVER as $k => $v) {
		if(substr($k, 0, 9) == "HTTP_FILE") {
			$k = substr(strtolower($k), 5);
			$headers[$k] = $v;
		} 
	}
	
	$file = new stdClass;
	$file->name = str_replace("\0", "", rawurldecode($headers['file_name']));
	$file->size = $headers['file_size'];
	$file->content = file_get_contents("php://input");
	
	$filename_ext = strtolower(array_pop(explode('.',$file->name)));
	$allow_file = array("jpg", "png", "bmp", "gif"); 
	
	if(!in_array($filename_ext, $allow_file)) {
		echo "NOTALLOW_".$file->name;
	} else {
		$uploadDir = '../../upload/';
		if(!is_dir($uploadDir)){
			mkdir($uploadDir, 0777);
		}
		
		/* 파일이름 중복 방지를 위한 코드 */
		$addName = strtotime(date("Y-m-d H:i:s"));  //현재날짜,시간,분초구하기
		$milliseconds = round(microtime(true) * 1000);  //밀리초 구하기
		$addName .= $milliseconds;       //파일이름에 밀리초 추가하기
		$file->name = $addName . '_' . $file->name;
		//중복방지 코드 끝
		
		$newPath = $uploadDir.iconv("utf-8", "cp949", $file->name);
		
		if(file_put_contents($newPath, $file->content)) {
			$sFileInfo .= "&bNewLine=true";
			$sFileInfo .= "&sFileName=".$file->name;
			$sFileInfo .= "&sFileURL=https://du3.shjey.com/board/editor_web/upload/".$file->name;
			
			$fileFormat = substr($file->name,-4);
			$fileName = substr($file->name,0,-4);
			$thumbPath = $_SERVER["DOCUMENT_ROOT"]."/board/editor_web/upload/".$fileName. ".thumb".$fileFormat;
			
			thumbnail($_SERVER["DOCUMENT_ROOT"]."/board/editor_web/upload/".$file->name, substr($file->name,-3), $thumbPath, 200, 200);
		}
		
		echo $sFileInfo;
	}

function thumbnail($filePath, $fileFormat, $save_filename, $width, $height){
	if($fileFormat == "gif"){
		$src_img = ImageCreateFromGIF($filePath); //GIF 파일로부터 이미지를 읽어옵니다
	}else if($fileFormat == "jpg"){
		$src_img = ImageCreateFromJPEG($filePath); //JPG파일로부터 이미지를 읽어옵니다
	}else if($fileFormat == "png"){
		$src_img = ImageCreateFromPNG($filePath); //PNG 파일로부터 이미지를 읽어옵니다
	}
	$img_info = @getImageSize($filePath);//원본이미지의 정보를 얻어옵니다
	$img_width = $img_info[0];
	$img_height = $img_info[1];

	$x = (int) (($img_width - $img_height) / 2);
	$y = (int) (($img_height - $img_width) / 2);

	if($x < 1){
		$x = 0;
		$targetSize = $img_width;
	}

	if($y < 1){
		$y = 0;
		$targetSize = $img_height;
	}
	
	//높이가 너비의 2배 이상이면 중간지점 찾지 않고 좌측 꼭지점 부터 자름
	if($img_height > ($img_width * 2))	{
		$y = 0;
	}
	$dst_img = imagecreatetruecolor($width, $height); //타겟이미지를 생성합니다
	ImageCopyResampled($dst_img, $src_img, 0, 0, $x, $y, $width, $height, $targetSize, $targetSize); //ImageCopyResized 보다 높은 퀄리티
	ImageInterlace($dst_img);

	if($fileFormat == "gif"){
		ImageGIF($dst_img,  $save_filename); //실제로 이미지파일을 생성합니다
	}else if($fileFormat == "jpg"){
		ImageJPEG($dst_img,  $save_filename); //실제로 이미지파일을 생성합니다
	}else if($fileFormat == "png"){
		ImagePNG($dst_img,  $save_filename); //실제로 이미지파일을 생성합니다
	}		
	ImageDestroy($dst_img);
	ImageDestroy($src_img);
}
	
?>