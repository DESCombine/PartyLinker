<?php
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    use User\UserUtility;
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/authenticated_request.php");
    require_once(getenv("PL_ROOTDIRECTORY")."db/user.php");
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/cors.php");
    global $driver;
    global $username;
    $users = UserUtility::retrieve_online_followed($driver, $username);
    header('Content-Type: application/json');
    echo json_encode($users, JSON_PRETTY_PRINT);
?>