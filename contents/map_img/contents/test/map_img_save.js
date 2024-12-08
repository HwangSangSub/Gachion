/*
 * phantomjs를 활용한 웹 스크레이핑 0.1, 2013-11-30
 * http://start.goodtime.co.kr
 * (c) 2013 이동련
 * MIT 라이선스
 */
 
/********** 설정 영역. 아래 설정 값들을 적절히 수정해야 함 ***********/
 
// 시작 웹페이지의 URL. 이 주소부터 시작해서 컨텐트를 스크레이핑한다.
var url = 'https://places.gachita.kr/contents/map_img/contents/map_img_save.php?idx=309';
 
// 웹페이지에서 어떤 컨텐트를 스크레이핑할지 지정하는 CSS 선택자
var contentSelectors = ['#map'];
 
// 다음 웹페이지 링크를 찾을 수 있는 CSS 선택자
var nextLinkSelector = '#map';
 
// 최대로 가져올 웹페이지 수
var maxPages = 5;
 
// 가져온 컨텐트 캡처 이미지를 저장할 폴더
var saveTo = './';
 
/********** 이 이하는 스크레이핑 실행 코드 ***************/
var index = 0;
var webpage = require('webpage');
var page = webpage.create();
 
page.open(url, scrape);
 
function scrape(status) {
    if (status != 'success') {
        console.log('웹페이지 열기 오류: ' + url);
        phantom.exit();
    }
 
    console.log(++index + ': ' + url);
 
    for (var i = 0; i < contentSelectors.length;) {
        var clipRect = page.evaluate(function (selector) {
            var o = document.querySelector(selector);
            return o ? o.getBoundingClientRect() : null;
        }, contentSelectors[i]);
 
        ++i;
        if (clipRect) {
            page.clipRect = clipRect;
            page.render(saveTo + '/' + index + '-' + i + '.png');
        }
    }
 
    url = page.evaluate(function(selector) {
        var o = document.querySelector(selector);
        return o ? o.href : null;
    }, nextLinkSelector);
 
    if (index >= maxPages || !url) {
        phantom.exit();
    }
 
    page.open(url, scrape);
}