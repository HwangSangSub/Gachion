var webshot = require("webshot");

var option = {
	streamType: "jpg",
	windowSize: {
		width:540,
		height:360
	},
	shotSize: {
		width:"all",
		height:"all"
	}
};

webshot("https://places.gachita.kr/contents/map_img/contents/map_img_save.php?idx=16","16_con.jpg", options, () => {
	if(err){
		return console.log(err);
	}

	console.log("Image succesfully created");
});