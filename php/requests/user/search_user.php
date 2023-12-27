<?php
    
    require_once(getenv("PL_ROOTDIRECTORY")."db/user.php");
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    header('Content-Type: application/json');

    $query = $_GET['query'];
    global $driver;
    
    $users = User\UserUtility::from_db_with_username_like($driver, $query);
    echo json_encode($users, JSON_PRETTY_PRINT);
    $driver->close_connection();
?>