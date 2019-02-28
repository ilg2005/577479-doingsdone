<?php
session_start();

if (isset($_SESSION['user'])) {
    $_SESSION['user'] = [];
    header('Location: guest.php');
    exit();
}
//unset($_SESSION['user']);

?>
