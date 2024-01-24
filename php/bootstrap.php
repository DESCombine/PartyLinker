<?php
require_once(getenv("PL_ROOTDIRECTORY")."db/dbdriver.php");
require_once(getenv("PL_ROOTDIRECTORY")."vendor/autoload.php");
require_once(getenv("PL_ROOTDIRECTORY")."php/requests/cors.php");
define("UPLOAD_DIR", "../upload/");
// All this are enviroment variable that need to be set before running the server
$driver = new DBDriver(getenv("PL_SERVERNAME"), getenv("PL_USERNAME"), getenv("PL_PASSWORD"), getenv("PL_DBNAME"));
try {
    $driver->connect();
} catch (Exception $e) {
    throw new Exception("Error while connecting to the database: " . $e->getMessage());
}

$domain = $_SERVER['HTTP_HOST'];
if ($domain == "localhost")
    $domain = "http://localhost";
else {
    $domain = "https://partylinker.live";
}
?>