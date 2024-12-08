
/********************************
전체 삭제
********************************/
$("#bt_m_a_del").live("click", function(){
  var chk = $("input:checkbox[id='chk']").is(":checked");
  var message;	

	if( chk == false )
	{
	    message = "삭제할 게시물을 선택 하시기 바랍니다.";
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

			var action = "/taxiKing/udev/config/memPointManagerProc.php";
			$.ajax({
				type: "POST",
				url: action,
				data: 'mode=allDel&chk='+itemstr,
				success: function(response) {
				  alert("삭제되었습니다.");
				   $("form:first").submit();
				}
			});

		}

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
			var action = "/taxiKing/udev/config/memPointManagerProc.php";
			$.ajax({
				type: "POST",
				url: action,
				data: 'mode=allDel&chk='+idx,
				success: function(response) {
					if($.trim(response) == 'success') {
						alert("삭제되었습니다.");
						$("form:first").submit();
					}
					else if($.trim(response) == 'error') {
						alert("에러입니다. 관리자에게 문의해 주세요.");
						$("form:first").submit();
					}
				}
			});
	  }
	 //삭제끝
}


