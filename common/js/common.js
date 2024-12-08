//IE7~IE8 trim() 함수 만들어 주기
if (typeof String.prototype.trim !== 'function') {
	String.prototype.trim = function() {
		return this.replace(/^\s+|\s+$/g, '');
	}
}

function setPng24(obj) {
	obj.width = obj.height = 1;
	obj.className = obj.className.replace(/\bpng24\b/i, '');
	obj.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='"
			+ obj.src + "',sizingMethod='image');"
	obj.src = '';
	return '';
}

function getScrollTop() {

	var top1 = document.body.scrollTop;
	var top2 = $("html, body").scrollTop();

	if (top1 < 1) {
		return top2;
	} else {
		return top1;
	}
}

function numberFormat(num) {
	if (num < 10) {
		num = "0" + num;
	} else {
		num = num;
	}

	return num;
}

function getWeek(date) {
	var d = date.split("-");

	newDate = new Date(d[0], eval(d[1]) - 1, d[2]);

	var week = newDate.getDay();
	var weekStr = "일|월|화|수|목|금|토";
	var weeks = weekStr.split("|");

	return weeks[week];
}

Date.prototype.format = function(f) {

	if (!this.valueOf())
		return " ";

	var weekName = [ "일요일", "월요일", "화요일", "수요일", "목요일", "금요일", "토요일" ];
	var d = this;

	return f.replace(/(yyyy|yy|MM|dd|E|hh|mm|ss|a\/p)/gi, function($1) {

		switch ($1) {

		case "yyyy":
			return d.getFullYear();

		case "yy":
			return (d.getFullYear() % 1000).zf(2);

		case "MM":
			return (d.getMonth() + 1).zf(2);

		case "dd":
			return d.getDate().zf(2);

		case "E":
			return weekName[d.getDay()];

		case "HH":
			return d.getHours().zf(2);

		case "hh":
			return ((h = d.getHours() % 12) ? h : 12).zf(2);

		case "mm":
			return d.getMinutes().zf(2);

		case "ss":
			return d.getSeconds().zf(2);

		case "a/p":
			return d.getHours() < 12 ? "오전" : "오후";

		default:
			return $1;

		}

	});

};



Number.prototype.format = function(){
    if(this==0) return 0;
 
    var reg = /(^[+-]?\d+)(\d{3})/;
    var n = (this + '');
 
    while (reg.test(n)) n = n.replace(reg, '$1' + ',' + '$2');
 
    return n;
};
 
// 문자열 타입에서 쓸 수 있도록 format() 함수 추가
String.prototype.format = function(){
    var num = parseFloat(this);
    if( isNaN(num) ) return "0";
 
    return num.format();
};


//리스트 전체 체크 함수
function checkAll(obj, className) {
	var checked = $(obj).is(":checked");
	$("input[type=checkbox]." + className).prop("checked", checked);
}


//jquery datepicker
$(function() {
	$("input.date").datepicker({
		dateFormat: 'yy-mm-dd',
		monthNamesShort: ['01 월','02 월','03 월','04 월','05 월','06 월','07 월','08 월','09 월','10 월','11 월','12 월'],
		dayNamesMin: ['일','월','화','수','목','금','토'],
		changeMonth : true,
		changeYear : true,
		showMonthAfterYear : true
	});
});


//pupup
function popup() {

	var sw=800;    //띄울 창의 넓이
	var sh=600;    //띄울 창의 높이

	var url = arguments[0];
	var width = arguments[1];
	var height = arguments[2];


	var cw=screen.availWidth;     //화면 넓이
	var ch=screen.availHeight;    //화면 높이


	 if(width > 0) {
		var sw=width;
	 }

	 if(height > 0) {
		var sh=height;
	 }


	 var ml=(cw-sw)/2;        //가운데 띄우기위한 창의 x위치
	 var mt=(ch-sh)/2;         //가운데 띄우기위한 창의 y위치

	 var urlList = url.split("/");
	 var page = urlList[urlList.length-1].split(".");

	window.open(url, page[0], 'toolbar=no, status=no, directories=no, scrollbars=yes, location=no, resizable=yes, border=0, menubar=no, left=' + ml + ', top=' + mt + ', width=' + sw + ', height=' + sh);
}













//place holder
(function ($) {
	$.fn.placeHolder = function() {
		var str = navigator.userAgent.toLowerCase();
		return this.each(function(index) {
			if(str.indexOf('trident/4.0') != -1 || str.indexOf('trident/5.0') != -1 || str.indexOf('msie 7.0') != -1 || str.indexOf('msie 6.0') != -1) {
				var message = $(this).attr("placeholder");
				var type = $(this).attr("type");

				if($(this).val().length < 1) {
					if(type == "password") {
						$(this).attr("type", "text");
					}

					$(this).val(message);
				}

				$(this).focusin(function() {
					if($(this).val() == message) {
						$(this).val("");
						if(type == "password") {
							$(this).attr("type", "password");
						}
					}
				});

				$(this).focusout(function() {
					if($(this).val().length < 1) {
						if(type == "password") {
							$(this).attr("type", "text");
						}

						$(this).val(message);
					}
				});
			}
		});

	};

	$.fn.removeHoler = function() {
		var str = navigator.userAgent.toLowerCase();
		return this.each(function(index) {
			if(str.indexOf('trident/4.0') != -1 || str.indexOf('trident/5.0') != -1 || str.indexOf('msie 7.0') != -1 || str.indexOf('msie 6.0') != -1) {
				var message = $(this).attr("placeholder");
				if($(this).val() == message) {
					$(this).val("");
				}
			}
		});
	};

	$.fn.resetHolder = function() {
		var str = navigator.userAgent.toLowerCase();
		return this.each(function(index) {
			if(str.indexOf('trident/4.0') != -1 || str.indexOf('trident/5.0') != -1 || str.indexOf('msie 7.0') != -1 || str.indexOf('msie 6.0') != -1) {
				var message = $(this).attr("placeholder");
				var type = $(this).attr("type");

				if($(this).val().length < 1) {
					if(type == "password") {
						$(this).attr("type", "text");
					}

					$(this).val(message);
				}
			}
		});
	};
}) (jQuery);