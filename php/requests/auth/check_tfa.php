<?php
require_once(getenv("PL_ROOTDIRECTORY") . "php/bootstrap.php");
require_once(getenv("PL_ROOTDIRECTORY") . "db/user.php");
require_once(getenv("PL_ROOTDIRECTORY")."php/auth_utility.php");
use User\UserUtility;

global $driver;
global $domain;


$inserted_code = $_POST['code'];
$token = $_POST['token'];
$right_code = UserUtility::retrieve_tfa($driver, $token);
// convert to int
$inserted_code = intval($inserted_code);
$right_code = intval($right_code);

echo $inserted_code;
echo $right_code;
if($inserted_code == $right_code) {
    UserUtility::reset_tfa($driver, $token);
    $username = UserUtility::check_tfa_success($driver, $token);
    if($username != null) {
        set_token_cookie($username, $_POST["remember"]);
        UserUtility::delete_tfa($driver, $token);
        header("Location: ".$domain);
    } else {
        header("Location: ".$domain."/login/twofactorauth.html?error=1&token=".$token);
    }

}
else {
    header("Location: ".$domain."/login/twofactorauth.html?error=1&token=".$token);
}
?>