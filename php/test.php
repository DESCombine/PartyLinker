<?php 
require_once(getenv("PL_ROOTDIRECTORY"). "php/bootstrap.php");
require_once(getenv("PL_ROOTDIRECTORY"). "db/user.php");
//echo "Connected to database <br />";
global $driver;
//use the User::from_data method with some test data
$user = User::from_form( "test3", "test3", "test3", "test3", "2023-12-15", "test3", "test3", "test3", "test3");
$user->db_serialize($driver);

echo json_encode($user, JSON_PRETTY_PRINT);

?>