<?php
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/authenticated_request.php");
    use User\UserUtility;
    require_once(getenv("PL_ROOTDIRECTORY")."db/user.php");
    
    global $driver;
    global $username;
    // Retrieve the settings of the user and return them as a json object
    $settings = UserUtility::retrieve_settings($driver, $username);
    header('Content-Type: application/json');
    echo json_encode($settings, JSON_PRETTY_PRINT);
?>