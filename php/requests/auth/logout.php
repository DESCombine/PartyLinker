<?php
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    use Firebase\JWT\JWT;
    require_once(getenv("PL_ROOTDIRECTORY")."db/user.php");
    use User\UserUtility;
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/cors.php");
    global $driver;
    global $domain;
    if($_POST['feedback'] != "") {
        User\UserUtility::insertFeedback($driver, $_POST["feedback"]);
    }
    unset($_COOKIE['token']);
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
    setcookie($cookie_name, '', $cookie_options);
    header("Location: " . $domain);
?>