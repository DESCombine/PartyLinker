<?php
    use EventPost\EventPostUtility;
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/cors.php");
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/authenticated_request.php");
    require_once(getenv("PL_ROOTDIRECTORY")."db/event_post.php");
    global $driver;
    global $username;
    $max_posts = 15;
    $posts = EventPostUtility::recent_events_followed($driver, $username, $max_posts);
    header('Content-Type: application/json');
    echo json_encode($posts, JSON_PRETTY_PRINT);
?>