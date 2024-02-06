<?php
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/authenticated_request.php");
    use Post\PostUtility;
    require_once(getenv("PL_ROOTDIRECTORY")."db/post.php");

    global $username;
    global $driver;
    // Load the posts of the user and return them as a json object
    if(isset($_GET["user"])) {
        $profile_user = $_GET["user"];
    } else {
        $profile_user = $username;
    }
    
    $posts = PostUtility::from_db_with_username($driver, $profile_user, $username);
    header('Content-Type: application/json');
    echo json_encode($posts, JSON_PRETTY_PRINT);
?>