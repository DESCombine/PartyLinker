<?php
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/authenticated_request.php");
    use Post\PostUtility;
    require_once(getenv("PL_ROOTDIRECTORY")."db/post.php");

    global $username;
    global $driver;

    if(isset($_GET["user"])) {
        $profile_user = $_GET["user"];
    } else {
        $profile_user = $username;
    }
    
    $posts = PostUtility::from_db_with_username($profile_user, $driver, $username);
    header('Content-Type: application/json');
    echo json_encode($posts, JSON_PRETTY_PRINT);
?>