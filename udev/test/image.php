<?
    $image = $_POST['image'];
    echo "<img src='$image' alt='image' />";
    $decoded = base64_decode(str_replace('data:image/png;base64,', '', $image));
    $name = time();
    file_put_contents("/img/" . $name . ".png", $decoded);
    echo '<p><a href="download.php?img='.$name.'.png">Download</a></p>';
?>