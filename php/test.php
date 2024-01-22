<?php 
require_once(getenv("PL_ROOTDIRECTORY"). "php/bootstrap.php");

require_once(getenv("PL_ROOTDIRECTORY"). "php/img_upload_handler.php");
require_once(getenv("PL_ROOTDIRECTORY"). "php/requests/email/tag_notify_handler.php");

tag_notify_handler("Hi my name @sevetom @danilo.maglia @manu.sanchi");
?>