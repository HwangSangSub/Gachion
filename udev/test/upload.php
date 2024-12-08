<? 
    // 설정
    $uploads_dir = '../contents/img';
    $allowed_ext = array('jpg','jpeg','png','gif');
 
    // 폴더 존재 여부 확인 ( 없으면 생성 ) 
    if ( !is_dir ( $uploads_dir ) ){
        mkdir( $uploads_dir );
    }
     
    // 변수 정리
    $error = $_FILES['mainImgInput']['error'];
    $name = $_FILES['mainImgInput']['name'];
    $ext = array_pop(explode('.', $name));
     
    // 오류 확인
    if( $error != UPLOAD_ERR_OK ) {
        switch( $error ) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                echo "파일이 너무 큽니다. ($error)";
                break;
            case UPLOAD_ERR_NO_FILE:
                echo "파일이 첨부되지 않았습니다. ($error)";
                break;
            default:
                echo "파일이 제대로 업로드되지 않았습니다. ($error)";
        }
        exit;
    }
     
    // 확장자 확인
    if( !in_array($ext, $allowed_ext) ) {
        echo "허용되지 않는 확장자입니다.";
        exit;
    }
     
    // 파일 업로드
    move_uploaded_file( $_FILES['mainImgInput']['tmp_name'], "$uploads_dir/$name");
 
    // 파일 정보 출력
    echo "
        <h2>파일 정보</h2>
        <p>파일명: $name</p>
        <p>확장자: $ext</p>
        <p>파일형식: {$_FILES['mainImgInput']['type']}</p>
        <p>파일크기: {$_FILES['mainImgInput']['size']} 바이트</p>
        ";
 
 
    // 파일 압축 메소드 
    function compress($source, $destination, $quality) {
 
        $info = getimagesize($source);
 
        if ($info['mime'] == 'image/jpeg') 
            $image = imagecreatefromjpeg($source);
 
        elseif ($info['mime'] == 'image/gif') 
            $image = imagecreatefromgif($source);
 
        elseif ($info['mime'] == 'image/png') 
            $image = imagecreatefrompng($source);
 
        imagejpeg($image, $destination, $quality);
 
        return $destination;
    }
 
    // 파일 리사이즈 후 복사하기
    $source_img = $_FILES['mainImgInput']['tmp_name'];
    $destination_img = $_FILES['mainImgInput']['tmp_name'];
	/*
퀄리티를 90 으로 했을 때는, 원래 사이즈가 440kb 인 경우 110kb 
퀄리티를 50 으로 했을 때는, 원래 사이즈가 440kb 인 경우 44.0kb 정도로 줄여졌다.	
	*/
    $d = compress($source_img, $destination_img, 90);
 
?>