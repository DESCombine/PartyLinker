<?php
    use EventPhoto\EventPhotoUtility;
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/authenticated_request.php");
    require_once(getenv("PL_ROOTDIRECTORY")."db/event_photo.php");
    global $driver;
    global $username;
    $max_photos = 15;
    $photos = EventPhotoUtility::recent_photos_followed($driver, $username, $max_photos);
    header('Content-Type: application/json');
    echo json_encode($photos, JSON_PRETTY_PRINT);
?>