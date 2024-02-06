<?php
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    use Event\EventUtility;
    require_once(getenv("PL_ROOTDIRECTORY")."db/event.php");
    
    global $driver;
    $ev = $_GET['event'];
    // Get the event informations and return them as a json object
    $event = EventUtility::from_db_with_event_id($driver, $ev);
    header('Content-Type: application/json');
    echo json_encode($event, JSON_PRETTY_PRINT);
?>