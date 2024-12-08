<?
	$menu = "2";
	$smenu = "1";

	include "../common/inc/inc_header.php";  //헤더 

	$PG_table = $GnTable["member"];
	$JO_table = $GnTable["memberlevel"];

	$sql_search = " where mb_sess_flag = '1' and mem_leb > 0 ";

	if ($fr_date != "" || $to_date != "" ) {
		$sql_search.=" and (first_regist between '".$fr_date."' and '".$to_date."')";
		//$sql_search.=" and (DATE_FORMAT(a.first_regist,'%Y-%m-%d') >= '".$fr_date."' and DATE_FORMAT(a.first_regist,'%Y-%m-%d') <= '".$to_date."')";
	}

	/// 검색값이 넘어왔을 경우 검색 코드를 적용합니다.
	if($findword != "") $sql_search .= " and $findType like '%$findword%' ";

	// 테이블의 전체 레코드수만 얻음
	$sql = " select count(*) as cnt from $PG_table $sql_search";
	$row = sql_fetch($sql,FALSE);
	$total_count = $row[cnt];

	//echo $sql."<BR>";
	//exit;

	$rows = 10;
	$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
	if ($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
	$from_record = ($page - 1) * $rows; // 시작 열을 구함

	if (!$sort1)
	{
		$sort1  = "first_regist";
		$sort2 = "desc";
	}
	$sql_order = "order by $sort1 $sort2";

	// 탈퇴회원수
	$sql = " select count(*) as cnt from $PG_table where mb_sess_flag = '0'";
	$row = sql_fetch($sql);
	$leave_count = $row[cnt];

	// 출력할 레코드를 얻음
	$sql  = " select a.*, b.leb_name from $PG_table a left join $JO_table b on (a.mem_leb = b.leb_level)
		   $sql_search
		   $sql_order
		   limit $from_record, $rows ";

//echo $sql."<BR>";
//exit;

	$result = sql_query($sql);


	$qstr = "fr_date=".urlencode($fr_date)."&to_date=".urlencode($to_date)."&findType=".urlencode($findType)."&findword=".urlencode($findword)."&sort1=$sort1&sort2=$sort2";

	include "../common/inc/inc_gnb.php";  //헤더 
	include "../common/inc/inc_menu.php";  //메뉴 

?>



<div id="wrapper">
    <div id="container" class="">
        <div class="container_wr">
        <h1 id="container_title">회원관리</h1>

		<div class="local_ov01 local_ov">
			<span class="btn_ov01"><span class="ov_txt">총회원수 </span><span class="ov_num"><?=number_format($total_count);?>명 </span>&nbsp;
			<span class="btn_ov01"> <span class="ov_txt">탈퇴  </span><span class="ov_num"><?=number_format($leave_count);?>명</span>
		</div>

		<form id="fsearch" name="fsearch" class="local_sch01 local_sch" method="get" autocomplete="off">

		<div class="sch_last">
			<strong>가입일검색</strong>
			<input type="text" name="fr_date" id="fr_date" value="<?=$fr_date?>" class="frm_input" size="11" maxlength="10">
			<label for="fr_date" class="sound_only">시작일</label>
			~
			<input type="text" name="to_date" id="to_date" value="<?=$to_date?>"  class="frm_input" size="11" maxlength="10">
			<label for="to_date" class="sound_only">종료일</label>
		</div>

		<label for="findType" class="sound_only">검색대상</label>
		<select name="findType" id="findType">
			<option value="mem_name" <?if($findType=="mem_name"){?>selected<?}?>>이름</option>
			<option value="mem_id" <?if($findType=="mem_id"){?>selected<?}?>>아이디</option>
			<option value="leb_name" <?if($findType=="leb_name"){?>selected<?}?>>등급</option>
		</select>
		<label for="findword" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
		<input type="text" name="findword" id="findword" value="<?=$findword?>" class=" frm_input">
		<input type="submit" class="btn_submit" value="검색">
</form>

<div class="local_desc01 local_desc">
    <p>
        회원자료 삭제 시 다른 회원이 기존 회원아이디를 사용하지 못하도록 회원아이디, 이름은 삭제하지 않고 영구 보관합니다.
    </p>
</div>


<form name="fmemberlist" id="fmemberlist" action="./member_list_update.php" onsubmit="return fmemberlist_submit(this);" method="post" autocomplete="off">

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption>회원관리 목록</caption>
    <thead>

	<!-- 아이디, 이름, 등급, 휴대폰번호, 가입일 -->
    <tr>
        <th scope="col" id="mb_list_chk" >
            <label for="chkall" class="sound_only">회원 전체</label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
        </th>
        <th scope="col" id="mb_list_id"><a href="/adm/member_list.php?&amp;sst=mb_id&amp;sod=asc&amp;sfl=&amp;stx=&amp;sca=&amp;page=1">아이디</a></th>
        <th scope="col" id="mb_list_mailc"><a href="/adm/member_list.php?&amp;sst=mb_email_certify&amp;sod=desc&amp;sfl=&amp;stx=&amp;sca=&amp;page=1">이름</a></th>
        <th scope="col" id="mb_list_mailc"><a href="/adm/member_list.php?&amp;sst=mb_email_certify&amp;sod=desc&amp;sfl=&amp;stx=&amp;sca=&amp;page=1">등급</a></th>
        <th scope="col" id="mb_list_open"><a href="/adm/member_list.php?&amp;sst=mb_open&amp;sod=desc&amp;sfl=&amp;stx=&amp;sca=&amp;page=1">휴대폰</a></th>
        <th scope="col" id="mb_list_mailr"><a href="/adm/member_list.php?&amp;sst=mb_mailling&amp;sod=desc&amp;sfl=&amp;stx=&amp;sca=&amp;page=1">가입일</a></th>
        <th scope="col" id="mb_list_auth">상태</th>
        <th scope="col" id="mb_list_mobile">포인트</th>
        <th scope="col" id="mb_list_mng">관리</th>
    </tr>
    </thead>
    <tbody>

    <?
    for ($i=0; $row=sql_fetch_array($result); $i++) {

        $bg = 'bg'.($i%2);

		if($row[mb_sess_flag] == "1") { 
			$mb_sess_flag = "정상"; 
		} else { 
			$mb_sess_flag = "탈퇴"; 
		}

    ?>

    
    <tr class="<?=$bg?>">
        <td headers="mb_list_chk" class="td_chk" >
            <input type="hidden" name="mb_id[<?=$i?>]" id="mb_id_<?=$i >" value="<?=$row['mem_id'] ?>" >
            <label for="chk_<?=$i?>" class="sound_only"><?=get_text($row['mem_name']); ?>님</label>
            <input type="checkbox" name="chk[]" value="<?=$i?>" id="chk_<?=$i?>">
        </td>
        <td headers="mb_list_id"><?=$row['mem_id']?></td>
        <td headers="mb_list_id" class="td_name sv_use"><?=$row['mem_name']?></td>
        <td headers="mb_list_open"><?=$row['leb_name']?></td>
        <td headers="mb_list_open"><?=$row['mem_phone']?></td>
        <td headers="mb_list_lastcall" class="td_date"><?=substr($row['first_regist'],2,8)?></td>
        <td headers="mb_list_auth" class="td_mbstat"><?=$mb_sess_flag?></td>
        <td headers="mb_list_point" class="td_num"><a href="point_list.php?sfl=mb_id&amp;stx=edith"><?=number_format($row[mem_point])?></a></td>
        <td headers="mb_list_mng" class="td_mng td_mng_s"><a href="./member_form.php?sst=&amp;sod=&amp;sfl=&amp;stx=&amp;page=&amp;w=u&amp;mb_id=edith" class="btn btn_03">수정</a><a href="./boardgroupmember_form.php?mb_id=edith" class="btn btn_02">삭제</a>
		</td>
    </tr>
    <? } if ($i == 0) { ?>
	<tr>
		<td colspan="9" class="empty_table">자료가 없습니다.</td>
	</tr>
	<? } ?>
        </tbody>
    </table>
</div>

<div class="btn_fixed_top">
    <input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value" class="btn btn_02">
</div>


</form>
<nav class="pg_wrap">
	<?=get_apaging($default[page_list], $page, $total_page, "$_SERVER[PHP_SELF]?$qstr&page="); ?>
</nav>

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


	function fmemberlist_submit(f) {

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
