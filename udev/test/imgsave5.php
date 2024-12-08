<html>
 <HEAD>
  <TITLE>html2canvas_exam01</TITLE>
 
 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.js"></script>
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://www.nihilogic.dk/labs/canvas2image/canvas2image.js"></script>
  
<SCRIPT LANGUAGE="JavaScript">
<!--
function  html2img(){
  var canvas ="";
  html2canvas($("#SavePart"), {
  onrendered: function(canvas) {
  // canvas is the final rendered <canvas> element
   document.getElementById("theimage").src = canvas.toDataURL();
     Canvas2Image.saveAsPNG(canvas);
  }
  });
  //alert(document.getElementById("SavePart").innerHTML);
}
//-->
</SCRIPT>
 
 </HEAD>
 
 <BODY>
   
<div id="SavePart">
<H> 안녕하세요!!!!</H>
<img src='https://places.gachita.kr/contents/map_img/places/map_pin_bg_01_lunch_box_70.png' />
</div>
<FORM>
  <INPUT type='BUTTON' value='버튼' onclick='html2img()'> <!-- 버튼 클릭 이벤트-->
</FORM>
<image id="theimage"></image>
 </BODY>
</HTML>