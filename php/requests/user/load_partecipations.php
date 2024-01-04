<?php
    use User\UserUtility;
    require_once(getenv("PL_ROOTDIRECTORY")."../php/bootstrap.php");
    require_once(getenv("PL_ROOTDIRECTORY")."../db/event_photo.php");
    require_once(getenv("PL_ROOTDIRECTORY")."../php/requests/cors.php");
    global $driver;
    $event = $_GET['event'];
    $comments = UserUtility::retrieve_partecipation($driver, $event);
    header('Content-Type: application/json');
    echo json_encode($comments, JSON_PRETTY_PRINT);
?>