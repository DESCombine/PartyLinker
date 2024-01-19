<?php
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    use Post\PostUtility;
    require_once(getenv("PL_ROOTDIRECTORY")."db/post.php");
    
    global $driver;
    $ev = $_GET['event'];
    $event = PostUtility::from_db_all_posts_with_event_id($driver, $ev);
    header('Content-Type: application/json');
    echo json_encode($event, JSON_PRETTY_PRINT);
?>