<?php
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    use User\UserUtility;
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/authenticated_request.php");
    require_once(getenv("PL_ROOTDIRECTORY")."db/user.php");
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/cors.php");
    header('Content-Type: application/json');
    
    global $driver;
    global $username;
    $user = UserUtility::from_db_with_username($driver, $username);
    if( $user == null ){
        http_response_code(401);
        echo json_encode(array("error" => "Invalid token"));
        exit();
    }
    echo json_encode($user, JSON_PRETTY_PRINT);
?>