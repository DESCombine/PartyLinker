<?php
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    use Firebase\JWT\JWT;
    require_once(getenv("PL_ROOTDIRECTORY")."db/user.php");
    use User\UserUtility;
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/cors.php");
    header('Content-Type: application/json');
    $key = getenv("PL_JWTKEY");
    //$request = json_decode(file_get_contents('php://input'), true);
    $username = $_POST["username"];
    $password = $_POST["password"];
    $user = UserUtility::from_db_with_username($driver, $username);
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
    $cookie_options = array(
        'path' => '/',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'None',
    );
    if($_SERVER["HTTP_HOST"] != "localhost") {
        $cookie_options['domain'] = '.partylinker.live';
    } 

    if($_POST["remember"] == "on") {
        $cookie_options['expires'] = time() + 86400 * 365;
    }
    setcookie($cookie_name, $cookie_value, $cookie_options);
    $settings = UserUtility::retrieve_settings($driver, $username);
    echo json_encode(array("message" => "success"), JSON_PRETTY_PRINT);
    global $domain;
    if ($settings->getTFA()) {
        header("Location: ".$domain."/login/twofactorauth.html");
    } else {
        UserUtility::update_online($driver, $username);
        header("Location: ".$domain);
    }
    $driver->close_connection();
?>