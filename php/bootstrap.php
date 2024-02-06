<?php
/**
 * This file is used to include all the files that are needed in every request
 * It is included in every request
 * It includes the db driver, the cors file and the autoload file
 * It also sets the domain variable that is used to set the domain in the cookies
 */
require_once(getenv("PL_ROOTDIRECTORY")."db/dbdriver.php");
require_once(getenv("PL_ROOTDIRECTORY")."vendor/autoload.php");
require_once(getenv("PL_ROOTDIRECTORY")."php/requests/cors.php");
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