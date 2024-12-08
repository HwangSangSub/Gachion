<? include "../common/inc/inc_header.php";  //헤더 ?>
<? include "../common/inc/inc_menu.php";  //메뉴 ?>



<div id="wrapper">

    <div id="container" class="">

        <div class="container_wr">

        <h1 id="container_title">회원관리</h1>


<div class="local_ov01 local_ov">
    <a href="/adm/member_list.php" class="ov_listall">전체목록</a>    <span class="btn_ov01"><span class="ov_txt">총회원수 </span><span class="ov_num"> 1명 </span></span>
    <a href="?sst=mb_intercept_date&amp;sod=desc&amp;sfl=&amp;stx=" class="btn_ov01"> <span class="ov_txt">차단 </span><span class="ov_num">0명</span></a>
    <a href="?sst=mb_leave_date&amp;sod=desc&amp;sfl=&amp;stx=" class="btn_ov01"> <span class="ov_txt">탈퇴  </span><span class="ov_num">0명</span></a>
</div>


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

<form id="fsearch" name="fsearch" class="local_sch01 local_sch" method="get">


<div class="sch_last">
    <strong>기간별검색</strong>
    <input type="text" name="fr_date" value="2018-04-20" id="fr_date" class="frm_input" size="11" maxlength="10">
    <label for="fr_date" class="sound_only">시작일</label>
    ~
    <input type="text" name="to_date" value="2018-04-20" id="to_date" class="frm_input" size="11" maxlength="10">
    <label for="to_date" class="sound_only">종료일</label>
</div>












<label for="sfl" class="sound_only">검색대상</label>
<select name="sfl" id="sfl">
    <option value="mb_id">회원아이디</option>
    <option value="mb_nick">닉네임</option>
    <option value="mb_name">이름</option>
    <option value="mb_level">권한</option>
    <option value="mb_email">E-MAIL</option>
    <option value="mb_tel">전화번호</option>
    <option value="mb_hp">휴대폰번호</option>
    <option value="mb_point">포인트</option>
    <option value="mb_datetime">가입일시</option>
    <option value="mb_ip">IP</option>
    <option value="mb_recommend">추천인</option>
</select>
<label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
<input type="text" name="stx" value="" id="stx" required class="required frm_input">







<input type="submit" class="btn_submit" value="검색">

</form>

<div class="local_desc01 local_desc">
    <p>
        회원자료 삭제 시 다른 회원이 기존 회원아이디를 사용하지 못하도록 회원아이디, 이름, 닉네임은 삭제하지 않고 영구 보관합니다.
    </p>
</div>


<form name="fmemberlist" id="fmemberlist" action="./member_list_update.php" onsubmit="return fmemberlist_submit(this);" method="post">
<input type="hidden" name="sst" value="mb_datetime">
<input type="hidden" name="sod" value="desc">
<input type="hidden" name="sfl" value="">
<input type="hidden" name="stx" value="">
<input type="hidden" name="page" value="1">
<input type="hidden" name="token" value="">

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption>회원관리 목록</caption>
    <thead>
    <tr>
        <th scope="col" id="mb_list_chk" rowspan="2" >
            <label for="chkall" class="sound_only">회원 전체</label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
        </th>
        <th scope="col" id="mb_list_id" colspan="2"><a href="/adm/member_list.php?&amp;sst=mb_id&amp;sod=asc&amp;sfl=&amp;stx=&amp;sca=&amp;page=1">아이디</a></th>
        <th scope="col" rowspan="2" id="mb_list_cert"><a href="/adm/member_list.php?&amp;sst=mb_certify&amp;sod=desc&amp;sfl=&amp;stx=&amp;sca=&amp;page=1">본인확인</a></th>
        <th scope="col" id="mb_list_mailc"><a href="/adm/member_list.php?&amp;sst=mb_email_certify&amp;sod=desc&amp;sfl=&amp;stx=&amp;sca=&amp;page=1">메일인증</a></th>
        <th scope="col" id="mb_list_open"><a href="/adm/member_list.php?&amp;sst=mb_open&amp;sod=desc&amp;sfl=&amp;stx=&amp;sca=&amp;page=1">정보공개</a></th>
        <th scope="col" id="mb_list_mailr"><a href="/adm/member_list.php?&amp;sst=mb_mailling&amp;sod=desc&amp;sfl=&amp;stx=&amp;sca=&amp;page=1">메일수신</a></th>
        <th scope="col" id="mb_list_auth">상태</th>
        <th scope="col" id="mb_list_mobile">휴대폰</th>
        <th scope="col" id="mb_list_lastcall"><a href="/adm/member_list.php?&amp;sst=mb_today_login&amp;sod=desc&amp;sfl=&amp;stx=&amp;sca=&amp;page=1">최종접속</a></th>
        <th scope="col" id="mb_list_grp">접근그룹</th>
        <th scope="col" rowspan="2" id="mb_list_mng">관리</th>
    </tr>
    <tr>
        <th scope="col" id="mb_list_name"><a href="/adm/member_list.php?&amp;sst=mb_name&amp;sod=asc&amp;sfl=&amp;stx=&amp;sca=&amp;page=1">이름</a></th>
        <th scope="col" id="mb_list_nick"><a href="/adm/member_list.php?&amp;sst=mb_nick&amp;sod=asc&amp;sfl=&amp;stx=&amp;sca=&amp;page=1">닉네임</a></th>
        <th scope="col" id="mb_list_sms"><a href="/adm/member_list.php?&amp;sst=mb_sms&amp;sod=desc&amp;sfl=&amp;stx=&amp;sca=&amp;page=1">SMS수신</a></th>
        <th scope="col" id="mb_list_adultc"><a href="/adm/member_list.php?&amp;sst=mb_adult&amp;sod=desc&amp;sfl=&amp;stx=&amp;sca=&amp;page=1">성인인증</a></th>
        <th scope="col" id="mb_list_auth"><a href="/adm/member_list.php?&amp;sst=mb_intercept_date&amp;sod=desc&amp;sfl=&amp;stx=&amp;sca=&amp;page=1">접근차단</a></th>
        <th scope="col" id="mb_list_deny"><a href="/adm/member_list.php?&amp;sst=mb_level&amp;sod=desc&amp;sfl=&amp;stx=&amp;sca=&amp;page=1">권한</a></th>
        <th scope="col" id="mb_list_tel">전화번호</th>
        <th scope="col" id="mb_list_join"><a href="/adm/member_list.php?&amp;sst=mb_datetime&amp;sod=asc&amp;sfl=&amp;stx=&amp;sca=&amp;page=1">가입일</a></th>
        <th scope="col" id="mb_list_point"><a href="/adm/member_list.php?&amp;sst=mb_point&amp;sod=desc&amp;sfl=&amp;stx=&amp;sca=&amp;page=1"> 포인트</a></th>
    </tr>
    </thead>
    <tbody>
    
    <tr class="bg0">
        <td headers="mb_list_chk" class="td_chk" rowspan="2">
            <input type="hidden" name="mb_id[0]" value="edith" id="mb_id_0">
            <label for="chk_0" class="sound_only">최고관리자 최고관리자님</label>
            <input type="checkbox" name="chk[]" value="0" id="chk_0">
        </td>
        <td headers="mb_list_id" colspan="2" class="td_name sv_use">
            edith                    </td>
        <td headers="mb_list_cert"  rowspan="2" class="td_mbcert">
            <input type="radio" name="mb_certify[0]" value="ipin" id="mb_certify_ipin_0" >
            <label for="mb_certify_ipin_0">아이핀</label><br>
            <input type="radio" name="mb_certify[0]" value="hp" id="mb_certify_hp_0" >
            <label for="mb_certify_hp_0">휴대폰</label>
        </td>
        <td headers="mb_list_mailc"><span class="txt_true">Yes</span></td>
        <td headers="mb_list_open">
            <label for="mb_open_0" class="sound_only">정보공개</label>
            <input type="checkbox" name="mb_open[0]" checked value="1" id="mb_open_0">
        </td>
        <td headers="mb_list_mailr">
            <label for="mb_mailling_0" class="sound_only">메일수신</label>
            <input type="checkbox" name="mb_mailling[0]" checked value="1" id="mb_mailling_0">
        </td>
        <td headers="mb_list_auth" class="td_mbstat">
            정상        </td>
        <td headers="mb_list_mobile" class="td_tel"></td>
        <td headers="mb_list_lastcall" class="td_date">18-04-20</td>
        <td headers="mb_list_grp" class="td_numsmall"></td>
        <td headers="mb_list_mng" rowspan="2" class="td_mng td_mng_s"><a href="./member_form.php?sst=&amp;sod=&amp;sfl=&amp;stx=&amp;page=&amp;w=u&amp;mb_id=edith" class="btn btn_03">수정</a><a href="./boardgroupmember_form.php?mb_id=edith" class="btn btn_02">그룹</a></td>
    </tr>
    <tr class="bg0">
        <td headers="mb_list_name" class="td_mbname">최고관리자</td>
        <td headers="mb_list_nick" class="td_name sv_use"><div><span class="sv_wrap">
<a href="http://soloution.cafe24.com/bbs/profile.php?mb_id=edith" class="sv_member" title="최고관리자 자기소개" target="_blank" rel="nofollow" onclick="return false;"><span class="profile_img"><img src="http://soloution.cafe24.com/img/no_profile.gif" alt="no_profile" width="20" height="20"></span> 최고관리자</a>
<span class="sv">
<a href="http://soloution.cafe24.com/bbs/memo_form.php?me_recv_mb_id=edith" onclick="win_memo(this.href); return false;">쪽지보내기</a>
<a href="http://soloution.cafe24.com/bbs/formmail.php?mb_id=edith&amp;name=%EC%B5%9C%EA%B3%A0%EA%B4%80%EB%A6%AC%EC%9E%90&amp;email=qcmu1ZyclZF3zJLQoZein2akxtg=" onclick="win_email(this.href); return false;">메일보내기</a>
<a href="http://soloution.cafe24.com/bbs/profile.php?mb_id=edith" onclick="win_profile(this.href); return false;">자기소개</a>
<a href="http://soloution.cafe24.com/bbs/new.php?mb_id=edith">전체게시물</a>
<a href="http://soloution.cafe24.com/adm/member_form.php?w=u&amp;mb_id=edith" target="_blank">회원정보변경</a>
<a href="http://soloution.cafe24.com/adm/point_list.php?sfl=mb_id&amp;stx=edith" target="_blank">포인트내역</a>
</span>

<noscript class="sv_nojs"><span class="sv">
<a href="http://soloution.cafe24.com/bbs/memo_form.php?me_recv_mb_id=edith" onclick="win_memo(this.href); return false;">쪽지보내기</a>
<a href="http://soloution.cafe24.com/bbs/formmail.php?mb_id=edith&amp;name=%EC%B5%9C%EA%B3%A0%EA%B4%80%EB%A6%AC%EC%9E%90&amp;email=qcmu1ZyclZF3zJLQoZein2akxtg=" onclick="win_email(this.href); return false;">메일보내기</a>
<a href="http://soloution.cafe24.com/bbs/profile.php?mb_id=edith" onclick="win_profile(this.href); return false;">자기소개</a>
<a href="http://soloution.cafe24.com/bbs/new.php?mb_id=edith">전체게시물</a>
<a href="http://soloution.cafe24.com/adm/member_form.php?w=u&amp;mb_id=edith" target="_blank">회원정보변경</a>
<a href="http://soloution.cafe24.com/adm/point_list.php?sfl=mb_id&amp;stx=edith" target="_blank">포인트내역</a>
</span>
</noscript></span></div></td>
        
        <td headers="mb_list_sms">
            <label for="mb_sms_0" class="sound_only">SMS수신</label>
            <input type="checkbox" name="mb_sms[0]"  value="1" id="mb_sms_0">
        </td>
        <td headers="mb_list_adultc">
            <label for="mb_adult_0" class="sound_only">성인인증</label>
            <input type="checkbox" name="mb_adult[0]"  value="1" id="mb_adult_0">
        </td>
        <td headers="mb_list_deny">
                        <input type="checkbox" name="mb_intercept_date[0]"  value="20180420" id="mb_intercept_date_0" title="차단하기">
            <label for="mb_intercept_date_0" class="sound_only">접근차단</label>
                    </td>
        <td headers="mb_list_auth" class="td_mbstat">
            
<select id="mb_level[0]" name="mb_level[0]">
<option value="1">1</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>
<option value="5">5</option>
<option value="6">6</option>
<option value="7">7</option>
<option value="8">8</option>
<option value="9">9</option>
<option value="10" selected="selected">10</option>
</select>
        </td>
        <td headers="mb_list_tel" class="td_tel"></td>
        <td headers="mb_list_join" class="td_date">18-04-19</td>
        <td headers="mb_list_point" class="td_num"><a href="point_list.php?sfl=mb_id&amp;stx=edith">200</a></td>

    </tr>

        </tbody>
    </table>
</div>

<div class="btn_fixed_top">
    <input type="submit" name="act_button" value="선택수정" onclick="document.pressed=this.value" class="btn btn_02">
    <input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value" class="btn btn_02">
        <a href="./member_form.php" id="member_add" class="btn btn_01">회원추가</a>
    
</div>


</form>


<script>

$(function(){
    $("#fr_date, #to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });
});

function fvisit_submit(act)
{
    var f = document.fvisit;
    f.action = act;
    f.submit();
}


function fmemberlist_submit(f)
{
    if (!is_checked("chk[]")) {
        alert(document.pressed+" 하실 항목을 하나 이상 선택하세요.");
        return false;
    }

    if(document.pressed == "선택삭제") {
        if(!confirm("선택한 자료를 정말 삭제하시겠습니까?")) {
            return false;
        }
    }

    return true;
}
</script>

</div>    

<? include "../common/inc/inc_footer.php";  //푸터 ?>
