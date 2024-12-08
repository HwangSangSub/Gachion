<?

	include "../../lib/common.php"; 

	//암호화할 때:  
    $password = trim($user_pw);

	$DB_con = db1();

	$query = "
			SELECT mem_Id, mem_Pwd, mem_Lv, mem_Nm, login_Cnt 
			FROM TB_MEMBERS  
			WHERE mem_Id = :mem_Id AND b_Disply = 'N' AND mem_Lv = 0" ;
	$stmt = $DB_con->prepare($query);
	$stmt->bindparam(":mem_Id",$user_id);
	$user_id = trim($user_id);
	$stmt->execute();
	$num = $stmt->rowCount();

	if($num < 1)  { //아닐경우
		echo "error";
	} else {

		while($row=$stmt->fetch(PDO::FETCH_ASSOC)) {

			$hash = $row['mem_Pwd'];
			$mem_SId = $row['mem_SId'];	           // 회원고유 아이디
			
			 if (password_verify($password, $hash)) { // 비밀번호가 일치하는지 비교합니다. 

				echo "success";  // 비밀번호가 맞음 

				$login_Cnt = $row['login_Cnt'];      // 로그인 횟수
				$login_Cnt = $login_Cnt + 1;

				# 마지막 로그인 시간을 업데이트 한다.
				$upQquery = "UPDATE TB_MEMBERS SET login_Date = now(), login_Cnt = :login_Cnt WHERE  mem_Id = :mem_Id  LIMIT 1";
				$upStmt = $DB_con->prepare($upQquery);
				$upStmt->bindparam(":mem_Id",$user_id);
				$upStmt->bindparam(":login_Cnt",$login_Cnt);
				$upStmt->execute();

				$mem_Id = $user_id;									   // 아이디
				$mem_Pwd = $row['mem_Pwd'];	           // 비밀번호
				$mem_Nm = $row['mem_Nm'];      // 닉네임
				$mem_Lv = $row['mem_Lv'];      // 등급

				setcookie("du_udev[id]", $mem_Id, false, "/");
				setcookie("du_udev[pw]", $mem_Pwd, false, "/");
				setcookie("du_udev[name]", $mem_Nm, false, "/");
				setcookie("du_udev[lv]", $mem_Lv, false, "/");
			
			} else { 
			  echo "error";  // 비밀번호가 틀림 
			} 
		}
	}

	dbClose($DB_con);
	$stmt = null;
	$upStmt = null;
	$upStmt2 = null;



?>
