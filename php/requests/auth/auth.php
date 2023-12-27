<?php
    use Firebase\JWT\JWT;
    require_once(getenv("PL_ROOTDIRECTORY")."db/user.php");
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    header('Content-Type: application/json');
    $key = getenv("PL_JWTKEY");

    $request = json_decode(file_get_contents('php://input'), true);
    $username = $request["username"];
    $password = $request["password"];
    global $driver;
    $user = User\UserUtility::from_db_with_username($driver, $username);
    if( $user == null ){
        http_response_code(401);
        echo json_encode(array("error" => "Username not found"));
        exit();
    }
    if( !$user->check_password($password) ){
        http_response_code(401);
        echo json_encode(array("error" => "Wrong password"));
        exit();
    }
    $payload = array(
        "username" => $username,
    );
    $jwt = JWT::encode($payload, $key, 'HS256');
    echo json_encode(array("token" => $jwt));
    $driver->close_connection();
?>