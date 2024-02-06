<?php
    
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    use Post\PostUtility;
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/authenticated_request.php");
    require_once(getenv("PL_ROOTDIRECTORY")."db/post.php");
    global $driver;
    global $username;
    // Load the posts of users that the user follows and return them as a json object
    $max_posts = 20;
    $posts = PostUtility::recent_posts_followed($driver, $username, $max_posts);
    header('Content-Type: application/json');
    echo json_encode($posts, JSON_PRETTY_PRINT);
?>