//CasperJS 객체 생성 
var casper = require('casper').create(); 

// CasperJS처리 시작 
casper.start(); 

// 화면 사이즈 설정 
casper.viewport(540, 360); 

// UserAgent 설정 
casper.userAgent('User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.157 Safari/537.36'); 

// 강아지로 검색 
var text = encodeURIComponent("강아지"); 
//casper.open('https://places.gachita.kr/contents/map_img/contents/map_img_save.php?idx=309' + text); 
casper.open('https://www.youtube.com/watch?v=6wG4lSB76Q0'); 

// 화면 캡쳐---- (¦4) 
casper.wait(3000, function () {
	casper.then(function(){ 
		this.capture('1.png',{ 
			top:0, left:0, width: 540, height: 360 
		}); 
	}); 
});
/*
setTimeout(function() {
	casper.then(function(){ 
		this.capture('309.png',{ 
			top:0, left:0, width: 540, height: 360 
		}); 
	}); 
}, 3000);
*/
// 실행개시 
casper.run();