<?php
    use User\UserUtility;
    require_once(getenv("PL_ROOTDIRECTORY")."db/user.php");
    header('Content-Type: application/json');
    $headers = apache_request_headers();
    if(!isset($_COOKIE["token"])){
        http_response_code(401);
        echo json_encode(array("error" => "No token provided"));
        exit();
    }
    $username = UserUtility::retrieve_username_from_token($_COOKIE["token"]);
?>