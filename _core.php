<?php
require "phpedb.php";

// database config
$db=new database();
$db->db="database_name"; // MODIFY
$db->connect("localhost", "root", "*****"); // MODIFY
