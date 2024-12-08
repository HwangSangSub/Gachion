<?
//처리 프로세스 메세지
function proc_msg($msg, $url) {
	if ($msg == "reg") {
		$msg = "등록이 되었습니다.";
	} else if ($msg == "save") {
		$msg = "저장 되었습니다.";
	} else if ($msg == "mod") {
		$msg = "수정이 되었습니다.";
	} else if ($msg == "del") {
		$msg = "삭제 되었습니다.";
	} else if ($msg == "join") {
		$msg = "회원 가입이 되었습니다.";
	} else if ($msg == "mjoin") {
		$msg = "회원 정보가 수정 되었습니다.";
	} else if ($msg == "online") {
		$msg = "온라인 상담이 접수 되었습니다.";
	} else if ($msg == "etc") {
		$msg = "잘못된 접근 방식입니다.";
	} else if ($msg == "cnt") {
		$msg = "접속자 분석 초기화가 되었습니다.";
	} else if ($msg == "loginChk") {
		$msg = "로그인 후 이용하실 수 있습니다. 로그인을 해주세요!";
	} else if ($msg == "prodNo") {
		$msg = "제품이 삭제 되었거나 제품 정보가 없는 여행상품입니다.";
	} else if ($msg == "guide") {
		$msg = "가이드 신청이 접수 되었습니다.";
	} else if ($msg == "secede") {
		$msg = "탈퇴가 되었습니다. 이용해 주셔서 감사합니다.";
	} else if ($msg == "prodCancle") {
		$msg = "결제에 실패하였습니다. 다시한번 확인 바랍니다.";
	} else if ($msg == "ordCancle") {
		$msg = "주문 취소 신청이 접수 되었습니다.";
	} else if ($msg == "mreg") {
		$msg = "메세지를 보냈습니다.";
	} else if ($msg == "adminChk") {
		$msg = "허용하지 않는 접근입니다. 관리자로 로그인해주시기 바랍니다.";
	} 
	echo "<script type='text/javascript'>alert('$msg');location.replace('$url');</script>";
}


//처리 프로세스 메세지
function proc_msg2($msg) {
	echo "<script type='text/javascript'>alert('$msg');location.replace('/udev');</script>";
	exit;
}


//처리 프로세스 메세지
function proc_msg3($msg) {
	echo "<script type='text/javascript'>alert('$msg');history.go(-1);</script>";
	exit;
}


//처리 프로세스 메세지
function proc_msg4($url) {
	echo "<script type='text/javascript'>";
	echo "location.replace('".$url."')";	
	echo "</script>";	
}


//처리 프로세스 메세지
function proc_msg5($msg) {
	echo "<script type='text/javascript'>alert('$msg');history.go(-2);</script>";
	exit;
}


//처리 프로세스 메세지
function proc_amsg($msg) {
	echo "<script type='text/javascript'>alert('$msg');location.replace('/member/login.php');</script>";
	exit;
}


//모바일처리 프로세스 메세지
function proc_mmsg($msg) {
	echo "<script type='text/javascript'>alert('$msg');location.replace('/m');</script>";
	exit;
}


//로그인체크 메세지
function loginUChk($msg) {
	echo "<script type='text/javascript'>alert('$msg');location.replace('/member/login.php');</script>";
	exit;
}

//로그인체크 메세지
function loginUMChk($msg) {
	echo "<script type='text/javascript'>alert('$msg');location.replace('/m/member/login.php');</script>";
	exit;
}
           
//로그인체크 메세지
function loginChk() {
	echo "<script type='text/javascript'>location.replace('/');</script>";
	exit;
}


//모바일로그인체크 메세지
function loginMChk() {
	echo "<script type='text/javascript'>location.replace('/m');</script>";
	exit;
}

//모바일로그인체크 메세지
function loginMChk2() {
	echo "<script type='text/javascript'>location.replace('/cataract/m');</script>";
	exit;
}

//관리자로그인체크 메세지
function proc_admin($msg) {
	echo "<script type='text/javascript'>alert('$msg'); location.replace('../');</script>";
	exit;
}
