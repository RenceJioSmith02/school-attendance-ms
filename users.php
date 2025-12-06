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
    
    <div class="container flex flex-col jus-start ">

        <!-- Header -->
        <header class="flex jus-between al-center">

            <!-- Logo -->
            <a href="admin.php" class="logo-container-header flex jus-center al-center" style="gap: 10px;">
                <img src="./assets/images/system_image/school-logo.png" width="40" alt="Logo">
                <h1>JOLNHS</h1>
            </a>

            <!-- Desktop Links -->
            <div class="links">
                <a class="link" href="dashboard.php">Dashboard</a>
                &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
                <a class="link" href="users.php">Report</a>
                &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
                <a class="link" href="users.php">Profile</a>
            </div>

            <!-- Desktop Logout Button -->
            <button class="btn-secondary logout-btn">Logout</button>

            <!-- Mobile Burger Icon -->
            <div class="burger" id="burgerMenu">
                <span></span>
                <span></span>
                <span></span>
            </div>

        </header>

        <!-- Mobile Sidebar -->
        <div class="sidebar" id="mobileSidebar">

            <a class="link" href="dashboard.php">Dashboard</a>
            <a class="link" href="users.php">Report</a>
            <a class="link" href="users.php">Profile</a>

            <button class="btn-secondary" style="margin-top:20px;">Logout</button>

        </div>


        <div class="content dashboard">
            <div class="top-controls flex jus-between al-center pt10 pb10">
                <div class="search-container" style="order: 2;">
                    <input type="text" id="searchInput" placeholder="Search...">
                    <img src="./assets/images/system_image/svg/search-icon.svg" class="search-icon" />
                </div>
                <div class="flex al-center jus center gap-10">
                    <button class="btn-primary" style="max-width: 150px;" id="showAddTeacherForm">+ Add Teacher</button>

                    <select id="customShowEntries" class="btn-primary custom-filter" style="max-width: 150px;">
                        <option class="text-left" value="10" selected>Show 10</option>
                        <option class="text-left" value="25">Show 25</option>
                        <option class="text-left" value="50">Show 50</option>
                        <option class="text-left" value="100">Show 100</option>
                    </select>

                    <select class="btn-primary custom-filter" style="max-width: 150px;" name="" id="filterDepartment">
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

    // Initialize DataTable and store instance
    let table = $('#usersTable').DataTable({
        "ajax": {
            "url": "db/request.php",
            "type": "POST",
            "data": { action: "getTeachers" },
            "dataSrc": function(response) {
                if (response.status === "success") {
                    return response.data;
                } else {
                    console.error(response.message);
                    return [];
                }
            }
        },
        "columns": [
            {
                "data": null,
                "render": function(data, type, row, meta) {
                    return meta.row + 1; 
                }
            },
            { "data": "name" },
            {
                "data": "profile_photo",
                "render": function(data) {
                    if (!data) return "No photo";
                    return `<img src="uploads/${data}" width="40" height="40" style="border-radius:50%;">`;
                }
            },
            { "data": "department" },
            { "data": "age" },
            { "data": "gender" },
            { "data": "birthdate" },
            { "data": "address" },
            { "data": "email" },
            {
                "data": "user_id",
                "render": function(id) {
                    return `
                        <button class="btn-primary edit-user" data-id="${id}">Edit</button>
                        <button class="btn-secondary delete-user" data-id="${id}">Delete</button>
                    `;
                }
            }
        ]
    });

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

});
</script>



</body>
</html>