<?php

    $conn = new mysqli("localhost","root","","hotel",3307);
    if($conn->connect_error){
        die("connection failed");
    }



?>