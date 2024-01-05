<?php 
require_once(getenv("PL_ROOTDIRECTORY"). "php/bootstrap.php");

require_once(getenv("PL_ROOTDIRECTORY"). "php/img_upload_handler.php");

echo img_handler($_FILES["img"]);


?>