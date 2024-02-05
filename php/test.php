<?php 
    require_once(getenv("PL_ROOTDIRECTORY"). "php/bootstrap.php");
    require_once(getenv("PL_ROOTDIRECTORY"). "php/requests/email/comment_notify_handler.php");
    require_once(getenv("PL_ROOTDIRECTORY"). "php/requests/email/follow_notify_handler.php");
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/email/like_notify_handler.php");
    follow_notify_handler("sevetom");
    comment_notify_handler(14);
    like_notify_handler(14, "post");
    like_notify_handler(55, "comment");
?>