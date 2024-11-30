<?php
    session_start();
    if (isset($_SESSION['id_user'])) {
        $id_user = $_SESSION['id_user'];
    } else {
        header("Location: login.php");
        exit;
    }
?>