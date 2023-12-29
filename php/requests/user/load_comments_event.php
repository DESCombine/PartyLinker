<?php
    use EventPost\EventPostUtility;
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    require_once(getenv("PL_ROOTDIRECTORY")."db/event_post.php");
    global $driver;
    $event = $_GET['event'];
    $comments = EventPostUtility::comments_with_event($driver, $event);
    header('Content-Type: application/json');
    echo json_encode($comments, JSON_PRETTY_PRINT);
?>