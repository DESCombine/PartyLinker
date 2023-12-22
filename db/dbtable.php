<?php 

 interface DBTable extends JsonSerializable{
       // Insert a new row into the table
       public function db_serialize(DBDriver $driver);
 }
?>