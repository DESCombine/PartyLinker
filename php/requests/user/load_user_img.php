<?php
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    use User\UserUtility;
    require_once(getenv("PL_ROOTDIRECTORY")."db/user.php");
    
    global $driver;
    // Get the profile picture of the user and return it as a json object
    $us = $_GET['user'];
    $image = UserUtility::retrieve_profile_picture($driver, $us);
    header('Content-Type: application/json');
    echo json_encode($image, JSON_PRETTY_PRINT);
?>