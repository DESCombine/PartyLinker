<?php
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/authenticated_request.php");
    use Event\EventUtility;
    require_once(getenv("PL_ROOTDIRECTORY")."db/event.php");

    global $driver;
    global $username;
    // insert a new partecipation for the event passed in the request
    try {
        $request = json_decode(file_get_contents('php://input'), true);
        $event = $request["event_id"];
        EventUtility::insert_partecipation($driver, $event, $username);
    } catch (\Exception $e) {
        http_response_code(500);
        echo json_encode(array("message" => "Error while submitting partecipation: " . $e->getMessage()));
        exit();
    }
?>