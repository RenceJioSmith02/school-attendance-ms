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

                $email = $_POST['auth_username'] ?? '';
                $password = $_POST['auth_password'] ?? '';

                if (empty($email) || empty($password)) {
                    $response = ["status" => "error", "message" => "Please enter both email and password"];
                    break;
                }

                // fetch from users table
                $user = $mydb->select("users", "*", ["email" => $email]);

                if (!$user) {
                    $response = ["status" => "error", "message" => "Invalid email or password"];
                    break;
                }

                $user = $user[0];

                if (!password_verify($password, $user['password'])) {
                    $response = ["status" => "error", "message" => "Invalid email or password"];
                    break;
                }

                // SUCCESS
                $_SESSION['user_id'] = $user["id"];
                $_SESSION['username'] = $user["email"];
                $_SESSION['user_role'] = $user["role"];

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

            
            /* ---------------- GET TEACHERS FOR DATATABLE ---------------- */
            case "getTeachers":

                // OPTIONAL: search
                $search = $_POST['search'] ?? '';

                $sql = "
                    SELECT 
                        users.id AS user_id,
                        users.name,
                        users.profile_photo,
                        teachers.department,
                        users.age,
                        users.gender,
                        users.birthdate,
                        users.address,
                        users.email
                    FROM users
                    INNER JOIN teachers ON teachers.teacher_id = users.id
                    WHERE users.name LIKE ?
                    ORDER BY users.id DESC
                ";

                $params = ["%$search%"];
                $data = $mydb->rawQuery($sql, $params);

                $response = [
                    "status" => "success",
                    "data" => $data
                ];
                
                break;
            
            /* ---------------- GET STUDENTS FOR DATATABLE ---------------- */
            case "getStudents":

                $search = $_POST['search'] ?? '';

                $sql = "
                    SELECT 
                        users.id AS user_id,
                        users.name,
                        users.profile_photo,
                        students.lrn,
                        users.age,
                        users.gender,
                        users.birthdate,
                        users.address,
                        users.email,
                        students.guardian_email,
                        students.guardian_contact
                    FROM users
                    INNER JOIN students ON students.student_id = users.id
                    WHERE users.name LIKE ?
                    ORDER BY users.id DESC
                ";

                $params = ["%$search%"];
                $data = $mydb->rawQuery($sql, $params);

                $response = [
                    "status" => "success",
                    "data" => $data
                ];

                break;


            /* ---------------- ADD NEW TEACHER ---------------- */
            case "addTeacher":

                $fullname = $_POST['fullname'] ?? '';
                $department = $_POST['departments'] ?? '';
                $address = $_POST['address'] ?? '';
                $birthdate = $_POST['birthdate'] ?? '';
                $gender = $_POST['gender'] ?? '';
                $email = $_POST['email'] ?? '';
                $password = $_POST['password'] ?? '';
                $isRegistered = $_POST['is_registered'] ?? 1;
                $role = "teacher";

                // Validate
                if (empty($fullname) || empty($department) || empty($email) || empty($password)) {
                    $response = ["status" => "error", "message" => "Please fill all required fields."];
                    break;
                }

                // Check if email already exists in users table
                $existing = $mydb->select("users", "*", ["email" => $email]);
                if ($existing) {
                    $response = ["status" => "error", "message" => "Email already exists!"];
                    break;
                }

                // hash password for login
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // Auto age calculation
                if (!empty($birthdate)) {
                    $age = (new DateTime())->diff(new DateTime($birthdate))->y;
                } else {
                    $age = null;
                }

                // INSERT USER
                $mydb->insert("users", [
                    "name" => $fullname,
                    "profile_photo" => "default.png",
                    "age" => $age,
                    "gender" => $gender,
                    "birthdate" => $birthdate,
                    "address" => $address,
                    "email" => $email,
                    "password" => $hashedPassword,
                    "role" => $role,
                    "is_registered" => $isRegistered
                ]);

                $userId = $mydb->getLastId();

                if (!$userId) {
                    $response = ["status" => "error", "message" => "Failed to create user record."];
                    break;
                }

                // INSERT TEACHER RECORD
                $mydb->insert("teachers", [
                    "teacher_id" => $userId,
                    "department" => $department
                ]);

                $response = [
                    "status" => "success",
                    "message" => "Teacher added successfully",
                    "user_id" => $userId
                ];
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
