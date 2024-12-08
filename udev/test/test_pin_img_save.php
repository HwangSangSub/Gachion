<?
include "../common/inc/inc_header.php";  //헤더
$kml_File = "1.kml";
?>
<meta name='viewport' content='initial-scale=1,maximum-scale=1,user-scalable=no' />
<script src='https://api.tiles.mapbox.com/mapbox-gl-js/v1.5.0/mapbox-gl.js'></script>
<link href='https://api.tiles.mapbox.com/mapbox-gl-js/v1.5.0/mapbox-gl.css' rel='stylesheet' />
<script src="../../js/FileSaver.min.js"></script>
<script src="../../js/html2canvas.js"></script>
<script src="../../js/canvas2image.js"></script>

<style>
    body {
        margin: 0;
    }

    #map {
        position: absolute;
        top: 0;
        bottom: 0;
        width: 600px;
        height: 500px;
        margin: 1%;
        background-color: black;
        border-style: solid;
        border-width: 5px;
        border-color: grey;
    }

    #screenshotPlaceholder {
        position: absolute;
        left: 50%;
        top: 0;
        bottom: 0;
        width: 600px;
        height: 500px;
        margin: 1%;
    }

    #buttonDiv {
        pointer-events: none;
        position: absolute;
        top: 0;
        bottom: 0;
        width: 46%;
        margin: 1%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    h1 {
        color: white;
        text-align: center;
        position: relative;
        top: 50%;
        transform: translateY(-50%);
        margin: 0;
        height: 0;
    }

    button {
        pointer-events: auto;
        transform: translate(0, 200%);
        padding: 10px 20px;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        color: white;
        background-color: rgb(42, 42, 242);
        box-shadow: 0px 5px 0px 0px rgb(11, 11, 92);
    }

    button:active {
        transform: translate(0, calc(200% + 5px));
        -webkit-transform: translate(0, calc(200% + 5px));
        text-decoration: none;
        box-shadow: 0px 2px 0px 0px rgb(11, 11, 92);
    }

    /* removes firefox dotted line around button text */
    ::-moz-focus-inner {
        border: 0;
    }
</style>
<?
$con_Idx = "1";
$area_Code = "홍대";
$time_Chk = "0";
$month = "10";
$day = "24";
$time = "23";

$fname = "1_홍대_" . $month . $day . "_" . $time . ".png";
?>
<div style=""><span style="color:#fff;font-size:30px;background-color:#ff625b;"><img src="https://places.gachita.kr/udev/admin/data/code_img/photo.php?id=1680674097_on" />연경대광로제비앙아파트</span></div>
<script type="text/javascript">
    function printDiv(div) {
        var filename = "<?php echo $fname ?>"
        div = div[0];
        html2canvas(div).then(function(canvas) {
            var myImage = canvas.toDataURL();
            downloadURI(myImage, filename);
        });
    }

    function downloadURI(uri, name) {
        var link = document.createElement("a");
        link.download = name;
        link.href = uri;
        document.body.appendChild(link);
        link.click();
    }
    /*
    $(window).load(function() {
    	setTimeout(function(){printDiv($('.mapboxgl-canvas'));},2000);
    });
    */
    $(window).ready(function() {
        setTimeout(function() {
            printDiv($('.mapboxgl-canvas'));
        }, 2000);
    });
</script>