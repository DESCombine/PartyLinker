<?php
    
    require_once($_SERVER["DOCUMENT_ROOT"]."../../db/user.php");
    require_once($_SERVER["DOCUMENT_ROOT"]."../../php/bootstrap.php");
    header('Content-Type: application/json');

    $query = $_GET['query'];
    global $driver;
    
    $users = User::from_db_with_username_like($driver, $query);
    echo json_encode($users, JSON_PRETTY_PRINT);
    $driver->close_connection();
?>