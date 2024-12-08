<?
include "../lib/common.php";
include "../../lib/functionDB.php";

$base_url = $PHP_SELF;

$DB_con = db1();

$sql_search=" WHERE b_Disply = 'N'";

if($findword != "")  {
	$sql_search .= " AND `{$findType}` LIKE '%{$findword}%' ";
}
$findType = trim($findType);
$findword = trim($findword);

$member_query = "
	SELECT *
	FROM TB_MEMBERS
	{$sql_search}
	ORDER BY idx DESC;
";
$member_stmt = $DB_con->prepare($member_query);
if($findword != "")  {
	$member_stmt->bindValue(":findType",$findType);		
	$member_stmt->bindValue(":findword",$findword );
}
$member_stmt->execute();
$member_numCnt = $member_stmt->rowCount();

?>
<!doctype html>
<html lang="ko">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="imagetoolbar" content="no">
		<meta http-equiv="X-UA-Compatible" content="IE=Edge">
		<title>리얼플레이스_아이디검색</title>
		<link rel="stylesheet" href="<?=DU_UDEV_DIR?>/common/css/admin.css">
		<link type="text/css" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/themes/base/jquery-ui.css" rel="stylesheet" />
		<!--[if lte IE 8]>
		<script src="<?=DU_UDEV_DIR?>/common/js/html5.js"></script>
		<![endif]-->
		<script src="<?=DU_UDEV_DIR?>/common/js/jquery-1.8.3.min.js"></script>
		<script src="<?=DU_UDEV_DIR?>/common/js/jquery.menu.js?ver=<?=rand();?>"></script>
		<script src="<?=DU_UDEV_DIR?>/common/js/common.js?ver=<?=rand();?>"></script>
		<script src="<?=DU_UDEV_DIR?>/common/js/wrest.js?ver=<?=rand();?>"></script>
		<script src="<?=DU_UDEV_DIR?>/common/js/placeholders.min.js"></script>
		<link rel="stylesheet" href="<?=DU_UDEV_DIR?>/common/js/font-awesome/css/font-awesome.min.css">
		<link type="text/css" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/themes/base/jquery-ui.css" rel="stylesheet" />
		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
		<script>
			function idSelect(memId){
				$("#reg_Id", opener.document).val(memId); //jquery 이용
				self.close();
			}
		</script>
	</head>
	<body>
		<form id="fsearch" name="fsearch" class="local_sch01 local_sch" target="<?$_SERVER['PHP_SELF']?>" method="get" autocomplete="off">
			<label for="findType" class="sound_only">검색대상</label>
			<select name="findType" id="findType">
				<option value="mem_Id" <?if($findType=="mem_Id"){?>selected<?}?>>아이디</option>
				<option value="mem_NickNm" <?if($findType=="mem_NickNm"){?>selected<?}?>>닉네임</option>
			</select>
			<label for="findword" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
			<input type="text" name="findword" id="findword" value="<?=$findword?>" class=" frm_input">
			<input type="submit" class="btn_submit" value="검색">
			<a href="<?=$base_url?>" class="btn btn_06">새로고침</a>
		</form>
		<div class="tbl_head01 tbl_wrap">
			<table>
				<thead>
					<tr>
						<th scope="col" id="idx">순번</th>
						<th scope="col" id="mem_NickNm">회원명</th>
						<th scope="col" id="mem_Id">회원아이디</th>
						<th scope="col" id="mb_list_date">관리</th>
					</tr>
				</thead>
				<tbody>
					<?
					if($member_numCnt > 0)   {
						$member_stmt->setFetchMode(PDO::FETCH_ASSOC);
						while($member_row =$member_stmt->fetch()) {
							$idx = $member_row['idx'];
							$mem_Id = $member_row['mem_Id'];
							$mem_NickNm = $member_row['mem_NickNm'];
					?>
					<tr class="<?=$bg?>">
						<td headers="idx"><?=$idx?></td>
						<td headers="mem_NickNm"><?=$mem_NickNm?></td>
						<td headers="mem_Id"><?=$mem_Id?></td>
						<td headers="mb_list_mng" class="td_mng td_mng_s">
							<a href="javascript:idSelect('<?=$mem_Id?>');" class="btn btn_03">선택</a>
						</td>
					</tr>
					<? 
						}
					?>   
					<? } else { ?>
					<tr>
						<td colspan="6" class="empty_table">자료가 없습니다.</td>
					</tr>
					<? } ?>
				</tbody>
			</table>
		</div>
	</body>
</html>
<?
dbClose($DB_con);
?>