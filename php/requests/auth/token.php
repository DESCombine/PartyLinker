<?php 
    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;
    use User\UserUtility;
    require_once(getenv("PL_ROOTDIRECTORY")."db/user.php");
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    header('Content-Type: application/json');
    $headers = apache_request_headers();
    if(!isset($headers["Authorization"])){
        http_response_code(401);
        echo json_encode(array("error" => "No token provided"));
        exit();
    }
    if(preg_match("/Bearer\s(\S+)/", $headers["Authorization"], $matches) !== 1){
        http_response_code(401);
        echo json_encode(array("error" => "Invalid token"));
        exit();
    }
    global $driver;
    $token = $matches[1];
    $decoded = JWT::decode($token, new Key(getenv("PL_JWTKEY"), 'HS256'));
    $username = ((array) $decoded)["username"];
    $user = UserUtility::from_db_with_username($driver, $username);
    if( $user == null ){
        http_response_code(401);
        echo json_encode(array("error" => "Invalid token"));
        exit();
    }
    echo json_encode($user, JSON_PRETTY_PRINT);
?>