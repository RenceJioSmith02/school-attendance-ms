<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If not logged in → redirect to login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php?ERROR=1");
    exit;
}

// Include DB
// require_once "../db/db.php";
// $mydb = new myDB();

// Verify the session user still exists
$user = $mydb->select_one("users", "*", ["id" => $_SESSION['user_id']]);

// If user not found (deleted) → force logout
if (!$user) {
    session_unset();
    session_destroy();
    header("Location: ../index.php?ERROR=2");
    exit;
}

// Refresh session with updated data
$_SESSION['email'] = $user['email'];
$_SESSION['user_role'] = $user['role'];
?>