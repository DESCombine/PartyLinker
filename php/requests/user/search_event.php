<?php
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    use Event\EventUtility;
    require_once(getenv("PL_ROOTDIRECTORY")."db/event.php");
    
    global $driver;
    $event = $_GET['event'];
    $event = EventUtility::from_db_with_name($driver, $event);
    header('Content-Type: application/json');
    echo json_encode($event, JSON_PRETTY_PRINT);
?>