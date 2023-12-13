<?php 
require("../db/dbdriver.php");
require("../db/user.php");
$driver = new DBDriver("localhost", "root", "", "partylinker_dev");
$driver->connect();
echo "Connected to database <br />";
//use the User::from_data method with some test data
$user = User::from_data( "test", "test", "test", "test", "test", "test", "test", "test", "test");
$user->db_serialize($driver);
?>