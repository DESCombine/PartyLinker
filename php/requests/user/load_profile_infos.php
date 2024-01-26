<?php
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    require_once(getenv("PL_ROOTDIRECTORY")."db/user.php");
    use User\UserUtility;
    if(!isset($_GET["user"])) {
        require_once(getenv("PL_ROOTDIRECTORY")."php/requests/authenticated_request.php");
    } else {
        $username = $_GET["user"];
    }
    
    require_once(getenv("PL_ROOTDIRECTORY")."db/post.php");
    
    global $driver;
    global $username;
    $max_posts = 20;
    $posts = UserUtility::from_db_with_username($driver, $username);
    header('Content-Type: application/json');
    echo json_encode($posts, JSON_PRETTY_PRINT);
?>