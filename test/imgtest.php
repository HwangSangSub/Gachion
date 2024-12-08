<?php
include "/var/www/places/udev/lib/common.php";
$fname = "1";
$pin_Icon = "https://places.gachita.kr/udev/admin/data/code_img/photo.php?id=1678695842_on";
$pin_Color = "#7a4a3a";
?>
<html>

<head>
    <meta charset="utf-8">
    <title>Bubbly — CSS speech bubbles made easy</title>
    <script src="<?= DU_UDEV_DIR ?>/common/js/jquery-1.8.3.min.js"></script>
    <script src="../js/FileSaver.min.js"></script>
    <script src="../js/html2canvas.js"></script>
    <script src="../js/canvas2image.js"></script>
</head>

<body>
    <style id="code" contenteditable="">
        .speech-bubble {
            position: relative;
            background: <?= $pin_Color ?>;
            border-radius: .4em;
            display: inline-block;
            padding-right: 14px;
            color: #fff;
        }

        .speech-bubble>h1 {
            line-height: 80px;
            display: flex;
            text-align: center;
            justify-content: center;
        }

        .speech-bubble-tail {
            position: absolute;
            bottom: -20px;
            left: calc(50% - 20px);
            width: 0;
            height: 0;
            border: 20px solid transparent;
            border-top-color: <?= $pin_Color ?>;
            border-bottom: 0;
        }
    </style>
    <hgroup class="speech-bubble">
        <h1><img src="<?= $pin_Icon ?>" />Bubbly</h1>
        <div class="speech-bubble-tail"></div>
    </hgroup>
    <script type="text/javascript">
        function printDiv(div) {
            var filename = "<?php echo $fname ?>"
            div = div[0];
            html2canvas(div, {
                useCORS: true, // CORS 사용
                scrollX: 0,
                scrollY: -window.scrollY, // 스크롤 포지션 보정
                windowWidth: document.documentElement.clientWidth,
                windowHeight: document.documentElement.clientHeight,
                letterRendering: true,
                background: null, // 배경색 없음
                logging: false, // 로깅 끄기
                allowTaint: false, // allowTaint 옵션 추가
                // foreignObjectRendering: true, // foreignObjectRendering 옵션 추가
                // onclone: function(cloneDoc) {
                //     // .speech-bubble-tail 요소 추가
                //     var tail = $('<div class="speech-bubble-tail"></div>');
                //     $(cloneDoc).find('.speech-bubble').append(tail);
                // }
            }).then(function(canvas) {
                var myImage = canvas.toDataURL();
                console.log(myImage);
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
        $(window).ready(function() {
            setTimeout(function() {
                printDiv($('.speech-bubble'));
            }, 2000);
        });
    </script>
</body>

</html>