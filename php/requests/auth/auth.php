<?php
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    use Firebase\JWT\JWT;
    require_once(getenv("PL_ROOTDIRECTORY")."db/user.php");
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/cors.php");
    header('Content-Type: application/json');
    $key = getenv("PL_JWTKEY");
    $request = json_decode(file_get_contents('php://input'), true);
    $username = $request["username"];
    $password = $request["password"];
    global $driver;
    $user = User\UserUtility::from_db_with_username($driver, $username);
    if( $user == null ){
        http_response_code(401);
        echo json_encode(array("error" => "Username not found"), JSON_PRETTY_PRINT);
        exit();
    }
    if( !$user->check_password($password) ){
        http_response_code(401);
        echo json_encode(array("error" => "Wrong password"), JSON_PRETTY_PRINT);
        exit();
    }
    $payload = array(
        "username" => $username,
    );
    $jwt = JWT::encode($payload, $key, 'HS256');
    $cookie_name = "token";
    $cookie_value = $jwt;

    setcookie($cookie_name, $cookie_value, time() + (86400 * 365), "/", ".partylinker.live");
    echo json_encode(array("message" => "success"), JSON_PRETTY_PRINT);
    $driver->close_connection();
?>