<?php
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    require_once(getenv("PL_ROOTDIRECTORY")."db/user.php");
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/cors.php");
    require_once(getenv("PL_ROOTDIRECTORY")."php/tfa_handler.php");
    require_once(getenv("PL_ROOTDIRECTORY")."php/auth_utility.php");

    use User\UserUtility;

    header('Content-Type: application/json');

    global $driver;
    $username = $_POST["username"];
    $password = $_POST["password"];
    $user = UserUtility::from_db_with_username($driver, $username);
    if( $user == null ){
        http_response_code(401);
        echo json_encode(array("error" => "Username not found"), JSON_PRETTY_PRINT);
        header("Location: ".$domain."/login/login.html?usernotfound=true");
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
        header("Location: ".$domain);
    }
    $driver->close_connection();
?>