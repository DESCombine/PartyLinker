<?php 
require_once($_SERVER['DOCUMENT_ROOT'] . "/php/bootstrap.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/db/user.php");
//echo "Connected to database <br />";
global $driver;
//use the User::from_data method with some test data
$user = User::from_form( "test2", "test2", "test2", "test2", "2023-12-15", "test", "test", "test", "test");
$user->db_serialize($driver);

echo json_encode($user, JSON_PRETTY_PRINT);
?>