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
                    $redirectPage = "class.php";
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


            /* ---------------- GET TEACHERS FOR TABLE ---------------- */
            case "getTeachers":
                try {
                    $search = $_POST["search"] ?? '';
                    $page = intval($_POST["page"] ?? 1);
                    $limit = 10;
                    $offset = ($page - 1) * $limit;

                    // Sorting
                    $sortColumn = $_POST['sortColumn'] ?? 'id';
                    $sortOrder = $_POST['sortOrder'] ?? 'DESC';
                    $allowedSortColumns = ['name', 'department', 'age', 'id'];
                    if (!in_array($sortColumn, $allowedSortColumns))
                        $sortColumn = 'id';
                    $sortOrder = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';

                    // Department filter
                    $whereClauses = ["users.name LIKE ?"];
                    $params = ["%$search%"];
                    if (!empty($_POST['department'])) {
                        $whereClauses[] = "teachers.department = ?";
                        $params[] = $_POST['department'];
                    }

                    // Registered filter
                    if (isset($_POST['registered']) && $_POST['registered'] !== '') {
                        $whereClauses[] = "users.is_registered = ?";
                        $params[] = $_POST['registered'];
                    }

                    $whereSQL = implode(" AND ", $whereClauses);

                    // Total rows
                    $countSql = "SELECT COUNT(*) AS total 
                     FROM users 
                     INNER JOIN teachers ON teachers.teacher_id = users.id 
                     WHERE $whereSQL";
                    $countData = $mydb->rawQuery($countSql, $params);
                    $total = $countData[0]['total'];

                    // Fetch paginated rows
                    $sql = "SELECT 
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
                    LIMIT $limit OFFSET $offset";

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


            /* ---------------- GET STUDENTS FOR TABLE ---------------- */
            case "getStudents":
                try {
                    $search = $_POST["search"] ?? '';
                    $page = intval($_POST["page"] ?? 1);
                    $limit = 10;
                    $offset = ($page - 1) * $limit;

                    // Sorting
                    $sortColumn = $_POST['sortColumn'] ?? 'id';
                    $sortOrder = $_POST['sortOrder'] ?? 'DESC';
                    $allowedSortColumns = ['name', 'lrn', 'age', 'id'];
                    if (!in_array($sortColumn, $allowedSortColumns))
                        $sortColumn = 'id';
                    $sortOrder = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';

                    // Search filter (no department for students)
                    $whereClauses = ["users.name LIKE ?"];
                    $params = ["%$search%"];

                    // Registered filter
                    if (isset($_POST['registered']) && $_POST['registered'] !== '') {
                        $whereClauses[] = "users.is_registered = ?";
                        $params[] = $_POST['registered'];
                    }

                    $whereSQL = implode(" AND ", $whereClauses);

                    // Total rows
                    $countSql = "SELECT COUNT(*) AS total 
                     FROM users 
                     INNER JOIN students ON students.student_id = users.id 
                     WHERE $whereSQL";
                    $countData = $mydb->rawQuery($countSql, $params);
                    $total = $countData[0]['total'];

                    // Fetch paginated rows
                    $sql = "SELECT 
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
                    LIMIT $limit OFFSET $offset";

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
                    $userId = $_SESSION["user_id"];

                    // Check for duplicate email
                    $existingEmail = $mydb->select_one("users", "*", ["email" => $_POST["email"]]);
                    if ($existingEmail && $existingEmail["id"] != $userId) {
                        $response = [
                            "status" => "error",
                            "message" => "Email is already in use."
                        ];
                        break;
                    }

                    // Prepare data for update
                    $userData = [
                        "name" => $_POST["fullname"],
                        "address" => $_POST["address"],
                        "birthdate" => $_POST["birthdate"],
                        "gender" => $_POST["gender"],
                        "email" => $_POST["email"]
                    ];

                    // Optional password
                    if (!empty($_POST["password"])) {
                        $userData["password"] = password_hash($_POST["password"], PASSWORD_DEFAULT);
                    }

                    $rowsUpdated = $mydb->update("users", $userData, ["id" => $userId]);
                    $rowsUpdatedTeacher = $mydb->update("teachers", ["department" => $_POST["departments"]], ["teacher_id" => $userId]);

                    if ($rowsUpdated > 0 || $rowsUpdatedTeacher > 0) {
                        $response = [
                            "status" => "success",
                            "message" => "Teacher updated successfully"
                        ];
                    } else {
                        $response = [
                            "status" => "error",
                            "message" => "No changes were made (check IDs and input values)."
                        ];
                    }

                } catch (Exception $e) {
                    $response = [
                        "status" => "error",
                        "message" => "Failed to update teacher: " . $e->getMessage()
                    ];
                }
                break;



            /* ---------------- UPDATE STUDENT ---------------- */
            case "update_student":
                try {
                    $userId = $_SESSION["user_id"];

                    // Check for duplicate email
                    $existingEmail = $mydb->select_one("users", "*", ["email" => $_POST["email"]]);
                    if ($existingEmail && $existingEmail["id"] != $userId) {
                        $response = [
                            "status" => "error",
                            "message" => "Email is already in use."
                        ];
                        break;
                    }

                    // Update users table
                    $userData = [
                        "name" => $_POST["fullname"],
                        "address" => $_POST["address"],
                        "birthdate" => $_POST["birthdate"],
                        "gender" => $_POST["gender"],
                        "email" => $_POST["email"]
                    ];

                    // Optional password
                    if (!empty($_POST["password"])) {
                        $userData["password"] = password_hash($_POST["password"], PASSWORD_DEFAULT);
                    }

                    // Update students table
                    $studentData = [
                        "lrn" => $_POST["lrn"],
                        "guardian_contact" => $_POST["guardian_contact"],
                        "guardian_email" => $_POST["guardian_email"]
                    ];

                    $rowsUpdated = $mydb->update("users", $userData, ["id" => $userId]);
                    $rowsUpdatedStudent = $mydb->update("students", $studentData, ["student_id" => $userId]);

                    if ($rowsUpdated > 0 || $rowsUpdatedStudent > 0) {
                        $response = ["status" => "success", "message" => "Student updated successfully"];
                    } else {
                        $response = ["status" => "error", "message" => "No changes were made (check IDs and input values)."];
                    }


                } catch (Exception $e) {
                    $response = [
                        "status" => "error",
                        "message" => "Failed to update student: " . $e->getMessage()
                    ];
                }
                break;



            /* ---------------- DELETE USER ---------------- */
            case "deleteUser":
                try {
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
