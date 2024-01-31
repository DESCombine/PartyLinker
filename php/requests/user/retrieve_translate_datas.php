<?php
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    require_once(getenv("PL_ROOTDIRECTORY")."db/user.php");
    require_once(getenv("PL_ROOTDIRECTORY")."db/post.php");
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/authenticated_request.php");

    global $username;
    global $driver;
    $post_id = $_COOKIE["post_id"];

    use Post\PostUtility;
    $description = PostUtility::get_description_with_post_id($driver, $post_id);
    use User\UserUtility;
    $settings = UserUtility::retrieve_settings($driver, $username);

    $language = $settings->getLanguage();
    header('Content-Type: application/json');
    // associative array with the data to be encoded in the JSON format
    echo json_encode(["description" => $description, "language" => $language], JSON_PRETTY_PRINT)

?>