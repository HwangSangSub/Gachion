<?
	$menu = "2";
	$smenu = "1";

	include "../common/inc/inc_header.php";  //헤더 

	$base_url = $PHP_SELF;

	$sql_search=" WHERE 1 ";

	if($findType != "" && $findword != "")  {
		$sql_search .= " AND `{$findType}` LIKE '%{$findword}%' ";
	}else if($findType == "" && $findword != ""){
		$sql_search .= " AND ((`memLv` LIKE '%{$findword}%') OR (`memLv_Name` LIKE '%{$findword}%')) ";
	}

	$DB_con = db1();
	
	//전체 카운트
	$cntQuery = "";
	$cntQuery = "SELECT COUNT(memLv) AS cntRow FROM TB_MEMBER_LEVEL  {$sql_search} " ;
	$cntStmt = $DB_con->prepare($cntQuery);

	if($findword != "")  {
		$cntStmt->bindparam(":findType",$findType);		
		$cntStmt->bindparam(":findword",$findword );
	}

	$findType = trim($findType);
	$findword = trim($findword);

	$cntStmt->execute();
	$row = $cntStmt->fetch(PDO::FETCH_ASSOC);
	$totalCnt = $row['cntRow'];

	$rows = 10;
	$total_page  = ceil($totalCnt / $rows);  // 전체 페이지 계산
	if ($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
	$from_record = ($page - 1) * $rows; // 시작 열을 구함


	if (!$sort1)	{
		$sort1  = "memLv";
		$sort2 = "ASC";
	}

	$sql_order = "order by $sort1 $sort2";

	//목록
	$query = "";
	$query = "SELECT idx, memLv, memLv_Name, memMatCnt, memDc FROM TB_MEMBER_LEVEL {$sql_search} {$sql_order} limit  {$from_record}, {$rows}" ;
	$stmt = $DB_con->prepare($query);

	if($findword != "")  {
		$stmt->bindparam(":findType",$findType);		
		$stmt->bindparam(":findword",$findword );
	}

	$findType = trim($findType);
	$findword = trim($findword);

	$stmt->execute();
	$numCnt = $stmt->rowCount();

	$qstr = "findType=".urlencode($findType)."&amp;findword=".urlencode($findword);

	include "../common/inc/inc_gnb.php";  //헤더 
	include "../common/inc/inc_menu.php";  //메뉴 

?>
<script type="text/javascript" src="<?=DU_UDEV_DIR?>/member/js/memberManager.js"></script>

<div id="wrapper">
    <div id="container" class="">
        <div class="container_wr">
        <h1 id="container_title">회원등급관리</h1>

		<div class="local_ov01 local_ov">
			<span class="btn_ov01"><span class="ov_txt">총 수 </span><span class="ov_num"><?=number_format($totalCnt);?>건 </span>
		</div>

		<form id="fsearch" name="fsearch" class="local_sch01 local_sch" method="get" autocomplete="off">

		<label for="findType" class="sound_only">검색대상</label>
		<select name="findType" id="findType">
			<option  value="">전체</option>
			<option value="memLv_Name" <?if($findType=="memLv_Name"){?>selected<?}?>>회원등급명</option>
			<option value="memLv" <?if($findType=="memLv"){?>selected<?}?>>회원레벨</option>
		</select>
		<label for="findword" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
		<input type="text" name="findword" id="findword" value="<?=$findword?>" class=" frm_input">
		<input type="submit" class="btn_submit" value="검색">
		<a href="<?=$base_url?>" class="btn btn_06">새로고침</a>
</form>

<nav class="pg_wrap">
	<?=get_apaging($rows, $page, $total_page, "$_SERVER[PHP_SELF]?$qstr"); ?>
</nav>

<form name="fmlist" id="fmlist"  method="post" autocomplete="off">

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption>회원등급 목록</caption>
    <thead>

	<!-- 아이디, 이름, 등급, 휴대폰번호, 가입일 -->
    <tr>
        <th scope="col" id="mb_list_chk" >
            <label for="chkall" class="sound_only">전체</label>
            <input type="checkbox" name="chkall" class="chkc" id="chkAll">
        </th>
        <th scope="col" id="mb_list_id">회원등급명</th>
        <th scope="col" id="mb_list_mailc">회원레벨</th>
        <th scope="col" id="mb_list_mailc">등급조건</th>
        <th scope="col" id="mb_list_mailc">회원DC</th>
		<th scope="col" id="mb_list_mng">관리</th>
    </tr>
    </thead>
    <tbody>

    <?

	if($numCnt > 0)   {

		$stmt->setFetchMode(PDO::FETCH_ASSOC);

		while($row =$stmt->fetch()) {

			if ($row['memMatCnt'] == "") {
				$matCnt = "-";
			} else {
				$matCnt = "조건 ".$row['memMatCnt']." 점";
			}

			if ($row['memDc'] == "") {
				$mDc = "-";
			} else {
				$mDc = $row['memDc']." %";
			}

    ?>

    <tr class="<?=$bg?>">
        <td headers="mb_list_chk" class="td_chk" >
            <input type="hidden" name="mb_id[<?=$row['idx']?>]" id="mb_id_<?=$row['idx']?>" value="<?=$row['mem_Id'] ?>" >
            <input type="checkbox"  id="chk" class="chk" name="chk" value="<?=$row['idx']?>">
        </td>
        <td headers="mb_list_id"><?=$row['memLv_Name']?></td>
        <td headers="mb_list_id" ><?=$row['memLv']?></td>
        <td headers="mb_list_id"><?=$matCnt?></td>
        <td headers="mb_list_id" ><?=$mDc?></td>
		<td headers="mb_list_mng" class="td_mng td_mng_s">
<? if($_COOKIE['du_udev']['id'] != 'admin2'){ ?>
			<a href="memManagerReg.php?mode=mod&idx=<?=$row['idx']?>&<?=$qstr?>&page=<?=$page?>" class="btn btn_03">수정</a>
			<a href="javascript:chkDel('<?=$row['idx']?>')" class="btn btn_02">삭제</a>
<? } ?>
		</td>
    </tr>
    <? 

		}
	?>   
	<? } else { ?>
	<tr>
		<td colspan="5" class="empty_table">자료가 없습니다.</td>
	</tr>
	<? } ?>
        </tbody>
    </table>
</div>

<div class="btn_fixed_top">
<? if($_COOKIE['du_udev']['id'] != 'admin2'){ ?>
	<a href="#ALDel" id="bt_m_a_del" class="btn btn_02">선택삭제</a>
	<a href="memManagerReg.php" id="coupon_add" class="btn btn_01">회원등급 추가</a> 
<? } ?>
</div>

</form>
<nav class="pg_wrap">
	<?=get_apaging($rows, $page, $total_page, "$_SERVER[PHP_SELF]?$qstr"); ?>
</nav>
</div>    

<?
	dbClose($DB_con);
	$cntStmt = null;
	$stmt = null;

	 include "../common/inc/inc_footer.php";  //푸터 
	 
?>

