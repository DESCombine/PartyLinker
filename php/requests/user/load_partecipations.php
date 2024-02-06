<?php
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/authenticated_request.php");
    use Event\EventUtility;
    require_once(getenv("PL_ROOTDIRECTORY")."db/event.php");
    
    global $driver;
    global $username;
    // Load all the partecipations to the event and return them as a json object
    $event = $_GET['event'];
    $comments = EventUtility::retrieve_partecipations($driver, $event, $username);
    header('Content-Type: application/json');
    echo json_encode($comments, JSON_PRETTY_PRINT);
?>