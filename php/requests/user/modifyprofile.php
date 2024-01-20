<?php
require_once(getenv("PL_ROOTDIRECTORY") . "php/bootstrap.php");
use Firebase\JWT\JWT;

require_once(getenv("PL_ROOTDIRECTORY") . "db/user.php");
require_once(getenv("PL_ROOTDIRECTORY") . "php/requests/cors.php");
header('Content-Type: application/json');
$key = getenv("PL_JWTKEY");
//$request = json_decode(file_get_contents('php://input'), true);
$name = $_POST["name"];
$surname = $_POST["surname"];
$birth_date = $_POST["birthdate"];
$email = $_POST["email"];
$phone = $_POST["phone"];
$username = $_POST["username"];
$password = $_POST["password"];
$gender = $_POST["gender"];
if (is_null($_POST["organizer"])) {
    $organizer = 0;
} else {
    $organizer = $_POST["organizer"];
}

$profilePhoto = $_POST["profilePhoto"];
$bannerPhoto = $_POST["bannerPhoto"];
$bio = $_POST["bio"];
$language = $_POST["language"];
if (is_null($_POST["notifications"])) {
    $notifications = 0;
} else {
    $notifications = $_POST["notifications"];
}
if (is_null($_POST["2FA"])) {
    $TFA = 0;
} else {
    $TFA = $_POST["2FA"];
}

// create user
$user = new User\DBUser();
$user->update_infos($driver, $name, $surname, $birth_date, $email, $phone, $username, $password, $gender, $organizer, $profilePhoto, $bannerPhoto, $bio, $language, $notifications, $TFA);



echo json_encode(array("message" => "success"), JSON_PRETTY_PRINT);
header("Location: https://partylinker.live/profile");
$driver->close_connection();
?>