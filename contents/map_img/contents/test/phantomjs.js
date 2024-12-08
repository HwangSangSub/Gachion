var webPage = require('webpage');
var page = webPage.create();

page.viewportSize = { width: 540, height: 360 };
page.open("https://places.gachita.kr/contents/map_img/contents/map_img_save.php?idx=16", function start(status) {
  console.log(page['objectName']);
  console.log(page['title']);
  console.log(page['url']);
  page.render('test05.jpeg', {format: 'jpeg', quality: '100'});
  phantom.exit();
});