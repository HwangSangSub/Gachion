<?
include "../lib/common.php";
include "../../lib/functionDB.php";

$base_url = $PHP_SELF;

$DB_con = db1();
if($findType == "code_Sub_Div_Name"){
	$findType = "code_Sub_Div";
	if($findword == "음식"){
		$findword = '1';
	}else if($findword == "음료"){
		$findword = '2';
	}else if($findword == "디저트"){
		$findword = '3';
	}else if($findword == "여행"){
		$findword = '4';
	}else if($findword == "오락"){
		$findword = '5';
	}else if($findword == "풍경"){
		$findword = '6';
	}else if($findword == "병원/약국" || $findword == "병원" || $findword == "약국"){
		$findword = '7';
	}else if($findword == "기타"){
		$findword = '8';
	}else{
	}
}


$sql_search=" WHERE use_Bit = '0' AND code_Div = 'placeicon' AND code <> '0' ";

if($findword != "")  {
	$sql_search .= " AND `{$findType}` LIKE '%{$findword}%' ";
}
$findType = trim($findType);
$findword = trim($findword);

$code_query = "
	SELECT *
	FROM TB_CONFIG_CODE
	{$sql_search}
	ORDER BY code_Sub_Div;
";
$code_stmt = $DB_con->prepare($code_query);
if($findword != "")  {
	$code_stmt->bindValue(":findType",$findType);		
	$code_stmt->bindValue(":findword",$findword );
}
$code_stmt->execute();
$code_numCnt = $code_stmt->rowCount();

if($findType == "code_Sub_Div"){
	$findType = "code_Sub_Div_Name";
	if($findword == "1"){
		$findword = '음식';
	}else if($findword == "2"){
		$findword = '음료';
	}else if($findword == "3"){
		$findword = '디저트';
	}else if($findword == "4"){
		$findword = '여행';
	}else if($findword == "5"){
		$findword = '오락';
	}else if($findword == "6"){
		$findword = '풍경';
	}else if($findword == "7"){
		$findword = '병원/약국';
	}else if($findword == "8"){
		$findword = '기타';
	}else{
	}
}
?>
<!doctype html>
<html lang="ko">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="imagetoolbar" content="no">
		<meta http-equiv="X-UA-Compatible" content="IE=Edge">
		<title>리얼플레이스_지점아이콘검색</title>
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
			$(document).ready(function() {
				$("#findword").focus();
			});
			function idSelect(code, code_Name, img_src){
				$("#place_Icon", opener.document).val(code); //jquery 이용
				$("#place_Icon_name", opener.document).val(code_Name); //jquery 이용
				$("#place_Icon_img", opener.document).attr("src", img_src); //jquery 이용
				self.close();
			}
		</script>
	</head>
	<body>
		<form id="fsearch" name="fsearch" class="local_sch01 local_sch" target="<?$_SERVER['PHP_SELF']?>" method="get" autocomplete="off">
			<label for="findType" class="sound_only">검색대상</label>
			<select name="findType" id="findType">
				<option value="code_Sub_Div_Name" <?if($findType=="code_Sub_Div_Name"){?>selected<?}?>>카테고리명</option>
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
						<th scope="col" id="code_Sub_Name">카테고리</th>
						<th scope="col" id="code_Name">코드명</th>
						<th scope="col" id="code_Img">코드이미지</th>
						<th scope="col" id="mb_list_date">관리</th>
					</tr>
				</thead>
				<tbody>
					<?
					if($code_numCnt > 0)   {
						$code_stmt->setFetchMode(PDO::FETCH_ASSOC);
						$idx = 1;
						while($code_row =$code_stmt->fetch()) {
							$code = $code_row['code'];
							$code_Sub_Div = $code_row['code_Sub_Div'];
							if($code_Sub_Div == '1'){
									$code_Sub_Name = "음식";
							}else if($code_Sub_Div == '2'){
									$code_Sub_Name = "음료";
							}else if($code_Sub_Div == '3'){
									$code_Sub_Name = "디저트";
							}else if($code_Sub_Div == '4'){
									$code_Sub_Name = "여행";
							}else if($code_Sub_Div == '5'){
									$code_Sub_Name = "오락";
							}else if($code_Sub_Div == '6'){
									$code_Sub_Name = "풍경";
							}else if($code_Sub_Div == '7'){
									$code_Sub_Name = "병원/약국";
							}else if($code_Sub_Div == '8'){
									$code_Sub_Name = "기타";
							}else{
							}
							$code_Name = $code_row['code_Name'];
							$code_On_Img = $code_row['code_On_Img'];
							$img_src = "/udev/admin/data/code_img/photo.php?id=".$code_On_Img;
					?>
					<tr class="<?=$bg?>">
						<td headers="idx"><?=$idx?></td>
						<td headers="code_Sub_Name"><?=$code_Sub_Name?></td>
						<td headers="code_Name"><?=$code_Name?></td>
						<td headers="code_Img"><img name="place_Icon_img" id="place_Icon_img" src="/udev/admin/data/code_img/photo.php?id=<? echo $code_On_Img?>" style="height:100px"></td>
						<td headers="mb_list_mng" class="td_mng td_mng_s">
							<a href="javascript:idSelect('<?=$code?>', '<?=$code_Name?>','<?=$img_src?>');" class="btn btn_03">선택</a>
						</td>
					</tr>
					<? 
						$idx++;
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