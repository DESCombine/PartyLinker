<?php
    use Post\PostUtility;
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    require_once(getenv("PL_ROOTDIRECTORY")."db/post.php");
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/cors.php");
    global $driver;
    $post = $_GET['post'];
    $comments = PostUtility::comments_with_post($driver, $post);
    header('Content-Type: application/json');
    echo json_encode($comments, JSON_PRETTY_PRINT);
?>