<?
/*
* 프로그램				: 등록된 지점의 지역순 조회시 시구분 조회
* 페이지 설명			: 등록된 지점 목록을 보여줌(지도에 표시하기 위함)
* 파일명					: view_place_si.php
* 관련DB					: TB_CONTENTS, TB_PLACE
*/
include "../lib/common.php";
include "../lib/functionDB.php";

$con_Idx = trim($conIdx);						// 지도고유번호 
$con_chk_Bit = contentsChk($con_Idx);		// 지도고유번호 확인 
$DB_con = db1();
if($con_chk_Bit == "1") {						// 지점이 정상적으로 조회되는 경우에만 api 실행
	// 지점 배열등록
	$arr_query = "
		SELECT REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(SUBSTRING_INDEX(addr, ' ', case when addr like '%경기도%' then 2 else 1 end), '특별시', ''), '광역시', ''), '특별자치시', ''), '세종시', '세종'), '경상남도', '경남'), '경상북도', '경북'), '전라남도', '전남'), '전라북도', '전북'), '충청남도', '충남'), '충청북도', '충북'), '제주시', '제주'), '제주특별자치도', '제주'), '시', '')
			as si
		FROM TB_PLACE 
		WHERE (con_Idx = :con_Idx OR idx in (SELECT place_Idx FROM TB_MEMBERS_SHARE WHERE con_Idx = :con_Idx AND use_Bit = 'Y'))
			AND delete_Bit = '0'
        GROUP BY si
		ORDER BY case 
			when si like '%서울%' OR si like '%서울특별시%' then 1
			when si like '%부산%' OR si like '%부산광역시%' then 2
			when si like '%대구%' OR si like '%대구광역시%' then 3
			when si like '%인천%' OR si like '%인천광역시%' then 4
			when si like '%광주%' OR si like '%광주광역시%' then 5
			when si like '%대전%' OR si like '%대전광역시%' then 6
			when si like '%울산%' OR si like '%울산광역시%' then 7
			when si like '%세종%' OR si like '%세종특별자치시%' then 8
			when si like '%경기%' OR si like '%경기도%' then 9
			when si like '%강원%' OR si like '%강원도%' then 10
			when si like '%충청북도%' OR si like '%충북%' then 11
			when si like '%충청남도%' OR si like '%충남%' then 12
			when si like '%전라북도%' OR si like '%전북%' then 13
			when si like '%전라남도%' OR si like '%전남%' then 14
			when si like '%경상북도%' OR si like '%경북%' then 15
			when si like '%경상남도%' OR si like '%경남%' then 16
			when si like '%제주특별자치도%' OR si like '%제주%' then 17
			else 99 end;
	";
	$arr_stmt = $DB_con->prepare($arr_query);
	$arr_stmt->bindParam(":con_Idx", $con_Idx);
	$arr_stmt->execute();
	$silists = [];
	while($arr_row=$arr_stmt->fetch(PDO::FETCH_ASSOC)) {
		$si = $arr_row['si'];											// 시구분
		if($chk_idx == 0){
			$c_si = $si;
			$chk_idx = $chk_idx + 1;
			array_push($silists, $si);
		}
		if($si == $c_si){												// 같다면
		}else{																// 다르다면
			$c_si = $si;
			array_push($silists, $si);
		}
	}
	$si_Cnt = count($silists);
	$chkData = [];
	$chkData["result"] = "success";
	$chkData["si_lists"] = $silists;
	$output = str_replace('\\\/', '/', json_encode($chkData, JSON_UNESCAPED_UNICODE));
	echo  urldecode($output);
    dbClose($DB_con);
    $stmt = null;
} else { //빈값일 경우
	$result = array("result" => "error", "errorMsg" => "등록된 지도가 없음");
	echo json_encode($result, JSON_UNESCAPED_UNICODE); 
}
?>



