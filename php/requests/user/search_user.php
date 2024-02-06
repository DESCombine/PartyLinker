<?php
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    require_once(getenv("PL_ROOTDIRECTORY")."db/user.php");
    
    header('Content-Type: application/json');

    $query = $_GET['query'];
    global $driver;
    // Search username in the database and return the results as a json object
    $users = User\UserUtility::from_db_with_username_like($driver, $query);
    echo json_encode($users, JSON_PRETTY_PRINT);
    $driver->close_connection();
?>