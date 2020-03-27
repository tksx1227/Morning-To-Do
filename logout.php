<?php
    unset($_SESSION['user_info']);

    if (isset($_COOKIE["PHPSESSID"])) {
        setcookie("PHPSESSID", '', time() - 1800, '/');
    }

    session_destroy();

    header('Location: task.php?login');
    exit();
?>