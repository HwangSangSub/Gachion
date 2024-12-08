<?
	include "../../lib/functionDB.php";
	$menu = "2";
	$smenu = "1";

	include "../common/inc/inc_header.php";  //헤더 

	$base_url = $PHP_SELF;
	

	$sql_search=" WHERE 1=1 AND delete_Bit = '0'";

	if ($fr_date != "" || $to_date != "" ) {
		$sql_search.=" AND (DATE_FORMAT(reg_Date,'%Y-%m-%d') >= :fr_date AND DATE_FORMAT(reg_Date,'%Y-%m-%d') <= :to_date)";
	}
		
	if($findword != "")  {
	    if ($findType == "reg_Id") {
	        $sql_search .= " AND reg_Id LIKE :findword ";
	    }else if($findType == "mem_Tel"){
			$sql_search .= " AND mem_Tel LIKE :findword ";
	    }
	}

	if ($od_status != "" ) {
		if($od_status == "0"){
	        $sql_search .= " AND admin_Bit IN (0,1) ";
		}else if($od_status == "1"){
	        $sql_search .= " AND admin_Bit IN (0) ";
		}else if($od_status == "2"){
	        $sql_search .= " AND admin_Bit IN (1) ";
		}
	}	

	$DB_con = db1();

	//전체 카운트
	$cntQuery = "";
	$cntQuery = "SELECT COUNT(idx)  AS cntRow FROM TB_CONTENTS {$sql_search} " ;
	$cntStmt = $DB_con->prepare($cntQuery);

	if ($fr_date != "" || $to_date != "" ) {
	    $cntStmt->bindValue(":fr_date",$fr_date);
	    $cntStmt->bindValue(":to_date",$to_date);
	}

	if($findword != "")  {
	    $cntStmt->bindValue(':findword','%'.trim($findword).'%');
	}

	$cntStmt->execute();
	$row = $cntStmt->fetch(PDO::FETCH_ASSOC);
	$totalCnt = $row['cntRow'];


	if($rows == ''){
		$rows = '10';
	}
	$total_page  = ceil($totalCnt / $rows);  // 전체 페이지 계산
	if ($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
	$from_record = ($page - 1) * $rows; // 시작 열을 구함


	if (!$sort1)	{
		$sort1  = "mod_Date";
		$sort2 = "DESC";
	}

	$sql_order = "order by {$sort1} {$sort2}";

	//목록
	$query = "";
	$query = " SELECT idx, con_Name, category, open_Bit, admin_Bit, reg_Id, reg_Date, mod_Date" ;
	$query .= " FROM TB_CONTENTS ";
	$query .= " {$sql_search} {$sql_order} limit  {$from_record}, {$rows} ;";
	// echo $query."<BR>";
	// exit;

	$stmt = $DB_con->prepare($query);

	if ($fr_date != "" || $to_date != "" ) {
	    $stmt->bindValue(":fr_date",$fr_date);
	    $stmt->bindValue(":to_date",$to_date);
	}

	if($findword != "")  {
	    $stmt->bindValue(':findword','%'.trim($findword).'%');
	}
	
	$stmt->execute();
	$numCnt = $stmt->rowCount();

	$qstr = "fr_date=".urlencode($fr_date)."&amp;to_date=".urlencode($to_date)."&amp;findType=".urlencode($findType)."&amp;findOs=".urlencode($findOs)."&amp;findMir=".urlencode($findMir)."&amp;rows=".urlencode($rows)."&amp;findword=".urlencode($findword)."&amp;od_status=".urlencode($od_status);
	
	include "../common/inc/inc_gnb.php";		//헤더 
	include "../common/inc/inc_menu.php";		//메뉴 
	include "../common/inc/inc_mir.php";		//미르페이 
?>
<script type="text/javascript" src="<?=DU_UDEV_DIR?>/member/js/member.js"></script>
<style>
	.c04_on{background-color:#f24c27;border:1px solid #f24c27;color:#fff;}
</style>
<div id="wrapper">
    <div id="container" class="">
        <div class="container_wr">
        <h1 id="container_title">지도관리</h1>

		<div class="local_ov01 local_ov">
			<span class="btn_ov01"><span class="ov_txt">총 지도 수 </span><span class="ov_num"><?=number_format($totalCnt);?>개 </span>&nbsp;
			<!--
			<span class="btn_ov01"> <span class="ov_txt">탈퇴  </span><span class="ov_num"><?=number_format($leave_count);?>명</span>
			-->
		</div>


		<form class="local_sch03 local_sch"  autocomplete="off">

		<div>
			<strong>리스트출력</strong>
			<select id="rows" name="rows" onchange="$('.local_sch').submit();">
				<option value="10" <? if ( $rows == "10") { ?>selected="selected"<? } ?>>10개 씩 보기</option>
				<option value="15" <? if ( $rows == "15" ) { ?>selected="selected"<? } ?>>15개 씩 보기</option>
				<option value="20" <? if ( $rows == "20" ) { ?>selected="selected"<? } ?>>20개 씩 보기</option>
			</select>
		</div>
		
		<div>
    		<strong>분류</strong>
    		<select name="findType" id="findType">
    			<option value="reg_Id" <?if($findType=="reg_Id"){?>selected<?}?>>등록자</option>
    			<!--<option value="mem_Tel" <?if($findType=="mem_Tel"){?>selected<?}?>>연락처</option>-->
    		</select>
    		<label for="findword" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
    		<input type="text" name="findword" id="findword" value="<?=$findword?>" class=" frm_input">
		</div>
	
        <div>
            <strong>관리자공개여부</strong>
        	<span class="bg <? if ($od_status == "") { ?>all_on<? } ?>">
            <input type="radio" name="od_status" value="" id="od_status_all" <?php echo get_checked($od_status, ''); ?>>
            <label for="od_status_all">전체</label>
        	</span>
        	<span class="bg <? if ($od_status == "1") { ?>c01_on<? } ?>">
            <input type="radio" name="od_status" value="1" id="od_status_matchS" <?php echo get_checked($od_status, '1'); ?>>
            <label for="od_status_matchS">공개</label>
        	</span>
        	<span class="bg <? if ($od_status == "2") { ?>c02_on<? } ?>">
            <input type="radio" name="od_status" value="2" id="od_status_matchR" <?php echo get_checked($od_status, '2'); ?>>
            <label for="od_status_matchR">비공개</label>
        	</span>
        </div>
        	
		<div class="sch_last">
			<strong>가입일</strong>
			<input type="text" name="fr_date" id="fr_date" value="<?=$fr_date?>" class="frm_input" size="11" maxlength="10">
			<label for="fr_date" class="sound_only">시작일</label>
			~
			<input type="text" name="to_date" id="to_date" value="<?=$to_date?>"  class="frm_input" size="11" maxlength="10">
			<label for="to_date" class="sound_only">종료일</label>
			<input type="submit" value="검색" class="btn_submit">

			<a href="<?=$base_url?>" class="btn btn_06">새로고침</a>
		</div>
		</form>

<div class="local_desc01 local_desc">
    <p>
		빨간색 표는 관리자패널티로 인한 비공개 처리된 지도입니다.
    </p>
</div>

<nav class="pg_wrap">
	<?=get_apaging($rows, $page, $total_page, "$_SERVER[PHP_SELF]?$qstr"); ?>
</nav>

<form name="fmemberlist" id="fmemberlist"  method="post" autocomplete="off">

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption>지도관리 목록</caption>
    <thead>

	<!-- 아이디, 이름, 등급, 휴대폰번호, 가입일 -->
    <tr>
        <th scope="col" id="mb_list_chk" >
            <label for="chkall" class="sound_only">지도 전체</label>
            <input type="checkbox" name="chkall" class="chkc" id="chkAll" onclick="check_all(this.form)">
        </th>
		<th scope="col" id="mb_list_idx">순번</th>
        <th scope="col" id="mb_list_id">지도명</th>
		<th scope="col" id="mb_list_open">카테고리</th>
        <th scope="col" id="mb_list_mailr">공개여부</th>
        <th scope="col" id="mb_list_mailr">등록자</th>		
        <th scope="col" id="mb_list_mailr">최근수정일</th>
		<th scope="col" id="mb_list_mng"  class="last_cell">관리</th>
    </tr>
    </thead>
    <tbody>

    <?

	if($numCnt > 0)   {

		$stmt->setFetchMode(PDO::FETCH_ASSOC);

		while($row =$stmt->fetch()) {
			$from_record++;
			$idx = $row['idx'];
			$con_Name = $row['con_Name'];
			if($row['category'] == "1") { 
				$c_Disply = "음식"; 
			} else if($row['category'] == "2") { 
				$c_Disply = "음료"; 
			} else if($row['category'] == "3") { 
				$c_Disply = "디저트"; 
			} else if($row['category'] == "4") { 
				$c_Disply = "여행"; 
			} else if($row['category'] == "5") { 
				$c_Disply = "오락"; 
			} else if($row['category'] == "6") { 
				$c_Disply = "풍경"; 
			} else if($row['category'] == "7") { 
				$c_Disply = "병원/약국"; 
			} else if($row['category'] == "8") { 
				$c_Disply = "기타"; 
			}


			if($row['open_Bit'] == "0") { 
				$o_Disply = "전체공개"; 
			} else if($row['open_Bit'] == "1") { 
				$o_Disply = "비공개"; 
			}
			
			$admin_Bit = $row['admin_Bit'];

			$reg_Id = $row['reg_Id'];
			$reg_Nname = memNickInfo($reg_Id);				// 회원닉네임
			if($reg_Nname == ''){
				$reg_Nname = "";
			}

			$reg_Date = $row['reg_Date'];
			if($reg_Date != ""){
				$regDate = substr($reg_Date,0,10)."<br>(".substr($reg_Date,11,5).")";
			}else{
				$regDate = "-";
			}
			$mod_Date = $row['mod_Date'];
			if($mod_Date != ""){
				$modDate = substr($mod_Date,0,10)."<br>(".substr($mod_Date,11,5).")";
			}else{
				$modDate = "-";
			}
    ?>

    <tr class="bg <? if ($admin_Bit == "1") { ?>c04_on<? } ?>">
        <td headers="mb_list_chk" class="td_chk" >
            <input type="hidden" name="contents[<?=$idx?>]" id="contents_<?=$idx?>" value="<?=$idx?>" >
            <input type="checkbox" id="chk_<?=$idx?>" class="chk" name="chk[]" value="<?=$idx?>">
        </td>
        <td headers="mb_list_idx" class="td_idx"><?=$from_record?></td>
        <td headers="mb_list_id"><?=$con_Name?></td>
		<td headers="mb_list_open" class="td_name td_mng_s"><?=$c_Disply?></td>
        <td headers="mb_list_lastcall" class="td_date"><?=$o_Disply?></td>
        <td headers="mb_list_lastcall" class="td_date"><?=$reg_Nname?></td>
        <td headers="mb_list_lastcall" class="td_date"><?=$modDate?></td>
		<td headers="mb_list_mng" class="td_mng td_mng_s">
			<a href="reg_contents.php?mode=mod&idx=<?=$idx?>&<?=$qstr?>&page=<?=$page?>" class="btn btn_03">상세</a>
		</td>
    </tr>
    <? 

		}
	?>   
	<? } else { ?>
	<tr>
		<td colspan="8" class="empty_table">자료가 없습니다.</td>
	</tr>
	<? } ?>
        </tbody>
    </table>
</div>

<div class="btn_fixed_top">
	<a href="reg_contents.php" id="config_add" class="btn btn_01">등록</a> 
</div>

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
	$mcntStmt = null;
	$mstmt = null;

	 include "../common/inc/inc_footer.php";  //푸터 
	 
?>
