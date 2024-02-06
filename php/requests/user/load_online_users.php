<?php
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    use User\UserUtility;
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/authenticated_request.php");
    require_once(getenv("PL_ROOTDIRECTORY")."db/user.php");
    
    global $driver;
    global $username;
    // Load the online users that the user follows and return them as a json object
    $users = UserUtility::retrieve_online_followed($driver, $username);
    header('Content-Type: application/json');
    echo json_encode($users, JSON_PRETTY_PRINT);
?>