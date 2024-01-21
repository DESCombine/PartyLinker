<?php 
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/authenticated_request.php");
    require_once(getenv("PL_ROOTDIRECTORY")."db/post.php");
    use Post\PostUtility;
    require_once(getenv("PL_ROOTDIRECTORY")."db/event.php");
    use Event\EventUtility;
    header('Content-Type: application/json');

    global $driver;
    global $username;

    $request = json_decode(file_get_contents('php://input'), true);
    $event_id = $request["event_id"];
    $image = $request["image"];
    $description = $request["description"];
    $event_post = 0;
    if ($event_id == 0) {
        $event_post = 1;
        $name = $request["name"];
        $location = $request["location"];
        $starting_date = $request["starting_date"];
        $ending_date = $request["ending_date"];
        $vips = $request["vips"];
        $max_capacity = $request["max_capacity"];
        $price = $request["price"];
        $minimum_age = $request["minimum_age"];
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