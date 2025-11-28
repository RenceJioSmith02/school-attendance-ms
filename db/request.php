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

            /* ---------------- ADD MEMBER ---------------- */
            case "addMember":
                $fname = trim($_POST['user_fname'] ?? '');
                $mname = trim($_POST['user_mname'] ?? '');
                $lname = trim($_POST['user_lname'] ?? '');
                $suffix = trim($_POST['user_suffix'] ?? '');
                $email = trim($_POST['user_email'] ?? '');
                $contact = trim($_POST['user_contact'] ?? '');
                $bday = trim($_POST['user_birthday'] ?? '');
                $rfid = trim($_POST['user_rfid'] ?? '');

                // ========== VALIDATION ==========

                // Check for exact full name
                $checkName = $mydb->rawQuery(
                    "SELECT user_id FROM users_user 
                        WHERE user_fname = ? AND user_mname = ? AND user_lname = ? AND user_suffix = ?",
                    [$fname, $mname, $lname, $suffix]
                );
                if (!empty($checkName)) {
                    $response = [
                        "status" => "error",
                        "message" => "A member with the same full name already exists."
                    ];
                    break;
                }

                // Check for duplicate RFID, contact number or email

                if (!empty($rfid)) {
                    $checkRfid = $mydb->rawQuery(
                        "SELECT user_id FROM users_user WHERE user_rfid = ?",
                        [$rfid]
                    );
                    if (!empty($checkRfid)) {
                        $response = [
                            "status" => "error",
                            "message" => "RFID is already registered."
                        ];
                        break;
                    }
                }

                if (!empty($email)) {
                    $checkEmail = $mydb->rawQuery(
                        "SELECT user_id FROM users_user WHERE user_email = ?",
                        [$email]
                    );
                    if (!empty($checkEmail)) {
                        $response = [
                            "status" => "error",
                            "message" => "Email address is already registered."
                        ];
                        break;
                    }
                }

                if (!empty($contact)) {
                    $checkContact = $mydb->rawQuery(
                        "SELECT user_id FROM users_user WHERE user_contact = ?",
                        [$contact]
                    );
                    if (!empty($checkContact)) {
                        $response = [
                            "status" => "error",
                            "message" => "Contact number is already registered."
                        ];
                        break;
                    }
                }

                // Age validation (must be 16+)
                if (!empty($bday)) {
                    $birthDate = new DateTime($bday);
                    $today = new DateTime('now', new DateTimeZone('Asia/Manila'));
                    $age = $birthDate->diff($today)->y;

                    if ($age < 16) {
                        $response = [
                            "status" => "error",
                            "message" => "Member must be at least 17 years old."
                        ];
                        break;
                    }
                }

                // ========== IF PASSES VALIDATION, PROCEED ==========
                $userData = [
                    'user_fname' => $fname,
                    'user_mname' => $mname,
                    'user_lname' => $lname,
                    'user_suffix' => $suffix,
                    'user_email' => $email,
                    'user_height' => $_POST['user_height'] ?? '',
                    'user_weight' => $_POST['user_weight'] ?? '',
                    'user_birthday' => $bday,
                    'user_gender' => $_POST['user_gender'] ?? '',
                    'user_address' => $_POST['user_address'] ?? '',
                    'user_contact' => $contact,
                    'user_status' => 'active',
                    'user_image' => 'default.png',
                    'user_rfid' => $rfid ?? ''
                ];
                $mydb->insert('users_user', $userData);
                $userId = $mydb->getLastId();

                /* --- Handle Image Upload --- */
                if (isset($_FILES['user_image']) && $_FILES['user_image']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = "../assets/images/user_image/";
                    if (!is_dir($uploadDir))
                        mkdir($uploadDir, 0777, true);

                    $fileExt = pathinfo($_FILES['user_image']['name'], PATHINFO_EXTENSION);
                    $randomStr = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 20);
                    $filename = $userId . "-" . $randomStr . "." . strtolower($fileExt);

                    if (move_uploaded_file($_FILES['user_image']['tmp_name'], $uploadDir . $filename)) {
                        $mydb->update('users_user', ['user_image' => $filename], ['user_id' => $userId]);
                    }
                }

                /* --- Add Membership if provided --- */
                if (!empty($_POST['mem_type'])) {
                    if ($_POST['mem_type'] === "Monthly") {
                        $start = new DateTime('now', new DateTimeZone('Asia/Manila'));
                        $end = (clone $start)->modify('+1 month');
                        $memData = [
                            'user_id' => $userId,
                            'mem_type' => $_POST['mem_type'],
                            'mem_start_date' => $start->format('Y-m-d'),
                            'mem_end_date' => $end->format('Y-m-d')
                        ];
                    } else {
                        $memData = [
                            'user_id' => $userId,
                            'mem_type' => $_POST['mem_type'],
                            'mem_start_date' => '0000-00-00',
                            'mem_end_date' => '0000-00-00'
                        ];
                    }
                    $mydb->insert('users_membership', $memData);
                }

                $response = [
                    "status" => "success",
                    "message" => "Member added successfully",
                    "user_id" => $userId
                ];
                break;

            /* ---------------- GET MEMBERS (with search + pagination) ---------------- */
            case "getMembers":
                try {
                    $search = $_POST['search'] ?? '';
                    $page = isset($_POST['page']) ? (int) $_POST['page'] : 1;
                    $limit = 15; // rows per page
                    $offset = ($page - 1) * $limit;

                    // Base queries
                    $countSql = "SELECT COUNT(*) as total
                                FROM users_user u
                                LEFT JOIN users_membership m ON u.user_id = m.user_id";

                    $sql = "SELECT u.*, m.*
                            FROM users_user u
                            LEFT JOIN users_membership m ON u.user_id = m.user_id";

                    $params = [];
                    $types = ""; // leave empty unless needed

                    // Apply search filter
                    if (!empty($search)) {
                        $where = " WHERE 
                        u.user_fname LIKE ? OR
                        u.user_mname LIKE ? OR
                        u.user_lname LIKE ? OR
                        u.user_suffix LIKE ? OR
                        u.user_email LIKE ? OR
                        u.user_height LIKE ? OR
                        u.user_weight LIKE ? OR
                        u.user_birthday LIKE ? OR
                        u.user_gender LIKE ? OR
                        u.user_address LIKE ? OR
                        u.user_contact LIKE ? OR
                        u.user_status LIKE ? OR
                        m.mem_type LIKE ? OR
                        m.mem_start_date LIKE ? OR
                        m.mem_end_date LIKE ?";

                        $sql .= $where;
                        $countSql .= $where;

                        $params = array_fill(0, 15, "%$search%");
                        $types = str_repeat("s", count($params));
                    }

                    // Get total count
                    $countResult = $mydb->rawQuery($countSql, $params, $types);
                    $total = $countResult[0]['total'] ?? 0;

                    // Add pagination
                    $sql .= " LIMIT ? OFFSET ?";
                    $params[] = $limit;
                    $params[] = $offset;
                    $types .= "ii"; // add types for LIMIT and OFFSET

                    // Run main query
                    $result = $mydb->rawQuery($sql, $params, $types);

                    $response = [
                        "status" => "success",
                        "data" => $result,
                        "total" => $total,
                        "page" => $page,
                        "limit" => $limit
                    ];
                } catch (Exception $e) {
                    $response = [
                        "status" => "error",
                        "message" => $e->getMessage()
                    ];
                }
                break;

            /* ---------------- GET SINGLE MEMBERS ---------------- */
            case "get_single_member":
                if (isset($_POST['id'])) {
                    $id = (int) $_POST['id']; // cast for safety

                    $sql = "SELECT u.*, m.*
                            FROM users_user u
                            LEFT JOIN users_membership m ON u.user_id = m.user_id
                            WHERE u.user_id = ?";

                    // Pass the ID as integer
                    $member = $mydb->rawQuery($sql, [$id], "i");

                    $response = $member[0] ?? [];
                } else {
                    $response = ["status" => "error", "message" => "Missing member ID"];
                }
                break;


            /* ---------------- UPDATE MEMBERS ---------------- */
            case "update_member":
                $id = $_POST['user_id'];
                $fname = trim($_POST['user_fname']);
                $mname = trim($_POST['user_mname'] ?? '');
                $lname = trim($_POST['user_lname']);
                $suffix = trim($_POST['user_suffix'] ?? '');
                $email = trim($_POST['user_email'] ?? '');
                $contact = trim($_POST['user_contact'] ?? '');
                $bday = trim($_POST['user_birthday']);
                $rfid = trim($_POST['user_rfid'] ?? '');
                $start = trim($_POST['mem_start_date']);
                $end = trim($_POST['mem_end_date']);


                // ========== VALIDATION ==========

                // Check for exact full name (exclude current user)
                $checkName = $mydb->rawQuery(
                    "SELECT user_id FROM users_user 
                            WHERE user_fname = ? AND user_mname = ? AND user_lname = ? AND user_suffix = ? 
                            AND user_id != ?",
                    [$fname, $mname, $lname, $suffix, $id]
                );
                if (!empty($checkName)) {
                    $response = [
                        "status" => "error",
                        "message" => "Another member with the same full name already exists."
                    ];
                    break;
                }

                // Check duplicate email (exclude current user)

                if (!empty($rfid)) {
                    $checkRfid = $mydb->rawQuery(
                        "SELECT user_id FROM users_user WHERE user_rfid = ? AND user_id != ?",
                        [$rfid, $id]
                    );
                    if (!empty($checkRfid)) {
                        $response = [
                            "status" => "error",
                            "message" => "This RFID is already registered to another member."
                        ];
                        break;
                    }
                }

                // Check duplicate RFID (exclude current user)

                if (!empty($email)) {
                    $checkEmail = $mydb->rawQuery(
                        "SELECT user_id FROM users_user WHERE user_email = ? AND user_id != ?",
                        [$email, $id]
                    );
                    if (!empty($checkEmail)) {
                        $response = [
                            "status" => "error",
                            "message" => "This email address is already registered to another member."
                        ];
                        break;
                    }
                }

                // Check duplicate contact (exclude current user)
                if (!empty($contact)) {
                    $checkContact = $mydb->rawQuery(
                        "SELECT user_id FROM users_user WHERE user_contact = ? AND user_id != ?",
                        [$contact, $id]
                    );
                    if (!empty($checkContact)) {
                        $response = [
                            "status" => "error",
                            "message" => "This contact number is already registered to another member."
                        ];
                        break;
                    }
                }

                // Age validation (must be 12+)
                if (!empty($bday)) {
                    $birthDate = new DateTime($bday);
                    $today = new DateTime('now', new DateTimeZone('Asia/Manila'));
                    $age = $birthDate->diff($today)->y;

                    if ($age < 12) {
                        $response = [
                            "status" => "error",
                            "message" => "Member must be at least 12 years old."
                        ];
                        break;
                    }
                }

                // ========== HANDLE IMAGE UPLOAD ==========
                $uploadDir = "../assets/images/user_image/";
                $filename = null;

                if (isset($_FILES['user_image']) && $_FILES['user_image']['error'] === UPLOAD_ERR_OK) {
                    // Get current image before uploading new one
                    $oldImage = $mydb->select("users_user", "user_image", ["user_id" => $id]);
                    $oldImageName = $oldImage[0]['user_image'] ?? null;

                    $fileTmp = $_FILES['user_image']['tmp_name'];
                    $fileExt = pathinfo($_FILES['user_image']['name'], PATHINFO_EXTENSION);

                    $randomStr = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6);
                    $newName = $id . "-" . $randomStr . "." . strtolower($fileExt);

                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    if (move_uploaded_file($fileTmp, $uploadDir . $newName)) {
                        $filename = $newName;

                        // Delete old image (if not default)
                        if ($oldImageName && $oldImageName !== "default.png") {
                            $oldPath = $uploadDir . $oldImageName;
                            if (file_exists($oldPath)) {
                                unlink($oldPath);
                            }
                        }
                    }
                }

                // ========== UPDATE USER DATA ==========
                $userData = [
                    'user_fname' => $fname,
                    'user_mname' => $mname,
                    'user_lname' => $lname,
                    'user_suffix' => $suffix,
                    'user_email' => $email,
                    'user_height' => $_POST['user_height'] ?? '',
                    'user_weight' => $_POST['user_weight'] ?? '',
                    'user_birthday' => $bday,
                    'user_gender' => $_POST['user_gender'],
                    'user_address' => $_POST['user_address'],
                    'user_contact' => $contact,
                    'user_rfid' => $rfid
                ];

                if ($filename !== null) {
                    $userData['user_image'] = $filename;
                }

                $mydb->update('users_user', $userData, ['user_id' => $id]);

                // ========== UPDATE MEMBERSHIP ==========
                if ($_POST['mem_type'] === "Monthly") {
                    // $start = new DateTime('now', new DateTimeZone('Asia/Manila'));
                    // $end = (clone $start)->modify('+1 month');

                    $startDate = DateTime::createFromFormat('Y-m-d', $start);
                    $endDate = DateTime::createFromFormat('Y-m-d', $end);

                    $startFormatted = $startDate ? $startDate->format('Y-m-d') : null;
                    $endFormatted = $endDate ? $endDate->format('Y-m-d') : null;


                    $memData = [
                        'mem_type' => $_POST['mem_type'],
                        'mem_start_date' => $startFormatted,
                        'mem_end_date' => $endFormatted
                    ];
                } else {
                    $memData = [
                        'mem_type' => $_POST['mem_type'],
                        'mem_start_date' => '0000-00-00',
                        'mem_end_date' => '0000-00-00'
                    ];
                }

                $mydb->update('users_membership', $memData, ['user_id' => $id]);

                $response = [
                    "status" => "success",
                    "message" => "Member updated successfully"
                ];
                break;

            /* ---------------- DELETE MEMBERS ---------------- */
            case 'delete_member':
                if (isset($_POST['delete_id']) && is_numeric($_POST['delete_id'])) {
                    $id = intval($_POST['delete_id']);

                    // Get user details before deleting
                    $user = $mydb->select("users_user", "*", ["user_id" => $id]);
                    if ($user) {
                        $user = $user[0];

                        // Paths
                        $imageDir = "../assets/images/user_image/";
                        $qrDir = "../assets/images/qrcode/";

                        // Delete user image file if exists and not default.png
                        if (!empty($user['user_image']) && $user['user_image'] !== "default.png") {
                            $imgPath = $imageDir . $user['user_image'];
                            if (file_exists($imgPath) && is_file($imgPath)) {
                                unlink($imgPath);
                            }
                        }

                        // Finally, delete the user from DB
                        $mydb->delete('users_user', ['user_id' => $id]);
                        $response = ["status" => "success", "message" => "User deleted successfully"];
                    } else {
                        $response = ["status" => "error", "message" => "User not found"];
                    }
                } else {
                    $response = ["status" => "error", "message" => "Invalid request"];
                }
                break;


            /* ---------------- Check the monthly expiration ---------------- */
            case "check_monthly_expiry":
                try {
                    // Date 7 days from today
                    $tz = new DateTimeZone('Asia/Manila');
                    $checkDate = (new DateTime('now', $tz))
                        ->add(new DateInterval('P7D'))
                        ->format('Y-m-d');

                    // Get Monthly memberships ending in 7 days and not yet reminded
                    $sql = "SELECT u.user_email, u.user_fname, u.user_lname, m.mem_end_date, m.mem_id
                            FROM users_membership m
                            JOIN users_user u ON u.user_id = m.user_id
                            WHERE m.mem_type = 'Monthly'
                            AND DATE(m.mem_end_date) = ?
                            AND m.reminder_sent = 0";

                    $usersExpiring = $mydb->rawQuery($sql, [$checkDate], "s");

                    $sentCount = 0;

                    if ($usersExpiring) {
                        foreach ($usersExpiring as $user) {
                            // Create NEW PHPMailer instance for each user
                            $mail = new PHPMailer(true);

                            try {
                                $mail->isSMTP();
                                $mail->Host = 'smtp.gmail.com';
                                $mail->SMTPAuth = true;
                                $mail->Username = 'baltazardakilakrissanto@gmail.com';  // Gmail
                                $mail->Password = 'esnhbcgjguxwawbm';       // App password
                                $mail->SMTPSecure = 'tls';                  // or 'ssl' if using 465
                                $mail->Port = 587;                          // 587 for TLS, 465 for SSL

                                $mail->setFrom('baltazardakilakrissanto@gmail.com', 'DKB Fitness Gym');
                                $mail->isHTML(true);

                                // Add recipient
                                $mail->addAddress(
                                    $user['user_email'],
                                    $user['user_fname'] . ' ' . $user['user_lname']
                                );

                                // Email content
                                $mail->Subject = "Your Monthly Subscription is About to Expire";
                                $mail->Body = "
                                                <p>Hello <strong>{$user['user_fname']} {$user['user_lname']}</strong>,</p>
                                                <p>Your monthly subscription will expire on <strong>{$user['mem_end_date']}</strong>.</p>
                                                <p>Please renew your subscription to continue enjoying our services.</p>
                                                <p>Thank you!</p>
                                            ";

                                // Send email
                                if ($mail->send()) {
                                    // Mark reminder as sent
                                    $mydb->update(
                                        "users_membership",
                                        ["reminder_sent" => 1],
                                        ["mem_id" => $user['mem_id']]
                                    );
                                    $sentCount++;
                                }
                            } catch (Exception $e) {
                                error_log("Email failed for {$user['user_email']}: " . $mail->ErrorInfo);
                            }
                        }
                    }

                    /* ---------------- NEW: Update user active/inactive based on logs ---------------- */
                    $users = $mydb->select("users_user", "user_id");
                    $activeCount = 0;
                    $inactiveCount = 0;

                    foreach ($users as $user) {
                        $userId = $user['user_id'];

                        // Get last log of this user
                        $sql = "SELECT log_time_in 
                                FROM users_log 
                                WHERE user_id=? 
                                ORDER BY log_time_in DESC 
                                LIMIT 1";
                        $lastLog = $mydb->rawQuery($sql, [$userId], "i");

                        $isActive = false;
                        if ($lastLog) {
                            $lastLogTime = strtotime($lastLog[0]['log_time_in']);
                            $daysDiff = (time() - $lastLogTime) / (60 * 60 * 24);
                            $isActive = ($daysDiff <= 30);
                        }

                        // Update user status
                        $newStatus = $isActive ? "active" : "inactive";
                        $mydb->update("users_user", ["user_status" => $newStatus], ["user_id" => $userId]);

                        if ($isActive)
                            $activeCount++;
                        else
                            $inactiveCount++;
                    }

                    $response = [
                        "status" => "success",
                        "message" => "Expiry notifications processed and user statuses updated",
                        "sent" => $sentCount,
                        "activeCount" => $activeCount,
                        "inactiveCount" => $inactiveCount
                    ];

                } catch (Exception $e) {
                    error_log("Check monthly expiry error: " . $e->getMessage());
                    $response = [
                        "status" => "error",
                        "message" => $e->getMessage()
                    ];
                }
                break;


            /* ---------------- GET ALL LOGS ---------------- */
            case "get_logs":
            case "search_logs":
                $page = isset($_POST['page']) ? (int) $_POST['page'] : 1;
                $limit = 15;
                $offset = ($page - 1) * $limit;

                $search = trim($_POST['search_logs'] ?? '');
                $params = [];
                $types = "";

                if ($search === "") {
                    // Today's logs
                    $today = date("Y-m-d");
                    $where = "WHERE DATE(l.log_time_in) = ?";
                    $params[] = $today;
                    $types = "s"; // date as string
                } else {
                    // Search logs
                    $param = "%{$search}%";
                    $where = "WHERE u.user_fname LIKE ? 
                    OR u.user_lname LIKE ? 
                    OR l.user_id LIKE ? 
                    OR l.log_time_in LIKE ? 
                    OR l.log_time_out LIKE ?
                    OR m.mem_type LIKE ?";

                    $params = [$param, $param, $param, $param, $param, $param];
                    $types = "ssssss"; // all strings
                }

                // Total count
                $countSql = "SELECT COUNT(*) as total
                        FROM users_log l
                        JOIN users_user u ON u.user_id = l.user_id
                        JOIN users_membership m ON l.user_id = m.user_id
                        $where";

                $countRes = $mydb->rawQuery($countSql, $params, $types);
                $total = $countRes[0]['total'] ?? 0;

                // Paginated query
                $sql = "SELECT l.*, u.user_fname, u.user_image, u.user_lname, m.*
                        FROM users_log l
                        JOIN users_user u ON u.user_id = l.user_id
                        JOIN users_membership m ON l.user_id = m.user_id
                        $where
                        ORDER BY l.log_time_in DESC
                        LIMIT ? OFFSET ?";

                $params[] = $limit;
                $params[] = $offset;
                $types .= "ii"; // last 2 are integers

                $rows = $mydb->rawQuery($sql, $params, $types);

                $response = [
                    "status" => "success",
                    "data" => $rows,
                    "total" => $total,
                    "page" => $page,
                    "limit" => $limit
                ];
                break;


            /* ---------------- PROCESS RFID SCAN ---------------- */
            case "rfid_scan":
                if (!empty($_POST['user_rfid'])) {
                    $user_rfid = trim($_POST['user_rfid']);

                    // Lookup user with membership info
                    $sql = "
                        SELECT u.*, m.mem_type, m.mem_start_date, m.mem_end_date
                        FROM users_user u
                        LEFT JOIN users_membership m ON u.user_id = m.user_id
                        WHERE u.user_rfid = ?
                        LIMIT 1
                    ";
                    $rows = $mydb->rawQuery($sql, [$user_rfid], "s");

                    if (!$rows) {
                        $response = ["status" => "error", "message" => "Invalid RFID Card"];
                        break;
                    }

                    $user = $rows[0];
                    $userId = $user['user_id'];

                    // Check today's log
                    $today = date("Y-m-d");
                    $sql = "SELECT * FROM users_log 
                            WHERE user_id=? 
                            AND DATE(log_time_in)=? 
                            ORDER BY log_id DESC LIMIT 1";
                    $existing = $mydb->rawQuery($sql, [$userId, $today], "is");

                    if (!$existing) {
                        // First scan → Check-in
                        $mydb->insert("users_log", [
                            "user_id" => $userId,
                            "log_time_in" => date("Y-m-d H:i:s"),
                            "log_time_out" => "0000-00-00 00:00:00"
                        ]);

                        // Handle expired membership → force Walk-in
                        $memType = $user['mem_type'] ?? "Walk-in";
                        if ($memType === "Monthly" && !empty($user['mem_end_date'])) {
                            if (strtotime($user['mem_end_date']) < strtotime($today)) {
                                $memType = "Walk-in";
                            }
                        }

                        $response = [
                            "status" => "success",
                            "message" => "Check-in successful",
                            "user" => [
                                "user_image" => $user['user_image'] ?? "default.png",
                                "name" => $user['user_fname'] . " " .
                                    ($user['user_mname'] ? $user['user_mname'][0] . " " : "") .
                                    $user['user_lname'] . " " .
                                    ($user['user_suffix'] ?? ""),
                                "gender" => $user['user_gender'] ?? "",
                                "mobile" => $user['user_contact'] ?? "",
                                "email" => $user['user_email'] ?? "",
                                "address" => $user['user_address'] ?? "",
                                "mem_start_date" => $user['mem_start_date'] ?? "",
                                "mem_end_date" => $user['mem_end_date'] ?? "",
                                "mem_type" => $memType
                            ]
                        ];
                    } else {
                        $log = $existing[0];

                        if ($log['log_time_out'] == "0000-00-00 00:00:00" || $log['log_time_out'] == null) {
                            $checkinTime = strtotime($log['log_time_in']);
                            $now = time();
                            $diffMinutes = ($now - $checkinTime) / 60;

                            if ($diffMinutes < 30) {
                                $response = [
                                    "status" => "error",
                                    "message" => "Checkout not allowed until 30 minutes after check-in"
                                ];
                                break;
                            }

                            // Second scan ≥30 mins → Checkout
                            $mydb->update("users_log", [
                                "log_time_out" => date("Y-m-d H:i:s")
                            ], ["log_id" => $log['log_id']]);

                            $response = ["status" => "success", "message" => "Check-out successful"];
                        } else {
                            $response = [
                                "status" => "error",
                                "message" => "You already checked in today"
                            ];
                        }
                    }
                } else {
                    $response = ["status" => "error", "message" => "Missing RFID code"];
                }
                break;


            /* ---------------- Update expired memberships ---------------- */
            case "update_expired_memberships":
                $today = date("Y-m-d");

                // Update all expired memberships
                $sql = "UPDATE users_membership 
                        SET mem_type = 'Walk-in' 
                        WHERE mem_end_date <= ?";

                $mydb->rawQuery($sql, [$today], "s");

                $response = [
                    "status" => "success",
                    "message" => "Expired memberships updated to Walk-in"
                ];
                break;


            /* ---------------- GET DASHBOARD DATA ---------------- */
            case "get_dashboard":
                $filter = $_POST['filter'] ?? 'daily';

                $labels = [];
                $dates = [];
                $values = [];
                $title = "";
                $xTitle = "";

                if ($filter == "daily") {
                    // === Daily visitors (this month)
                    $startOfMonth = date("Y-m-01");
                    $endOfMonth = date("Y-m-t");

                    $sql = "
                            SELECT 
                                DAY(log_time_in) as day_number,
                                COUNT(DISTINCT CONCAT(user_id, DATE(log_time_in))) as total_visitors
                            FROM users_log
                            WHERE DATE(log_time_in) BETWEEN ? AND ?
                            GROUP BY day_number
                            ORDER BY day_number;
                        ";
                    $data = $mydb->rawQuery($sql, [$startOfMonth, $endOfMonth], "ss");

                    $daysInMonth = date("t");
                    for ($i = 1; $i <= $daysInMonth; $i++) {
                        $dateStr = date("Y-m-") . str_pad($i, 2, "0", STR_PAD_LEFT);
                        $labels[] = (string) $i;
                        $dates[] = $dateStr;
                        $values[$i] = 0;
                    }
                    foreach ($data as $row) {
                        $values[(int) $row['day_number']] = (int) $row['total_visitors'];
                    }

                    $title = "Daily Visitors (" . date("F Y") . ")";
                    $xTitle = "Days of the Month";

                } elseif ($filter == "weekly") {
                    // === Weekly visitors (current month, grouped Mon-Sun)
                    $year = date("Y");
                    $month = date("m");

                    $sql = "
                            SELECT WEEK(log_time_in, 1) - WEEK(DATE_SUB(log_time_in, INTERVAL DAYOFMONTH(log_time_in)-1 DAY), 1) + 1 as week_in_month,
                                COUNT(DISTINCT CONCAT(user_id, DATE(log_time_in))) as total_visitors
                            FROM users_log
                            WHERE YEAR(log_time_in) = ? AND MONTH(log_time_in) = ?
                            GROUP BY week_in_month
                            ORDER BY week_in_month;
                        ";
                    $data = $mydb->rawQuery($sql, [$year, $month], "ii");

                    foreach ($data as $row) {
                        $labels[] = "w" . $row['week_in_month']; // w1, w2, w3...
                        $dates[] = "Week " . $row['week_in_month'] . " of " . date("F Y");
                        $values[] = (int) $row['total_visitors'];
                    }

                    $title = "Weekly Visitors (" . date("F Y") . ")";
                    $xTitle = "Weeks";

                } elseif ($filter == "monthly") {
                    // === Monthly visitors (Jan-Dec of current year)
                    $year = date("Y");
                    $sql = "
                            SELECT MONTH(log_time_in) as month_num,
                                COUNT(DISTINCT CONCAT(user_id, DATE(log_time_in))) as total_visitors
                            FROM users_log
                            WHERE YEAR(log_time_in) = ?
                            GROUP BY month_num
                            ORDER BY month_num;
                        ";
                    $data = $mydb->rawQuery($sql, [$year], "i");

                    for ($m = 1; $m <= 12; $m++) {
                        $labels[] = date("M", mktime(0, 0, 0, $m, 1));
                        $dates[] = date("F Y", mktime(0, 0, 0, $m, 1));
                        $values[$m] = 0;
                    }
                    foreach ($data as $row) {
                        $values[(int) $row['month_num']] = (int) $row['total_visitors'];
                    }

                    $title = "Monthly Visitors ($year)";
                    $xTitle = "Months";

                } elseif ($filter == "annual") {
                    // === Annual visitors (last 5 years including current)
                    $currentYear = date("Y");
                    $startYear = $currentYear - 4;

                    $sql = "
                        SELECT 
                            YEAR(log_time_in) AS year_num, 
                            COUNT(DISTINCT CONCAT(user_id, DATE(log_time_in))) AS total_visitors
                        FROM users_log
                        WHERE YEAR(log_time_in) BETWEEN ? AND ?
                        GROUP BY year_num
                        ORDER BY year_num;
                    ";

                    $data = $mydb->rawQuery($sql, [$startYear, $currentYear], "ii");

                    // Initialize labels and values for all 5 years
                    for ($y = $startYear; $y <= $currentYear; $y++) {
                        $labels[] = (string) $y;
                        $dates[] = (string) $y;
                        $values[$y] = 0;
                    }

                    // Fill in the data from DB
                    foreach ($data as $row) {
                        $values[(int) $row['year_num']] = (int) $row['total_visitors'];
                    }

                    $title = "Annual Visitors ($startYear - $currentYear)";
                    $xTitle = "Years";
                }


                // =====================
                // Boxes data (same for all filters)
                // =====================
                $monthly = $mydb->rawQuery(
                    "SELECT COUNT(*) as cnt FROM users_membership WHERE mem_type = ?",
                    ["Monthly"],
                    "s"
                )[0]['cnt'];

                $walkin = $mydb->rawQuery(
                    "SELECT COUNT(*) as cnt FROM users_membership WHERE mem_type = ?",
                    ["Walk-in"],
                    "s"
                )[0]['cnt'];

                $visitors = $mydb->rawQuery(
                    "SELECT COUNT(*) as cnt FROM users_log WHERE DATE(log_time_in) = CURDATE()",
                    [],
                    ""
                )[0]['cnt'];

                $active = $mydb->rawQuery(
                    "SELECT COUNT(*) as cnt FROM users_user WHERE user_status = ?",
                    ["active"],
                    "s"
                )[0]['cnt'];

                $inactive = $mydb->rawQuery(
                    "SELECT COUNT(*) as cnt FROM users_user WHERE user_status = ?",
                    ["inactive"],
                    "s"
                )[0]['cnt'];

                $response = [
                    "chart" => [
                        "labels" => array_values($labels),
                        "dates" => array_values($dates),
                        "data" => array_values($values),
                        "title" => $title,
                        "xTitle" => $xTitle
                    ],
                    "members" => [
                        "monthly" => $monthly,
                        "walkin" => $walkin
                    ],
                    "visitors" => $visitors,
                    "status" => [
                        "active" => $active,
                        "inactive" => $inactive
                    ]
                ];
                break;


            /* ---------------- 8:30 trigger ---------------- */
            case "auto_logout_and_export":
                $today = (new DateTime("now", new DateTimeZone("Asia/Manila")))->format("Y-m-d");

                // 1. Logout all users logged in today but not logged out
                $sql = "UPDATE users_log 
                        SET log_time_out = NOW() 
                        WHERE DATE(log_time_in) = ? 
                        AND (log_time_out IS NULL OR log_time_out = '0000-00-00 00:00:00')";
                $mydb->rawQuery($sql, [$today]);


                /* ------------------- EXPORT LOGS (Separated by Membership Type) ------------------- */
                $sql = "SELECT u.user_id, u.user_fname, u.user_lname, m.mem_type, ul.log_time_in, ul.log_time_out
                        FROM users_log ul
                        JOIN users_user u ON ul.user_id = u.user_id
                        LEFT JOIN users_membership m ON u.user_id = m.user_id
                        WHERE DATE(ul.log_time_in) = ?";
                $rows = $mydb->rawQuery($sql, [$today]);

                // Directories
                $monthlyLogDir = __DIR__ . "/../csv/log_record/monthly/";
                $walkinLogDir = __DIR__ . "/../csv/log_record/walk-in/";

                if (!is_dir($monthlyLogDir))
                    mkdir($monthlyLogDir, 0777, true);
                if (!is_dir($walkinLogDir))
                    mkdir($walkinLogDir, 0777, true);

                // File names
                $todayLabel = (new DateTime("now", new DateTimeZone("Asia/Manila")))->format("M d, Y");
                $monthlyFile = $monthlyLogDir . $todayLabel . " - MONTHLY LOG REPORT.csv";
                $walkinFile = $walkinLogDir . $todayLabel . " - WALK-IN LOG REPORT.csv";

                // Open files
                $fpMonthly = fopen($monthlyFile, "w");
                $fpWalkin = fopen($walkinFile, "w");

                // Headers (added Membership Type)
                fputcsv($fpMonthly, ["User ID", "First Name", "Last Name", "Membership Type", "Time In", "Time Out"]);
                fputcsv($fpWalkin, ["User ID", "First Name", "Last Name", "Membership Type", "Time In", "Time Out"]);

                // Separate rows by mem_type
                foreach ($rows as $row) {
                    $line = [
                        $row['user_id'],
                        $row['user_fname'],
                        $row['user_lname'],
                        $row['mem_type'] ?: "Walk-in",
                        $row['log_time_in'],
                        $row['log_time_out']
                    ];

                    if (strtolower($row['mem_type']) === "monthly") {
                        fputcsv($fpMonthly, $line);
                    } else {
                        fputcsv($fpWalkin, $line);
                    }
                }

                // Close files
                fclose($fpMonthly);
                fclose($fpWalkin);


                /* ------------------- EXPORT USERS ------------------- */
                $userRows = $mydb->select("users_user");

                $userDir = __DIR__ . "/../csv/users_record/";
                if (!is_dir($userDir))
                    mkdir($userDir, 0777, true);

                $userFile = $userDir . "users.csv";
                $fp = fopen($userFile, "w");
                if (!empty($userRows)) {
                    fputcsv($fp, array_keys($userRows[0])); // header
                    foreach ($userRows as $row) {
                        fputcsv($fp, $row);
                    }
                }
                fclose($fp);

                /* ------------------- EXPORT MEMBERSHIP ------------------- */
                $memRows = $mydb->select("users_membership");

                $memDir = __DIR__ . "/../csv/membership_record/";
                if (!is_dir($memDir))
                    mkdir($memDir, 0777, true);

                $memFile = $memDir . "membership.csv";
                $fp = fopen($memFile, "w");
                if (!empty($memRows)) {
                    fputcsv($fp, array_keys($memRows[0])); // header
                    foreach ($memRows as $row) {
                        fputcsv($fp, $row);
                    }
                }
                fclose($fp);

                $response = [
                    "status" => "success",
                    "message" => "Users logged out and CSVs generated",
                    "files" => [
                        "monthly_log" => "../csv/log_record/monthly/" . basename($monthlyFile),
                        "walkin_log" => "../csv/log_record/walk-in/" . basename($walkinFile),
                        "users" => "../csv/users_record/" . basename($userFile),
                        "membership" => "../csv/membership_record/" . basename($memFile)
                    ]
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
