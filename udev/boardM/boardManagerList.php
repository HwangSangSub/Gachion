<?
	$menu = "6";
	$smenu = "1";

	include "../common/inc/inc_header.php";  //헤더 

	$base_url = $PHP_SELF;

	$sql_search=" WHERE 1 ";

	if($b_Type != "")  {
		$sql_search .= " AND b_Type = :b_Type";
	}

	if($findword != "")  {
		$sql_search .= " AND `{$findType}` LIKE '%{$findword}%' ";
	}

	$DB_con = db1();

	//전체 카운트
	$cntQuery = "";
	$cntQuery = "SELECT COUNT(b_Idx)  AS cntRow FROM TB_BOARD_SET {$sql_search} " ;
	$cntStmt = $DB_con->prepare($cntQuery);

	if($b_Type != "")  {
	    $cntStmt->bindValue(":b_Type",$b_Type);
	}

	$cntStmt->execute();
	$row = $cntStmt->fetch(PDO::FETCH_ASSOC);
	$totalCnt = $row['cntRow'];


	$cntStmt = null;

	$rows = 10;
	$total_page  = ceil($totalCnt / $rows);  // 전체 페이지 계산
	if ($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
	$from_record = ($page - 1) * $rows; // 시작 열을 구함


	if (!$sort1)	{
		$sort1  = "reg_Date";
		$sort2 = "DESC";
	}

	$sql_order = "order by $sort1 $sort2";

	// 게시물 건수
	$borCSql = "  , ( SELECT COUNT(b_Idx) FROM TB_BOARD WHERE TB_BOARD.b_Idx = TB_BOARD_SET.b_Idx ) AS b_Cnt  ";

	//목록
	$query = "";
	$query = "SELECT idx, b_Idx, b_Type, b_Title, b_Disply, b_ListLv, b_ViewLv, b_WriteLv {$borCSql}  FROM TB_BOARD_SET {$sql_search} {$sql_order} limit  {$from_record}, {$rows}" ;
	$stmt = $DB_con->prepare($query);

	if($b_Type != "")  {
	    $stmt->bindValue(":b_Type",$b_Type);
	}

	$stmt->execute();
	$numCnt = $stmt->rowCount();

	$qstr = "b_Type=".urlencode($b_Type)."&amp;findType=".urlencode($findType)."&amp;findword=".urlencode($findword);

	include "../common/inc/inc_gnb.php";  //헤더 
	include "../common/inc/inc_menu.php";  //메뉴 

	if ( $du_udev[lv] == 0 ) { //최고권한관리자 
		$titNm = "게시판 환경 설정 관리";
	} else {
		$titNm = "게시판 관리";
	}
		

?>
<script type="text/javascript" src="<?=DU_UDEV_DIR?>/boardM/js/boardManager.js"></script>

<div id="wrapper">
    <div id="container" class="">
        <div class="container_wr">
        <h1 id="container_title"><?=$titNm?></h1>

		<div class="local_ov01 local_ov">
			<span class="btn_ov01"><span class="ov_txt">총 수 </span><span class="ov_num"><?=number_format($totalCnt);?>건 </span>
		</div>


		<form class="local_sch03 local_sch"  autocomplete="off">

		<? if ( $du_udev['lv'] == 0 ) { //최고권한관리자 ?>
		<div>
			<strong>게시판타입</strong>
			<select id="b_Type" name="b_Type">
				<option value="">게시판타입선택</option>
				<option value="1" <? if ($b_Type == '1') { ?>selected="selected"<? } ?>>일반게시판</option>
				<option value="2" <? if ($b_Type == '2') { ?>selected="selected"<? } ?>>갤러리게시판</option>
				<option value="3" <? if ($b_Type == '3') { ?>selected="selected"<? } ?>>웹진게시판</option>
				<option value="4" <? if ($b_Type == '4') { ?>selected="selected"<? } ?>>FAQ게시판</option>
				<option value="5" <? if ($b_Type == '5') { ?>selected="selected"<? } ?>>기타게시판</option>
				<option value="6" <? if ($b_Type == '6') { ?>selected="selected"<? } ?>>온라인상담게시판</option>
			</select>
		</div>
		<? } ?>

		<div>
				<strong>분류</strong>
				<select name="findType" id="findType">
					<option value="b_Title" <?if($findType=="b_Title"){?>selected<?}?>>제목</option>
				</select>
				<label for="findword" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
				<input type="text" name="findword" id="findword" value="<?=$findword?>" class=" frm_input">
				<input type="submit" class="btn_submit" value="검색">
				<a href="<?=$base_url?>" class="btn btn_06">새로고침</a>
		</div>

		</form>


<form name="fmemberlist" id="fmemberlist"  method="post" autocomplete="off">

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption>게시판 환경 관리 목록</caption>
    <thead>

    <tr>
		<? if ( $du_udev['lv'] == 0 ) { //최고권한관리자 ?>
        <th scope="col" id="mb_list_chk" >
            <label for="chkall" class="sound_only">게시판 환경 전체</label>
            <input type="checkbox" name="chkall" class="chkc" id="chkAll">
        </th>
		<? } ?>
        <th scope="col" id="mb_list_id">게시판명</th>
        <th scope="col" id="mb_list_mailc">게시판타입</th>
		 <th scope="col" id="mb_list_open">게시글수</th>		 
        <th scope="col" id="mb_list_auth">노출여부</th>
		<th scope="col" >관리</th>
    </tr>
    </thead>
    <tbody>

    <?

	if($numCnt > 0)   {

		$stmt->setFetchMode(PDO::FETCH_ASSOC);

		while($row =$stmt->fetch()) {
       // $bg = 'bg'.($stmt->fetch()%2);

			//게시판 형태
			$chkType = $row['b_Type'];

			if ($chkType == "1") {
				$bType = "일반";
			} else if ($chkType == "2") {
				$bType = "갤러리";
			} else if ($chkType == "3") {
				$bType = "웹진";
			} else if ($chkType == "4") {
				$bType = "FAQ";
			} else if ($chkType == "5") {
				$bType = "기타";
			} else if ($chkType == "6") {
				$bType = "온라인상담";
			} else if ($chkType == "7") {
				$bType = "고객체험후기";
			} else if ($chkType == "8") {
				$bType = "이벤트";
			}

			if($row['b_Disply'] == "Y") { 
				$b_Disply = "노출"; 
			} else { 
				$b_Disply = "미노출"; 
			}
		
    ?>

    <tr class="<?=$bg?>">
		<? if ( $du_udev['lv'] == 0 ) { //최고권한관리자 ?>
        <td headers="mb_list_chk" class="td_chk" >
            <input type="hidden" name="mb_id[<?=$row['b_Idx']?>]" id="mb_id_<?=$row['b_Idx']?>" value="<?=$row['b_Idx'] ?>" >
            <input type="checkbox"  id="chk" class="chk" name="chk" value="<?=$row['b_Idx']?>">
        </td>
		<? } ?>

        <td headers="mb_list_id"><?=$row['b_Title']?></td>
        <td headers="mb_list_id"><?= $bType?>게시판</td>
		<td headers="mb_list_open" class="td_name sv_use"><?=$row['b_Cnt']?></td>
        <td headers="mb_list_auth" class="td_mbstat td_mng_s"><?=$b_Disply?></td>
		<td class="td_mng td_mng_l2">


			<? if ( $row['b_Idx'] == "21" ) { //온라인상담 ?>
				<a href="/board/boardReg.php?board_id=2" target="_blank" class="btn btn_01">바로가기</a>
			<? } else { ?>
				<a href="/board/boardList.php?board_id=<?=$row['b_Idx']?>" class="btn btn_01" target="_blank">바로가기</a>
			<? } ?>		


		<? if ( $du_udev['lv'] == 0 ) { //최고권한관리자 ?>
			<a href="javascript:chkCopy('<?=$row['b_Idx']?>')" class="btn btn_04">복사</a>

			<a href="boardMangerReg.php?mode=mod&idx=<?=$row['b_Idx']?>&<?=$qstr?>&page=<?=$page?>" class="btn btn_03">수정</a>
			<a href="javascript:chkDel('<?=$row['b_Idx']?>')" class="btn btn_02">삭제</a>
			<? } ?>	

		</td>
    </tr>
    <? 

		}
	
	   } else {
			if ( $du_udev['lv'] == 0 ) {
				$chkCol = "7";
			} else {
				$chkCol = "6";
			}

?>
	<tr>
		<td colspan="<?=$chkCol?>" class="empty_table">자료가 없습니다.</td>
	</tr>
	<? } ?>
        </tbody>
    </table>
</div>

<? if ( $du_udev['lv'] == 0 ) { //최고권한관리자 ?>
<div class="btn_fixed_top">
	<a href="#ALDel" id="bt_m_a_del" class="btn btn_02">선택삭제</a>
	<a href="boardMangerReg.php" id="coupon_add" class="btn btn_01">게시판 추가</a> 
</div>
<? } ?>

</form>
<nav class="pg_wrap">
	<?=get_apaging($rows, $page, $total_page, "$_SERVER[PHP_SELF]?$qstr"); ?>
</nav>

<script>
	$(function(){
		$("#fr_date, #to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });
	});

</script>

</div>    

<?
	dbClose($DB_con);
	$cntStmt = null;
	$stmt = null;

	 include "../common/inc/inc_footer.php";  //푸터 
	 
?>
