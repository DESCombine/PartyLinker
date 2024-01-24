<?php
require_once(getenv("PL_ROOTDIRECTORY") . "php/bootstrap.php");
require_once(getenv("PL_ROOTDIRECTORY") . "php/requests/authenticated_request.php");
require_once(getenv("PL_ROOTDIRECTORY") . "db/user.php");
use User\UserUtility;

global $driver;
global $username;

$inserted_code = $_POST['code'];
$right_code = UserUtility::retrieve_tfa($driver, $username);
// convert to int
$inserted_code = intval($inserted_code);
$right_code = intval($right_code);
global $domain;
if($inserted_code == $right_code) {
    UserUtility::reset_tfa($driver, $username);
    UserUtility::update_online($driver, $username);
    header("Location: ".$domain);
}
else {
    header("Location: ".$domain."/login/twofactorauth.html?error=1");
}
?>