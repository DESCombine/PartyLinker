<?php
/**
 * This requests load from the db the most recent past and future events
 * It is used in the map to show recent events so that the user can see what events are happening around them
 */
require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
use Event\EventUtility;
require_once(getenv("PL_ROOTDIRECTORY")."php/requests/authenticated_request.php");
require_once(getenv("PL_ROOTDIRECTORY")."db/event.php");
global $driver;
global $username;

$events = EventUtility::from_db_recents_and_future_events($driver);

header('Content-Type: application/json');
echo json_encode($events, JSON_PRETTY_PRINT);
?>