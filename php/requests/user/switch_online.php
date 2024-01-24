<?php
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/authenticated_request.php");
    require_once(getenv("PL_ROOTDIRECTORY")."db/user.php");
    use User\UserUtility;

    global $driver;
    global $username;
    UserUtility::update_online($driver, $username);
    echo json_encode(array("message" => "success"), JSON_PRETTY_PRINT);
?>