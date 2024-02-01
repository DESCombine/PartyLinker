<?php
require_once(getenv("PL_ROOTDIRECTORY") . "php/bootstrap.php");
require_once(getenv("PL_ROOTDIRECTORY") . "php/requests/authenticated_request.php");
require_once(getenv("PL_ROOTDIRECTORY") . "db/post.php");
use Post\PostUtility;

global $driver;
global $username;

$event = $_GET["event"];
$post = PostUtility::load_post_event($driver, $event, $username);
header('Content-Type: application/json');
echo json_encode($post, JSON_PRETTY_PRINT);

?>