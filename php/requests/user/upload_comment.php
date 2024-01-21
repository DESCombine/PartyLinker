<?php
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/authenticated_request.php");
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/email/tag_notifications_handler.php");
    use Post\PostUtility;
    require_once(getenv("PL_ROOTDIRECTORY")."db/post.php");

    global $driver;
    global $username;
    try {
        $request = json_decode(file_get_contents('php://input'), true);
        $post = $request["post_id"];
        $content = $request["content"];
        tag_notifications_handler($content);
        PostUtility::insert_comment($driver, $post, $username, $content);
    } catch (\Exception $e) {
        http_response_code(500);
        echo json_encode(array("message" => "Error while liking post: " . $e->getMessage()));
        exit();
    }
?>