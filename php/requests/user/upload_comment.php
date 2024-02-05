<?php
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/authenticated_request.php");
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/email/tag_notify_handler.php");
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/email/comment_notify_handler.php");
    require_once(getenv("PL_ROOTDIRECTORY")."db/post.php");
    use Post\PostUtility;

    global $driver;
    global $username;
    try {
        $request = json_decode(file_get_contents('php://input'), true);
        $post = $request["post_id"];
        $content = $request["content"];
        tag_notify_handler($content);
        PostUtility::insert_comment($driver, $post, $username, $content);
        comment_notify_handler($post_id);
    } catch (\Exception $e) {
        http_response_code(500);
        echo json_encode(array("message" => "Error while liking post: " . $e->getMessage()));
        exit();
    }
?>