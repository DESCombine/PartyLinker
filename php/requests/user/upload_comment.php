<?php
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/authenticated_request.php");
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/email/tag_notify_handler.php");
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/email/comment_notify_handler.php");
    require_once(getenv("PL_ROOTDIRECTORY")."db/post.php");
    use Post\PostUtility;

    global $driver;
    global $username;
    // Insert a comment in the database and send a notification to the user
    try {
        $request = json_decode(file_get_contents('php://input'), true);
        $post = $request["post_id"];
        $content = $request["content"];
        // call the function to check if the comment contains tags and notify the tagged users
        tag_notify_handler($content);
        PostUtility::insert_comment($driver, $post, $username, $content);
        // Send a notification to the user
        comment_notify_handler($post);
    } catch (\Exception $e) {
        http_response_code(500);
        echo json_encode(array("message" => "Error while liking post: " . $e->getMessage()));
        exit();
    }
?>