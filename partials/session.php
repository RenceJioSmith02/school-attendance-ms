<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// // Include database connection
// require_once "../db/db.php";
// $mydb = new myDB();

// Check if logged in
if (!isset($_SESSION['auth_id']) || !isset($_SESSION['username'])) {
    header("Location: index.php?ERROR=1&user_id=". $_SESSION['auth_id']."&username=". $_SESSION['username']);
    exit;
}

// Validate user, ensure not deleted
$userAuth = $mydb->select("users_auth", "*", [
    "auth_id" => $_SESSION['auth_id'],
    "auth_username" => $_SESSION['username']
]);

if (!$userAuth) {
    session_unset();
    session_destroy();
    header("Location: index.php?ERROR=2");
    exit;
}

$userAuth = $userAuth[0];

// Refresh session data
$_SESSION['user_id'] = $userAuth['auth_id'];
$_SESSION['username'] = $userAuth['auth_username'];
$_SESSION['user_role'] = $userAuth['auth_role'];

