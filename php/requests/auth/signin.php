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
$username = $_POST["username"];
$password = $_POST["password"];
// check if user exists
$user = User\UserUtility::from_db_with_username($driver, $username);
if ($user != null) {
    http_response_code(401);
    echo json_encode(array("error" => "Username already used"), JSON_PRETTY_PRINT);
    exit();
} else {
    // create user
    $user = new User\DBUser();
    $user->__construct($username, $email, $name, $surname, $birth_date, null, null, null, null, $password, 0, null, null);
    $user->create_password($password);
    $user->db_serialize($driver);
}


echo json_encode(array("message" => "success"), JSON_PRETTY_PRINT);
header("Location: https://partylinker.live/login/login.html");
$driver->close_connection();
?>