<?php
    use User\UserUtility;
    require_once(getenv("PL_ROOTDIRECTORY")."db/user.php");
    if(!isset($_COOKIE["token"])){
        $domain = $_SERVER['HTTP_HOST'];
        if ($domain == "localhost") {
            $domain = "http://localhost";
        } else {
            $domain = "https://partylinker.live";
        }
        header("Location: ".$domain."/login/login.html");
        echo json_encode(array("error" => "No token provided"));
    }
    $username = UserUtility::retrieve_username_from_token($_COOKIE["token"]);
?>