
var conIdx  = process.argv.slice(2);

var baseUrl = "https://places.gachita.kr/contents/map_img/contents/map_img_save.php?idx=";

let url = baseUrl.concat(conIdx[0]);

const options = {
	width: 540,
	height: 360
};

const captureWebsite = require('capture-website');

(async () => {
//https://places.gachita.kr/contents/map_img/contents/map_img_save.php?idx=340

	await captureWebsite.file(url, conIdx+'.jpg'
	,{
		options,	
		beforeScreenshot: async (page, browser) => {
			// await checkSomething();
			// await page.click('#btnSave');
			await page.waitForSelector('#capture');
			//await page.waitForNavigation()
		}
	}
  );
})();
