<?php
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/authenticated_request.php");
    require_once(getenv("PL_ROOTDIRECTORY")."db/user.php");
    use User\UserUtility;
    global $driver;
    global $username;
    $user = UserUtility::all_infos_with_username($driver, $username);
    header('Content-Type: application/json');
    echo json_encode($user, JSON_PRETTY_PRINT);
?>