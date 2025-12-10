<?php
require "db/db.php";
$mydb = new myDB();
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

    <!-- Add teacher form -->
    <div class="form-container modal scroller-format" id="add-teacher">

        <div class="header flex jus-center al-center flex-col">
            <h2>New Teacher</h2>
            <p>Please fill up the required information.</p>
            <span class="close-btn" onclick="hideModals()">&times;</span>
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
                        <input type="password" name="password" placeholder="Enter your password" required><br>
                    </div>
                </div>

                <input type="hidden" name="is_registered" value="1">

            </div>

            <div class="form-buttons flex jus-center al-center flex-col gap10 mt20">
                <input class="btn-primary" style="width: 100%; font-weight:500; font-size: 15px;" type="submit"
                    name="cerate" value="CREATE" style="max-width: 80px;">
            </div>
        </form>

    </div>


    <!-- Add student form -->
    <div class="form-container modal scroller-format" id="add-student">

        <div class="header flex jus-center al-center flex-col">
            <h2>New Student</h2>
            <p>Please fill up the required information.</p>
            <span class="close-btn" onclick="hideModals()">&times;</span>
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

                <input type="hidden" name="is_registered" value="1">

            </div>

            <div class="form-buttons flex jus-center al-center flex-col gap10 mt20">
                <input class="btn-primary" style="width: 100%; font-weight:500; font-size: 15px;" type="submit"
                    name="cerate" value="CREATE" style="max-width: 80px;">
            </div>
        </form>

    </div>



    <div class="container flex flex-col jus-start ">

        <!-- Header -->
        <?php include 'partials/header.php'; ?>


        <div class="content dashboard">
            <div class="top-controls flex jus-between al-center pt10 pb10">
                <div class="search-container" style="order: 2;">
                    <input type="text" id="searchInput" placeholder="Search...">
                    <img src="./assets/images/system_image/svg/search-icon.svg" class="search-icon" />
                </div>
                <div class="flex al-center jus center gap-10">
                    <button class="btn-primary" style="max-width: 150px;" id="reload">Reload</button>
                    <button class="btn-primary" style="max-width: 150px;" id="showAddTeacherForm">+ Add Teacher</button>
                    <button class="btn-primary" style="max-width: 150px; display: none;" id="showAddStudentForm">+ Add
                        Student</button>

                    <select id="filterRole" class="btn-primary custom-filter" style="max-width: 150px;">
                        <option class="text-left" value="teacher">Teacher</option>
                        <option class="text-left" value="student">Student</option>
                    </select>

                    <select id="filterDepartment" class="btn-primary custom-filter" style="max-width: 150px;">
                        <option class="text-left" value="" selected>All Departments</option>
                        <option class="text-left" value="Mathematics">Mathematics</option>
                        <option class="text-left" value="Science">Science</option>
                        <option class="text-left" value="English">English</option>
                        <option class="text-left" value="Filipino">Filipino</option>
                        <option class="text-left" value="Mapeh">Mapeh</option>
                    </select>

                    <select id="filterRegistered" class="btn-primary custom-filter" style="max-width: 150px;">
                        <option class="text-left" value="1">Registered</option>
                        <option class="text-left" value="0">Unregistered</option>
                    </select>
                </div>
            </div>


            <div class="table-container">

                <div class="table-scroll">
                    <table id="usersTable">
                        <thead id="tableHead"></thead>
                        <tbody id="tableBody"></tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="pagination-container" style="text-align:right;">
                    <button id="prevPage">&laquo; Prev</button>
                    <span id="pageInfo">1 of 1</span>
                    <button id="nextPage">Next &raquo;</button>
                </div>

            </div>


        </div>


    </div>



    <script src="assets/js/main.js"></script>

<script>
$(document).ready(function() {

    // --------------------- STATE ---------------------
    let currentRole = localStorage.getItem("selectedRole") || "teacher";
    let currentPage = 1;
    let totalPages = 1;
    let userData = [];
    let currentSort = { column: null, asc: true };

    // --------------------- INIT ---------------------
    $('#filterRole').val(currentRole);
    applyRoleUI(currentRole);
    loadTableHeader(currentRole);
    loadUsers();

    // --------------------- ROLE UI ---------------------
    function applyRoleUI(role) {
        if (role === "teacher") {
            $('#showAddTeacherForm').show();
            $('#showAddStudentForm').hide();
            $('#filterDepartment').show();
        } else {
            $('#showAddTeacherForm').hide();
            $('#showAddStudentForm').show();
            $('#filterDepartment').hide();
        }
    }

    // --------------------- TABLE HEADER ---------------------
    function loadTableHeader(role) {
        let header = "";
        if (role === "teacher") {
            header = `
                <tr>
                    <th>No.</th>
                    <th><button class="sort-btn" data-column="name">Name</button></th>
                    <th>Photo</th>
                    <th><button class="sort-btn" data-column="department">Department</button></th>
                    <th><button class="sort-btn" data-column="age">Age</button></th>
                    <th>Gender</th>
                    <th>Birthdate</th>
                    <th>Address</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
            `;
        } else {
            header = `
                <tr>
                    <th>No.</th>
                    <th>Name</th>
                    <th>Photo</th>
                    <th><button class="sort-btn" data-column="lrn">LRN</button></th>
                    <th>Age</th>
                    <th>Gender</th>
                    <th>Birthdate</th>
                    <th>Address</th>
                    <th>Email</th>
                    <th>Guardian Email</th>
                    <th>Guardian Contact</th>
                    <th>Action</th>
                </tr>
            `;
        }
        $("#tableHead").html(header);
    }

    // --------------------- LOAD USERS ---------------------
    function loadUsers() {
        $.ajax({
            url: "db/request.php",
            method: "POST",
            data: {
                action: currentRole === "teacher" ? "getTeachers" : "getStudents",
                search: $("#searchInput").val(),
                department: $("#filterDepartment").val(),
                registered: $("#filterRegistered").val(),
                page: currentPage
            },
            dataType: "json",
            success: function(res) {
                if (res.status === "success") {
                    userData = res.data;
                    totalPages = Math.ceil(res.total / res.limit);
                    applySorting();
                    renderUsers(userData);
                    updatePagination();
                } else {
                    $("#tableBody").html(`<tr><td colspan="12">No data found</td></tr>`);
                    totalPages = 1;
                    updatePagination();
                }
            }
        });
    }

    // --------------------- RENDER USERS ---------------------

    function renderUsers(data) {
        let rows = "";
        let count = (currentPage - 1) * 10 + 1;

        data.forEach(u => {

            // Determine actions based on registration status
            let actionButtons = "";

            if (u.is_registered == 0) {
                actionButtons = `
                    <button class="btn-primary register-user" data-id="${u.user_id}">Register</button>
                `;
            } else {
                actionButtons = `
                    <button class="btn-primary edit-user" data-id="${u.user_id}">Edit</button>
                    <button class="btn-delete delete-user" data-id="${u.user_id}">Delete</button>
                `;
            }

            if (currentRole === "teacher") {
                rows += `
                    <tr>
                        <td>${count++}</td>
                        <td>${u.name}</td>
                        <td><img src="assets/images/user_image/${u.profile_photo || 'default.png'}" width="40" style="border-radius:50%;"></td>
                        <td>${u.department}</td>
                        <td>${u.age}</td>
                        <td>${u.gender}</td>
                        <td>${u.birthdate}</td>
                        <td>${u.address}</td>
                        <td>${u.email}</td>
                        <td>${actionButtons}</td>
                    </tr>
                `;
            } else {
                rows += `
                    <tr>
                        <td>${count++}</td>
                        <td>${u.name}</td>
                        <td><img src="assets/images/user_image/${u.profile_photo || 'default.png'}" width="40" style="border-radius:50%;"></td>
                        <td>${u.lrn}</td>
                        <td>${u.age}</td>
                        <td>${u.gender}</td>
                        <td>${u.birthdate}</td>
                        <td>${u.address}</td>
                        <td>${u.email}</td>
                        <td>${u.guardian_email}</td>
                        <td>${u.guardian_contact}</td>
                        <td>${actionButtons}</td>
                    </tr>
                `;
            }
        });

        $("#tableBody").html(rows);
    }


        // --------------------- REGISTER USER ---------------------

    $(document).on("click", ".register-user", function() {
        let userId = $(this).data("id");

        Swal.fire({
            title: "Register this user?",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#15285C",
            cancelButtonColor: "#e74c3c",
            confirmButtonText: "Register"
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    url: "db/request.php",
                    type: "POST",
                    data: {
                        action: "registerUser",
                        user_id: userId
                    },
                    dataType: "json",
                    success: function(res) {
                        if (res.status === "success") {
                            Swal.fire("Success", res.message, "success");
                            loadUsers();
                        } else {
                            Swal.fire("Error", res.message, "error");
                        }
                    },
                    error: function() {
                        Swal.fire("Error", "Something went wrong", "error");
                    }
                });

            }
        });
    });


    // function renderUsers(data) {
    //     let rows = "";
    //     let count = (currentPage - 1) * 10 + 1;

    //     data.forEach(u => {
    //         if (currentRole === "teacher") {
    //             rows += `
    //                 <tr>
    //                     <td>${count++}</td>
    //                     <td>${u.name}</td>
    //                     <td><img src="assets/images/user_image/${u.profile_photo || 'default.png'}" width="40" style="border-radius:50%;"></td>
    //                     <td>${u.department}</td>
    //                     <td>${u.age}</td>
    //                     <td>${u.gender}</td>
    //                     <td>${u.birthdate}</td>
    //                     <td>${u.address}</td>
    //                     <td>${u.email}</td>
    //                     <td>
    //                         <button class="btn-primary edit-user" data-id="${u.user_id}">Edit</button>
    //                         <button class="btn-delete delete-user" data-id="${u.user_id}">Delete</button>
    //                     </td>
    //                 </tr>
    //             `;
    //         } else {
    //             rows += `
    //                 <tr>
    //                     <td>${count++}</td>
    //                     <td>${u.name}</td>
    //                     <td><img src="assets/images/user_image/${u.profile_photo || 'default.png'}" width="40" style="border-radius:50%;"></td>
    //                     <td>${u.lrn}</td>
    //                     <td>${u.age}</td>
    //                     <td>${u.gender}</td>
    //                     <td>${u.birthdate}</td>
    //                     <td>${u.address}</td>
    //                     <td>${u.email}</td>
    //                     <td>${u.guardian_email}</td>
    //                     <td>${u.guardian_contact}</td>
    //                     <td>
    //                         <button class="btn-primary edit-user" data-id="${u.user_id}">Edit</button>
    //                         <button class="btn-delete delete-user" data-id="${u.user_id}">Delete</button>
    //                     </td>
    //                 </tr>
    //             `;
    //         }
    //     });

    //     $("#tableBody").html(rows);
    // }

    // --------------------- SEARCH ---------------------
    $("#searchInput").on("input", function() {
        currentPage = 1;
        loadUsers();
    });

    // --------------------- ROLE CHANGE ---------------------
    $("#filterRole").on("change", function() {
        currentRole = $(this).val();
        localStorage.setItem("selectedRole", currentRole);
        currentPage = 1;
        applyRoleUI(currentRole);
        loadTableHeader(currentRole);
        loadUsers();
    });

    // --------------------- DEPARTMENT FILTER ---------------------
    $("#filterDepartment").on("change", function() {
        currentPage = 1;
        loadUsers();
    });

    // --------------------- REGISTERED FILTER ---------------------
    $("#filterRegistered").on("change", function() {
    currentPage = 1;
        loadUsers();
    });


    // --------------------- PAGINATION ---------------------
    function updatePagination() {
        $("#pageInfo").text(`${currentPage} of ${totalPages}`);
        $("#prevPage").prop("disabled", currentPage === 1);
        $("#nextPage").prop("disabled", currentPage === totalPages);
    }

    $("#prevPage").on("click", function() {
        if (currentPage > 1) {
            currentPage--;
            loadUsers();
        }
    });

    $("#nextPage").on("click", function() {
        if (currentPage < totalPages) {
            currentPage++;
            loadUsers();
        }
    });

    // --------------------- SORTING ---------------------
    $(document).on("click", ".sort-btn", function() {
        let col = $(this).data("column");
        currentSort = {
            column: col,
            asc: currentSort.column === col ? !currentSort.asc : true
        };
        applySorting();
        renderUsers(userData);
    });

    function applySorting() {
        if (!currentSort.column) return;
        let { column, asc } = currentSort;
        userData.sort((a, b) => {
            let valA = a[column] || "";
            let valB = b[column] || "";
            return asc ? String(valA).localeCompare(String(valB)) : String(valB).localeCompare(String(valA));
        });
    }

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


    // --------------------- DELETE USER ---------------------
    $(document).on("click", ".delete-user", function() {
        let userId = $(this).data("id");

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#15285C',
            cancelButtonColor: '#e74c3c',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "db/request.php",
                    type: "POST",
                    data: {
                        action: "deleteUser",
                        user_id: userId,
                        role: currentRole
                    },
                    dataType: "json",
                    success: function(res) {
                        if (res.status === "success") {
                            Swal.fire(
                                'Deleted!',
                                res.message,
                                'success'
                            );
                            loadUsers(); // reload table
                        } else {
                            Swal.fire('Error', res.message, 'error');
                        }
                    },
                    error: function(err) {
                        Swal.fire('Error', 'Something went wrong', 'error');
                    }
                });
            }
        });
    });


    // --------------------- RELOAD BUTTON ---------------------
    $("#reload").on("click", function() {
        location.reload();
    });

});
</script>




</body>

</html>