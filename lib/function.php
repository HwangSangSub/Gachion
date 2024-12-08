<?

function Encrypt($str, $secret_key='secret key', $secret_iv='secret iv')
{
    $key = hash('sha256', $secret_key);
    $iv = substr(hash('sha256', $secret_iv), 0, 32)    ;

    return str_replace("=", "", base64_encode(
                 openssl_encrypt($str, "AES-256-CBC", $key, 0, $iv))
    );
}


function Decrypt($str, $secret_key='secret key', $secret_iv='secret iv')
{
    $key = hash('sha256', $secret_key);
    $iv = substr(hash('sha256', $secret_iv), 0, 32);

    return openssl_decrypt(
            base64_decode($str), "AES-256-CBC", $key, 0, $iv
    );
}


function pdo_bind_like($stx, $pos) {
    switch ($pos) {
        case 1  : $stx = '%' . str_replace(array('\\', '%', '_'), array('\\\\', '\\%', '\\_'), $stx);
                  break;
        case 2  : $stx = str_replace(array('\\', '%', '_'), array('\\\\', '\\%', '\\_'), $stx) . '%';
                  break;
        default : $stx = '%' . str_replace(array('\\', '%', '_'), array('\\\\', '\\%', '\\_'), $stx) . '%';
    }

    return $tex;
} 


// 제목별로 컬럼 정렬하는 QUERY STRING
// $type 이 1이면 반대
function title_sort($col, $type=0) {
    global $sort1, $sort2, $findType, $findword;
    global $_SERVER;
    global $page;

    $q1 = "sort1=$col";
    if ($type) {
        $q2 = "sort2=desc";
        if ($sort1 == $col) {
            if ($sort2 == "desc") {
                $q2 = "sort2=asc";
            }
        }
    } else {
        $q2 = "sort2=asc";
        if ($sort1 == $col) {
            if ($sort2 == "asc") {
                $q2 = "sort2=desc";
            }
        }
    }
    return "$_SERVER[PHP_SELF]?findType=$findType&amp;findword=$findword&amp;$q1&amp;$q2&amp;page=$page";
}



// 한페이지에 보여줄 행, 현재페이지, 총페이지수, URL(사용자)
function get_fpaging($write_pages, $cur_page, $total_page, $url, $add="") {
/*
	echo "write_pages=".$write_pages."<BR>";
	echo "cur_page=".$cur_page."<BR>";	
	echo "total_page=".$total_page."<BR>";
	echo "url=".$url."<BR>";
	echo "add=".$add."<BR>";
*/
    //$url = preg_replace('#&amp;page=[0-9]*(&amp;page=)$#', '$1', $url);
    $url = preg_replace('#&amp;page=[0-9]*#', '', $url) . '&amp;page=';

    $str = '';
    if ($cur_page > 1) {
        $str .= '<li><a href="'.$url.'1'.$add.'"><<</a></li>'.PHP_EOL;
    } else {
        $str .= '<li><a href="#"><<</a></li>'.PHP_EOL;
	}

    $start_page = ( ( (int)( ($cur_page - 1 ) / $write_pages ) ) * $write_pages ) + 1;
    $end_page = $start_page + $write_pages - 1;

    if ($end_page >= $total_page) $end_page = $total_page;

    if ($start_page > 1) $str .= '<li><a href="'.$url.($start_page-1).$add.'"><</a></li>'.PHP_EOL;


    if ($total_page > 1) {
        for ($k=$start_page;$k<=$end_page;$k++) {
            if ($cur_page != $k)
                $str .= '<li><a href="'.$url.$k.$add.'">'.$k.'</a></li>'.PHP_EOL;
            else
                $str .= '<li><a href="#" class="on">'.$k.'</a></li>'.PHP_EOL;
        }
    }

    if ($total_page > $end_page) $str .= '<li><a href="'.$url.($end_page+1).$add.'">></a></li>'.PHP_EOL;

    if ($cur_page < $total_page) {
        $str .= '<li><a href="'.$url.$total_page.$add.'" class="pg_page pg_end">>></a></li>'.PHP_EOL;
    } else {
        $str .= '<li><a href="#">>></a></li>'.PHP_EOL;
	}


    if ($str)
        return "{$str}";
    else
        return "";
}



// 한페이지에 보여줄 행, 현재페이지, 총페이지수, URL(관리자)
function get_apaging($write_pages, $cur_page, $total_page, $url, $add="") {

/*
	echo "write_pages=".$write_pages."<BR>";
	echo "cur_page=".$cur_page."<BR>";	
	echo "total_page=".$total_page."<BR>";
	echo "url=".$url."<BR>";
	echo "add=".$add."<BR>";
*/

    //$url = preg_replace('#&amp;page=[0-9]*(&amp;page=)$#', '$1', $url);
    $url = preg_replace('#&amp;page=[0-9]*#', '', $url) . '&amp;page=';

    $str = '';
    if ($cur_page > 1) {
        $str .= '<a href="'.$url.'1'.$add.'" class="pg_page pg_start">처음</a>'.PHP_EOL;
    }

    $start_page = ( ( (int)( ($cur_page - 1 ) / $write_pages ) ) * $write_pages ) + 1;
    $end_page = $start_page + $write_pages - 1;

    if ($end_page >= $total_page) $end_page = $total_page;

    if ($start_page > 1) $str .= '<a href="'.$url.($start_page-1).$add.'" class="pg_page pg_prev">이전</a>'.PHP_EOL;

    if ($total_page > 1) {
        for ($k=$start_page;$k<=$end_page;$k++) {
            if ($cur_page != $k)
                $str .= '<a href="'.$url.$k.$add.'" class="pg_page">'.$k.'<span class="sound_only">페이지</span></a>'.PHP_EOL;
            else
                $str .= '<span class="sound_only">열린</span><strong class="pg_current">'.$k.'</strong><span class="sound_only">페이지</span>'.PHP_EOL;
        }
    }

    if ($total_page > $end_page) $str .= '<a href="'.$url.($end_page+1).$add.'" class="pg_page pg_next">다음</a>'.PHP_EOL;

    if ($cur_page < $total_page) {
        $str .= '<a href="'.$url.$total_page.$add.'" class="pg_page pg_end">맨끝</a>'.PHP_EOL;
    }

    if ($str)
        return "<nav class=\"pg_wrap\"><span class=\"pg\">{$str}</span></nav>";
    else
        return "";
}


//회원 추천코드
function get_code() {
    $len = 8;
    $chars = "ABCDEFGHJKLMNPQRSTUVWXYZ123456789";

    srand((double)microtime()*1000000);

    $i = 0;
    $str = '';

    while ($i < $len) {
        $num = rand() % strlen($chars);
        $tmp = substr($chars, $num, 1);
        $str .= $tmp;
        $i++;
    }

    $str = preg_replace("/([0-9A-Z]{4})([0-9A-Z]{4})([0-9A-Z]{4})([0-9A-Z]{4})/", "\\1-\\2-\\3-\\4", $str);

    return $str;
}



//회원 문자코드
function get_smsCode() {
    $len = 6;
	
    //$chars = "abcdefghijklmnopqrstuvwxyz123456789";
    $chars = "0123456789";
    srand((double)microtime()*1000000);

    $i = 0;
    $str = '';

    while ($i < $len) {
        $num = rand() % strlen($chars);
        $tmp = substr($chars, $num, 1);
        $str .= $tmp;
        $i++;
    }

    $str = preg_replace("/([0-9A-Z]{4})([0-9A-Z]{4})([0-9A-Z]{4})([0-9A-Z]{4})/", "\\1-\\2-\\3-\\4", $str);

    return $str;
}




// 쿠폰번호 생성함수
function get_coupon_id() {
    $len = 16;
    $chars = "ABCDEFGHJKLMNPQRSTUVWXYZ123456789";

    srand((double)microtime()*1000000);

    $i = 0;
    $str = '';

    while ($i < $len) {
        $num = rand() % strlen($chars);
        $tmp = substr($chars, $num, 1);
        $str .= $tmp;
        $i++;
    }

    $str = preg_replace("/([0-9A-Z]{4})([0-9A-Z]{4})([0-9A-Z]{4})([0-9A-Z]{4})/", "\\1-\\2-\\3-\\4", $str);

    return $str;
}


//주회원아이디(랜덤생성)
function getRandString($length = 10) {
    $characters = '1234567890';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 1; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


//회원 아이디 (랜덤  : 년도 + 랜덤수 + 일자)
function getRandID($nowYear, $nowMonth, $nowDay, $length = 18) {
    $characters = '1234567890';
    $charAll = $nowYear.$nowMonth.$nowDay.$characters;
    
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 1; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}



//구글 지도 좌표 값 구하기
function getMapList($addr) {
   $string = str_replace (" ", "+", urlencode($addr));
   $gUrl = "https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyBAm_wDUkAZwtSDHp8OX3HM8h85tKnSR7c&address=".$string."&sensor=false&language=ko";
   $tempUrl = json_decode(file_get_contents($gUrl),true);

	//var_dump($tempUrl);
	$lng = $tempUrl['results'][0]['geometry']['location']['lng']; //경도
	$lat = $tempUrl['results'][0]['geometry']['location']['lat']; //위도

	return array($lng, $lat);
}
	//echo $lng."<BR>";
	//echo $lat."<BR>";


//티맵 지도 좌표 값 구하기
function getTMapList($lat, $lng) {
	  $tgUrl = "https://api2.sktelecom.com/tmap/geo/coordconvert?version=1&lat=".$lat."&lon=".$lng."&fromCoord=WGS84GEO&toCoord=EPSG3857&callback=&appKey=AIzaSyBAm_wDUkAZwtSDHp8OX3HM8h85tKnSR7c"; 
	 // echo $tgUrl."<BR>";
	  $tmap = json_decode(file_get_contents($tgUrl),true);

	  $tmlng = $tmap['coordinate']['lon']; //경도
	  $tmlat = $tmap['coordinate']['lat']; //위도

	return array($tmlng, $tmlat);
}


//티맵 거리,소용시간,택시예상요금 값 구하기
function getTMapDetail($endTLng, $endTLat, $startTLng, $startTLat, $startName, $endName) {
	 $key = "AIzaSyBAm_wDUkAZwtSDHp8OX3HM8h85tKnSR7c";  //티맵키
	 $tmUrl = "https://api2.sktelecom.com/tmap/routes?version=1&callback=&endX=".$endTLng."&endY=".$endTLat."&startX=".$startTLng."&startY=".$startTLat."&reqCoordType=EPSG3857&resCoordType=WGS84GEO&tollgateFareOption=1&roadType=32&directionOption=0&gpsTime=10000&angle=90&speed=60&uncetaintyP=3&uncetaintyA=3&uncetaintyAP=12&camOption=0&carType=0&startName=".$startName."&endName=".$endName."&searchOption=0&appKey=".$key; 
	 //echo $tmUrl."<BR>";
	 //exit;

	 $tmapFild = json_decode(file_get_contents($tmUrl), true);
	//print_r(json_decode($tmapFild, true));

	$totalDistance = $tmapFild['features']['0']['properties']['totalDistance']; //경로 총길이(m)
    $totalTime = $tmapFild['features']['0']['properties']['totalTime']; //경로 총 소요시간(초)
    //$totalFare = $tmapFild['features']['0']['properties']['totalFare']; //경로 총 요금 (원)
    $taxiFare = $tmapFild['features']['0']['properties']['taxiFare']; //택시 예상 요금 (원)

	return array($totalDistance, $totalTime, $taxiFare);
}


// 이미지 업로드
function img_upload($srcfile, $filename, $dir)
{
    if($filename == '')
        return '';

    $size = @getimagesize($srcfile);
    if($size[2] < 1 || $size[2] > 3)
        return '';

    //php파일도 getimagesize 에서 Image Type Flag 를 속일수 있다
    if (!preg_match('/\.(gif|jpe?g|png)$/i', $filename))
        return '';
 
    if(!is_dir($dir)) {
        @mkdir($dir, DU_DIR_PERMISSION);
        @chmod($dir, DU_DIR_PERMISSION);
    }

    $pattern = "/[#\&\+\-%@=\/\\:;,'\"\^`~\|\!\?\*\$#<>\(\)\[\]\{\}]/";

    $filename = preg_replace("/\s+/", "", $filename);
    $filename = preg_replace( $pattern, "", $filename);

    $filename = preg_replace_callback(
                          "/[가-힣]+/",
                          create_function('$matches', 'return base64_encode($matches[0]);'),
                          $filename);

    $filename = preg_replace( $pattern, "", $filename);
    $prepend = '';

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

    $filename = $prepend.$filename;

    upload_file($srcfile, $filename, $dir);

    $file = str_replace(DU_DATA_PATH.'/coupon/', '', $dir.'/'.$filename);

    return $file;
}


// 파일을 업로드 함
function upload_file($srcfile, $destfile, $dir)
{
    if ($destfile == "") return false;
    // 업로드 한후 , 퍼미션을 변경함
    @move_uploaded_file($srcfile, $dir.'/'.$destfile);
    @chmod($dir.'/'.$destfile, DU_FILE_PERMISSION);
    return true;
}


// 이미지 썸네일 삭제
function del_thumbnail($dir, $file)
{
    if(!$dir || !$file)
        return;

    $filename = preg_replace("/\.[^\.]+$/i", "", $file); // 확장자제거

    $files = glob($dir.'/thumb-'.$filename.'*');

    if(is_array($files)) {
        foreach($files as $thumb_file) {
            @unlink($thumb_file);
        }
    }
}


	 // IE인지 HTTP_USER_AGENT로 확인
	$ie = isset($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== false); 



//------------------------------------------------------------------------
// 날짜시간함수.
// @e : 날짜 문자열
// @f : 형식구분자
Function DateHard($e, $f) 
{
	
	if (!$e=="") {
		$arrDate = explode("-", $e);
		$intYear = $arrDate[0];   //년도
		$intMonth = $arrDate[1];  //월
		$intDay = $arrDate[2];       //일
		
		$arrDay = explode(" ", $intDay);   //일, 시간 구분
		$intDay = $arrDay[0];  // 시간
		$intTime = $arrDay[1];  // 시간
		
		$arrTime = explode(":", $intTime);  //시간 구분하기
		$intHh = $arrTime[0];  // 시간
		$intMi = $arrTime[1];  // 분
		$intSs = $arrTime[2];  // 초
		
		
		switch ($f) {
			case "1";
				$DateHard = $intYear."-".$intMonth."-".$intDay;
				break;
			case "2";
				$DateHard = $intYear.".".$intMonth.".".$intDay;
				break;	
			case "3";
				$DateHard = $intYear."/".$intMonth."/".$intDay;
				break;	
			case "4";
				$DateHard = $intYear."/".$intMonth."/".$intDay."&nbsp;".$intHh.":".$intMi;
				break;
			case "5";
			    $DateHard = $intYear."년".$intMonth."월".$intDay."일";
				break;				
			case "6";
				$DateHard = $intYear."/".$intMonth."/".$intDay."&nbsp;".$intHh."시".$intMi;
				break;		
			case "7";
				$DateHard = $intYear."년".$intMonth."월".$intDay."일 ".$intHh."시".$intMi."분";
				break;		
			case "8";
				$DateHard = $intYear."-".$intMonth."-".$intDay." ".$intHh.":".$intMi;
				break;


			case "95";
				$DateHard = $intYear;
				break;				
			case "96";
				$DateHard = $intMonth;
				break;				
		

			default:	
				$DateHard = $e;
		
		}
		
		return $DateHard;
		
	}	
	
	
}


//이름 중간에 * 출력
function mytory_asterisk($string) {
    $string = trim($string);
    $length = mb_strlen($string, 'utf-8');

    $string_changed = $string;
    if ($length <= 2) {
        // 한두 글자면 그냥 뒤에 별표 붙여서 내보낸다.
        $string_changed = mb_substr($string, 0, 1, 'utf-8') . '*';
    }
    if ($length >= 3) {
        // 3으로 나눠서 앞뒤.
        $leave_length = floor($length/3); // 남겨 둘 길이. 반올림하니 너무 많이 남기게 돼, 내림으로 해서 남기는 걸 줄였다.
        $asterisk_length = $length - ($leave_length * 2);
        $offset = $leave_length + $asterisk_length;
        $head = mb_substr($string, 0, $leave_length, 'utf-8');
        $tail = mb_substr($string, $offset, $leave_length, 'utf-8');
        $string_changed = $head . implode('', array_fill(0, $asterisk_length, '*')) . $tail;
    }
    return $string_changed;
}


//파일 중복 처리함수
function GetUniqFileName($FN, $PN)
{

	$FileExt = substr(strrchr($FN, "."), 1); // 확장자 추출
	$FileName = substr($FN, 0, strlen($FN) - strlen($FileExt) - 1); // 화일명 추출
	//echo "lll=".$FileExt."<BR>";
	//exit;
	$ret = "$FileName.$FileExt";
	
	
	//echo "#####".$ret;
	//exit;
	$FileCnt = 0;
	while(file_exists($PN.$ret)) // 화일명이 중복되지 않을때 까지 반복
	{
		$FileCnt++;
		$ret = $FileName."_".$FileCnt.".".$FileExt; // 화일명뒤에 (_1 ~ n)의 값을 붙여서....
	}

	return($ret); // 중복되지 않는 화일명 리턴
}



//int형 변환
function toNumber($val) {
    
    if (is_numeric($val)) {
        $int = (int)$val;
        $float = (float)$val;
        $val = ($int == $float) ? $int : $float;
        return $val;
    } else {
        // trigger_error("Cannot cast $val to a number", E_USER_WARNING);
        // return null;
    }
}

?>
