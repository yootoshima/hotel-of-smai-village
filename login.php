<?php 

include "/db.php";

$user = $_REQUEST['username'];
$pass = $_REQUEST['password'];

$sql = "select * from customers where cus_name = '$user' and password = '$pass'"
?>