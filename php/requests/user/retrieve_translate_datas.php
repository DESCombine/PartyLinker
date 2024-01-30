<?php
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    require_once(getenv("PL_ROOTDIRECTORY")."db/user.php");
    require_once(getenv("PL_ROOTDIRECTORY")."db/post.php");

    global $username;
    global $driver;
    $post_id = $_COOKIE["post_id"];

    use Post\PostUtility;
    $description = PostUtility::get_description_with_post_id($driver, $post_id);
    use User\UserUtility;
    $username = "danilo.maglia";
    $settings = UserUtility::retrieve_settings($driver, $username);

    $language = $settings->getLanguage();
    header('Content-Type: application/json');
    echo json_encode($description, JSON_PRETTY_PRINT);
    //echo json_encode($language, JSON_PRETTY_PRINT);

?>