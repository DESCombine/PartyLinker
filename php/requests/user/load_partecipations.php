<?php
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    use Event\EventUtility;
    require_once(getenv("PL_ROOTDIRECTORY")."db/event.php");
    
    global $driver;
    global $username;
    $event = $_GET['event'];
    $comments = EventUtility::retrieve_partecipations($driver, $event, $username);
    header('Content-Type: application/json');
    echo json_encode($comments, JSON_PRETTY_PRINT);
?>