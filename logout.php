<?php
session_start();
session_unset();
session_destroy();

// Clear cookie if exists
if (isset($_COOKIE['remember_me_user'])) {
    setcookie('remember_me_user', '', time() - 3600, "/");
}

header("Location: login.php");
exit();
?>