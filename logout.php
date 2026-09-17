<?php 

    session_start();
    if (empty($_SESSION['username']) || (empty($_SESSION['password']))) {
    header("Location: /hotel-of-smai-village/index.php");
    exit();
}
    
    unset($_SESSION["cus_name"]);
    unset($_SESSION["password"]);
    header("Location: /hotel-of-smai-village/index.php");

?>