<?
	$menu = "0";
	$smenu = "7";

	include "../common/inc/inc_header.php";  //헤더 

	$base_url = $PHP_SELF;

	$sql_search=" WHERE 1";

	if($findword != "")  {
		$sql_search .= " AND `{$findType}` LIKE '%{$findword}%' ";
	}

	$DB_con = db1();
	
	//전체 카운트
	$cntQuery = "";
	$cntQuery = "SELECT COUNT(idx) AS cntRow FROM TB_LANGUAGE  {$sql_search} " ;
	$cntStmt = $DB_con->prepare($cntQuery);

	if($findword != "")  {
	    $cntStmt->bindValue(":findType",$findType);		
	    $cntStmt->bindValue(":findword",$findword );
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
		$sort1  = "idx";
		$sort2 = "DESC";
	}

	$sql_order = "order by $sort1 $sort2";

	//목록
	$query = "";
	$query = "SELECT idx, korea, english, reg_Date FROM TB_LANGUAGE {$sql_search} {$sql_order} limit  {$from_record}, {$rows}" ;
	$stmt = $DB_con->prepare($query);

	if($findword != "")  {
	    $stmt->bindValue(":findType",$findType);		
	    $stmt->bindValue(":findword",$findword );
	}

	$findType = trim($findType);
	$findword = trim($findword);

	$stmt->execute();
	$numCnt = $stmt->rowCount();


	$qstr = "findType=".urlencode($findType)."&amp;findword=".urlencode($findword);

	include "../common/inc/inc_gnb.php";  //헤더 
	include "../common/inc/inc_menu.php";  //메뉴 

?>
<script type="text/javascript" src="<?=DU_UDEV_DIR?>/config/js/memPoint.js"></script>
<script type="text/javascript" src="<?=DU_UDEV_DIR?>/member/js/memberManager.js"></script>

<script type="text/javascript">
	function chkDel(idx){
		var con_test = confirm("해당코드를 삭제하시겠습니까?");
		if(con_test == true){
			var allData = { 
				"idx" : idx
				,"qstr" : "<?php echo $qstr;?>"
				,"page" : "<?php echo $page;?>"
				,"mode" : "del"
			};
			$.ajax({
			url:"/udev/config/configLangProc.php",
				type:'POST',
				dataType : 'json',
				data: allData,
				success:function(data){
					location.reload();
				},
				error:function(jqXHR, textStatus, errorThrown){
					alert("에러 발생~~ \n" + textStatus + " : " + errorThrown);
					location.reload();
				}
			});
		}else if(con_test == false){
			location.reload();
		}
	}
</script>
<div id="wrapper">
    <div id="container" class="">
        <div class="container_wr">
        <h1 id="container_title">언어 환경설정</h1>

		<div class="local_ov01 local_ov">
			<span class="btn_ov01"><span class="ov_txt">총 수 </span><span class="ov_num"><?=number_format($totalCnt);?>건 </span>
		</div>

		<form id="fsearch" name="fsearch" class="local_sch01 local_sch" method="get" autocomplete="off">

		<label for="findType" class="sound_only">검색대상</label>
		<select name="findType" id="findType">
			<option value="">전체</option>
			<option value="korea" <?if($findType=="korea"){?>selected<?}?>>메세지명</option>
		</select>
		<label for="findword" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
		<input type="text" name="findword" id="findword" value="<?=$findword?>" class=" frm_input">
		<input type="submit" class="btn_submit" value="검색">
		<a href="<?=$base_url?>" class="btn btn_06">새로고침</a>
</form>

<nav class="pg_wrap">
	<?=get_apaging($rows, $page, $total_page, "$_SERVER[PHP_SELF]?$sqstr"); ?>
</nav>

<form name="fmlist" id="fmlist"  method="post" autocomplete="off">

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption>메세지 목록</caption>
    <thead>

    <tr>
        <th scope="col" id="mb_list_chk" >
            <label for="chkall" class="sound_only">전체</label>
            <input type="checkbox" name="chkall" class="chkc" id="chkAll">
        </th>
        <th scope="col" id="mb_list_idx">순번</th>
        <th scope="col" id="mb_list_code_n">메세지명</th>
        <th scope="col" id="mb_list_date">등록일</th>
        <th scope="col" id="mb_list_admin">관리</th>
    </tr>
    </thead>
    <tbody>

    <?

	if($numCnt > 0)   {

		$stmt->setFetchMode(PDO::FETCH_ASSOC);

		while($row =$stmt->fetch()) {
			$idx = $row['idx'];
			$korea = $row['korea'];
			$reg_Date = $row['reg_Date'];

    ?>

    <tr class="<?=$bg?>">
        <td headers="mb_list_chk" class="td_chk" >
            <input type="checkbox"  id="chk" class="chk" name="chk" value="<?=$idx?>">
        </td>
        <td headers="mb_list_id"><?=$idx?></td>
        <td headers="mb_list_id"><?=$korea?></td>
        <td headers="mb_list_id"><?=$reg_Date?></td>
		<td headers="mb_list_mng" class="td_mng td_mng_s"><a href="configLangReg.php?mode=mod&idx=<?=$idx?>&<?=$qstr?>&page=<?=$page?>" class="btn btn_03">수정</a><a href="javascript:chkDel('<?=$idx?>')" class="btn btn_02">삭제</a>
		</td>
    </tr>
    <? 

		}
	?>   
	<? } else { ?>
	<tr>
		<td colspan="4" class="empty_table">자료가 없습니다.</td>
	</tr>
	<? } ?>
        </tbody>
    </table>
</div>

<div class="btn_fixed_top">
	<a href="configLangReg.php" id="config_add" class="btn btn_01">등록</a> 
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
