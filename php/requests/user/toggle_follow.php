<?php
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    use User\UserUtility;
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/authenticated_request.php");
    require_once(getenv("PL_ROOTDIRECTORY")."db/user.php");
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/email/follow_notify_handler.php");
    
    global $driver;
    global $username;
    $toFollow = $_GET["user"];
    try{
        if (UserUtility::toggle_follow($driver, $username, $toFollow)) {
            follow_notify_handler($toFollow);
        }
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(array("message" => "error", "error" => $e->getMessage()));
        exit();
    }
    
    header('Content-Type: application/json');
    echo json_encode(array("message" => "success"), JSON_PRETTY_PRINT);

?>