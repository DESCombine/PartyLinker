<?php 
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    use User\UserUtility;
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/authenticated_request.php");
    $toCheck = $_GET["user"];
    
    require_once(getenv("PL_ROOTDIRECTORY")."db/post.php");
    
    global $driver;
    global $username;
    $max_posts = 20;
    // connect to the database and check if the user follows the other user. Then return the result as a json object
    $flag = UserUtility::check_if_follows($driver, $username, $toCheck);
    header('Content-Type: application/json');
    if($flag) {
        echo json_encode(array("follows" => true), JSON_PRETTY_PRINT);
    } else {
        echo json_encode(array("follows" => false), JSON_PRETTY_PRINT);
    }


?>