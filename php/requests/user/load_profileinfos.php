<?php
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    use User\UserUtility;
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/authenticated_request.php");
    require_once(getenv("PL_ROOTDIRECTORY")."db/post.php");
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/cors.php");
    global $driver;
    global $username;
    $max_posts = 20;
    $posts = UserUtility::from_db_with_username($driver, $username);
    header('Content-Type: application/json');
    echo json_encode($posts, JSON_PRETTY_PRINT);
?>