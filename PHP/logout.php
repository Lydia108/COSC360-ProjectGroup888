<?php
session_start();
//check login
if(isset($_SESSION['user_id'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
} else {
    header("Location: login.php");
    exit();
}
?>
