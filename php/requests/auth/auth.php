<?php
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    use Firebase\JWT\JWT;
    require_once(getenv("PL_ROOTDIRECTORY")."db/user.php");
    use User\UserUtility;
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/cors.php");
    require_once(getenv("PL_ROOTDIRECTORY")."php/tfa_handler.php");
    require_once(getenv("PL_ROOTDIRECTORY")."php/auth_utility.php");
    header('Content-Type: application/json');


    global $driver;
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
        header("Location: ".$domain."/login/login.html?wrongpassword=true");
        exit();
    }
    $settings = UserUtility::retrieve_settings($driver, $username);
    echo json_encode(array("message" => "success"), JSON_PRETTY_PRINT);
    global $domain;
    if ($settings->getTFA() && $settings->getTFA() != null) {
        $token = tfa_send($username);
        $token = $token."&remember=".$_POST["remember"];
        header("Location: ".$domain."/login/twofactorauth.html?token=".$token);
    } else {
        set_token_cookie($username, $_POST["remember"]);
        // header("Location: ".$domain);
    }
    $driver->close_connection();






    
    //$request = json_decode(file_get_contents('php://input'), true);



?>