<?php
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/authenticated_request.php");
    use Post\PostUtility;
    require_once(getenv("PL_ROOTDIRECTORY")."db/post.php");
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/email/like_notify_handler.php");

    global $driver; 
    global $username;
    try {
        $request = json_decode(file_get_contents('php://input'), true);
        $like = $request["like_id"];
        $type = $request["type"];
        PostUtility::insert_like($driver, $like, $username, $type);
        like_notify_handler($like, $type);
    } catch (\Exception $e) {
        http_response_code(500);
        echo json_encode(array("message" => "Error while liking post: " . $e->getMessage()));
        exit();
    }
?>