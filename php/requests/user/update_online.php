<?php
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/authenticated_request.php");
    require_once(getenv("PL_ROOTDIRECTORY")."db/user.php");
    use User\UserUtility;

    global $driver;
    global $username;
    // Update the online status of the user and all the other users
    UserUtility::update_online($driver, $username, 1);
    $onlineUsers = UserUtility::update_timestamps($driver, $username);
    foreach ($onlineUsers as $onlineUser) {
        UserUtility::update_online($driver, $onlineUser, 0);
    }
    header('Content-Type: application/json');
    echo json_encode(array("message" => "success"), JSON_PRETTY_PRINT);
?>