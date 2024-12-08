<?
header('Content-Type: application/json; charset=UTF-8');
	include "../lib/common.php";
	include "../lib/functionDB.php";  //공통 db함수
	
	
	$mem_Id = trim($memId);			//아이디
	$category = trim($category);		//카테고리 설정값 (기본은 설정값은 'all' 로 모든 카테고리를 보게 한다.)
	//$mem_Id = "shut7720@hanmail.net";

	if ($mem_Id != "" ) {  //아이디가 있을 경우
			$DB_con = db1();
			//가입중인 회원상태 확인.
			$memQuery = "SELECT idx from TB_MEMBERS  WHERE mem_Id = :mem_Id AND b_Disply = 'N' " ;
			$stmt = $DB_con->prepare($memQuery);
			$stmt->bindparam(":mem_Id",$mem_Id);
			$stmt->execute();
			$num = $stmt->rowCount();
			if($num < 1)  { //아닐경우
				$result = array("result" => "error", "errorMsg" => "아이디가 없습니다. 확인 후 다시 시도해주세요.");
			} else {
				while($row=$stmt->fetch(PDO::FETCH_ASSOC)) {

					$mIdx = $row['idx'];											// 고유번호

					//선택한 카테고리를 업데이트 해준다.
					$upQquery = "UPDATE TB_MEMBERS_CONFIG SET mem_Category = :mem_Category, last_Update = 'Category', update_Date = now() WHERE idx = :idx AND mem_Id = :mem_Id LIMIT 1";
					$upStmt = $DB_con->prepare($upQquery);
					$upStmt->bindparam(":idx",$mIdx);
					$upStmt->bindparam(":mem_Id",$mem_Id);
					$upStmt->bindparam(":mem_Category",$category);
					$upStmt->execute();
					$result = array("result" => "success");
				}
			}
	}else{
		$result = array("result" => "error");
	}


	dbClose($DB_con);
	$stmt = null;
	$upMStmt = null;
	$upStmt = null;
	$upStmt2 = null;
	$chktmt = null;
	$upStmt3 = null;
  echo json_encode($result); 

?>