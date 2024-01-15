<?php
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    use Post\PostUtility;
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/authenticated_request.php");
    require_once(getenv("PL_ROOTDIRECTORY")."db/post.php");
    
    global $driver;
    global $username;
    $posts = PostUtility::from_db_with_username($driver, $username);
    header('Content-Type: application/json');
    echo json_encode($posts, JSON_PRETTY_PRINT);
?>