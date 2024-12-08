/********************************
전체 삭제
********************************/
$("#bt_m_a_del").live("click", function(){
  var chk = $("input:checkbox[id='chk']").is(":checked");
  var message;	

	if( chk == false )
	{
	    message = "삭제할 게시판을 선택 하시기 바랍니다.";
		alert(message);

	} else { //삭제

		//삭제시작
	  if(!confirm("한번 삭제한 자료는 복구할 방법이 없습니다.\n\n정말 삭제하시겠습니까?"))   {
		   return;                                      
	  }  else {

			var idlist = [];
			var itemstr ="";
			$('input[name=chk]:checked').each(function(){idlist.push(this.value)});
		 
			$.each(idlist,function(index, item){
			itemstr += item + '/';

			});

			var action = "/taxiKing/udev/boardM/boardManagerProc.php";
			$.ajax({
				type: "POST",
				url: action,
				data: 'mode=del&chk='+itemstr,
				success: function(response) {
					if(response == 'success') {
						alert("삭제되었습니다.");
						$("form:first").submit();
					} else if(response == 'error') {
						alert("에러입니다. 관리자에게 문의해 주세요.");
						$("form:first").submit();
					}
				}
			});

		}
	//삭제끝

	}

});


/********************************
삭제 
********************************/
function chkDel(idx){

		//삭제시작
	  if(!confirm("한번 삭제한 자료는 복구할 방법이 없습니다.\n\n정말 삭제하시겠습니까?"))   {
		   return;                                      
	  }  else {
			var action = "/taxiKing/udev/boardM/boardManagerProc.php";
			$.ajax({
				type: "POST",
				url: action,
				data: 'mode=del&chk='+idx,
				success: function(response) {
				//	alert(response);
					if( $.trim(response) == 'success') {
						alert("삭제되었습니다.");
						$("form:first").submit();
					} else {
						alert("에러입니다. 관리자에게 문의해 주세요.");
						$("form:first").submit();
					}
				}
			});
	  }
	 //삭제끝
}

/********************************
게시판 복사 
********************************/
function chkCopy(idx){

	//복사시작
	  if(!confirm("게시판을 복사 하시겠습니까?"))   {
		   return;                                      
	  }  else {
			var action = "/taxiKing/udev/boardM/boardManagerProc.php";
			$.ajax({
				type: "POST",
				url: action,
				data: 'mode=copy&chk='+idx,
				success: function(response) {
					if( $.trim(response) == 'success') {
						alert("복사되었습니다.");
						$("form:first").submit();
					}	else {
						alert("에러입니다. 관리자에게 문의해 주세요.");
						$("form:first").submit();
					}
				}
			});
	  }
	 //복사끝

}

/********************************
삭제 불가
********************************/
function chkNDel(idx){
	alert("해당 게시판의 게시글이 존재합니다. 게시글을 삭제한 후 삭제해 주세요!");
}







