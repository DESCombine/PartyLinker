<?php
    use EventPhoto\EventPhotoUtility;
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    require_once(getenv("PL_ROOTDIRECTORY")."db/event_photo.php");
    global $driver;
    $photo = $_GET['photo'];
    $comments = EventPhotoUtility::comments_with_photo($driver, $photo);
    header('Content-Type: application/json');
    echo json_encode($comments, JSON_PRETTY_PRINT);
?>