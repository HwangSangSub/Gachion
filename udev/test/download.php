    <? $file = $_GET['img'];
header('Content-Description: File Transfer');
header("Content-type: image/jpg");
header("Content-disposition: attachment; filename= ".$file."");
readfile($file);
?>