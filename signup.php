<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JOLNHS | Login</title>
    <link rel="icon" href="assets/images/system_image/school-logo.png" type="image/png">
    <link rel="stylesheet" href="assets/css/main.css">
    <script type="text/javascript" src="assets/js/jquery.min.js"></script>
    <script src="assets/js/sweetalert2.min.js"></script>
</head>
<body>
    
  <div class="login-container flex jus-center al-center">
    
    <div class="left flex al-center jus-center">
        <!-- Add Teahcer form -->
        <div class="form-container" id="teacherForm" style="display: none;">
            <div class="header flex jus-center al-center flex-col">
                <h2>Sign Up</h2>
                <p>Please fill up the the form with your information.</p>
            </div>

            <form method="POST" id="AddTeacherForm" enctype="multipart/form-data">

                <div class="form-input-container flex jus-center al-center">

                    <div class="input-group">
                            <label>Full Name</label>
                            <input type="text" name="fullname" required placeholder="Juan Dele Cruz">
                    </div>

                    <div class="form-group flex jus-center al-center">
                        <div class="input-group">
                            <label>Department</label>
                            <select name="departments" id="departments">
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
                            <input type="text" name="address" placeholder="123 Main St"><br>
                        </div>
                    </div>

                    <div class="form-group flex jus-center al-center">
                        <div class="input-group">
                            <label>Birthdate</label>
                            <input type="date" name="birthdate">
                        </div>
                        <div class="input-group">
                            <label>Gender</label>
                            <select name="gender" id="gender">
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

                    <input type="hidden" name="is_registered" value="0">

                </div>

                <div class="form-buttons flex jus-center al-center flex-col gap10 mt20">
                    <input class="btn-primary" style="width: 100%; font-weight:500; font-size: 15px;" type="submit" name="signup" value="SIGN UP" style="max-width: 80px;">
                    <p>already have an account? <a href="index.php">Login</a></p>
                </div>
            </form>
        </div>

        <!-- Add Student form -->
        <div class="form-container" id="studentForm" style="display: none;">

            <div class="header flex jus-center al-center flex-col">
                <h2>Sign Up</h2>
                <p>Please fill up the the form with your information.</p>
            </div>

            <form method="POST" id="AddStudentForm" enctype="multipart/form-data">

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
                            <input type="password" name="password" placeholder="Enter your password" required>
                        </div>
                    </div>

                    <input type="hidden" name="is_registered" value="0">

                </div>

                <div class="form-buttons flex jus-center al-center flex-col gap10 mt20">
                    <input class="btn-primary" style="width: 100%; font-weight:500; font-size: 15px;" type="submit" name="signup" value="SIGN UP" style="max-width: 80px;">
                    <p>already have an account? <a href="index.php">Login</a></p>
                </div>
            </form>
        </div>


        <!-- Role Selection -->
        <div class="form-container" id="roleSelection">
            <div class="header flex jus-center al-center flex-col">
                <h2>CREATE ACCOUNT</h2>
                <p>Please select what is your role.</p>
            </div>

            <div class="form-buttons flex jus-center al-center flex-col gap10 mt20">
                <button class="btn-primary" id="teacherBtn" style="width: 100%; font-weight:500; font-size: 15px;">TEACHER</button>
                <button class="btn-primary" id="studentBtn" style="width: 100%; font-weight:500; font-size: 15px;">STUDENT</button>
                <br>
                <p>already have an account? <a href="index.php">Login</a></p>
            </div>
        </div>
    
    </div>

    <div class="right flex al-center jus-center">
      <img src="./assets/images/system_image/school-logo.png" id="logo" width="250" alt="Logo" style="filter: drop-shadow(5px 5px 10px rgba(0,0,0,0.5));">
    </div>

  </div>


<script>

  const logo = document.getElementById("logo");

  document.addEventListener("mousemove", (e) => {
    const intensity = 45;

    const x = (window.innerWidth / 2 - e.clientX) / intensity;
    const y = (window.innerHeight / 2 - e.clientY) / intensity;

    // Strong tilt
    logo.style.transform = `rotateY(${-x * 2}deg) rotateX(${y * 2}deg) scale(1.05)`;

    // Shadow follows opposite of cursor movement
    const shadowX = x * 3;   // multiplier = shadow intensity
    const shadowY = y * 3;
    const blur = 20;

    logo.style.filter = `drop-shadow(${shadowX}px ${shadowY}px ${blur}px rgba(0,0,0,0.55))`;
  });

  document.addEventListener("mouseleave", () => {
    logo.style.transform = "rotateY(0deg) rotateX(0deg) scale(1)";
    logo.style.filter = "drop-shadow(5px 5px 15px rgba(0,0,0,0.55))";
  });




    // Role Selection Logic
    const teacherBtn = document.getElementById("teacherBtn");
    const studentBtn = document.getElementById("studentBtn");

    const teacherForm = document.getElementById("teacherForm");
    const studentForm = document.getElementById("studentForm");
    const roleSelection = document.getElementById("roleSelection");

    // Show Teacher Form
    teacherBtn.addEventListener("click", () => {
        roleSelection.style.display = "none";
        studentForm.style.display = "none";

        teacherForm.style.display = "block";
    });

    // Show Student Form
    studentBtn.addEventListener("click", () => {
        roleSelection.style.display = "none";
        teacherForm.style.display = "none";

        studentForm.style.display = "block";
    });




        // --------------------- ADD TEACHER / STUDENT ---------------------
    $(document).on("submit", "#AddTeacherForm", function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        formData.append("action", "addTeacher");
        formData.append("role", "teacher");
        formData.append("profile_photo", "default.png");

        $.ajax({
            url: "db/request.php",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function(res) {
                if (res.status === "success") {
                    Swal.fire({ icon: "success", title: "Teacher Added!", timer: 1500, showConfirmButton: false });
                    $("#AddTeacherForm")[0].reset();
                    loadUsers();
                } else {
                    Swal.fire({ icon: "error", title: "Error", text: res.message });
                }
            }
        });
    });

    $(document).on("submit", "#AddStudentForm", function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        formData.append("action", "addStudent");
        formData.append("role", "student");
        formData.append("profile_photo", "default.png");

        $.ajax({
            url: "db/request.php",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function(res) {
                if (res.status === "success") {
                    Swal.fire({ icon: "success", title: "Student Added!", timer: 1500, showConfirmButton: false });
                    $("#AddStudentForm")[0].reset();
                    loadUsers();
                } else {
                    Swal.fire({ icon: "error", title: "Error", text: res.message });
                }
            }
        });
    });
</script>




</body>
</html>


