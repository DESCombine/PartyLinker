<?php
    /**
     * This file is used to authenticate the user
     * It is included in every request that needs to authenticate the user
     * It gets the username from the token and sets it in the global scope so that it can be used in the request
     * If the token is not provided it returns an error
     * If the token is invalid it returns an error
     */
    use User\UserUtility;
    require_once(getenv("PL_ROOTDIRECTORY")."db/user.php");
    if(!isset($_COOKIE["token"])){
        $domain = $_SERVER['HTTP_HOST'];
        if ($domain == "localhost") {
            $domain = "http://localhost";
        } else {
            $domain = "https://partylinker.live";
        }
        header('Content-Type: application/json');
        echo json_encode(array("error" => "No token provided"));
        exit();
    }
    $username = UserUtility::retrieve_username_from_token($_COOKIE["token"]);
?>