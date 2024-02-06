<?php 

/**
 * Interface for a database table
 */
 interface DBTable extends JsonSerializable{
       // Insert a new row into the table
       public function db_serialize(DBDriver $driver);
 }
?>