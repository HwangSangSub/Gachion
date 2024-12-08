<?
	include "../common/inc/inc_header.php";  //헤더 
	
	$DB_con = db1();
	$titNm = "회원검색";


	
	$view_id="등록된 회원 아이디가 없습니다.";

	if($_POST['mode'] == "search")
	{
		if($_POST['mem_uid'])
		{
			$listQuery = "select * from TB_MEMBERS where mem_Id like '%".$_POST['mem_uid']."%'";
			$listStmt = $DB_con->prepare($listQuery);
			$listStmt->execute();
			$numCnt = $listStmt->rowCount();

			$view_id = "";

			if($numCnt > 0)
			{
				while($row = $listStmt->fetch()) {
					$view_id .= "<span onclick=\"javascript:partents_data('".$row['mem_Id']."');\">".$row['mem_NickNm'].'('.$row['mem_Id'].')</span><br>';
				}
			}
			else
			{
				$view_id="등록된 회원 아이디가 없습니다.";
			}
		}
	}
?>
<script>
function partents_data(uid)
{
	opener.setMemId(uid);
	window.close(); 
}
</script>
<style>
#container {

    padding: 0 0 0 220px;
    margin-top: 50px;
    height: 100%;
    background: #fff;
    min-width: 400px;

}
</style>

<div id="wrapper">

    <div id="container" class="container-small" style="width:100%;padding:0px ;mix-width:400px;">

        <h1 id="container_title" style="padding-left:10px;top:0px;"><?=$titNm?></h1>
        <div class="container_wr">
			<form name="fmember" id="fmember" action="./pointIdSearch.php" onsubmit="return fmember_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off">
			<input type="hidden" name="mode" id="mode" value="search">
			<div class="tbl_frm01 tbl_wrap" style="">
				<table>
				<caption><?=$titNm?></caption>
				<tbody>
				<tr height="45">
					<th align="center">회원아이디</th>
					<td class="objects">
						<input type="text" class="frm_input" name="mem_uid" id="mem_uid" style="width:60%;" placeholder="검색할 회원아이디">&nbsp;<input type="submit" value="검색" class="btn_submit btn" accesskey='s'>
					</td>
				</tr>		
				<tr height="45">
					<th align="center">검색된 회원아이디</th>
					<td class="objects">
						<div id="view_ids"><?= $view_id ?></div>
					</td>
				</tr>		
				<tr>
					<td colspan="2"></td>
				</tr>
				</table>
				
			

			
			</form>


			<script>
			function fmember_submit(f)
			{
				if (!f.mem_uid.value) {
					alert('검색할 회원아이디를 입력해주세요..');
					return false;
				}

				return true;
			}
			</script>
		</div>
	</div>   
</div>   	



<?
	dbClose($DB_con);
	$stmt = null;
	$meInfoStmt = null;
	$mEtcStmt = null;
	$mstmt = null;

	 
?>
