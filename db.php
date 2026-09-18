<?php

    $conn = new mysqli("localhost","root","","hotel",3306);
    if($conn->connect_error){
        die("connection failed");
    }



?>