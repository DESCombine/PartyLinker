<?php
require_once(getenv("PL_ROOTDIRECTORY") . "php/bootstrap.php");
require_once(getenv("PL_ROOTDIRECTORY") . "php/requests/authenticated_request.php");
use Post\PostUtility;

require_once(getenv("PL_ROOTDIRECTORY") . "db/post.php");
$event = $_GET["event"];
$post = PostUtility::load_post_id($driver, $event);
header('Content-Type: application/json');
echo json_encode($post, JSON_PRETTY_PRINT);

?>