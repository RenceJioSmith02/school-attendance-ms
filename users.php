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

                    <input type="hidden" name="is_registered" value="1">

                </div>

                <div class="form-buttons flex jus-center al-center flex-col gap10 mt20">
                    <input class="btn-primary" style="width: 100%; font-weight:500; font-size: 15px;" type="submit" name="cerate" value="CREATE" style="max-width: 80px;">
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
                    <button class="btn-primary" style="max-width: 150px;" id="showAddTeacherForm">+ Add Teacher</button>
                    <button class="btn-primary" style="max-width: 150px; display: none;" id="showAddStudentForm">+ Add Student</button>

                    <select id="filterRole" class="btn-primary custom-filter" style="max-width: 150px;">
                        <option class="text-left" value="teacher">Teacher</option>
                        <option class="text-left" value="student">Student</option>
                    </select>

                    <select id="customShowEntries" class="btn-primary custom-filter" style="max-width: 150px;">
                        <option class="text-left" value="10" selected>Show 10</option>
                        <option class="text-left" value="25">Show 25</option>
                        <option class="text-left" value="50">Show 50</option>
                        <option class="text-left" value="100">Show 100</option>
                    </select>

                    <select id="filterDepartment" class="btn-primary custom-filter" style="max-width: 150px;">
                        <option class="text-left" value="" selected>All Departments</option>
                        <option class="text-left" value="Mathematics">Mathematics</option>
                        <option class="text-left" value="Science">Science</option>
                        <option class="text-left" value="English">English</option>
                        <option class="text-left" value="Filipino">Filipino</option>
                        <option class="text-left" value="Mapeh">Mapeh</option>
                    </select>
                </div>
            </div>

            <div class="table-container">
                <table class="table" id="usersTable">
                    <thead>
                        <tr>
                            <th>no.</th>
                            <th>Name</th>
                            <th>Profile Photo</th>
                            <th>Department</th>
                            <th>Age</th>
                            <th>Gender</th>
                            <th>Birthdate</th>
                            <th>Address</th>
                            <th>Email</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Table rows will be populated here -->
                    </tbody>
                </table>
            </div>

        </div>


    </div>



<script src="assets/js/main.js"></script>
<script>
$(document).ready(function() {

    // Default role
    let currentRole = "teacher";

    // Initial load of table
    let table = initDataTable("teacher");

    // initialize the DataTable
    function initDataTable(role) {

        let ajaxAction = role === "teacher" ? "getTeachers" : "getStudents";

        let columns = [];

        if (role === "teacher") {
            columns = [
                { data: null, render: (d, t, r, meta) => meta.row + 1 },
                { data: "name" },
                { data: "profile_photo", render: renderPhoto },
                { data: "department" },
                { data: "age" },
                { data: "gender" },
                { data: "birthdate" },
                { data: "address" },
                { data: "email" },
                { data: "user_id", render: renderActions }
            ];
        } else if (role === "student") {
            columns = [
                { data: null, render: (d, t, r, meta) => meta.row + 1 },
                { data: "name" },
                { data: "profile_photo", render: renderPhoto },
                { data: "lrn" },
                { data: "age" },
                { data: "gender" },
                { data: "birthdate" },
                { data: "address" },
                { data: "email" },
                { data: "guardian_email" },
                { data: "guardian_contact" },
                { data: "user_id", render: renderActions }
            ];
        }


        return $('#usersTable').DataTable({
            destroy: true,     
            ajax: {
                url: "db/request.php",
                type: "POST",
                data: { action: ajaxAction },
                dataSrc: r => r.status === "success" ? r.data : []
            },
            columns: columns
        });
    }

    // Re-render profile photo
    function renderPhoto(data) {
        return data ? `<img src="assets/images/user_image/${data}" width="40" height="40" style="border-radius:50%;">` : 'No photo';
    }

    // Edit/Delete buttons
    function renderActions(id) {
        return `
            <button class="btn-primary edit-user" data-id="${id}">Edit</button>
            <button class="btn-secondary delete-user" data-id="${id}">Delete</button>
        `;
    }


    /* ---------------- FILTER ROLE LOGIC ---------------- */
    $('#filterRole').on('change', function() {

        currentRole = $(this).val();

        // UI adjustments
        if (currentRole === "teacher") {
            $('#showAddTeacherForm').show();
            $('#filterDepartment').show();
            $('button:contains("+ Add Student")').hide();
        } else {
            $('button:contains("+ Add Student")').show();
            $('#showAddStudentForm').hide();
            $('#filterDepartment').hide();
        }

        // RESET search and filters
        $('#searchInput').val("");
        table.search("").columns().search("");

        // RESET department filter
        $('#filterDepartment').val("");

        // DESTROY + CLEAR TABLE to prevent leftover rows
        table.clear().destroy();

        // Change table header BEFORE reinitializing
        refreshTableHeader(currentRole);

        // Reinitialize clean DataTable
        table = initDataTable(currentRole);
    });


    function refreshTableHeader(role) {

        let header = "";

        if (role === "teacher") {
            header = `
                <tr>
                    <th>No.</th>
                    <th>Name</th>
                    <th>Profile Photo</th>
                    <th>Department</th>
                    <th>Age</th>
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
                    <th>Profile Photo</th>
                    <th>LRN</th>
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

        $('#usersTable thead').html(header);
    }


    /* ---------------- CUSTOM FILTERS ---------------- */

    // Custom Search Input
    $('#searchInput').on('keyup', function () {
        table.search(this.value).draw();
    });


    // Department Filter — Column index 3 (Department column)
    $('#filterDepartment').on('change', function () {
        let value = this.value;

        // Apply filter only to Department column
        table.column(3).search(value).draw();
    });

    // Custom Show Entries Dropdown
    $('#customShowEntries').on('change', function () {
        table.page.len($(this).val()).draw();
    });



    /* ---------------- ADD TEACHER AJAX ---------------- */
    $(document).on("submit", "#AddTeacherForm", function(e) {
        e.preventDefault(); // stop normal form submit

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

            success: function(response) {

                if (response.status === "success") {

                    Swal.fire({
                        icon: "success",
                        title: "Teacher Added Successfully!",
                        timer: 1500,
                        showConfirmButton: false
                    });

                    hideModals(); // close modal

                    $("#AddTeacherForm")[0].reset(); // reset form

                    // Reload table
                    $("#usersTable").DataTable().ajax.reload(null, false);
                } 
                else {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: response.message
                    });
                }
            },

            error: function(xhr) {
                console.log("AJAX Error:", xhr.responseText);
            }
        });

    });


















});
</script>



</body>
</html>