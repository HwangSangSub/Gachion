<?

	include "../../lib/common.php"; 
	include "../../lib/alertLib.php";
	include "../../lib/thumbnail.lib.php";   //썸네일


	$DB_con = db1();
	
	if ($mode == "reg") {		// 추가일 경우
		
		$DATA["taxi_OrdPoint"]					= $taxi_OrdPoint;
		$DATA["taxi_Memo"]					= date("Y-m-d H:i:s")." ".addslashes($taxi_Memo);
		$DATA["taxi_Sign"]						= $taxi_Sign;
		$DATA["taxi_PState"]					= $taxi_PState;			//관리자직접 입력
		$DATA["reg_Date"]						= date("Y-m-d H:i:s");


		if($taxi_MemTeype == "ALL")
		{

			//회원상태 b_Disply=>'N'(가입) 인 상태의 회원만 캐시 추가
			$listQuery = "select * from TB_MEMBERS where b_Disply='N' ";
			$listStmt = $DB_con->prepare($listQuery);
			$listStmt->execute();
			
			while($row = $listStmt->fetch()) {
				//회원아이디
				$DATA["taxi_MemId"]					= $row['mem_Id'];

				//캐시입력 정보 입력
				$insQuery = " insert into TB_POINT_HISTORY set ";
				$i = 0;
				foreach($DATA as $key => $val)
				{
					if($i >0) $insQuery .= " , ";
					$insQuery .= $key ." = '".$val."' ";

					$i++;
				}				
				$DB_con->exec($insQuery);
				
				// 회원 캐시 정보
				$cashQuery = "select mem_Point from TB_MEMBERS_ETC where mem_Id='".$row['mem_Id']."' ";
				$cashStmt = $DB_con->prepare($cashQuery);
				$cashStmt->execute();		
				$cashRow = $cashStmt->fetch();
				$mem_Point = $cashRow['mem_Point'];
				if(!$mem_Point) $mem_Point = 0;
				if($taxi_Sign == "0")
				{
					$mem_points = $mem_Point  + $taxi_OrdPoint;
				}
				else
				{
					$mem_points = $mem_Point  - $taxi_OrdPoint;
				}

				// 회원정보 캐시 update
				$updateQuery = "update TB_MEMBERS_ETC set mem_Point = ".$mem_points." where mem_Id='".$row['mem_Id']."' ";
				$DB_con->exec($updateQuery);

				$insQuery="";
				$DATA["taxi_MemId"]="";
			}

		}
		else if($taxi_MemTeype =="level")
		{
			//레벨별 회원리스트구하기
			//$taxi_MemLevel
			//회원상태 b_Disply=>'N'(가입) 인 상태의 회원만 캐시 추가
			$listQuery = "select * from TB_MEMBERS where b_Disply='N' and mem_Lv='".$taxi_MemLevel."' ";
			$listStmt = $DB_con->prepare($listQuery);
			$listStmt->execute();
			
			while($row = $listStmt->fetch()) {
				//회원아이디
				$DATA["taxi_MemId"]					= $row['mem_Id'];

				//캐시입력 정보 입력
				$insQuery = " insert into TB_POINT_HISTORY set ";
				$i = 0;
				foreach($DATA as $key => $val)
				{
					if($i >0) $insQuery .= " , ";
					$insQuery .= $key ." = '".$val."' ";

					$i++;
				}				
				$DB_con->exec($insQuery);


				// 회원 캐시 정보
				$cashQuery = "select mem_Point from TB_MEMBERS_ETC where mem_Id='".$row['mem_Id']."' ";
				$cashStmt = $DB_con->prepare($cashQuery);
				$cashStmt->execute();		
				$cashRow = $cashStmt->fetch();
				$mem_Point = $cashRow['mem_Point'];
				if(!$mem_Point) $mem_Point = 0;
				if($taxi_Sign == "0")
				{
					$mem_points = $mem_Point  + $taxi_OrdPoint;
				}
				else
				{
					$mem_points = $mem_Point  - $taxi_OrdPoint;
				}

				// 회원정보 캐시 update
				$updateQuery = "update TB_MEMBERS_ETC set mem_Point = ".$mem_points." where mem_Id='".$row['mem_Id']."' ";
				$DB_con->exec($updateQuery);

				$insQuery="";
				$DATA["taxi_MemId"]="";
			}
		}
		else if($taxi_MemTeype =="pub")
		{
			//개별회원
			//taxi_MemId
			$DATA["taxi_MemId"]					= $taxi_MemId;

			//캐시입력 정보 입력
			$insQuery = " insert into TB_POINT_HISTORY set ";
			$i = 0;
			foreach($DATA as $key => $val)
			{
				if($i >0) $insQuery .= " , ";
				$insQuery .= $key ." = '".$val."' ";

				$i++;
			}
			//echo $insQuery;
			$DB_con->exec($insQuery);
			
			// 회원 캐시 정보
			$cashQuery = "select mem_Point from TB_MEMBERS_ETC where mem_Id='".$taxi_MemId."' ";
			$cashStmt = $DB_con->prepare($cashQuery);
			$cashStmt->execute();		
			$row = $cashStmt->fetch();
			$mem_Point = $row['mem_Point'];
			if(!$mem_Point) $mem_Point = 0;
			if($taxi_Sign == "0")
			{
				$mem_points = $mem_Point  + $taxi_OrdPoint;
			}
			else
			{
				$mem_points = $mem_Point  - $taxi_OrdPoint;
			}

			// 회원정보 캐시 update
			$updateQuery = "update TB_MEMBERS_ETC set mem_Point = ".$mem_points." where mem_Id='".$taxi_MemId."' ";
			//echo $updateQuery ;
			$DB_con->exec($updateQuery);

		}

		// 입력정보
		$preUrl = "pointList.php?page=$page&$qstr";
		$message = "reg";
		proc_msg($message, $preUrl);
	

	}
	
	dbClose($DB_con);
	?>