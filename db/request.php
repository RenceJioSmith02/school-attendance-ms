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

// if ($_SESSION['user_role'] !== 'student') {
//     exit(json_encode(["success" => false, "message" => "Unauthorized"]));
// }

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';

        switch ($action) {

            /* ---------------- LOGIN PROCESS ---------------- */
            case "login":

                $email = $_POST['email'] ?? '';
                $password = $_POST['password'] ?? '';

                if (empty($email) || empty($password)) {
                    $response = ["status" => "error", "message" => "Please enter both email and password"];
                    break;
                }

                // get user
                $user = $mydb->select("users", "*", ["email" => $email]);

                if (!$user) {
                    $response = ["status" => "error", "message" => "Invalid email or password"];
                    break;
                }

                $user = $user[0];

                // verify registration
                if ($user['is_registered'] == 0) {
                    $response = ["status" => "error", "message" => "Account not registered. Please contact the administrator."];
                    break;
                }

                // verify password
                if (!password_verify($password, $user['password'])) {
                    $response = ["status" => "error", "message" => "Invalid email or password"];
                    break;
                }

                // set session
                $_SESSION['user_id'] = $user["id"];
                $_SESSION['email'] = $user["email"];
                $_SESSION['user_role'] = $user["role"];

                // role-based redirect
                if ($user["role"] === "admin") {
                    $redirectPage = "users.php";
                } else {
                    $redirectPage = "classrooms.php";
                }

                $response = [
                    "status" => "success",
                    "message" => "Login successful",
                    "redirect" => $redirectPage
                ];
                break;
                

            /* ---------------- LOGOUT PROCESS ---------------- */
            case "logout":
                $_SESSION = []; //clears all session data.
                session_unset();
                session_destroy();

                $response = [
                    "status" => "success",
                    "message" => "Logged out successfully"
                ];
                break;



            case "getTeachers":
                try {

                    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
                        exit(json_encode(["success" => false, "message" => "Unauthorized"]));
                    }

                    $search = $_POST["search"] ?? '';
                    $page = intval($_POST["page"] ?? 1);
                    $limit = 10;
                    $offset = ($page - 1) * $limit;

                    $sortColumn = $_POST['sortColumn'] ?? 'id';
                    $sortOrder = $_POST['sortOrder'] ?? 'DESC';
                    $allowedSortColumns = ['name', 'department', 'age', 'id'];
                    if (!in_array($sortColumn, $allowedSortColumns)) $sortColumn = 'id';
                    $sortOrder = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';

                    $searchTerm = "%$search%";

                    $whereClauses = [
                        "(
                            users.name LIKE ?
                            OR teachers.department LIKE ?
                            OR users.age LIKE ?
                            OR users.gender LIKE ?
                            OR users.address LIKE ?
                            OR users.email LIKE ?
                            OR users.is_registered LIKE ?
                        )"
                    ];

                    $params = [
                        $searchTerm,
                        $searchTerm,
                        $searchTerm,
                        $searchTerm,
                        $searchTerm,
                        $searchTerm,
                        $searchTerm
                    ];

                    if (!empty($_POST['department'])) {
                        $whereClauses[] = "teachers.department = ?";
                        $params[] = $_POST['department'];
                    }

                    if (isset($_POST['registered']) && $_POST['registered'] !== '') {
                        $whereClauses[] = "users.is_registered = ?";
                        $params[] = $_POST['registered'];
                    }

                    $whereSQL = implode(" AND ", $whereClauses);

                    $countSql = "
                        SELECT COUNT(*) AS total
                        FROM users
                        INNER JOIN teachers ON teachers.teacher_id = users.id
                        WHERE $whereSQL
                    ";

                    $total = $mydb->rawQuery($countSql, $params)[0]['total'];

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
                            users.email,
                            users.is_registered
                        FROM users
                        INNER JOIN teachers ON teachers.teacher_id = users.id
                        WHERE $whereSQL
                        ORDER BY $sortColumn $sortOrder
                        LIMIT $limit OFFSET $offset
                    ";

                    $data = $mydb->rawQuery($sql, $params);

                    $response = [
                        "status" => "success",
                        "data" => $data,
                        "total" => $total,
                        "limit" => $limit,
                        "page" => $page
                    ];

                } catch (Exception $e) {
                    $response = [
                        "status" => "error",
                        "message" => "Failed to fetch teachers: " . $e->getMessage()
                    ];
                }
                break;


            case "getStudents":
                try {

                    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
                        exit(json_encode(["success" => false, "message" => "Unauthorized"]));
                    }

                    $search = $_POST["search"] ?? '';
                    $page = intval($_POST["page"] ?? 1);
                    $limit = 10;
                    $offset = ($page - 1) * $limit;

                    $sortColumn = $_POST['sortColumn'] ?? 'id';
                    $sortOrder = $_POST['sortOrder'] ?? 'DESC';
                    $allowedSortColumns = ['name', 'lrn', 'age', 'id'];
                    if (!in_array($sortColumn, $allowedSortColumns)) $sortColumn = 'id';
                    $sortOrder = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';

                    $searchTerm = "%$search%";

                    $whereClauses = [
                        "(
                            users.name LIKE ?
                            OR students.lrn LIKE ?
                            OR users.age LIKE ?
                            OR users.gender LIKE ?
                            OR users.address LIKE ?
                            OR users.email LIKE ?
                            OR students.guardian_email LIKE ?
                            OR students.guardian_contact LIKE ?
                            OR users.is_registered LIKE ?
                        )"
                    ];

                    $params = [
                        $searchTerm,
                        $searchTerm,
                        $searchTerm,
                        $searchTerm,
                        $searchTerm,
                        $searchTerm,
                        $searchTerm,
                        $searchTerm,
                        $searchTerm
                    ];

                    if (isset($_POST['registered']) && $_POST['registered'] !== '') {
                        $whereClauses[] = "users.is_registered = ?";
                        $params[] = $_POST['registered'];
                    }

                    $whereSQL = implode(" AND ", $whereClauses);

                    $countSql = "
                        SELECT COUNT(*) AS total
                        FROM users
                        INNER JOIN students ON students.student_id = users.id
                        WHERE $whereSQL
                    ";

                    $total = $mydb->rawQuery($countSql, $params)[0]['total'];

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
                            students.guardian_contact,
                            users.is_registered
                        FROM users
                        INNER JOIN students ON students.student_id = users.id
                        WHERE $whereSQL
                        ORDER BY $sortColumn $sortOrder
                        LIMIT $limit OFFSET $offset
                    ";

                    $data = $mydb->rawQuery($sql, $params);

                    $response = [
                        "status" => "success",
                        "data" => $data,
                        "total" => $total,
                        "limit" => $limit,
                        "page" => $page
                    ];

                } catch (Exception $e) {
                    $response = [
                        "status" => "error",
                        "message" => "Failed to fetch students: " . $e->getMessage()
                    ];
                }
                break;


            /* ---------------- GET USER INFO ---------------- */
            case "get_user_info":
                try {
                    if (!isset($_SESSION['user_id'])) {
                        $response = [
                            "status" => "error",
                            "message" => "Not logged in"
                        ];
                        break;
                    }

                    $user_id = $_SESSION['user_id'];
                    $role = $_SESSION['user_role'];

                    // Main user record
                    $user = $mydb->select_one("users", "*", ["id" => $user_id]);

                    if (!$user) {
                        $response = [
                            "status" => "error",
                            "message" => "User not found"
                        ];
                        break;
                    }

                    // Base data
                    $data = [
                        "fullname" => $user["name"],
                        "email" => $user["email"],
                        "role" => $role,
                        "address" => $user["address"],
                        "gender" => $user["gender"],
                        "birthdate" => $user["birthdate"],
                        "is_registered" => $user["is_registered"],
                        "profile_photo" => $user["profile_photo"],
                    ];

                    // Teacher info
                    if ($role === "teacher") {
                        $teacher = $mydb->select_one("teachers", "*", [
                            "teacher_id" => $user_id
                        ]);

                        if ($teacher) {
                            $data["department"] = $teacher["department"];
                        }
                    }

                    // Student info
                    if ($role === "student") {
                        $student = $mydb->select_one("students", "*", [
                            "student_id" => $user_id
                        ]);

                        if ($student) {
                            $data["lrn"] = $student["lrn"];
                            $data["guardian_contact"] = $student["guardian_contact"];
                            $data["guardian_email"] = $student["guardian_email"];
                        }
                    }

                    // Admin: Load Quarters
                    if ($role === "admin") {

                        $quarters = $mydb->select("quarter");

                        // Always prepare 4 rows
                        $formatted = [
                            ["quarter_name" => "", "start_date" => "", "end_date" => ""],
                            ["quarter_name" => "", "start_date" => "", "end_date" => ""],
                            ["quarter_name" => "", "start_date" => "", "end_date" => ""],
                            ["quarter_name" => "", "start_date" => "", "end_date" => ""],
                        ];

                        for ($i = 0; $i < count($quarters) && $i < 4; $i++) {
                            $formatted[$i] = [
                                "quarter_name" => $quarters[$i]["quarter_name"],
                                "start_date" => $quarters[$i]["start_date"],
                                "end_date" => $quarters[$i]["end_date"],
                            ];
                        }

                        $data["quarters"] = $formatted;
                    }

                    $response = [
                        "success" => true,
                        "message" => "User info loaded",
                        "data" => $data
                    ];

                } catch (Exception $e) {
                    $response = [
                        "status" => "error",
                        "message" => "Failed to fetch user info: " . $e->getMessage()
                    ];
                }
                break;




            /* ---------------- REGITER NEW USER ---------------- */
            case "registerUser":
                try {
                    $user_id = $_POST["user_id"];
                    $sql = "UPDATE users SET is_registered = 1 WHERE id = ?";
                    $mydb->rawQuery($sql, [$user_id]);

                    $response = [
                        "status" => "success",
                        "message" => "User successfully registered!"
                    ];
                } catch (Exception $e) {
                    $response = [
                        "status" => "error",
                        "message" => "Failed to register user: " . $e->getMessage()
                    ];
                }
                break;


            /* ---------------- ADD NEW TEACHER ---------------- */
            case "addTeacher":

                try {

                    // if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
                    //     exit(json_encode(["success" => false, "message" => "Unauthorized"]));
                    // }
                    
                    $fullname = $_POST['fullname'] ?? '';
                    $department = $_POST['departments'] ?? '';
                    $address = $_POST['address'] ?? '';
                    $birthdate = $_POST['birthdate'] ?? '';
                    $gender = $_POST['gender'] ?? '';
                    $email = $_POST['email'] ?? '';
                    $password = $_POST['password'] ?? '';
                    $isRegistered = $_POST['is_registered'] ?? 0;
                    $role = "teacher";

                    // Validate
                    if (empty($fullname) || empty($department) || empty($address) || empty($birthdate) || empty($email) || empty($password)) {
                        $response = ["status" => "error", "message" => "Please fill all required fields."];
                        break;
                    }

                    // Check if email already exists
                    $existing = $mydb->select("users", "*", ["email" => $email]);
                    if ($existing) {
                        $response = ["status" => "error", "message" => "Email already exists!"];
                        break;
                    }

                    // Hash password
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                    // Auto age calculation
                    if (!empty($birthdate)) {
                        $age = (new DateTime())->diff(new DateTime($birthdate))->y;
                    } else {
                        $age = null;
                    }

                    // INSERT INTO USERS TABLE
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

                    // INSERT INTO TEACHERS TABLE
                    $mydb->insert("teachers", [
                        "teacher_id" => $userId,
                        "department" => $department
                    ]);

                    $response = [
                        "status" => "success",
                        "message" => "Teacher added successfully",
                        "user_id" => $userId
                    ];

                } catch (Exception $e) {
                    $response = [
                        "status" => "error",
                        "message" => "Failed to add teacher: " . $e->getMessage()
                    ];
                }

                break;


            /* ---------------- ADD NEW STUDENT ---------------- */
            case "addStudent":

                try {

                    // if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
                    //     exit(json_encode(["success" => false, "message" => "Unauthorized"]));
                    // }

                    $fullname = $_POST['fullname'] ?? '';
                    $lrn = $_POST['lrn'] ?? '';
                    $address = $_POST['address'] ?? '';
                    $birthdate = $_POST['birthdate'] ?? '';
                    $gender = $_POST['gender'] ?? '';
                    $email = $_POST['email'] ?? '';
                    $password = $_POST['password'] ?? '';
                    $guardian_email = $_POST['guardian_email'] ?? '';
                    $guardian_contact = $_POST['guardian_contact'] ?? '';
                    $isRegistered = $_POST['is_registered'] ?? 0;
                    $role = "student";

                    // REQUIRED VALIDATION
                    if (
                        empty($fullname) || empty($lrn) || empty($address) || empty($birthdate) ||
                        empty($guardian_contact) || empty($guardian_email) ||
                        empty($email) || empty($password)
                    ) {

                        $response = ["status" => "error", "message" => "Please fill all required fields."];
                        break;
                    }

                    // CHECK EMAIL DUPLICATE
                    $existingEmail = $mydb->select("users", "*", ["email" => $email]);
                    if ($existingEmail) {
                        $response = ["status" => "error", "message" => "Email already exists!"];
                        break;
                    }

                    // CHECK LRN DUPLICATE
                    $existingLRN = $mydb->select("students", "*", ["lrn" => $lrn]);
                    if ($existingLRN) {
                        $response = ["status" => "error", "message" => "LRN already exists!"];
                        break;
                    }

                    // HASH PASSWORD
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                    // AUTO AGE CALCULATION
                    if (!empty($birthdate)) {
                        $age = (new DateTime())->diff(new DateTime($birthdate))->y;
                    } else {
                        $age = null;
                    }

                    // INSERT INTO USERS
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

                    // INSERT INTO STUDENTS TABLE
                    $mydb->insert("students", [
                        "student_id" => $userId,
                        "lrn" => $lrn,
                        "guardian_email" => $guardian_email,
                        "guardian_contact" => $guardian_contact
                    ]);

                    $response = [
                        "status" => "success",
                        "message" => "Student added successfully!",
                        "user_id" => $userId
                    ];

                } catch (Exception $e) {
                    $response = [
                        "status" => "error",
                        "message" => "Failed to add student: " . $e->getMessage()
                    ];
                }

                break;


            /* ---------------- UPDATE TEACHER ---------------- */
            case "update_teacher":
                try {

                    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
                        exit(json_encode(["success" => false, "message" => "Unauthorized"]));
                    }

                    $userId = $_SESSION["user_id"];

                    // Check for duplicate email
                    $existingEmail = $mydb->select_one("users", "*", ["email" => $_POST["email"]]);
                    if ($existingEmail && $existingEmail["id"] != $userId) {
                        $response = ["status" => "error", "message" => "Email is already in use."];
                        break;
                    }

                    // ========== HANDLE IMAGE UPLOAD ==========
                    $uploadDir = "../assets/images/user_image/";
                    $photoFilename = null;

                    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {

                        // Get old image
                        $old = $mydb->select_one("users", "profile_photo", ["id" => $userId]);
                        $oldImage = $old ? $old["profile_photo"] : null;

                        $tmp = $_FILES['profile_photo']['tmp_name'];
                        $ext = pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION);
                        $rand = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6);
                        $newName = $userId . "-" . $rand . "." . strtolower($ext);

                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }

                        if (move_uploaded_file($tmp, $uploadDir . $newName)) {
                            $photoFilename = $newName;

                            // Delete old image if not default
                            if ($oldImage && $oldImage !== "default.png") {
                                $oldPath = $uploadDir . $oldImage;
                                if (file_exists($oldPath))
                                    unlink($oldPath);
                            }
                        }
                    }

                    // Prepare user update data
                    $userData = [
                        "name" => $_POST["fullname"],
                        "address" => $_POST["address"],
                        "birthdate" => $_POST["birthdate"],
                        "gender" => $_POST["gender"],
                        "email" => $_POST["email"]
                    ];

                    if ($photoFilename) {
                        $userData["profile_photo"] = $photoFilename;
                    }

                    if (!empty($_POST["password"])) {
                        $userData["password"] = password_hash($_POST["password"], PASSWORD_DEFAULT);
                    }

                    $mydb->update("users", $userData, ["id" => $userId]);

                    // Update teacher table
                    $mydb->update("teachers", ["department" => $_POST["departments"]], ["teacher_id" => $userId]);

                    $response = ["status" => "success", "message" => "Teacher updated successfully"];

                } catch (Exception $e) {
                    $response = ["status" => "error", "message" => "Failed to update teacher: " . $e->getMessage()];
                }
                break;



            /* ---------------- UPDATE STUDENT ---------------- */
            case "update_student":
                try {

                    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
                        exit(json_encode(["success" => false, "message" => "Unauthorized"]));
                    }

                    $userId = $_SESSION["user_id"];

                    // Check email duplication
                    $existingEmail = $mydb->select_one("users", "*", ["email" => $_POST["email"]]);
                    if ($existingEmail && $existingEmail["id"] != $userId) {
                        $response = ["status" => "error", "message" => "Email is already in use."];
                        break;
                    }

                    // ========== HANDLE IMAGE UPLOAD ==========
                    $uploadDir = "../assets/images/user_image/";
                    $photoFilename = null;

                    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {

                        $old = $mydb->select_one("users", "profile_photo", ["id" => $userId]);
                        $oldImage = $old ? $old["profile_photo"] : null;

                        $tmp = $_FILES['profile_photo']['tmp_name'];
                        $ext = pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION);
                        $rand = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6);
                        $newName = $userId . "-" . $rand . "." . strtolower($ext);

                        if (!is_dir($uploadDir))
                            mkdir($uploadDir, 0777, true);

                        if (move_uploaded_file($tmp, $uploadDir . $newName)) {
                            $photoFilename = $newName;

                            if ($oldImage && $oldImage !== "default.png") {
                                $oldPath = $uploadDir . $oldImage;
                                if (file_exists($oldPath))
                                    unlink($oldPath);
                            }
                        }
                    }

                    // Build user data
                    $userData = [
                        "name" => $_POST["fullname"],
                        "address" => $_POST["address"],
                        "birthdate" => $_POST["birthdate"],
                        "gender" => $_POST["gender"],
                        "email" => $_POST["email"],
                    ];

                    if ($photoFilename) {
                        $userData["profile_photo"] = $photoFilename;
                    }

                    if (!empty($_POST["password"])) {
                        $userData["password"] = password_hash($_POST["password"], PASSWORD_DEFAULT);
                    }

                    $mydb->update("users", $userData, ["id" => $userId]);

                    // Update students table
                    $studentData = [
                        "lrn" => $_POST["lrn"],
                        "guardian_contact" => $_POST["guardian_contact"],
                        "guardian_email" => $_POST["guardian_email"]
                    ];

                    $mydb->update("students", $studentData, ["student_id" => $userId]);

                    $response = ["status" => "success", "message" => "Student updated successfully"];

                } catch (Exception $e) {
                    $response = ["status" => "error", "message" => "Failed to update student: " . $e->getMessage()];
                }
                break;




            /* ---------------- DELETE USER ---------------- */
            case "deleteUser":
                try {

                    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
                        exit(json_encode(["success" => false, "message" => "Unauthorized"]));
                    }

                    $userId = $_POST['user_id'] ?? 0;
                    $role = $_POST['role'] ?? '';

                    if (!$userId || !in_array($role, ['teacher', 'student'])) {
                        $response = ["status" => "error", "message" => "Invalid request"];
                        break;
                    }

                    // Delete from role-specific table first
                    if ($role === "teacher") {
                        $mydb->delete("teachers", ["teacher_id" => $userId]);
                    } else {
                        $mydb->delete("students", ["student_id" => $userId]);
                    }

                    // Delete from users table
                    $mydb->delete("users", ["id" => $userId]);

                    $response = ["status" => "success", "message" => "User deleted successfully"];
                } catch (Exception $e) {
                    $response = ["status" => "error", "message" => "Failed to delete user: " . $e->getMessage()];
                }
                break;



            /* ---------------- UPDATE QUARTERS ---------------- */
            case "update_quarters":
                try {

                    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
                        exit(json_encode(["success" => false, "message" => "Unauthorized"]));
                    }

                    // Build array of 4 quarters from POST
                    $quarters = [];
                    for ($i = 0; $i < 4; $i++) {
                        $quarters[] = [
                            "quarter_name" => $_POST["quarter_name"][$i],
                            "start_date" => $_POST["start_date"][$i],
                            "end_date" => $_POST["end_date"][$i],
                        ];
                    }

                    // Fetch existing quarters
                    $existing = $mydb->select("quarter");

                    // If no quarters exist → INSERT 4 rows
                    if (count($existing) == 0) {

                        foreach ($quarters as $q) {
                            $mydb->insert("quarter", $q);
                        }

                    } else {

                        // UPDATE existing rows
                        for ($i = 0; $i < count($existing); $i++) {
                            $mydb->update("quarter", $quarters[$i], ["id" => $existing[$i]["id"]]);
                        }

                        // INSERT missing rows if less than 4
                        if (count($existing) < 4) {
                            for ($i = count($existing); $i < 4; $i++) {
                                $mydb->insert("quarter", $quarters[$i]);
                            }
                        }
                    }

                    $response = [
                        "status" => "success",
                        "message" => "Quarter settings updated"
                    ];

                } catch (Exception $e) {
                    $response = [
                        "status" => "error",
                        "message" => "Failed to update quarters: " . $e->getMessage()
                    ];
                }
                break;


            /* ---------------- UPDATE ADMIN PASSWORD ---------------- */
            case "update_admin_password":
                try {

                    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
                        exit(json_encode(["success" => false, "message" => "Unauthorized"]));
                    }

                    $userId = $_SESSION["user_id"];
                    $newPass = password_hash($_POST["password"], PASSWORD_DEFAULT);

                    $mydb->update("users", [
                        "password" => $newPass
                    ], ["id" => $userId]);

                    $response = [
                        "status" => "success",
                        "message" => "Password updated"
                    ];

                } catch (Exception $e) {
                    $response = [
                        "status" => "error",
                        "message" => "Failed to update admin password: " . $e->getMessage()
                    ];
                }
                break;




            /* ---------------- ADD CLASSROOM ---------------- */
            case "add_classroom":
                try {

                    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== "teacher") {
                        $response = ["status" => "error", "message" => "Unauthorized request."];
                        break;
                    }

                    $teacher_id = $_SESSION['user_id'];

                    $section_name = $_POST['section_name'] ?? '';
                    $grade_level = $_POST['grade_level'] ?? '';
                    $subject_name = $_POST['subject_name'] ?? '';
                    $subject_description = $_POST['subject_description'] ?? '';
                    $background_color = $_POST['class_color'] ?? '#15285C';

                    // Generate academic year
                    $year = date("Y");
                    $academic_year = $year . " - " . ($year + 1);

                    // Status default
                    $status = "active";

                    // Validate required fields
                    if (empty($section_name) || empty($grade_level) || empty($subject_name)) {
                        $response = ["status" => "error", "message" => "Please fill all required fields."];
                        break;
                    }

                    // Insert into classroom table
                    $mydb->insert("classrooms", [
                        "teacher_id" => $teacher_id,
                        "section_name" => $section_name,
                        "grade_level" => $grade_level,
                        "academic_year" => $academic_year,
                        "subject_name" => $subject_name,
                        "subject_description" => $subject_description,
                        "background_color" => $background_color,
                        "status" => $status
                    ]);

                    $response = [
                        "status" => "success",
                        "message" => "Classroom successfully created."
                    ];

                } catch (Exception $e) {
                    $response = [
                        "status" => "error",
                        "message" => "Failed to create classroom: " . $e->getMessage()
                    ];
                }
                break;


            /* ---------------- UPDATE CLASSROOM ---------------- */
            case "update_classroom":

                
                try {
                    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'teacher') {
                        exit(json_encode(["success" => false, "message" => "Unauthorized"]));
                    }

                    $class_id = $_POST["class_id"] ?? null;

                    if (!$class_id) {
                        $response = ["status" => "error", "message" => "Missing classroom ID."];
                        break;
                    }

                    $updateData = [
                        "section_name" => $_POST["section_name"],
                        "grade_level" => $_POST["grade_level"],
                        "subject_name" => $_POST["subject_name"],
                        "subject_description" => $_POST["subject_description"],
                        "background_color" => $_POST["class_color"]
                    ];

                    $rows = $mydb->update("classrooms", $updateData, ["id" => $class_id]);

                    if ($rows > 0) {
                        $response = ["status" => "success", "message" => "Classroom updated successfully"];
                    } else {
                        $response = ["status" => "error", "message" => "No changes made."];
                    }

                } catch (Exception $e) {
                    $response = ["status" => "error", "message" => $e->getMessage()];
                }

                break;


                
            /* ---------------- GET CLASSROOMS ---------------- */
            case "get_classrooms":

                
                try {
                    if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'teacher' && $_SESSION['user_role'] !== 'student')) {
                        exit(json_encode(["success" => false, "message" => "Unauthorized"]));
                    }

                    if (!isset($_SESSION['user_id'])) {
                        $response = ["success" => false, "message" => "Not logged in"];
                        break;
                    }

                    $user_id = $_SESSION['user_id'];
                    $role = $_SESSION['user_role'];

                    // Receive filters
                    $status = $_POST["status"] ?? "active";
                    $search = $_POST["search"] ?? "";
                    $searchLike = "%" . $search . "%";

                    // ========== TEACHER ==========
                    if ($role === "teacher") {

                        $sql = "
                            SELECT c.*, 
                                u.name AS teacher_name,
                                u.profile_photo AS teacher_photo,
                                (
                                    SELECT COUNT(*) 
                                    FROM class_members 
                                    WHERE class_id = c.id AND status = 'joined'
                                ) AS total_students
                            FROM classrooms c
                            LEFT JOIN users u ON u.id = c.teacher_id
                            WHERE c.teacher_id = ?
                            AND c.status = ?
                            AND (
                                    c.section_name LIKE ?
                                    OR c.subject_name LIKE ?
                                    OR c.academic_year LIKE ?
                                    OR c.grade_level LIKE ?
                                )
                            ORDER BY c.created_at DESC
                        ";

                        $result = $mydb->rawQuery(
                            $sql,
                            [$user_id, $status, $searchLike, $searchLike, $searchLike, $searchLike]
                        );
                    }

                    // ========== STUDENT ==========
                    else if ($role === "student") {

                        $sql = "
                            SELECT c.*, 
                                u.name AS teacher_name,
                                u.profile_photo AS teacher_photo,
                                cm.status AS membership_status,
                                (
                                    SELECT COUNT(*) 
                                    FROM class_members 
                                    WHERE class_id = c.id AND status = 'joined'
                                ) AS total_students
                            FROM class_members cm
                            INNER JOIN classrooms c ON c.id = cm.class_id
                            LEFT JOIN users u ON u.id = c.teacher_id
                            WHERE cm.student_id = ?
                            AND c.status = ?
                            AND (
                                    c.section_name LIKE ?
                                    OR c.subject_name LIKE ?
                                    OR c.academic_year LIKE ?
                                    OR c.grade_level LIKE ?
                                )
                            ORDER BY c.created_at DESC
                        ";

                        $result = $mydb->rawQuery(
                            $sql,
                            [$user_id, $status, $searchLike, $searchLike, $searchLike, $searchLike]
                        );
                    }

                    // ========== OTHER ROLES ==========
                    else {
                        $result = [];
                    }

                    $response = [
                        "success" => true,
                        "data" => $result
                    ];

                } catch (Exception $e) {
                    $response = [
                        "success" => false,
                        "message" => "Failed to load classrooms: " . $e->getMessage()
                    ];
                }

                break;



            /* ---------------- GET SINGLE CLASSROOM ---------------- */
            case "get_single_classroom":

                
                try {
                    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'teacher') {
                        exit(json_encode(["success" => false, "message" => "Unauthorized"]));
                    }

                    $id = $_POST["id"] ?? null;

                    if (!$id) {
                        $response = ["success" => false, "message" => "Missing classroom ID"];
                        break;
                    }

                    $class = $mydb->select_one("classrooms", "*", ["id" => $id]);

                    if (!$class) {
                        $response = ["success" => false, "message" => "Classroom not found"];
                        break;
                    }

                    $response = ["success" => true, "data" => $class];

                } catch (Exception $e) {
                    $response = ["success" => false, "message" => $e->getMessage()];
                }

                break;


            /* ---------------- ARCHIVING ---------------- */
            case "archive_classroom":

                
                try {
                    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'teacher') {
                        exit(json_encode(["success" => false, "message" => "Unauthorized"]));
                    }

                    $class_id = $_POST["class_id"];

                    $sql = "UPDATE classrooms SET status = 'archived' WHERE id = ?";
                    $mydb->rawQuery($sql, [$class_id]);

                    $response = [
                        "status" => "success",
                        "message" => "Class archived successfully"
                    ];

                } catch (Exception $e) {
                    $response = [
                        "status" => "error",
                        "message" => $e->getMessage()
                    ];
                }

                break;


            /* ---------------- UNARCHIVING ---------------- */
            case "unarchive_classroom":

                try {
                    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'teacher') {
                        exit(json_encode(["success" => false, "message" => "Unauthorized"]));
                    }

                    $class_id = $_POST["class_id"];

                    $sql = "UPDATE classrooms SET status = 'active' WHERE id = ?";
                    $mydb->rawQuery($sql, [$class_id]);

                    $response = [
                        "status" => "success",
                        "message" => "Class unarchived successfully"
                    ];

                } catch (Exception $e) {
                    $response = [
                        "status" => "error",
                        "message" => $e->getMessage()
                    ];
                }

                break;


            /* ---------------- DELETE CLASSROOM ---------------- */
            case "delete_classroom":

                try {
                    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'teacher') {
                        exit(json_encode(["success" => false, "message" => "Unauthorized"]));
                    }

                    $class_id = $_POST["class_id"];

                    // Use the myDB delete function
                    $mydb->delete("classrooms", ["id" => $class_id]);

                    $response = [
                        "status" => "success",
                        "message" => "Classroom deleted successfully"
                    ];

                } catch (Exception $e) {
                    $response = [
                        "status" => "error",
                        "message" => $e->getMessage()
                    ];
                }

                break;


                /* ---------------- GET CLASS MEMBERS ---------------- */
            case "getClassMembers":
                try {
                    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'teacher') {
                        exit(json_encode(["success" => false, "message" => "Unauthorized"]));
                    }

                    $class_id = $_POST['class_id'];
                    $search   = $_POST['search'] ?? '';

                    $searchTerm = "%$search%";

                    $sql = "
                        SELECT
                            u.id AS student_id,
                            u.name,
                            u.profile_photo,
                            s.lrn,
                            u.age,
                            u.gender,
                            u.birthdate,
                            u.address,
                            u.email,
                            s.guardian_email,
                            s.guardian_contact,
                            cm.status
                        FROM class_members cm
                        INNER JOIN students s ON s.student_id = cm.student_id
                        INNER JOIN users u ON u.id = s.student_id
                        WHERE cm.class_id = ?
                        AND (
                            u.name LIKE ?
                            OR s.lrn LIKE ?
                            OR u.email LIKE ?
                        )
                        AND cm.status = 'joined'
                        ORDER BY u.name ASC
                    ";

                    $data = $mydb->rawQuery($sql, [
                        $class_id,
                        $searchTerm,
                        $searchTerm,
                        $searchTerm
                    ]);

                    $response = [
                        "status" => "success",
                        "data" => $data
                    ];

                } catch (Exception $e) {
                    $response = ["status" => "error", "message" => $e->getMessage()];
                }
                break;


            /* ---------------- GET CLASS MEMBERS ATTENDANCE ---------------- */
            case "getClassMembersAttendance":
                try {

                    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'teacher') {
                        exit(json_encode(["success" => false, "message" => "Unauthorized"]));
                    }

                    $class_id = $_POST['class_id'];
                    $date     = $_POST['attendance_date'];
                    $search   = $_POST['search'] ?? '';

                    $searchTerm = "%$search%";

                    $sql = "
                        SELECT
                            u.id AS student_id,
                            u.name,
                            u.profile_photo,
                            s.lrn,
                            u.age,
                            u.gender,
                            u.birthdate,
                            u.address,
                            u.email,
                            s.guardian_email,
                            s.guardian_contact,
                            cm.status AS class_status,
                            a.status  AS attendance_status,
                            a.reason
                        FROM class_members cm
                        INNER JOIN students s ON s.student_id = cm.student_id
                        INNER JOIN users u ON u.id = s.student_id
                        LEFT JOIN attendance a
                            ON a.student_id = cm.student_id
                            AND a.class_id = cm.class_id
                            AND a.date = ?
                        WHERE cm.class_id = ?
                        AND (
                            u.name LIKE ?
                            OR s.lrn LIKE ?
                            OR u.email LIKE ?
                        )
                        AND cm.status = 'joined'
                        ORDER BY u.name ASC
                    ";

                    $data = $mydb->rawQuery($sql, [
                        $date,
                        $class_id,
                        $searchTerm,
                        $searchTerm,
                        $searchTerm
                    ]);

                    $response = [
                        "status" => "success",
                        "data" => $data
                    ];

                } catch (Exception $e) {
                    $response = ["status" => "error", "message" => $e->getMessage()];
                }
                break;



            /* ---------------- GET ATTENDANCE REPORT ---------------- */
            case "getAttendanceReport":
                try {
                    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'teacher') {
                        exit(json_encode(["success" => false, "message" => "Unauthorized"]));
                    }

                    $class_id = $_POST['class_id'];
                    $quarter  = $_POST['quarter'] ?? null;
                    $search   = trim($_POST['search'] ?? '');

                    $params = [];
                    $searchSQL = "";

                    /* ---- SEARCH FILTER (Name, LRN, Email ONLY) ---- */
                    if ($search !== "") {
                        $searchSQL = "
                            AND (
                                u.name  LIKE ?
                                OR s.lrn LIKE ?
                                OR u.email LIKE ?
                            )
                        ";
                        $like = "%{$search}%";
                        $params[] = $like;
                        $params[] = $like;
                        $params[] = $like;
                    }

                    /* ---- QUARTER DATE FILTER ---- */
                    $dateFilter = "";
                    if ($quarter) {
                        $q = $mydb->rawQuery(
                            "SELECT start_date, end_date
                            FROM quarter
                            WHERE quarter_name = ?
                            LIMIT 1",
                            [$quarter]
                        );

                        if ($q) {
                            $dateFilter = "AND a.date BETWEEN ? AND ?";
                            $params[] = $q[0]['start_date'];
                            $params[] = $q[0]['end_date'];
                        }
                    }

                    /* class_id LAST */
                    $params[] = $class_id;

                    $sql = "
                        SELECT
                            u.id AS student_id,
                            u.name,
                            u.profile_photo,
                            s.lrn,
                            u.email,

                            COALESCE(SUM(CASE WHEN a.status = 'present' THEN 1 END), 0) AS total_present,
                            COALESCE(SUM(CASE WHEN a.status = 'late' THEN 1 END), 0)    AS total_late,
                            COALESCE(SUM(CASE WHEN a.status = 'absent' THEN 1 END), 0)  AS total_absent,
                            COALESCE(SUM(CASE WHEN a.status = 'excuse' THEN 1 END), 0)  AS total_excuse

                        FROM class_members cm
                        INNER JOIN students s ON s.student_id = cm.student_id
                        INNER JOIN users u ON u.id = s.student_id
                        LEFT JOIN attendance a
                            ON a.student_id = cm.student_id
                            AND a.class_id = cm.class_id
                            $dateFilter
                        WHERE cm.class_id = ?
                        AND cm.status = 'joined'
                        $searchSQL
                        GROUP BY u.id
                        ORDER BY u.name ASC
                    ";

                    $data = $mydb->rawQuery($sql, $params);

                    $response = [
                        "status" => "success",
                        "data" => $data
                    ];

                } catch (Exception $e) {
                    $response = [
                        "status" => "error",
                        "message" => $e->getMessage()
                    ];
                }
                break;


            /* ---------------- GET INVITABLE STUDENTS ---------------- */
            case "getInvitableStudents":
                try {

                    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'teacher') {
                        exit(json_encode(["success" => false, "message" => "Unauthorized"]));
                    }

                    $class_id = $_POST['class_id'];
                    $search = $_POST['search'] ?? '';
                    $page = intval($_POST['page'] ?? 1);
                    $limit = 10;
                    $offset = ($page - 1) * $limit;

                    $searchTerm = "%$search%";

                    $sql = "
                        SELECT
                            u.id AS student_id,
                            u.name,
                            u.profile_photo,
                            s.lrn,
                            u.age,
                            u.gender,
                            u.birthdate,
                            u.address,
                            u.email
                        FROM students s
                        INNER JOIN users u ON u.id = s.student_id
                        WHERE u.is_registered = 1
                        AND (
                            u.name LIKE ?
                            OR s.lrn LIKE ?
                            OR u.email LIKE ?
                            OR u.gender LIKE ?
                            OR u.address LIKE ?
                        )
                        AND u.id NOT IN (
                            SELECT student_id FROM class_members WHERE class_id = ?
                        )
                        ORDER BY u.name ASC
                        LIMIT $limit OFFSET $offset
                    ";

                    $params = [
                        $searchTerm,
                        $searchTerm,
                        $searchTerm,
                        $searchTerm,
                        $searchTerm,
                        $class_id
                    ];

                    $data = $mydb->rawQuery($sql, $params);

                    $response = [
                        "status" => "success",
                        "data" => $data
                    ];
                } catch (Exception $e) {
                    $response = ["status" => "error", "message" => $e->getMessage()];
                }
                break;



            /* ---------------- INVITE STUDENT TO CLASS ---------------- */
            case "inviteStudentToClass":
                try {

                    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'teacher') {
                        exit(json_encode(["success" => false, "message" => "Unauthorized"]));
                    }

                    $class_id = $_POST['class_id'];
                    $student_id = $_POST['student_id'];

                    $mydb->insert("class_members", [
                        "class_id" => $class_id,
                        "student_id" => $student_id,
                        "status" => "pending"
                    ]);

                    $response = [
                        "status" => "success",
                        "message" => "Student successfully added to class"
                    ];
                } catch (Exception $e) {
                    $response = ["status" => "error", "message" => $e->getMessage()];
                }
                break;


            /* ---------------- REMOVE STUDENT FROM CLASS ---------------- */
            case "removeStudentFromClass":
                try {

                    // Security check
                    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== "teacher") {
                        throw new Exception("Unauthorized access");
                    }

                    $class_id = $_POST['class_id'] ?? null;
                    $student_id = $_POST['student_id'] ?? null;

                    if (!$class_id || !$student_id) {
                        throw new Exception("Invalid parameters");
                    }

                    // Optional: verify this teacher owns the class
                    $class = $mydb->select_one("classrooms", "*", [
                        "id" => $class_id,
                        "teacher_id" => $_SESSION['user_id']
                    ]);

                    if (!$class) {
                        throw new Exception("You are not allowed to modify this class");
                    }

                    // Remove student from class
                    $mydb->rawQuery(
                        "DELETE FROM class_members WHERE class_id = ? AND student_id = ?",
                        [$class_id, $student_id]
                    );

                    $response = [
                        "status" => "success",
                        "message" => "Student removed from class successfully"
                    ];

                } catch (Exception $e) {
                    $response = [
                        "status" => "error",
                        "message" => $e->getMessage()
                    ];
                }
                break;




            /* ---------------- SAVE ATTENDANCE ---------------- */
            case "saveAttendance":
                try {
                    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'teacher') {
                        exit(json_encode(["success" => false, "message" => "Unauthorized"]));
                    }
                    
                    $class_id   = $_POST['class_id'];
                    $student_id = $_POST['student_id'];
                    $date       = $_POST['date'];
                    $status     = $_POST['status'];
                    $reason     = $_POST['reason'] ?? null;

                    // Check if attendance exists
                    $existing = $mydb->rawQuery(
                        "SELECT id FROM attendance 
                        WHERE class_id = ? AND student_id = ? AND date = ?",
                        [$class_id, $student_id, $date]
                    );

                    if (!empty($existing)) {
                        // Update
                        $mydb->rawQuery(
                            "UPDATE attendance 
                            SET status = ?, reason = ?
                            WHERE id = ?",
                            [$status, $reason, $existing[0]['id']]
                        );
                    } else {
                        // Insert
                        $mydb->rawQuery(
                            "INSERT INTO attendance 
                                (class_id, student_id, date, status, reason)
                            VALUES (?, ?, ?, ?, ?)",
                            [$class_id, $student_id, $date, $status, $reason]
                        );
                    }

                    $response = [
                        "status" => "success",
                        "message" => "Attendance saved"
                    ];

                } catch (Exception $e) {
                    $response = [
                        "status" => "error",
                        "message" => $e->getMessage()
                    ];
                }
                break;


            
                /* ---------------- DOWNLOAD ATTENDANCE REPORT ---------------- */
                case "downloadAttendanceReport":
                    try {

                        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== "teacher") {
                            throw new Exception("Unauthorized");
                        }

                        $class_id = $_POST['class_id'];
                        $quarter  = $_POST['quarter'] ?? null;
                        $search   = trim($_POST['search'] ?? '');

                        $params = [];
                        $searchSQL = "";

                        /* ---- SEARCH FILTER ---- */
                        if ($search !== "") {
                            $searchSQL = "
                                AND (
                                    u.name LIKE ?
                                    OR s.lrn LIKE ?
                                    OR u.email LIKE ?
                                )
                            ";
                            $like = "%$search%";
                            $params[] = $like;
                            $params[] = $like;
                            $params[] = $like;
                        }

                        /* ---- QUARTER DATE FILTER (SAME AS TABLE) ---- */
                        $dateFilter = "";
                        if ($quarter) {
                            $q = $mydb->rawQuery(
                                "SELECT start_date, end_date
                                FROM quarter
                                WHERE quarter_name = ?
                                LIMIT 1",
                                [$quarter]
                            );

                            if ($q) {
                                $dateFilter = "AND a.date BETWEEN ? AND ?";
                                $params[] = $q[0]['start_date'];
                                $params[] = $q[0]['end_date'];
                            }
                        }

                        /* class_id LAST */
                        $params[] = $class_id;

                        $sql = "
                            SELECT
                                u.name,
                                s.lrn,
                                u.email,
                                COALESCE(SUM(CASE WHEN a.status = 'present' THEN 1 END), 0) AS total_present,
                                COALESCE(SUM(CASE WHEN a.status = 'late' THEN 1 END), 0)    AS total_late,
                                COALESCE(SUM(CASE WHEN a.status = 'absent' THEN 1 END), 0)  AS total_absent,
                                COALESCE(SUM(CASE WHEN a.status = 'excuse' THEN 1 END), 0)  AS total_excuse
                            FROM class_members cm
                            INNER JOIN students s ON s.student_id = cm.student_id
                            INNER JOIN users u ON u.id = s.student_id
                            LEFT JOIN attendance a
                                ON a.student_id = cm.student_id
                                AND a.class_id = cm.class_id
                                $dateFilter
                            WHERE cm.class_id = ?
                            AND cm.status = 'joined'
                            $searchSQL
                            GROUP BY u.id
                            ORDER BY u.name ASC
                        ";

                        $rows = $mydb->rawQuery($sql, $params);

                        header("Content-Type: text/csv");
                        header("Content-Disposition: attachment; filename=attendance_report.csv");
                        header("Pragma: no-cache");
                        header("Expires: 0");

                        $output = fopen("php://output", "w");

                        fputcsv($output, [
                            "Name",
                            "LRN",
                            "Email",
                            "Total Present",
                            "Total Late",
                            "Total Absent",
                            "Total Excused"
                        ]);

                        foreach ($rows as $r) {
                            fputcsv($output, [
                                $r['name'],
                                $r['lrn'],
                                $r['email'],
                                $r['total_present'],
                                $r['total_late'],
                                $r['total_absent'],
                                $r['total_excuse']
                            ]);
                        }

                        fclose($output);
                        exit; // 🔥 REQUIRED

                    } catch (Exception $e) {
                        http_response_code(403);
                        echo $e->getMessage();
                        exit;
                    }
                    break;




            // students actions

            /* ---------------- JOIN CLASS ---------------- */
            case "joinClass":
                try {
                    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== "student") {
                        throw new Exception("Unauthorized");
                    }

                    $class_id = $_POST['class_id'];
                    $student_id = $_SESSION['user_id'];

                    // Update existing invitation
                    $mydb->rawQuery(
                        "UPDATE class_members 
             SET status = 'joined'
             WHERE class_id = ? AND student_id = ? AND status = 'pending'",
                        [$class_id, $student_id]
                    );

                    $response = [
                        "success" => true,
                        "message" => "You have successfully joined the class"
                    ];

                } catch (Exception $e) {
                    $response = [
                        "success" => false,
                        "message" => $e->getMessage()
                    ];
                }
                break;



            /* ---------------- DECLINE CLASS ---------------- */
            case "declineClass":
                try {
                    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== "student") {
                        throw new Exception("Unauthorized");
                    }

                    $class_id = $_POST['class_id'];
                    $student_id = $_SESSION['user_id'];

                    // Remove pending invitation
                    $mydb->rawQuery(
                        "DELETE FROM class_members 
             WHERE class_id = ? AND student_id = ? AND status = 'pending'",
                        [$class_id, $student_id]
                    );

                    $response = [
                        "success" => true,
                        "message" => "Class invitation declined"
                    ];

                } catch (Exception $e) {
                    $response = [
                        "success" => false,
                        "message" => $e->getMessage()
                    ];
                }
                break;



            /* ---------------- GET STUDENT ATTENDANCE (CALENDAR + TOTALS) ---------------- */
            case "getStudentAttendance":
                try {
                    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== "student") {
                        throw new Exception("Unauthorized");
                    }

                    $class_id = $_POST['class_id'];
                    $student_id = $_SESSION['user_id'];
                    $month = $_POST['month']; // YYYY-MM

                    $startDate = $month . "-01";
                    $endDate = date("Y-m-t", strtotime($startDate));

                    // ========== DAILY ATTENDANCE ==========
                    $attendanceSql = "
                        SELECT 
                            date,
                            status
                        FROM attendance
                        WHERE class_id = ?
                        AND student_id = ?
                        AND date BETWEEN ? AND ?
                    ";

                    $attendanceData = $mydb->rawQuery($attendanceSql, [
                        $class_id,
                        $student_id,
                        $startDate,
                        $endDate
                    ]);

                    // ========== TOTAL COUNTS ==========
                    $totalsSql = "
            SELECT
                SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) AS total_present,
                SUM(CASE WHEN status = 'absent'  THEN 1 ELSE 0 END) AS total_absent,
                SUM(CASE WHEN status = 'late'    THEN 1 ELSE 0 END) AS total_late,
                SUM(CASE WHEN status = 'excuse'  THEN 1 ELSE 0 END) AS total_excuse
            FROM attendance
            WHERE class_id = ?
              AND student_id = ?
              AND date BETWEEN ? AND ?
        ";

                    $totals = $mydb->rawQuery($totalsSql, [
                        $class_id,
                        $student_id,
                        $startDate,
                        $endDate
                    ])[0];

                    $response = [
                        "status" => "success",
                        "data" => $attendanceData,
                        "totals" => $totals
                    ];

                } catch (Exception $e) {
                    $response = [
                        "status" => "error",
                        "message" => $e->getMessage()
                    ];
                }
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
