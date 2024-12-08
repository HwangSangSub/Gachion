<?
$img = '1583197191';
				$file_dir = $_SERVER["DOCUMENT_ROOT"].'/contents/place_img/'.$img;
						rmdir($file_dir);
				if(is_dir($file_dir)){
					echo "이쑈어요";
				}
						
?>