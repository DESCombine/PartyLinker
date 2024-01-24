<?php
require_once(getenv("PL_ROOTDIRECTORY") . "php/bootstrap.php");
use Firebase\JWT\JWT;

require_once(getenv("PL_ROOTDIRECTORY") . "php/img_upload_handler.php");
require_once(getenv("PL_ROOTDIRECTORY") . "db/user.php");
require_once(getenv("PL_ROOTDIRECTORY") . "php/requests/cors.php");
header('Content-Type: application/json');
$key = getenv("PL_JWTKEY");
//$request = json_decode(file_get_contents('php://input'), true);
global $username;
$name = $_POST["name"];
$surname = $_POST["surname"];
$birth_date = $_POST["birthdate"];
$email = $_POST["email"];
$phone = $_POST["phone"];
$password = $_POST["password"];
$gender = $_POST["gender"];
if (isset($_POST["organizer"])) {
    $organizer = 1;
} else {
    $organizer = 0;
}

$profilePhoto = img_handler($_FILES["profilePhoto"]);
$bannerPhoto = img_handler($_FILES["bannerPhoto"]);
$bio = $_POST["bio"];
$language = $_POST["language"];
if (isset($_POST["notifications"])) {
    $notifications = 1;
} else {
    $notifications = 0;
}
if (isset($_POST["2FA"])) {
    $TFA = 1;
} else {
    $TFA = 0;
}
use User\UserUtility;

$username = UserUtility::retrieve_username_from_token($_COOKIE["token"]);

// create user
$user = new User\DBUser();
$user->create_password($password);
$password = $user->get_password();
$user->update_infos($driver, $name, $surname, $birth_date, $email, $phone, $username, $password, $gender, $organizer, $profilePhoto, $bannerPhoto, $bio, $language, $notifications, $TFA);
global $domain;
echo json_encode(array("message" => "success"), JSON_PRETTY_PRINT);
header("Location: http://" . $_SERVER["HTTP_HOST"] . "/profile");
$driver->close_connection();
?>