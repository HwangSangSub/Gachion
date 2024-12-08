<!doctype html>
<html lang="ko">
<head>
<meta charset="utf-8">
<meta http-equiv="imagetoolbar" content="no">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<title>주문내역 | 그누보드5</title>
<link rel="stylesheet" href="http://soloution.cafe24.com/adm/css/admin.css">
<link type="text/css" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/themes/base/jquery-ui.css" rel="stylesheet" />
<link type="text/css" href="http://soloution.cafe24.com/plugin/jquery-ui/style.css?ver=171222">
<!--[if lte IE 8]>
<script src="http://soloution.cafe24.com/js/html5.js"></script>
<![endif]-->
<script>
// 자바스크립트에서 사용하는 전역변수 선언
var g5_url       = "http://soloution.cafe24.com";
var g5_bbs_url   = "http://soloution.cafe24.com/bbs";
var g5_is_member = "1";
var g5_is_admin  = "super";
var g5_is_mobile = "";
var g5_bo_table  = "";
var g5_sca       = "";
var g5_editor    = "";
var g5_cookie_domain = "";
var g5_admin_url = "http://soloution.cafe24.com/adm";
</script>
<script src="http://soloution.cafe24.com/js/jquery-1.8.3.min.js"></script>
<script src="http://soloution.cafe24.com/js/jquery.menu.js?ver=171222"></script>
<script src="http://soloution.cafe24.com/js/common.js?ver=171222"></script>
<script src="http://soloution.cafe24.com/js/wrest.js?ver=171222"></script>
<script src="http://soloution.cafe24.com/js/placeholders.min.js"></script>
<link rel="stylesheet" href="http://soloution.cafe24.com/js/font-awesome/css/font-awesome.min.css">
</head>
<body>
<div id="hd_login_msg">최고관리자 최고관리자님 로그인 중 <a href="http://soloution.cafe24.com/bbs/logout.php">로그아웃</a></div>
<script>
var tempX = 0;
var tempY = 0;

function imageview(id, w, h)
{

    menu(id);

    var el_id = document.getElementById(id);

    //submenu = eval(name+".style");
    submenu = el_id.style;
    submenu.left = tempX - ( w + 11 );
    submenu.top  = tempY - ( h / 2 );

    selectBoxVisible();

    if (el_id.style.display != 'none')
        selectBoxHidden(id);
}
</script>

<div id="to_content"><a href="#container">본문 바로가기</a></div>

<header id="hd">
    <h1>그누보드5</h1>
    <div id="hd_top">
        <button type="button" id="btn_gnb" class="btn_gnb_close ">메뉴</button>
       <div id="logo"><a href="http://soloution.cafe24.com/adm"><img src="http://soloution.cafe24.com/adm/img/logo.png" alt="그누보드5 관리자"></a></div>

        <div id="tnb">
            <ul>
                <li class="tnb_li"><a href="http://soloution.cafe24.com/shop/" class="tnb_shop" target="_blank" title="쇼핑몰 바로가기">쇼핑몰 바로가기</a></li>
                <li class="tnb_li"><a href="http://soloution.cafe24.com/" class="tnb_community" target="_blank" title="커뮤니티 바로가기">커뮤니티 바로가기</a></li>
                <li class="tnb_li"><a href="http://soloution.cafe24.com/adm/service.php" class="tnb_service">부가서비스</a></li>
                <li class="tnb_li"><button type="button" class="tnb_mb_btn">관리자<span class="./img/btn_gnb.png">메뉴열기</span></button>
                    <ul class="tnb_mb_area">
                        <li><a href="http://soloution.cafe24.com/adm/member_form.php?w=u&amp;mb_id=edith">관리자정보</a></li>
                        <li id="tnb_logout"><a href="http://soloution.cafe24.com/bbs/logout.php">로그아웃</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    <nav id="gnb" class="gnb_large ">
        <h2>관리자 주메뉴</h2>
        <ul class="gnb_ul">
                        <li class="gnb_li">
                <button type="button" class="btn_op menu-100 menu-order-1" title="환경설정">환경설정</button>
                <div class="gnb_oparea_wr">
                    <div class="gnb_oparea">
                        <h3>환경설정</h3>
                        <ul><li data-menu="100100"><a href="http://soloution.cafe24.com/adm/config_form.php" class="gnb_2da  ">기본환경설정</a></li><li data-menu="100200"><a href="http://soloution.cafe24.com/adm/auth_list.php" class="gnb_2da  ">관리권한설정</a></li><li data-menu="100280"><a href="http://soloution.cafe24.com/adm/theme.php" class="gnb_2da gnb_grp_style gnb_grp_div">테마설정</a></li><li data-menu="100290"><a href="http://soloution.cafe24.com/adm/menu_list.php" class="gnb_2da gnb_grp_style ">메뉴설정</a></li><li data-menu="100300"><a href="http://soloution.cafe24.com/adm/sendmail_test.php" class="gnb_2da  gnb_grp_div">메일 테스트</a></li><li data-menu="100310"><a href="http://soloution.cafe24.com/adm/newwinlist.php" class="gnb_2da  ">팝업레이어관리</a></li><li data-menu="100800"><a href="http://soloution.cafe24.com/adm/session_file_delete.php" class="gnb_2da gnb_grp_style gnb_grp_div">세션파일 일괄삭제</a></li><li data-menu="100900"><a href="http://soloution.cafe24.com/adm/cache_file_delete.php" class="gnb_2da gnb_grp_style ">캐시파일 일괄삭제</a></li><li data-menu="100910"><a href="http://soloution.cafe24.com/adm/captcha_file_delete.php" class="gnb_2da gnb_grp_style ">캡챠파일 일괄삭제</a></li><li data-menu="100920"><a href="http://soloution.cafe24.com/adm/thumbnail_file_delete.php" class="gnb_2da gnb_grp_style ">썸네일파일 일괄삭제</a></li><li data-menu="100500"><a href="http://soloution.cafe24.com/adm/phpinfo.php" class="gnb_2da  gnb_grp_div">phpinfo()</a></li><li data-menu="100510"><a href="http://soloution.cafe24.com/adm/browscap.php" class="gnb_2da  ">Browscap 업데이트</a></li><li data-menu="100520"><a href="http://soloution.cafe24.com/adm/browscap_convert.php" class="gnb_2da  ">접속로그 변환</a></li><li data-menu="100410"><a href="http://soloution.cafe24.com/adm/dbupgrade.php" class="gnb_2da  ">DB업그레이드</a></li><li data-menu="100400"><a href="http://soloution.cafe24.com/adm/service.php" class="gnb_2da  ">부가서비스</a></li></ul>                    </div>
                </div>
            </li>
                        <li class="gnb_li">
                <button type="button" class="btn_op menu-200 menu-order-2" title="회원관리">회원관리</button>
                <div class="gnb_oparea_wr">
                    <div class="gnb_oparea">
                        <h3>회원관리</h3>
                        <ul><li data-menu="200100"><a href="http://soloution.cafe24.com/adm/member_list.php" class="gnb_2da  ">회원관리</a></li><li data-menu="200300"><a href="http://soloution.cafe24.com/adm/mail_list.php" class="gnb_2da  ">회원메일발송</a></li><li data-menu="200800"><a href="http://soloution.cafe24.com/adm/visit_list.php" class="gnb_2da gnb_grp_style gnb_grp_div">접속자집계</a></li><li data-menu="200810"><a href="http://soloution.cafe24.com/adm/visit_search.php" class="gnb_2da gnb_grp_style ">접속자검색</a></li><li data-menu="200820"><a href="http://soloution.cafe24.com/adm/visit_delete.php" class="gnb_2da gnb_grp_style ">접속자로그삭제</a></li><li data-menu="200200"><a href="http://soloution.cafe24.com/adm/point_list.php" class="gnb_2da  gnb_grp_div">포인트관리</a></li><li data-menu="200900"><a href="http://soloution.cafe24.com/adm/poll_list.php" class="gnb_2da  ">투표관리</a></li></ul>                    </div>
                </div>
            </li>
                        <li class="gnb_li">
                <button type="button" class="btn_op menu-300 menu-order-3" title="게시판관리">게시판관리</button>
                <div class="gnb_oparea_wr">
                    <div class="gnb_oparea">
                        <h3>게시판관리</h3>
                        <ul><li data-menu="300100"><a href="http://soloution.cafe24.com/adm/board_list.php" class="gnb_2da  ">게시판관리</a></li><li data-menu="300200"><a href="http://soloution.cafe24.com/adm/boardgroup_list.php" class="gnb_2da  ">게시판그룹관리</a></li><li data-menu="300300"><a href="http://soloution.cafe24.com/adm/popular_list.php" class="gnb_2da gnb_grp_style gnb_grp_div">인기검색어관리</a></li><li data-menu="300400"><a href="http://soloution.cafe24.com/adm/popular_rank.php" class="gnb_2da gnb_grp_style ">인기검색어순위</a></li><li data-menu="300500"><a href="http://soloution.cafe24.com/adm/qa_config.php" class="gnb_2da  gnb_grp_div">1:1문의설정</a></li><li data-menu="300600"><a href="http://soloution.cafe24.com/adm/contentlist.php" class="gnb_2da gnb_grp_style gnb_grp_div">내용관리</a></li><li data-menu="300700"><a href="http://soloution.cafe24.com/adm/faqmasterlist.php" class="gnb_2da gnb_grp_style ">FAQ관리</a></li><li data-menu="300820"><a href="http://soloution.cafe24.com/adm/write_count.php" class="gnb_2da  gnb_grp_div">글,댓글 현황</a></li></ul>                    </div>
                </div>
            </li>
                        <li class="gnb_li on">
                <button type="button" class="btn_op menu-400 menu-order-4" title="쇼핑몰관리">쇼핑몰관리</button>
                <div class="gnb_oparea_wr">
                    <div class="gnb_oparea">
                        <h3>쇼핑몰관리</h3>
                        <ul><li data-menu="400100"><a href="http://soloution.cafe24.com/adm/shop_admin/configform.php" class="gnb_2da  ">쇼핑몰설정</a></li><li data-menu="400400"><a href="http://soloution.cafe24.com/adm/shop_admin/orderlist.php" class="gnb_2da gnb_grp_style gnb_grp_div on">주문내역</a></li><li data-menu="400440"><a href="http://soloution.cafe24.com/adm/shop_admin/personalpaylist.php" class="gnb_2da gnb_grp_style ">개인결제관리</a></li><li data-menu="400200"><a href="http://soloution.cafe24.com/adm/shop_admin/categorylist.php" class="gnb_2da  gnb_grp_div">분류관리</a></li><li data-menu="400300"><a href="http://soloution.cafe24.com/adm/shop_admin/itemlist.php" class="gnb_2da  ">상품관리</a></li><li data-menu="400660"><a href="http://soloution.cafe24.com/adm/shop_admin/itemqalist.php" class="gnb_2da  ">상품문의</a></li><li data-menu="400650"><a href="http://soloution.cafe24.com/adm/shop_admin/itemuselist.php" class="gnb_2da  ">사용후기</a></li><li data-menu="400620"><a href="http://soloution.cafe24.com/adm/shop_admin/itemstocklist.php" class="gnb_2da  ">상품재고관리</a></li><li data-menu="400610"><a href="http://soloution.cafe24.com/adm/shop_admin/itemtypelist.php" class="gnb_2da  ">상품유형관리</a></li><li data-menu="400500"><a href="http://soloution.cafe24.com/adm/shop_admin/optionstocklist.php" class="gnb_2da  ">상품옵션재고관리</a></li><li data-menu="400800"><a href="http://soloution.cafe24.com/adm/shop_admin/couponlist.php" class="gnb_2da  ">쿠폰관리</a></li><li data-menu="400810"><a href="http://soloution.cafe24.com/adm/shop_admin/couponzonelist.php" class="gnb_2da  ">쿠폰존관리</a></li><li data-menu="400750"><a href="http://soloution.cafe24.com/adm/shop_admin/sendcostlist.php" class="gnb_2da gnb_grp_style gnb_grp_div">추가배송비관리</a></li><li data-menu="400410"><a href="http://soloution.cafe24.com/adm/shop_admin/inorderlist.php" class="gnb_2da gnb_grp_style ">미완료주문</a></li></ul>                    </div>
                </div>
            </li>
                        <li class="gnb_li">
                <button type="button" class="btn_op menu-500 menu-order-5" title="쇼핑몰현황/기타">쇼핑몰현황/기타</button>
                <div class="gnb_oparea_wr">
                    <div class="gnb_oparea">
                        <h3>쇼핑몰현황/기타</h3>
                        <ul><li data-menu="500110"><a href="http://soloution.cafe24.com/adm/shop_admin/sale1.php" class="gnb_2da  ">매출현황</a></li><li data-menu="500100"><a href="http://soloution.cafe24.com/adm/shop_admin/itemsellrank.php" class="gnb_2da  ">상품판매순위</a></li><li data-menu="500120"><a href="http://soloution.cafe24.com/adm/shop_admin/orderprint.php" class="gnb_2da gnb_grp_style gnb_grp_div">주문내역출력</a></li><li data-menu="500400"><a href="http://soloution.cafe24.com/adm/shop_admin/itemstocksms.php" class="gnb_2da gnb_grp_style ">재입고SMS알림</a></li><li data-menu="500300"><a href="http://soloution.cafe24.com/adm/shop_admin/itemevent.php" class="gnb_2da  gnb_grp_div">이벤트관리</a></li><li data-menu="500310"><a href="http://soloution.cafe24.com/adm/shop_admin/itemeventlist.php" class="gnb_2da  ">이벤트일괄처리</a></li><li data-menu="500500"><a href="http://soloution.cafe24.com/adm/shop_admin/bannerlist.php" class="gnb_2da gnb_grp_style gnb_grp_div">배너관리</a></li><li data-menu="500140"><a href="http://soloution.cafe24.com/adm/shop_admin/wishlist.php" class="gnb_2da  gnb_grp_div">보관함현황</a></li><li data-menu="500210"><a href="http://soloution.cafe24.com/adm/shop_admin/price.php" class="gnb_2da gnb_grp_style gnb_grp_div">가격비교사이트</a></li></ul>                    </div>
                </div>
            </li>
                        <li class="gnb_li">
                <button type="button" class="btn_op menu-900 menu-order-6" title="SMS 관리">SMS 관리</button>
                <div class="gnb_oparea_wr">
                    <div class="gnb_oparea">
                        <h3>SMS 관리</h3>
                        <ul><li data-menu="900100"><a href="http://soloution.cafe24.com/adm/sms_admin/config.php" class="gnb_2da  ">SMS 기본설정</a></li><li data-menu="900200"><a href="http://soloution.cafe24.com/adm/sms_admin/member_update.php" class="gnb_2da  ">회원정보업데이트</a></li><li data-menu="900300"><a href="http://soloution.cafe24.com/adm/sms_admin/sms_write.php" class="gnb_2da  ">문자 보내기</a></li><li data-menu="900400"><a href="http://soloution.cafe24.com/adm/sms_admin/history_list.php" class="gnb_2da gnb_grp_style gnb_grp_div">전송내역-건별</a></li><li data-menu="900410"><a href="http://soloution.cafe24.com/adm/sms_admin/history_num.php" class="gnb_2da gnb_grp_style ">전송내역-번호별</a></li><li data-menu="900500"><a href="http://soloution.cafe24.com/adm/sms_admin/form_group.php" class="gnb_2da  gnb_grp_div">이모티콘 그룹</a></li><li data-menu="900600"><a href="http://soloution.cafe24.com/adm/sms_admin/form_list.php" class="gnb_2da  ">이모티콘 관리</a></li><li data-menu="900700"><a href="http://soloution.cafe24.com/adm/sms_admin/num_group.php" class="gnb_2da gnb_grp_style gnb_grp_div">휴대폰번호 그룹</a></li><li data-menu="900800"><a href="http://soloution.cafe24.com/adm/sms_admin/num_book.php" class="gnb_2da gnb_grp_style ">휴대폰번호 관리</a></li><li data-menu="900900"><a href="http://soloution.cafe24.com/adm/sms_admin/num_book_file.php" class="gnb_2da gnb_grp_style ">휴대폰번호 파일</a></li></ul>                    </div>
                </div>
            </li>
                    </ul>
    </nav>

</header>
<script>
jQuery(function($){

    var menu_cookie_key = 'g5_admin_btn_gnb';

    $(".tnb_mb_btn").click(function(){
        $(".tnb_mb_area").toggle();
    });

    $("#btn_gnb").click(function(){
        
        var $this = $(this);

        try {
            if( ! $this.hasClass("btn_gnb_open") ){
                set_cookie(menu_cookie_key, 1, 60*60*24*365);
            } else {
                delete_cookie(menu_cookie_key);
            }
        }
        catch(err) {
        }

        $("#container").toggleClass("container-small");
        $("#gnb").toggleClass("gnb_small");
        $this.toggleClass("btn_gnb_open");

    });

    $(".gnb_ul li .btn_op" ).click(function() {
        $(this).parent().addClass("on").siblings().removeClass("on");
    });

});
</script>


<div id="wrapper">

    <div id="container" class="">

        <h1 id="container_title">주문내역</h1>
        <div class="container_wr">
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
<script>
jQuery(function($){
    $.datepicker.regional["ko"] = {
        closeText: "닫기",
        prevText: "이전달",
        nextText: "다음달",
        currentText: "오늘",
        monthNames: ["1월(JAN)","2월(FEB)","3월(MAR)","4월(APR)","5월(MAY)","6월(JUN)", "7월(JUL)","8월(AUG)","9월(SEP)","10월(OCT)","11월(NOV)","12월(DEC)"],
        monthNamesShort: ["1월","2월","3월","4월","5월","6월", "7월","8월","9월","10월","11월","12월"],
        dayNames: ["일","월","화","수","목","금","토"],
        dayNamesShort: ["일","월","화","수","목","금","토"],
        dayNamesMin: ["일","월","화","수","목","금","토"],
        weekHeader: "Wk",
        dateFormat: "yymmdd",
        firstDay: 0,
        isRTL: false,
        showMonthAfterYear: true,
        yearSuffix: ""
    };
	$.datepicker.setDefaults($.datepicker.regional["ko"]);
});
</script>
<div class="local_ov01 local_ov">
    <a href="/adm/shop_admin/orderlist.php" class="ov_listall">전체목록</a>    <span class="btn_ov01"><span class="ov_txt">전체 주문내역</span><span class="ov_num"> 0건</span></span>
    </div>

<form name="frmorderlist" class="local_sch01 local_sch">
<input type="hidden" name="doc" value="">
<input type="hidden" name="sort1" value="od_id">
<input type="hidden" name="sort2" value="desc">
<input type="hidden" name="page" value="1">
<input type="hidden" name="save_search" value="">

<label for="sel_field" class="sound_only">검색대상</label>
<select name="sel_field" id="sel_field">
    <option value="od_id"  selected="selected">주문번호</option>
    <option value="mb_id" >회원 ID</option>
    <option value="od_name" >주문자</option>
    <option value="od_tel" >주문자전화</option>
    <option value="od_hp" >주문자핸드폰</option>
    <option value="od_b_name" >받는분</option>
    <option value="od_b_tel" >받는분전화</option>
    <option value="od_b_hp" >받는분핸드폰</option>
    <option value="od_deposit_name" >입금자</option>
    <option value="od_invoice" >운송장번호</option>
</select>

<label for="search" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
<input type="text" name="search" value="" id="search" required class="required frm_input" autocomplete="off">
<input type="submit" value="검색" class="btn_submit">

</form>

<form class="local_sch03 local_sch">
<div>
    <strong>주문상태</strong>
    <input type="radio" name="od_status" value="" id="od_status_all"     checked="checked">
    <label for="od_status_all">전체</label>
    <input type="radio" name="od_status" value="주문" id="od_status_odr" >
    <label for="od_status_odr">주문</label>
    <input type="radio" name="od_status" value="입금" id="od_status_income" >
    <label for="od_status_income">입금</label>
    <input type="radio" name="od_status" value="준비" id="od_status_rdy" >
    <label for="od_status_rdy">준비</label>
    <input type="radio" name="od_status" value="배송" id="od_status_dvr" >
    <label for="od_status_dvr">배송</label>
    <input type="radio" name="od_status" value="완료" id="od_status_done" >
    <label for="od_status_done">완료</label>
    <input type="radio" name="od_status" value="전체취소" id="od_status_cancel" >
    <label for="od_status_cancel">전체취소</label>
    <input type="radio" name="od_status" value="부분취소" id="od_status_pcancel" >
    <label for="od_status_pcancel">부분취소</label>
</div>

<div>
    <strong>결제수단</strong>
    <input type="radio" name="od_settle_case" value="" id="od_settle_case01"         checked="checked">
    <label for="od_settle_case01">전체</label>
    <input type="radio" name="od_settle_case" value="무통장" id="od_settle_case02"   >
    <label for="od_settle_case02">무통장</label>
    <input type="radio" name="od_settle_case" value="가상계좌" id="od_settle_case03" >
    <label for="od_settle_case03">가상계좌</label>
    <input type="radio" name="od_settle_case" value="계좌이체" id="od_settle_case04" >
    <label for="od_settle_case04">계좌이체</label>
    <input type="radio" name="od_settle_case" value="휴대폰" id="od_settle_case05"   >
    <label for="od_settle_case05">휴대폰</label>
    <input type="radio" name="od_settle_case" value="신용카드" id="od_settle_case06" >
    <label for="od_settle_case06">신용카드</label>
    <input type="radio" name="od_settle_case" value="간편결제" id="od_settle_case07" >
    <label for="od_settle_case07">PG간편결제</label>
    <input type="radio" name="od_settle_case" value="KAKAOPAY" id="od_settle_case08" >
    <label for="od_settle_case08">KAKAOPAY</label>
</div>

<div>
    <strong>기타선택</strong>
    <input type="checkbox" name="od_misu" value="Y" id="od_misu01" >
    <label for="od_misu01">미수금</label>
    <input type="checkbox" name="od_cancel_price" value="Y" id="od_misu02" >
    <label for="od_misu02">반품,품절</label>
    <input type="checkbox" name="od_refund_price" value="Y" id="od_misu03" >
    <label for="od_misu03">환불</label>
    <input type="checkbox" name="od_receipt_point" value="Y" id="od_misu04" >
    <label for="od_misu04">포인트주문</label>
    <input type="checkbox" name="od_coupon" value="Y" id="od_misu05" >
    <label for="od_misu05">쿠폰</label>
    </div>

<div class="sch_last">
    <strong>주문일자</strong>
    <input type="text" id="fr_date"  name="fr_date" value="" class="frm_input" size="10" maxlength="10"> ~
    <input type="text" id="to_date"  name="to_date" value="" class="frm_input" size="10" maxlength="10">
    <button type="button" onclick="javascript:set_date('오늘');">오늘</button>
    <button type="button" onclick="javascript:set_date('어제');">어제</button>
    <button type="button" onclick="javascript:set_date('이번주');">이번주</button>
    <button type="button" onclick="javascript:set_date('이번달');">이번달</button>
    <button type="button" onclick="javascript:set_date('지난주');">지난주</button>
    <button type="button" onclick="javascript:set_date('지난달');">지난달</button>
    <button type="button" onclick="javascript:set_date('전체');">전체</button>
    <input type="submit" value="검색" class="btn_submit">
</div>
</form>

<form name="forderlist" id="forderlist" onsubmit="return forderlist_submit(this);" method="post" autocomplete="off">
<input type="hidden" name="search_od_status" value="">

<div class="tbl_head01 tbl_wrap">
    <table id="sodr_list">
    <caption>주문 내역 목록</caption>
    <thead>
    <tr>
        <th scope="col" rowspan="3">
            <label for="chkall" class="sound_only">주문 전체</label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
        </th>
        <th scope="col" id="th_ordnum" rowspan="2" colspan="2"><a href="/adm/shop_admin/orderlist.php?sort1=od_id&amp;sort2=asc&amp;page=1&amp;od_status=&amp;od_settle_case=&amp;od_misu=&amp;od_cancel_price=&amp;od_refund_price=&amp;od_receipt_point=&amp;od_coupon=&amp;fr_date=&amp;to_date=&amp;sel_field=od_id&amp;search=&amp;save_search=">주문번호</a></th>
        <th scope="col" id="th_odrer">주문자</th>
        <th scope="col" id="th_odrertel">주문자전화</th>
        <th scope="col" id="th_recvr">받는분</th>
        <th scope="col" rowspan="3">주문합계<br>선불배송비포함</th>
        <th scope="col" rowspan="3">입금합계</th>
        <th scope="col" rowspan="3">주문취소</th>
        <th scope="col" rowspan="3">쿠폰</th>
        <th scope="col" rowspan="3">미수금</th>
        <th scope="col" rowspan="3">보기</th>
    </tr>
    <tr>
        <th scope="col" id="th_odrid">회원ID</th>
        <th scope="col" id="th_odrcnt">주문상품수</th>
        <th scope="col" id="th_odrall">누적주문수</th>
    </tr>
    <tr>
        <th scope="col" id="odrstat">주문상태</th>
        <th scope="col" id="odrpay">결제수단</th>
        <th scope="col" id="delino">운송장번호</th>
        <th scope="col" id="delicom">배송회사</th>
        <th scope="col" id="delidate">배송일시</th>
    </tr>
    </thead>
    <tbody>
    <tr><td colspan="12" class="empty_table">자료가 없습니다.</td></tr>    </tbody>
    <tfoot>
    <tr class="orderlist">
        <th scope="row" colspan="3">&nbsp;</th>
        <td>&nbsp;</td>
        <td>0건</td>
        <th scope="row">합 계</th>
        <td>0</td>
        <td>0</td>
        <td>0</td>
        <td>0</td>
        <td>0</td>
        <td></td>
    </tr>
    </tfoot>
    </table>
</div>

<div class="local_cmd01 local_cmd">
    </div>

<div class="local_desc02 local_desc">
<p>
    &lt;무통장&gt;인 경우에만 &lt;주문&gt;에서 &lt;입금&gt;으로 변경됩니다. 가상계좌는 입금시 자동으로 &lt;입금&gt;처리됩니다.<br>
    &lt;준비&gt;에서 &lt;배송&gt;으로 변경시 &lt;에스크로배송등록&gt;을 체크하시면 에스크로 주문에 한해 PG사에 배송정보가 자동 등록됩니다.<br>
    <strong>주의!</strong> 주문번호를 클릭하여 나오는 주문상세내역의 주소를 외부에서 조회가 가능한곳에 올리지 마십시오.
</p>
</div>

</form>


<script>
$(function(){
    $("#fr_date, #to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });

    // 주문상품보기
    $(".orderitem").on("click", function() {
        var $this = $(this);
        var od_id = $this.text().replace(/[^0-9]/g, "");

        if($this.next("#orderitemlist").size())
            return false;

        $("#orderitemlist").remove();

        $.post(
            "./ajax.orderitem.php",
            { od_id: od_id },
            function(data) {
                $this.after("<div id=\"orderitemlist\"><div class=\"itemlist\"></div></div>");
                $("#orderitemlist .itemlist")
                    .html(data)
                    .append("<div id=\"orderitemlist_close\"><button type=\"button\" id=\"orderitemlist-x\" class=\"btn_frmline\">닫기</button></div>");
            }
        );

        return false;
    });

    // 상품리스트 닫기
    $(".orderitemlist-x").on("click", function() {
        $("#orderitemlist").remove();
    });

    $("body").on("click", function() {
        $("#orderitemlist").remove();
    });

    // 엑셀배송처리창
    $("#order_delivery").on("click", function() {
        var opt = "width=600,height=450,left=10,top=10";
        window.open(this.href, "win_excel", opt);
        return false;
    });
});

function set_date(today)
{
        if (today == "오늘") {
        document.getElementById("fr_date").value = "2018-04-20";
        document.getElementById("to_date").value = "2018-04-20";
    } else if (today == "어제") {
        document.getElementById("fr_date").value = "2018-04-19";
        document.getElementById("to_date").value = "2018-04-19";
    } else if (today == "이번주") {
        document.getElementById("fr_date").value = "2018-04-15";
        document.getElementById("to_date").value = "2018-04-20";
    } else if (today == "이번달") {
        document.getElementById("fr_date").value = "2018-04-01";
        document.getElementById("to_date").value = "2018-04-20";
    } else if (today == "지난주") {
        document.getElementById("fr_date").value = "2018-04-08";
        document.getElementById("to_date").value = "2018-04-14";
    } else if (today == "지난달") {
        document.getElementById("fr_date").value = "2018-03-01";
        document.getElementById("to_date").value = "2018-03-31";
    } else if (today == "전체") {
        document.getElementById("fr_date").value = "";
        document.getElementById("to_date").value = "";
    }
}
</script>

<script>
function forderlist_submit(f)
{
    if (!is_checked("chk[]")) {
        alert(document.pressed+" 하실 항목을 하나 이상 선택하세요.");
        return false;
    }

    /*
    switch (f.od_status.value) {
        case "" :
            alert("변경하실 주문상태를 선택하세요.");
            return false;
        case '주문' :

        default :

    }
    */

    if(document.pressed == "선택삭제") {
        if(confirm("선택한 자료를 정말 삭제하시겠습니까?")) {
            f.action = "./orderlistdelete.php";
            return true;
        }
        return false;
    }

    var change_status = f.od_status.value;

    if (f.od_status.checked == false) {
        alert("주문상태 변경에 체크하세요.");
        return false;
    }

    var chk = document.getElementsByName("chk[]");

    for (var i=0; i<chk.length; i++)
    {
        if (chk[i].checked)
        {
            var k = chk[i].value;
            var current_settle_case = f.elements['current_settle_case['+k+']'].value;
            var current_status = f.elements['current_status['+k+']'].value;

            switch (change_status)
            {
                case "입금" :
                    if (!(current_status == "주문" && current_settle_case == "무통장")) {
                        alert("'주문' 상태의 '무통장'(결제수단)인 경우에만 '입금' 처리 가능합니다.");
                        return false;
                    }
                    break;

                case "준비" :
                    if (current_status != "입금") {
                        alert("'입금' 상태의 주문만 '준비'로 변경이 가능합니다.");
                        return false;
                    }
                    break;

                case "배송" :
                    if (current_status != "준비") {
                        alert("'준비' 상태의 주문만 '배송'으로 변경이 가능합니다.");
                        return false;
                    }

                    var invoice      = f.elements['od_invoice['+k+']'];
                    var invoice_time = f.elements['od_invoice_time['+k+']'];
                    var delivery_company = f.elements['od_delivery_company['+k+']'];

                    if ($.trim(invoice_time.value) == '') {
                        alert("배송일시를 입력하시기 바랍니다.");
                        invoice_time.focus();
                        return false;
                    }

                    if ($.trim(delivery_company.value) == '') {
                        alert("배송업체를 입력하시기 바랍니다.");
                        delivery_company.focus();
                        return false;
                    }

                    if ($.trim(invoice.value) == '') {
                        alert("운송장번호를 입력하시기 바랍니다.");
                        invoice.focus();
                        return false;
                    }

                    break;
            }
        }
    }

    if (!confirm("선택하신 주문서의 주문상태를 '"+change_status+"'상태로 변경하시겠습니까?"))
        return false;

    f.action = "./orderlistupdate.php";
    return true;
}
</script>


        <noscript>
            <p>
                귀하께서 사용하시는 브라우저는 현재 <strong>자바스크립트를 사용하지 않음</strong>으로 설정되어 있습니다.<br>
                <strong>자바스크립트를 사용하지 않음</strong>으로 설정하신 경우는 수정이나 삭제시 별도의 경고창이 나오지 않으므로 이점 주의하시기 바랍니다.
            </p>
        </noscript>

        </div>    
        <footer id="ft">
            <p>
                Copyright &copy; soloution.cafe24.com. All rights reserved. YoungCart Version 5.3.1.1<br>
               <button type="button" class="scroll_top"><span class="top_img"></span><span class="top_txt">TOP</span></button>
           </p>
        </footer>
    </div>

</div>

<script>
$(".scroll_top").click(function(){
     $("body,html").animate({scrollTop:0},400);
})
</script>

<!-- <p>실행시간 : 0.00050997734069824 -->

<script src="http://soloution.cafe24.com/adm/admin.js?ver=171222"></script>
<script src="http://soloution.cafe24.com/js/jquery.anchorScroll.js?ver=171222"></script>
<script>
$(function(){

    var admin_head_height = $("#hd_top").height() + $("#container_title").height() + 5;

    $("a[href^='#']").anchorScroll({
        scrollSpeed: 0, // scroll speed
        offsetTop: admin_head_height, // offset for fixed top bars (defaults to 0)
        onScroll: function () { 
          // callback on scroll start
        },
        scrollEnd: function () { 
          // callback on scroll end
        }
    });

    var hide_menu = false;
    var mouse_event = false;
    var oldX = oldY = 0;

    $(document).mousemove(function(e) {
        if(oldX == 0) {
            oldX = e.pageX;
            oldY = e.pageY;
        }

        if(oldX != e.pageX || oldY != e.pageY) {
            mouse_event = true;
        }
    });

    // 주메뉴
    var $gnb = $(".gnb_1dli > a");
    $gnb.mouseover(function() {
        if(mouse_event) {
            $(".gnb_1dli").removeClass("gnb_1dli_over gnb_1dli_over2 gnb_1dli_on");
            $(this).parent().addClass("gnb_1dli_over gnb_1dli_on");
            menu_rearrange($(this).parent());
            hide_menu = false;
        }
    });

    $gnb.mouseout(function() {
        hide_menu = true;
    });

    $(".gnb_2dli").mouseover(function() {
        hide_menu = false;
    });

    $(".gnb_2dli").mouseout(function() {
        hide_menu = true;
    });

    $gnb.focusin(function() {
        $(".gnb_1dli").removeClass("gnb_1dli_over gnb_1dli_over2 gnb_1dli_on");
        $(this).parent().addClass("gnb_1dli_over gnb_1dli_on");
        menu_rearrange($(this).parent());
        hide_menu = false;
    });

    $gnb.focusout(function() {
        hide_menu = true;
    });

    $(".gnb_2da").focusin(function() {
        $(".gnb_1dli").removeClass("gnb_1dli_over gnb_1dli_over2 gnb_1dli_on");
        var $gnb_li = $(this).closest(".gnb_1dli").addClass("gnb_1dli_over gnb_1dli_on");
        menu_rearrange($(this).closest(".gnb_1dli"));
        hide_menu = false;
    });

    $(".gnb_2da").focusout(function() {
        hide_menu = true;
    });

    $('#gnb_1dul>li').bind('mouseleave',function(){
        submenu_hide();
    });

    $(document).bind('click focusin',function(){
        if(hide_menu) {
            submenu_hide();
        }
    });

    // 폰트 리사이즈 쿠키있으면 실행
    var font_resize_act = get_cookie("ck_font_resize_act");
    if(font_resize_act != "") {
        font_resize("container", font_resize_act);
    }
});

function submenu_hide() {
    $(".gnb_1dli").removeClass("gnb_1dli_over gnb_1dli_over2 gnb_1dli_on");
}

function menu_rearrange(el)
{
    var width = $("#gnb_1dul").width();
    var left = w1 = w2 = 0;
    var idx = $(".gnb_1dli").index(el);

    for(i=0; i<=idx; i++) {
        w1 = $(".gnb_1dli:eq("+i+")").outerWidth();
        w2 = $(".gnb_2dli > a:eq("+i+")").outerWidth(true);

        if((left + w2) > width) {
            el.removeClass("gnb_1dli_over").addClass("gnb_1dli_over2");
        }

        left += w1;
    }
}

</script>


<!-- <div style='float:left; text-align:center;'>RUN TIME : 0.00056004524230957<br></div> -->
<!-- ie6,7에서 사이드뷰가 게시판 목록에서 아래 사이드뷰에 가려지는 현상 수정 -->
<!--[if lte IE 7]>
<script>
$(function() {
    var $sv_use = $(".sv_use");
    var count = $sv_use.length;

    $sv_use.each(function() {
        $(this).css("z-index", count);
        $(this).css("position", "relative");
        count = count - 1;
    });
});
</script>
<![endif]-->

</body>
</html>
