<?php
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    use Firebase\JWT\JWT;
    require_once(getenv("PL_ROOTDIRECTORY")."db/user.php");
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/cors.php");
    header('Content-Type: application/json');
    $key = getenv("PL_JWTKEY");
    //$request = json_decode(file_get_contents('php://input'), true);
    $username = $_POST["username"];
    $password = $_POST["password"];
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
    $cookie_value = "Bearer ".$jwt;
    echo $_SERVER["HTTP_ORIGIN"];
    if($_SERVER["HTTP_ORIGIN"] == "http://localhost") {
        setcookie($cookie_name, $cookie_value, [
            'expires' => time() + 86400 * 365,
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'None',
        ]);
    } else {
        setcookie($cookie_name, $cookie_value, [
            'expires' => time() + 86400 * 365,
            'path' => '/',
            'domain' => '.partylinker.live',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'None',
        ]);
    }


    echo json_encode(array("message" => "success"), JSON_PRETTY_PRINT);
    header("Location: http://localhost");
    $driver->close_connection();
?>