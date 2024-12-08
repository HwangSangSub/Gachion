<?
	$menu = "2";
	$smenu = "2";

	include "../common/inc/inc_header.php";  //헤더 

	$base_url = $PHP_SELF;
	

	$sql_search=" WHERE 1=1 ";

	if ($fr_date != "" || $to_date != "" ) {
		$sql_search.=" AND (DATE_FORMAT(reg_Date,'%Y-%m-%d') >= :fr_date AND DATE_FORMAT(reg_Date,'%Y-%m-%d') <= :to_date)";
	}
		
	if($findword != "")  {
	    if ($findType == "place_Name") {
	        $sql_search .= " AND place_Name LIKE :findword ";
	    } else if ($findType == "place_Reg") {
	        $sql_search .= " AND reg_Id LIKE :findword ";
	    } else{
	        $sql_search .= "";
	    }
	}
	if($findword1 != "")  {
		if($findType == "place_Cate"){
			$sql_search .= " AND category LIKE :findword1 ";
	    }
	}
	

	$DB_con = db1();

	//전체 카운트
	$cntQuery = "";
	$cntQuery = "SELECT COUNT(idx)  AS cntRow FROM TB_PLACE {$sql_search} " ;
	$cntStmt = $DB_con->prepare($cntQuery);

	if ($fr_date != "" || $to_date != "" ) {
	    $cntStmt->bindValue(":fr_date",$fr_date);
	    $cntStmt->bindValue(":to_date",$to_date);
	}

	if($findword != "")  {
	    $cntStmt->bindValue(':findword','%'.trim($findword).'%');
	}
	if($findword1 != "")  {
	    $cntStmt->bindValue(':findword1','%'.trim($findword1).'%');
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
	$query = " SELECT idx, con_Idx, area_Code, category, place_Name, memo, smemo, tel, otime_Day, otime_Week, img, like_Cnt, share_Cnt, addr, lng, lat, reg_Id, reg_Date" ;
	$query .= " FROM TB_PLACE ";
	$query .= " {$sql_search} {$sql_order} limit  {$from_record}, {$rows} ;";
	//echo $query."<BR>";
	//exit;

	$stmt = $DB_con->prepare($query);

	if ($fr_date != "" || $to_date != "" ) {
	    $stmt->bindValue(":fr_date",$fr_date);
	    $stmt->bindValue(":to_date",$to_date);
	}

	if($findword != "")  {
	    $stmt->bindValue(':findword','%'.trim($findword).'%');
	}
	if($findword1 != "")  {
	    $stmt->bindValue(':findword1','%'.trim($findword1).'%');
	}
	
	$stmt->execute();
	$numCnt = $stmt->rowCount();

	$qstr = "fr_date=".urlencode($fr_date)."&amp;to_date=".urlencode($to_date)."&amp;findType=".urlencode($findType)."&amp;findOs=".urlencode($findOs)."&amp;findMir=".urlencode($findMir)."&amp;rows=".urlencode($rows)."&amp;findword=".urlencode($findword)."&amp;findword1=".urlencode($findword1);
	
	include "../common/inc/inc_gnb.php";		//헤더 
	include "../common/inc/inc_menu.php";		//메뉴 
?>
<script type="text/javascript" src="<?=DU_UDEV_DIR?>/member/js/member.js"></script>

<div id="wrapper">
    <div id="container" class="">
        <div class="container_wr">
        <h1 id="container_title">지점관리</h1>

		<div class="local_ov01 local_ov">
			<span class="btn_ov01"><span class="ov_txt">총 등록된 지점 수 </span><span class="ov_num"><?=number_format($totalCnt);?>개 </span>&nbsp;
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
			<div style="float:left;">
				<strong>분류</strong>
				<select name="findType" id="findType" onChange="findtype(this.value);">
					<option value="place_Name" <?if($findType=="place_Name"){?>selected<?}?>>지점이름</option>
					<option value="place_Reg" <?if($findType=="place_Reg"){?>selected<?}?>>등록자</option>
					<option value="place_Cate" <?if($findType=="place_Cate"){?>selected<?}?>>카테고리</option>
				</select>
			</div>
			<div id="find_normal" style="float:left;<?if($findType == "place_Cate"){?>display:none;<?}else{?>display:block;<?}?>">
				<label for="findword" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
				<input type="text" name="findword" id="findword" value="<?=$findword?>" class=" frm_input">
			</div>
			<div id="find_category" style="float:left; <?if($findType == "place_Cate"){?>display:block;<?}else{?>display:none;<?}?>">
				<label for="findword1" class="sound_only">카테고리<strong class="sound_only">선택 필수</strong></label>
				<select name="findword1" id="findword1">
					<option value="1" <?if($findword1=="1"){?>selected<?}?>>음식</option>
					<option value="2" <?if($findword1=="2"){?>selected<?}?>>음료</option>
					<option value="3" <?if($findword1=="3"){?>selected<?}?>>디저트</option>
					<option value="4" <?if($findword1=="4"){?>selected<?}?>>여행</option>
					<option value="5" <?if($findword1=="5"){?>selected<?}?>>오락</option>
					<option value="6" <?if($findword1=="6"){?>selected<?}?>>풍경</option>
					<option value="7" <?if($findword1=="7"){?>selected<?}?>>병원/약국</option>
					<option value="8" <?if($findword1=="8"){?>selected<?}?>>기타</option>
				</select>
			</div>
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
		상세보기 시 접속가능기간을 연장 할 수 있습니다.
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
        <th scope="col" id="mb_list_mailr">소속지도</th>
        <th scope="col" id="mb_list_id">지점명</th>
		<th scope="col" id="mb_list_open">카테고리</th>
        <th scope="col" id="mb_list_mailr">좋아요수</th>
        <th scope="col" id="mb_list_mailr">공유수</th>
        <th scope="col" id="mb_list_mailr">등록자</th>		
        <th scope="col" id="mb_list_mailr">등록일</th>
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
			$con_Idx = $row['con_Idx'];
			$chk_c_query = "SELECT idx, con_Name
			FROM TB_CONTENTS
			WHERE idx = :idx
			";
			$chk_c_stmt = $DB_con->prepare($chk_c_query);
			$chk_c_stmt->bindValue(":idx",$con_Idx);
			$chk_c_stmt->execute();
			$chk_c_row =$chk_c_stmt->fetch();
			$con_Name = $chk_c_row['con_Name'];

			$place_Name = $row['place_Name'];
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

			$like_Cnt = $row['like_Cnt'];
			$share_Cnt = $row['share_Cnt'];
			$reg_Id = $row['reg_Id'];

			$reg_Date = $row['reg_Date'];
			if($reg_Date != ""){
				$regDate = substr($reg_Date,0,10)."<br>(".substr($reg_Date,11,5).")";
			}else{
				$regDate = "-";
			}
    ?>

    <tr class="<?=$bg?>">
        <td headers="mb_list_chk" class="td_chk" >
            <input type="hidden" name="contents[<?=$idx?>]" id="contents_<?=$idx?>" value="<?=$idx?>" >
            <input type="checkbox" id="chk_<?=$idx?>" class="chk" name="chk[]" value="<?=$idx?>">
        </td>
        <td headers="mb_list_idx" class="td_idx"><?=$from_record?></td>
        <td headers="mb_list_id"><a href="detail_place.php?idx=<?=$con_Idx?>"><?=$con_Name?></a></td>
		<td headers="mb_list_open" class="td_name td_mng_s"><?=$place_Name?></td>
        <td headers="mb_list_lastcall" class="td_date"><?=$c_Disply?></td>
		<td headers="mb_list_lastcall" class="td_date"><?=$like_Cnt?></td>
		<td headers="mb_list_lastcall" class="td_date"><?=$share_Cnt?></td>
        <td headers="mb_list_lastcall" class="td_date"><?=$reg_Id?></td>
        <td headers="mb_list_lastcall" class="td_date"><?=$regDate?></td>
		<td headers="mb_list_mng" class="td_mng td_mng_s">
			<a href="reg_place.php?mode=mod&idx=<?=$idx?>&<?=$qstr?>&page=<?=$page?>" class="btn btn_03">상세</a>
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
	<a href="reg_place.php" id="config_add" class="btn btn_01">등록</a> 
</div>

</form>
<nav class="pg_wrap">
	<?=get_apaging($rows, $page, $total_page, "$_SERVER[PHP_SELF]?$qstr"); ?>
</nav>

<script>
	$(function(){
		$("#fr_date, #to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });
	});
	function findtype(value){
		if(value == "place_Cate"){
			$("#find_normal").hide();
			$("#find_category").show();
		}else{
			$("#find_normal").show();
			$("#find_category").hide();
		}
	}

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
