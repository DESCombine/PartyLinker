<?php
    use User\UserUtility;
    require_once(getenv("PL_ROOTDIRECTORY")."db/user.php");
    if(!isset($_COOKIE["token"])){
        echo json_encode(array("error" => "No token provided"));
        header("Location: https://partylinker.live/login/login.html");
    }
    $username = UserUtility::retrieve_username_from_token($_COOKIE["token"]);
?>