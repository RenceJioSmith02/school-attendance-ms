<?php
require "db/db.php";
$mydb = new myDB();

include_once 'partials/session.php'
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JOLNHS | Dashboard</title>
    <link rel="icon" href="assets/images/system_image/school-logo.png" type="image/png">
    <script type="text/javascript" src="assets/js/jquery.min.js"></script>
    <script src="assets/js/sweetalert2.min.js"></script>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>

    <!-- main css -->
    <link rel="stylesheet" href="assets/css/main.css">

</head>

<body>

    <!-- Overlay -->
    <div id="overlay" style="display:none;"></div>

    <!-- Update teacher form -->
    <div class="form-container modal scroller-format" id="update-teacher">

        <div class="header flex jus-center al-center flex-col">
            <h2>Update Teacher</h2>
            <p>Please fill up the required information.</p>
            <span class="close-btn" onclick="hideModals()">&times;</span>
        </div>

        <form method="POST" id="UpdateTeacherForm" enctype="multipart/form-data">

            <div class="form-input-container flex jus-center al-center">

                <div class="input-group">
                    <label>Full Name</label>
                    <input type="text" name="fullname" required placeholder="Juan Dele Cruz">
                </div>

                <div class="form-group flex jus-center al-center">
                    <div class="input-group">
                        <label>Department</label>
                        <select name="departments" id="departments" required>
                            <option value="" disabled selected>Select Department</option>
                            <option value="Mathematics">Mathematics</option>
                            <option value="Science">Science</option>
                            <option value="English">English</option>
                            <option value="History">History</option>
                            <option value="Physical Education">Physical Education</option>
                            <option value="Arts">Arts</option>
                            <option value="Music">Music</option>
                            <option value="Computer Science">Computer Science</option>
                            <option value="Administration">Administration</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label>Address</label>
                        <input type="text" name="address" placeholder="123 Main St" required>
                    </div>
                </div>

                <div class="form-group flex jus-center al-center">
                    <div class="input-group">
                        <label>Birthdate</label>
                        <input type="date" name="birthdate" required>
                    </div>
                    <div class="input-group">
                        <label>Gender</label>
                        <select name="gender" id="teacher_gender">
                            <option value="" disabled selected>Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                </div>

                <div class="form-group flex jus-center al-center">
                    <div class="input-group">
                        <label>Email</label>
                        <input type="email" name="email" placeholder="juan@gmail.com">
                    </div>
                    <div class="input-group">
                        <label>Password</label>
                        <input type="password" name="password" placeholder="Enter your password"><br>
                    </div>
                </div>

                <input type="hidden" name="is_registered" value="1">

            </div>

            <div class="form-buttons flex jus-center al-center flex-col gap10 mt20">
                <input class="btn-primary" style="width: 100%; font-weight:500; font-size: 15px;" type="submit"
                    name="update" value="UPDATE" style="max-width: 80px;">
            </div>
        </form>

    </div>


    <!-- Update student form -->
    <div class="form-container modal scroller-format" id="update-student">

        <div class="header flex jus-center al-center flex-col">
            <h2>Update Student</h2>
            <p>Please fill up the required information.</p>
            <span class="close-btn" onclick="hideModals()">&times;</span>
        </div>

        <form method="POST" id="UpdateStudentForm" enctype="multipart/form-data">

            <div class="form-input-container flex jus-center al-center">

                <div class="form-group flex jus-center al-center">
                    <div class="input-group">
                        <label>Full Name</label>
                        <input type="text" name="fullname" required placeholder="Juan Dele Cruz">
                    </div>
                    <div class="input-group">
                        <label>LRN</label>
                        <input type="text" name="lrn" placeholder="1054330007" required>
                    </div>
                </div>

                <div class="form-group flex jus-center al-center">
                    <div class="input-group">
                        <label>Birthdate</label>
                        <input type="date" name="birthdate" required>
                    </div>
                    <div class="input-group">
                        <label>Gender</label>
                        <select name="gender" id="student_gender">
                            <option value="" disabled selected>Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                </div>

                <div class="input-group">
                    <label>Address</label>
                    <input type="text" name="address" placeholder="123 Main St" required>
                </div>

                <div class="form-group flex jus-center al-center">
                    <div class="input-group">
                        <label>Guardian Contact</label>
                        <input type="text" name="guardian_contact" placeholder="09070654368" required>
                    </div>
                    <div class="input-group">
                        <label>Guardian Email</label>
                        <input type="email" name="guardian_email" placeholder="juan@gmail.com" required>
                    </div>
                </div>

                <div class="form-group flex jus-center al-center">
                    <div class="input-group">
                        <label>Email</label>
                        <input type="email" name="email" placeholder="juan@gmail.com" required>
                    </div>
                    <div class="input-group">
                        <label>Password</label>
                        <input type="password" name="password" placeholder="Enter your password">
                    </div>
                </div>

                <input type="hidden" name="is_registered" value="1">

            </div>

            <div class="form-buttons flex jus-center al-center flex-col gap10 mt20">
                <input class="btn-primary" style="width: 100%; font-weight:500; font-size: 15px;" type="submit"
                    name="update" value="UPDATE" style="max-width: 80px;">
            </div>
        </form>

    </div>


    <div class="container flex flex-col jus-start ">

        <!-- Header -->
        <?php include 'partials/header.php'; ?>

        <div class="content">

            <div class="top-controls flex jus-between al-center pt10 pb10"></div>

            <div class="profile-wrapper">

                <?php if(isset($_SESSION['user_role']) and ($_SESSION['user_role'] == 'student' || $_SESSION['user_role'] == 'teacher')) { ?>
                <!-- ===== PROFILE HEADER ===== -->
                <div class="profile-header flex al-center jus-between">
                    <div class="flex al-center gap-20">
                        <div class="profile-photo">
                            <img src="assets/images/user_image/default.png" id="profilePhoto" alt="Profile Photo">
                        </div>

                        <div class="profile-info-top">
                            <h2 id="profileName">Juan Dela Cruz</h2>
                            <p class="role" id="profileRole">Teacher</p>

                            <span class="reg-status" id="registeredBadge">Registered</span>
                        </div>
                    </div>

                    <div class="profile-actions pr50">
                        <button class="btn-primary" id="<?php echo (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'student')
                            ? 'showUpdateStudentForm'
                            : 'showUpdateTeacherForm'; ?>">Edit Profile</button>
                    </div>
                </div>

                <!-- ===== MAIN DETAILS ===== -->
                <div class="profile-section">
                    <h3>Personal Information</h3>
                    <div class="info-grid">
                        <div class="info-item"><label>Age:</label><span id="age">28</span></div>
                        <div class="info-item"><label>Gender:</label><span id="gender">Male</span></div>
                        <div class="info-item"><label>Birthdate:</label><span id="birthdate">1999-10-01</span></div>
                        <div class="info-item"><label>Email:</label><span id="email">sample@gmail.com</span></div>
                        <div class="info-item wide"><label>Address:</label><span id="address">Brgy Licaong, Science City of Muñoz</span></div>
                    </div>
                </div>

                <?php } ?>

                
                <?php if(isset($_SESSION['user_role']) and $_SESSION['user_role'] == 'teacher') { ?>
                <!-- ===== TEACHER ONLY ===== -->
                <div class="profile-section" id="teacherSection">
                    <h3>Teacher Information</h3>
                    <div class="info-grid">
                        <div class="info-item"><label>Department:</label><span id="department">Math Department</span></div>
                    </div>
                </div>
                <?php } ?>

                <?php if(isset($_SESSION['user_role']) and $_SESSION['user_role'] == 'student') { ?>
                <!-- ===== STUDENT ONLY ===== -->
                <div class="profile-section" id="studentSection">
                    <h3>Student Information</h3>
                    <div class="info-grid">
                        <div class="info-item"><label>LRN:</label><span id="lrn">1234567890</span></div>
                        <div class="info-item"><label>Guardian Email:</label><span id="guardianEmail">parent@gmail.com</span></div>
                        <div class="info-item"><label>Guardian Contact:</label><span id="guardianContact">09123456789</span></div>
                    </div>
                </div>
                <?php } ?>

            </div>

        </div>


    </div>


    <script src="assets/js/main.js"></script>


    <script>
$(document).ready(function() {
    loadUserProfile();
});

// ===============================
// LOAD CURRENT USER PROFILE
// ===============================
function loadUserProfile() {
    $.ajax({
        url: "db/request.php",
        type: "POST",
        data: { action: "get_user_info" },
        dataType: "json",
        success: function(res) {
            if (!res.success) {
                console.log("No user data.");
                return;
            }

            const d = res.data;

            // ===== UPDATE PROFILE HEADER =====
            $("#profileName").text(d.fullname);
            $("#profileRole").text(d.role);
            $("#gender").text(d.gender);
            $("#birthdate").text(d.birthdate);
            $("#email").text(d.email);
            $("#address").text(d.address);

            // Calculate age
            let age = new Date().getFullYear() - new Date(d.birthdate).getFullYear();
            $("#age").text(age);

            // ===== IF TEACHER =====
            if (d.role === "teacher") {
                $("#department").text(d.department);
            }

            // ===== IF STUDENT =====
            if (d.role === "student") {
                $("#lrn").text(d.lrn);
                $("#guardianEmail").text(d.guardian_email);
                $("#guardianContact").text(d.guardian_contact);
            }

            // ===== AUTOFILL UPDATE FORMS =====
            autofillUpdateForms(d);
        }
    });
}

// ===============================
// Autofill Update Teacher / Student Forms
// ===============================
function autofillUpdateForms(d) {

    if (d.role === "teacher") {
        $("#UpdateTeacherForm input[name='fullname']").val(d.fullname);
        $("#departments").val(d.department);
        $("#UpdateTeacherForm input[name='address']").val(d.address);
        $("#UpdateTeacherForm input[name='birthdate']").val(d.birthdate);
        $("#teacher_gender").val(d.gender);
        $("#UpdateTeacherForm input[name='email']").val(d.email);
    }

    if (d.role === "student") {
        $("#UpdateStudentForm input[name='fullname']").val(d.fullname);
        $("#UpdateStudentForm input[name='lrn']").val(d.lrn);
        $("#UpdateStudentForm input[name='birthdate']").val(d.birthdate);
        $("#student_gender").val(d.gender);
        $("#UpdateStudentForm input[name='address']").val(d.address);
        $("#UpdateStudentForm input[name='guardian_contact']").val(d.guardian_contact);
        $("#UpdateStudentForm input[name='guardian_email']").val(d.guardian_email);
        $("#UpdateStudentForm input[name='email']").val(d.email);
    }
}



function forceLogout() {
    $.ajax({
        url: "db/request.php",
        type: "POST",
        data: { action: "logout" },
        dataType: "json",
        success: function(resp) {
            if(resp.status === "success") {
                window.location.href = "index.php"; // redirect to login page
            } else {
                alert(resp.message || "Logout failed.");
            }
        },
        error: function(xhr, status, error) {
            console.error("AJAX Logout Error:", error);
            alert("Something went wrong during logout.");
        }
    });
}

// ===============================
// UPDATE TEACHER FORM SUBMIT
// ===============================
$("#UpdateTeacherForm").submit(function(e) {
    e.preventDefault();

    let formData = new FormData(this);
    formData.append("action", "update_teacher");

    $.ajax({
        url: "db/request.php",
        type: "POST",
        data: formData,
        cache: false,
        processData: false,
        contentType: false,
        dataType: "json",
        success: function(response) {
            if(response.status === "success") {
                Swal.fire("Success!", "Teacher updated successfully!", "success").then(() => {
                    // Force logout if email or password changed
                    // If email or password was changed, force logout
                    let emailChanged = formData.get("email") !== $("#email").text();
                    let passwordChanged = formData.get("password") !== "";

                    if(emailChanged || passwordChanged) {
                        forceLogout();
                    } else {
                        // hideModals();
                        // loadUserProfile();
                        location.reload();
                    }
                });
            } else {
                Swal.fire("Error!", response.message, "error");
            }
        }
    });
});

// ===============================
// UPDATE STUDENT FORM SUBMIT
// ===============================
$("#UpdateStudentForm").submit(function(e) {
    e.preventDefault();

    let formData = new FormData(this);
    formData.append("action", "update_student");

    $.ajax({
        url: "db/request.php",
        type: "POST",
        data: formData,
        cache: false,
        processData: false,
        contentType: false,
        dataType: "json",
        success: function(response) {
            if(response.status === "success") {
                Swal.fire("Success!", "Student updated successfully!", "success").then(() => {
                    // Force logout if email or password changed
                    // If email or password was changed, force logout
                    let emailChanged = formData.get("email") !== $("#email").text();
                    let passwordChanged = formData.get("password") !== "";

                    if(emailChanged || passwordChanged) {
                        forceLogout();
                    } else {
                        // hideModals();
                        // loadUserProfile();
                        location.reload();
                    }
                });
            } else {
                Swal.fire("Error!", response.message, "error");
            }
        }
    });
});

</script>


</body>

</html>




