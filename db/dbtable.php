<?php 

 interface DBTable {
       // Serialize to json
       public function json_serialize(): string;

       // Insert a new row into the table
       public function db_serialize(DBDriver $driver);
       


 }
?>