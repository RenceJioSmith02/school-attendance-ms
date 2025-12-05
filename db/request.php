<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('Asia/Manila');

// log errors instead of outputting
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../php-error.log');
error_reporting(E_ALL);


require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once "db.php";

$mydb = new myDB();
$response = ["status" => "error", "message" => "Unknown error"];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';

        switch ($action) {

            /* ---------------- LOGIN PROCESS ---------------- */
            case "login":
                $username = $_POST['auth_username'] ?? '';
                $password = $_POST['auth_password'] ?? '';

                if (empty($username) || empty($password)) {
                    $response = ["status" => "error", "message" => "Please enter both username and password"];
                    break;
                }

                // Fetch user auth info
                $userAuth = $mydb->select("users_auth", "*", ["auth_username" => $username]);

                if (!$userAuth) {
                    $response = ["status" => "error", "message" => "Invalid username or password"];
                    break;
                }

                $userAuth = $userAuth[0];

                // Verify password 
                if (!password_verify($password, $userAuth['auth_password'])) {
                    $response = ["status" => "error", "message" => "Invalid username or password"];
                    break;
                }

                $_SESSION['auth_id'] = $userAuth['auth_id'];
                $_SESSION['username'] = $username;
                $_SESSION['user_role'] = $userAuth['auth_role'];

                $response = [
                    "status" => "success",
                    "message" => "Login successful",
                    "redirect" => "dashboard.php"
                ];
                break;

            case "logout":
                $_SESSION = []; //clears all session data.
                session_unset();
                session_destroy();

                $response = [
                    "status" => "success",
                    "message" => "Logged out successfully"
                ];
                break;


            /* ---------------- RESET PASSWORD ---------------- */
            case "reset_password":
                $username = $_POST['username'] ?? '';
                $newPassword = $_POST['password'] ?? '';

                if (empty($username) || empty($newPassword)) {
                    $response = ["status" => "error", "message" => "Username and password are required."];
                    break;
                }

                // Check if admin exists
                $admin = $mydb->select("users_auth", "*", ["auth_username" => $username]);

                if (!$admin) {
                    $response = ["status" => "error", "message" => "Username not found."];
                    break;
                }

                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                // Update password
                $mydb->update("users_auth", ["auth_password" => $hashedPassword], ["auth_username" => $username]);

                $response = ["status" => "success", "message" => "Password reset successfully."];
                break;

            default:
                $response = ["status" => "error", "message" => "Unknown action"];
        }
    } else {
        $response = ["status" => "error", "message" => "Invalid request method"];
    }
} catch (Exception $e) {
    $response = ["status" => "error", "message" => "Server error: " . $e->getMessage()];
}

echo json_encode($response);
exit;


// $password = "";
// $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
// echo $hashedPassword;
