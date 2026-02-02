<?php
require_once "../auth/session.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1; // demo user
}
return $_SESSION;   
?>
