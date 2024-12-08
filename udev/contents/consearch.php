<?
include "../lib/common.php";
include "../../lib/functionDB.php";

$base_url = $PHP_SELF;

$DB_con = db1();

$sql_search=" WHERE delete_Bit = '0' AND open_Bit = '0'";

if($findword != "")  {
	$sql_search .= " AND `{$findType}` LIKE '%{$findword}%' ";
}
$findType = trim($findType);
$findword = trim($findword);

$con_query = "
	SELECT *
	FROM TB_CONTENTS
	{$sql_search}
	ORDER BY idx DESC;
";
$con_stmt = $DB_con->prepare($con_query);
if($findword != "")  {
	$con_stmt->bindValue(":findType",$findType);		
	$con_stmt->bindValue(":findword",$findword );
}
$con_stmt->execute();
$con_numCnt = $con_stmt->rowCount();

?>
<!doctype html>
<html lang="ko">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="imagetoolbar" content="no">
		<meta http-equiv="X-UA-Compatible" content="IE=Edge">
		<title>리얼플레이스_지도검색</title>
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
			function idSelect(conIdx, conName){
				$(opener.document).find('#area_Code option').remove();
				var areaCode = $("#area_Code_"+conIdx).val();
				var areaCodeArray = areaCode.split(",");
				for(var i=0;i<areaCodeArray.length;i++){
					$(opener.document).find('#area_Code').append("<option value='" + areaCodeArray[i] + "'>" + areaCodeArray[i] + "</option>"); 
				}
				$("#con_Idx", opener.document).val(conIdx); //jquery 이용
				$("#con_Name", opener.document).val(conName); //jquery 이용
				self.close();
			}
		</script>
	</head>
	<body>
		<form id="fsearch" name="fsearch" class="local_sch01 local_sch" target="<?$_SERVER['PHP_SELF']?>" method="get" autocomplete="off">
			<label for="findType" class="sound_only">검색대상</label>
			<select name="findType" id="findType">
				<option value="reg_Id" <?if($findType=="reg_Id"){?>selected<?}?>>아이디</option>
				<option value="con_Name" <?if($findType=="con_Name"){?>selected<?}?>>지도명</option>
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
						<th scope="col" id="con_Name">지도명</th>
						<th scope="col" id="con_RegId">등록자</th>
						<th scope="col" id="mb_list_date">관리</th>
					</tr>
				</thead>
				<tbody>
					<?
					if($con_numCnt > 0)   {
						$con_stmt->setFetchMode(PDO::FETCH_ASSOC);
						while($con_row =$con_stmt->fetch()) {
							$idx = $con_row['idx'];
							$con_Name = $con_row['con_Name'];
							$area_Code = $con_row['area_Code'];
							$reg_Id = $con_row['reg_Id'];
							$reg_Name = memNickInfo($reg_Id);				// 회원닉네임
					?>
					<tr class="<?=$bg?>">
						<input type="hidden" name="area_Code_<?=$idx?>" id="area_Code_<?=$idx?>" value="<?=$area_Code?>"/>
						<td headers="idx"><?=$idx?></td>
						<td headers="con_Name"><?=$con_Name?></td>
						<td headers="con_RegId"><?=$reg_Name?></td>
						<td headers="mb_list_mng" class="td_mng td_mng_s">
							<a href="javascript:idSelect('<?=$idx?>','<?=$con_Name?>');" class="btn btn_03">선택</a>
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