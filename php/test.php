<?php 
    require_once(getenv("PL_ROOTDIRECTORY"). "php/bootstrap.php");
    require_once(getenv("PL_ROOTDIRECTORY"). "php/tfa_handler.php");
    tfa_send("danilo.maglia");
?>