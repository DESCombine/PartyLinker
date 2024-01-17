<?php
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/authenticated_request.php");
    global $driver;
    global $username;
    header('Content-Type: application/json');
    echo json_encode($username, JSON_PRETTY_PRINT);
?>