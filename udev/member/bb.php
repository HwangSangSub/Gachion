<? include "../common/inc/inc_header.php";  //헤더 ?>
<? include "../common/inc/inc_menu.php";  //메뉴 ?>



<div id="wrapper">

    <div id="container" class="">

        <h1 id="container_title">회원 추가</h1>
        <div class="container_wr">
<form name="fmember" id="fmember" action="./member_form_update.php" onsubmit="return fmember_submit(this);" method="post" enctype="multipart/form-data">
<input type="hidden" name="w" value="">
<input type="hidden" name="sfl" value="">
<input type="hidden" name="stx" value="">
<input type="hidden" name="sst" value="">
<input type="hidden" name="sod" value="">
<input type="hidden" name="page" value="">
<input type="hidden" name="token" value="">

<div class="tbl_frm01 tbl_wrap">
    <table>
    <caption>회원 추가</caption>
    <colgroup>
        <col class="grid_4">
        <col>
        <col class="grid_4">
        <col>
    </colgroup>
    <tbody>
    <tr>
        <th scope="row"><label for="mb_id">아이디<strong class="sound_only">필수</strong></label></th>
        <td>
            <input type="text" name="mb_id" value="" id="mb_id" required class="frm_input required alnum_" size="15"  maxlength="20">
                    </td>
        <th scope="row"><label for="mb_password">비밀번호<strong class="sound_only">필수</strong></label></th>
        <td><input type="password" name="mb_password" id="mb_password" required class="frm_input required" size="15" maxlength="20"></td>
    </tr>
    <tr>
        <th scope="row"><label for="mb_name">이름(실명)<strong class="sound_only">필수</strong></label></th>
        <td><input type="text" name="mb_name" value="" id="mb_name" required class="required frm_input" size="15"  maxlength="20"></td>
        <th scope="row"><label for="mb_nick">닉네임<strong class="sound_only">필수</strong></label></th>
        <td><input type="text" name="mb_nick" value="" id="mb_nick" required class="required frm_input" size="15"  maxlength="20"></td>
    </tr>
    <tr>
        <th scope="row"><label for="mb_level">회원 권한</label></th>
        <td>
<select id="mb_level" name="mb_level">
<option value="1">1</option>
<option value="2" selected="selected">2</option>
<option value="3">3</option>
<option value="4">4</option>
<option value="5">5</option>
<option value="6">6</option>
<option value="7">7</option>
<option value="8">8</option>
<option value="9">9</option>
<option value="10">10</option>
</select>
</td>
        <th scope="row">포인트</th>
        <td><a href="./point_list.php?sfl=mb_id&amp;stx=" target="_blank">0</a> 점</td>
    </tr>
    <tr>
        <th scope="row"><label for="mb_email">E-mail<strong class="sound_only">필수</strong></label></th>
        <td><input type="text" name="mb_email" value="" id="mb_email" maxlength="100" required class="required frm_input email" size="30"></td>
        <th scope="row"><label for="mb_homepage">홈페이지</label></th>
        <td><input type="text" name="mb_homepage" value="" id="mb_homepage" class="frm_input" maxlength="255" size="15"></td>
    </tr>
    <tr>
        <th scope="row"><label for="mb_hp">휴대폰번호</label></th>
        <td><input type="text" name="mb_hp" value="" id="mb_hp" class="frm_input" size="15" maxlength="20"></td>
        <th scope="row"><label for="mb_tel">전화번호</label></th>
        <td><input type="text" name="mb_tel" value="" id="mb_tel" class="frm_input" size="15" maxlength="20"></td>
    </tr>
    <tr>
        <th scope="row">본인확인방법</th>
        <td colspan="3">
            <input type="radio" name="mb_certify_case" value="ipin" id="mb_certify_ipin" >
            <label for="mb_certify_ipin">아이핀</label>
            <input type="radio" name="mb_certify_case" value="hp" id="mb_certify_hp" >
            <label for="mb_certify_hp">휴대폰</label>
        </td>
    </tr>
    <tr>
        <th scope="row">본인확인</th>
        <td>
            <input type="radio" name="mb_certify" value="1" id="mb_certify_yes" >
            <label for="mb_certify_yes">예</label>
            <input type="radio" name="mb_certify" value="" id="mb_certify_no" checked="checked">
            <label for="mb_certify_no">아니오</label>
        </td>
        <th scope="row">성인인증</th>
        <td>
            <input type="radio" name="mb_adult" value="1" id="mb_adult_yes" >
            <label for="mb_adult_yes">예</label>
            <input type="radio" name="mb_adult" value="0" id="mb_adult_no" checked="checked">
            <label for="mb_adult_no">아니오</label>
        </td>
    </tr>
    <tr>
        <th scope="row">주소</th>
        <td colspan="3" class="td_addr_line">
            <label for="mb_zip" class="sound_only">우편번호</label>
            <input type="text" name="mb_zip" value="" id="mb_zip" class="frm_input readonly" size="5" maxlength="6">
            <button type="button" class="btn_frmline" onclick="win_zip('fmember', 'mb_zip', 'mb_addr1', 'mb_addr2', 'mb_addr3', 'mb_addr_jibeon');">주소 검색</button><br>
            <input type="text" name="mb_addr1" value="" id="mb_addr1" class="frm_input readonly" size="60">
            <label for="mb_addr1">기본주소</label><br>
            <input type="text" name="mb_addr2" value="" id="mb_addr2" class="frm_input" size="60">
            <label for="mb_addr2">상세주소</label>
            <br>
            <input type="text" name="mb_addr3" value="" id="mb_addr3" class="frm_input" size="60">
            <label for="mb_addr3">참고항목</label>
            <input type="hidden" name="mb_addr_jibeon" value=""><br>
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="mb_icon">회원아이콘</label></th>
        <td colspan="3">
            <span class="frm_info">이미지 크기는 <strong>넓이 22픽셀 높이 22픽셀</strong>로 해주세요.</span>            <input type="file" name="mb_icon" id="mb_icon">
                    </td>
    </tr>
    <tr>
        <th scope="row"><label for="mb_img">회원이미지</label></th>
        <td colspan="3">
            <span class="frm_info">이미지 크기는 <strong>넓이 60픽셀 높이 60픽셀</strong>로 해주세요.</span>            <input type="file" name="mb_img" id="mb_img">
                    </td>
    </tr>
    <tr>
        <th scope="row">메일 수신</th>
        <td>
            <input type="radio" name="mb_mailling" value="1" id="mb_mailling_yes" checked="checked">
            <label for="mb_mailling_yes">예</label>
            <input type="radio" name="mb_mailling" value="0" id="mb_mailling_no" >
            <label for="mb_mailling_no">아니오</label>
        </td>
        <th scope="row"><label for="mb_sms_yes">SMS 수신</label></th>
        <td>
            <input type="radio" name="mb_sms" value="1" id="mb_sms_yes" >
            <label for="mb_sms_yes">예</label>
            <input type="radio" name="mb_sms" value="0" id="mb_sms_no" checked="checked">
            <label for="mb_sms_no">아니오</label>
        </td>
    </tr>
    <tr>
        <th scope="row">정보 공개</th>
        <td colspan="3">
            <input type="radio" name="mb_open" value="1" id="mb_open_yes" checked="checked">
            <label for="mb_open_yes">예</label>
            <input type="radio" name="mb_open" value="0" id="mb_open_no" >
            <label for="mb_open_no">아니오</label>
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="mb_signature">서명</label></th>
        <td colspan="3"><textarea  name="mb_signature" id="mb_signature"></textarea></td>
    </tr>
    <tr>
        <th scope="row"><label for="mb_profile">자기 소개</label></th>
        <td colspan="3"><textarea name="mb_profile" id="mb_profile"></textarea></td>
    </tr>
    <tr>
        <th scope="row"><label for="mb_memo">메모</label></th>
        <td colspan="3"><textarea name="mb_memo" id="mb_memo"></textarea></td>
    </tr>

    
    
    <tr>
        <th scope="row"><label for="mb_leave_date">탈퇴일자</label></th>
        <td>
            <input type="text" name="mb_leave_date" value="" id="mb_leave_date" class="frm_input" maxlength="8">
            <input type="checkbox" value="20180420" id="mb_leave_date_set_today" onclick="if (this.form.mb_leave_date.value==this.form.mb_leave_date.defaultValue) {
this.form.mb_leave_date.value=this.value; } else { this.form.mb_leave_date.value=this.form.mb_leave_date.defaultValue; }">
            <label for="mb_leave_date_set_today">탈퇴일을 오늘로 지정</label>
        </td>
        <th scope="row">접근차단일자</th>
        <td>
            <input type="text" name="mb_intercept_date" value="" id="mb_intercept_date" class="frm_input" maxlength="8">
            <input type="checkbox" value="20180420" id="mb_intercept_date_set_today" onclick="if
(this.form.mb_intercept_date.value==this.form.mb_intercept_date.defaultValue) { this.form.mb_intercept_date.value=this.value; } else {
this.form.mb_intercept_date.value=this.form.mb_intercept_date.defaultValue; }">
            <label for="mb_intercept_date_set_today">접근차단일을 오늘로 지정</label>
        </td>
    </tr>

    
        <tr>
        <th scope="row"><label for="mb_1">여분 필드 1</label></th>
        <td colspan="3"><input type="text" name="mb_1" value="" id="mb_1" class="frm_input" size="30" maxlength="255"></td>
    </tr>
        <tr>
        <th scope="row"><label for="mb_2">여분 필드 2</label></th>
        <td colspan="3"><input type="text" name="mb_2" value="" id="mb_2" class="frm_input" size="30" maxlength="255"></td>
    </tr>
        <tr>
        <th scope="row"><label for="mb_3">여분 필드 3</label></th>
        <td colspan="3"><input type="text" name="mb_3" value="" id="mb_3" class="frm_input" size="30" maxlength="255"></td>
    </tr>
        <tr>
        <th scope="row"><label for="mb_4">여분 필드 4</label></th>
        <td colspan="3"><input type="text" name="mb_4" value="" id="mb_4" class="frm_input" size="30" maxlength="255"></td>
    </tr>
        <tr>
        <th scope="row"><label for="mb_5">여분 필드 5</label></th>
        <td colspan="3"><input type="text" name="mb_5" value="" id="mb_5" class="frm_input" size="30" maxlength="255"></td>
    </tr>
        <tr>
        <th scope="row"><label for="mb_6">여분 필드 6</label></th>
        <td colspan="3"><input type="text" name="mb_6" value="" id="mb_6" class="frm_input" size="30" maxlength="255"></td>
    </tr>
        <tr>
        <th scope="row"><label for="mb_7">여분 필드 7</label></th>
        <td colspan="3"><input type="text" name="mb_7" value="" id="mb_7" class="frm_input" size="30" maxlength="255"></td>
    </tr>
        <tr>
        <th scope="row"><label for="mb_8">여분 필드 8</label></th>
        <td colspan="3"><input type="text" name="mb_8" value="" id="mb_8" class="frm_input" size="30" maxlength="255"></td>
    </tr>
        <tr>
        <th scope="row"><label for="mb_9">여분 필드 9</label></th>
        <td colspan="3"><input type="text" name="mb_9" value="" id="mb_9" class="frm_input" size="30" maxlength="255"></td>
    </tr>
        <tr>
        <th scope="row"><label for="mb_10">여분 필드 10</label></th>
        <td colspan="3"><input type="text" name="mb_10" value="" id="mb_10" class="frm_input" size="30" maxlength="255"></td>
    </tr>
    
    </tbody>
    </table>
</div>

<div class="btn_fixed_top">
    <a href="./member_list.php?sst=&amp;sod=&amp;sfl=&amp;stx=&amp;page=" class="btn btn_02">목록</a>
    <input type="submit" value="확인" class="btn_submit btn" accesskey='s'>
</div>
</form>

<script>
function fmember_submit(f)
{
    if (!f.mb_icon.value.match(/\.(gif|jpe?g|png)$/i) && f.mb_icon.value) {
        alert('아이콘은 이미지 파일만 가능합니다.');
        return false;
    }

    if (!f.mb_img.value.match(/\.(gif|jpe?g|png)$/i) && f.mb_img.value) {
        alert('회원이미지는 이미지 파일만 가능합니다.');
        return false;
    }

    return true;
}
</script>

</div>    

<? include "../common/inc/inc_footer.php";  //푸터 ?>
