var system = require('system');

console.log(system.args[0]); // test.js
console.log(system.args[1]); // arg1
/*
console.log(system.args[2]); // arg2
console.log(system.args[3]); // arg3
*/
var page = require('webpage').create();

var t0 = performance.now();rf
var t1 = 0;

page.viewportSize = { width: 1525, height: 1240 };
page.open('https://places.gachita.kr/contents/map_img/contents/map_img_save.php', function () {
        page.render('test.png');
    t1 = performance.now();
    console.log("Call to doSomething took " + (t1 - t0) + " milliseconds.")
       phantom.exit();
});