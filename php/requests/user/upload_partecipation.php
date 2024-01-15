<?php
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    use Event\EventUtility;
    require_once(getenv("PL_ROOTDIRECTORY")."db/event.php");

    global $driver;
    global $username;
    try {
        $event = $_POST["event_id"];
        EventUtility::insert_partecipation($driver, $event, $username);
    } catch (\Exception $e) {
        http_response_code(500);
        echo json_encode(array("message" => "Error while submitting partecipation: " . $e->getMessage()));
        exit();
    }
?>