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

    <!-- invite student form -->
    <div class="form-container modal scroller-format" id="invite-student" style="max-width: 1280px;">

        <div class="header flex jus-center al-center flex-col">
            <h2>Invite New Student</h2>
            <span class="close-btn" onclick="hideModals()">&times;</span>
        </div>

        <div class="top-controls flex jus-between al-center pt10 pb10">
            <div class="search-container" style="order: 2;">
                <input type="text" id="inviteStudent_searchInput" placeholder="Search...">
                <img src="./assets/images/system_image/svg/search-icon.svg" class="search-icon" />
            </div>
            <div class="flex al-center jus center gap-10">
            </div>
        </div>

        <div class="table-container">

            <div class="table-scroll">
                <table id="invite_students_table">
                    <thead id="tableHead">
                        <tr>
                            <th>no.</th>
                            <th>Name</th>
                            <th>Photo</th>
                            <th>LRN</th>
                            <th>Age</th>
                            <th>Gender</th>
                            <th>Birthdate</th>
                            <th>Address</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
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



    <div class="container flex flex-col jus-start ">

        <!-- Header -->
        <?php include 'partials/header.php'; ?>


        <div class="content dashboard">
            <div class="top-controls flex jus-between al-center pt10 pb10">
                <div class="search-container" style="order: 2;">
                    <input type="text" id="classMember_searchInput" placeholder="Search...">
                    <img src="./assets/images/system_image/svg/search-icon.svg" class="search-icon" />
                </div>
                <div class="flex al-center jus center gap-10">

                    <select id="filterShowbuttons" class="btn-primary custom-filter" style="max-width: 150px;">
                        <option class="text-left" value="attendance">Attendance</option>
                        <option class="text-left" value="user_management">User Management</option>
                    </select>

                    <button class="btn-primary" style="max-width: 150px;" id="showInviteStudentForm">+ Invite Student</button>

                    <select id="filterQuarter" class="btn-primary custom-filter" style="max-width: 150px;">
                        <option class="text-left" value="First Quarter">First Quarter</option>
                        <option class="text-left" value="Second Quarter">Second Quarter</option>
                        <option class="text-left" value="Third Quarter">Third Quarter</option>
                        <option class="text-left" value="Fourth Quarter">Fourth Quarter</option>
                    </select>

                    <button class="btn-primary" style="max-width: 150px;" id="downloadReport">Download Report</button>

                </div>
            </div>


            <div class="table-container">

                <div class="table-scroll">
                    <table id="class_members_table">
                        <thead id="tableHead">
                            <tr>
                                <th>no.</th>
                                <th>Name</th>
                                <th>Photo</th>
                                <th>LRN</th>
                                <th>Age</th>
                                <th>Gender</th>
                                <th>Birthdate</th>
                                <th>Address</th>
                                <th>Email</th>
                                <th>Guardian Email</th>
                                <th>Guardian Contact</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
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

        $(document).ready(function () {

            // --------------------- STATE ---------------------
            let classId = new URLSearchParams(window.location.search).get("id");
            let currentPage = 1;

            // --------------------- INIT ---------------------
            loadClassMembers();
            loadInvitableStudents();

            // --------------------- FUNCTIONS ---------------------

            function loadClassMembers() {
                $.ajax({
                    url: "db/request.php",
                    type: "POST",
                    data: {
                        action: "getClassMembers",
                        class_id: classId,
                        search: $("#classMember_searchInput").val(),
                        page: currentPage
                    },
                    dataType: "json",
                    success: function (res) {
                        if (res.status === "success") {
                            renderClassMembers(res.data);
                        }
                    }
                });
            }

            function renderClassMembers(data) {
                let rows = "";
                let count = 1;

                data.forEach(s => {
                    rows += `
                    <tr>
                        <td>${count++}</td>
                        <td>${s.name}</td>
                        <td>
                            <img src="assets/images/user_image/${s.profile_photo || 'default.png'}"
                                width="40" style="border-radius:50%;">
                        </td>
                        <td>${s.lrn}</td>
                        <td>${s.age ?? ''}</td>
                        <td>${s.gender ?? ''}</td>
                        <td>${s.birthdate ?? ''}</td>
                        <td>${s.address ?? ''}</td>
                        <td>${s.email}</td>
                        <td>${s.guardian_email}</td>
                        <td>${s.guardian_contact}</td>
                        <td>
                            <span class="badge ${s.status}">
                                ${s.status}
                            </span>
                        </td>
                        <td>
                            <button class="btn-delete remove-student"
                                    data-id="${s.student_id}">
                                Remove
                            </button>
                            <div class="attendance-buttons">
                                <button class="btn-primary present-student"
                                        data-id="${s.student_id}">
                                    P
                                </button>
                                <button class="btn-primary late-student"
                                        data-id="${s.student_id}">
                                    L
                                </button>
                                <button class="btn-primary absent-student"
                                        data-id="${s.student_id}">
                                    A
                                </button>
                                <button class="btn-primary excuse-student"
                                        data-id="${s.student_id}">
                                    E
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
                });

                $("#class_members_table tbody").html(rows);
            }


            $("#classMember_searchInput").on("input", function () {
                currentPage = 1;
                loadClassMembers();
            });



            function loadInvitableStudents() {
                $.ajax({
                    url: "db/request.php",
                    type: "POST",
                    data: {
                        action: "getInvitableStudents",
                        class_id: classId,
                        search: $("#inviteStudent_searchInput").val()
                    },
                    dataType: "json",
                    success: function (res) {
                        if (res.status === "success") {
                            renderInviteStudents(res.data);
                        }
                    }
                });
            }

            function renderInviteStudents(data) {
                let rows = "";
                let count = 1;

                data.forEach(s => {
                    rows += `
                    <tr>
                        <td>${count++}</td>
                        <td>${s.name}</td>
                        <td>
                            <img src="assets/images/user_image/${s.profile_photo || 'default.png'}"
                                width="40" style="border-radius:50%;">
                        </td>
                        <td>${s.lrn}</td>
                        <td>${s.age ?? ''}</td>
                        <td>${s.gender ?? ''}</td>
                        <td>${s.birthdate ?? ''}</td>
                        <td>${s.address ?? ''}</td>
                        <td>${s.email}</td>
                        <td>
                            <span class="badge pending">Not Joined</span>
                        </td>
                        <td>
                            <button class="btn-primary invite-btn"
                                    data-id="${s.student_id}">
                                Invite
                            </button>
                        </td>
                    </tr>
                `;
                });

                $("#invite_students_table tbody").html(rows);
            }


            $("#inviteStudent_searchInput").on("input", loadInvitableStudents);


            $(document).on("click", ".invite-btn", function () {
                let studentId = $(this).data("id");

                $.post("db/request.php", {
                    action: "inviteStudentToClass",
                    class_id: classId,
                    student_id: studentId
                }, function (res) {
                    if (res.status === "success") {
                        Swal.fire("Invited!", res.message, "success");
                        loadInvitableStudents();
                        loadClassMembers();
                    }
                }, "json");
            });


            $(document).on("click", ".remove-student", function () {
                let studentId = $(this).data("id");

                Swal.fire({
                    title: "Remove student?",
                    icon: "warning",
                    showCancelButton: true
                }).then(res => {
                    if (res.isConfirmed) {
                        $.post("db/request.php", {
                            action: "removeStudentFromClass",
                            class_id: classId,
                            student_id: studentId
                        }, function (r) {
                            if (r.status === "success") {
                                Swal.fire("Removed!", r.message, "success");
                                loadClassMembers();
                            }
                        }, "json");
                    }
                });
            });














        });

    </script>




</body>

</html>