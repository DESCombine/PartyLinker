<?php
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    use Firebase\JWT\JWT;
    require_once(getenv("PL_ROOTDIRECTORY")."db/user.php");
    use User\UserUtility;
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/cors.php");
    global $driver;
    User\UserUtility::insertFeedback($driver, $_POST["feedback"]);
    unset($_COOKIE['token']);
    setcookie('token', '', -1, '/'); 
    header("Location: /");
?>