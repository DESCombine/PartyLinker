<?php 
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/authenticated_request.php");
    require_once(getenv("PL_ROOTDIRECTORY")."php/img_upload_handler.php");
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/email/tag_notifications_handler.php");
    require_once(getenv("PL_ROOTDIRECTORY")."db/post.php");
    use Post\PostUtility;
    require_once(getenv("PL_ROOTDIRECTORY")."db/event.php");
    use Event\EventUtility;
    header('Content-Type: application/json');

    global $driver;
    global $username;

    $event_id = $_POST["event-id"];
    $image = img_handler($_POST["image"]);
    $description = $_POST["description"];
    tag_notifications_handler($description);
    $event_post = 0;
    if ($event_id == 0) {
        $event_post = 1;
        $name = $_POST["event-name"];
        $location = $_POST["location"];
        $starting_date = $_POST["starting-date"];
        $ending_date = $_POST["ending-date"];
        $vips = $_POST["vips"];
        $max_capacity = $_POST["max-people"];
        $price = $_POST["price"];
        $minimum_age = $_POST["min-age"];
    }

    try {
        if ($event_id == 0) {
            $event = EventUtility::from_form($name, $location, $starting_date, $ending_date, $vips, $max_capacity, $price, $minimum_age);
            $event_id = $event->db_serialize($driver);
        }
        $post = PostUtility::from_form($event_id, $username, $image, $description, $event_post);
        $post->db_serialize($driver);
    } catch (\Exception $e) {
        http_response_code(400);
        echo json_encode(array("error" => "Error while posting: " . $e->getMessage()));
        exit();
    }
    echo json_encode(array("message" => "Post created successfully"));
    $driver->close_connection();
?>