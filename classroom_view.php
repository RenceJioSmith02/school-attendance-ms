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



    <!-- Attendance Reason Modal -->
    <div class="form-container modal scroller-format" id="attendance-reason-modal" style="max-width: 500px;">
        <div class="header flex jus-center al-center flex-col">
            <h2 id="attendanceReasonTitle">Reason</h2>
            <span class="close-btn" onclick="hideModals()">&times;</span>
        </div>

        <div class="content p20">
            <textarea id="attendanceReasonText" placeholder="Enter reason..."
                style="width:100%; min-height:120px;"></textarea>

            <input type="hidden" id="attendanceStudentId">
            <input type="hidden" id="attendanceStatus">

            <div class="flex jus-end gap-10 mt20">
                <button class="btn-secondary" onclick="hideModals()">Cancel</button>
                <button class="btn-primary" id="saveAttendanceReason">Save</button>
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
                        <option class="text-left" value="report">Report</option>
                    </select>

                    <button class="btn-primary" style="max-width: 150px;" id="showInviteStudentForm">+ Invite
                        Student</button>

                    <input type="date" name="attendanceDate" id="attendanceDate">

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
                                <th class="last-column">Action</th>
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
            let currentMode = "attendance"; // default

            // --------------------- INIT ---------------------
            loadClassMembers();
            applyModeUI();
            loadInvitableStudents();

            // --------------------- FUNCTIONS ---------------------
            function getTodayPH() {
                const now = new Date();
                const phTime = new Date(now.toLocaleString("en-US", { timeZone: "Asia/Manila" }));
                return phTime.toISOString().split("T")[0];
            }

            $("#attendanceDate").val(getTodayPH());



            function loadClassMembers() {

                if (currentMode === "attendance") {
                    action = "getClassMembersAttendance";
                } else if (currentMode === "user_management") {
                    action = "getClassMembers";
                } else {
                    loadAttendanceReport();
                    return;
                }

                $.post("db/request.php", {
                    action: action,
                    class_id: classId,
                    attendance_date: $("#attendanceDate").val(),
                    search: $("#classMember_searchInput").val()
                }, function (res) {
                    if (res.status === "success") {
                        renderClassMembers(res.data);
                    }
                }, "json");
            }




            $("#attendanceDate").on("change", function () {
                if (currentMode === "attendance") loadClassMembers();
            });

            $("#filterQuarter").on("change", function () {
                if (currentMode === "report") loadClassMembers();
            });



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
                            ${s.attendance_status
                            ? `<span class="badge ${s.attendance_status}" 
                                    title="${s.reason ?? ''}">
                                    ${s.attendance_status}
                                </span>`
                            : `<span class="badge pending">Not Set</span>`
                        }
                        </td>
                        <td class="last-column">
                            <button class="btn-delete remove-student"
                                    data-id="${s.student_id}">
                                Remove
                            </button>
                            <div class="attendance-buttons">
                                <button class="btn-primary present-student" style="background: #2ecc71; color: #fff;"
                                        data-id="${s.student_id}">
                                    P
                                </button>
                                <button class="btn-primary late-student" style="background: #f39c12; color: #fff;"
                                        data-id="${s.student_id}">
                                    L
                                </button>
                                <button class="btn-primary absent-student" style="background: #e74c3c; color: #fff;"
                                        data-id="${s.student_id}">
                                    A
                                </button>
                                <button class="btn-primary excuse-student" style="background: #555; color: #fff;"
                                        data-id="${s.student_id}">
                                    E
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
                });

                $("#class_members_table tbody").html(rows);
                applyModeUI();
            }


            $("#classMember_searchInput").on("input", function () {
                currentPage = 1;
                if (currentMode === "report") {
                    loadAttendanceReport();
                } else {
                    loadClassMembers();
                }
            });



            function loadAttendanceReport() {
                $.post("db/request.php", {
                    action: "getAttendanceReport",
                    class_id: classId,
                    quarter: $("#filterQuarter").val(),
                    search: $("#classMember_searchInput").val()
                }, function (res) {
                    if (res.status === "success") {
                        renderReportTable(res.data);
                    }
                }, "json");
            }



            function renderReportTable(data) {
                let rows = "";
                let count = 1;

                data.forEach(s => {
                    rows += `
                    <tr>
                        <td>${count++}</td>
                        <td>${s.name}</td>
                        <td><img src="assets/images/user_image/${s.profile_photo || 'default.png'}" width="40"></td>
                        <td>${s.lrn}</td>
                        <td>${s.email}</td>
                        <td>${s.total_present || 0}</td>
                        <td>${s.total_late || 0}</td>
                        <td>${s.total_absent || 0}</td>
                        <td>${s.total_excuse || 0}</td>
                    </tr>
                    `;
                });

                $("#class_members_table tbody").html(rows);
            }



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


            function applyModeUI() {

                if (currentMode === "attendance") {

                    $("#class_members_table thead").html(`
                        <tr>
                            <th>#</th>
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
                    `);

                    $("#attendanceDate").removeClass("hidden");
                    $("#showInviteStudentForm").addClass("hidden");
                    $("#filterQuarter").addClass("hidden");
                    $("#downloadReport").addClass("hidden");
                    $(".remove-student").addClass("hidden");
                    $(".attendance-buttons").removeClass("hidden");

                }
                else if (currentMode === "report") {

                    $("#class_members_table thead").html(`
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Photo</th>
                            <th>LRN</th>
                            <th>Email</th>
                            <th>Total Present</th>
                            <th>Total Late</th>
                            <th>Total Absent</th>
                            <th>Total Excuse</th>
                        </tr>
                    `);

                    $("#attendanceDate").addClass("hidden");
                    $("#showInviteStudentForm").addClass("hidden");
                    $("#filterQuarter").removeClass("hidden");
                    $("#downloadReport").removeClass("hidden");

                }
                else if (currentMode === "user_management") {

                    $("#class_members_table thead").html(`
                        <tr>
                            <th>#</th>
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
                    `);

                    $("#attendanceDate").addClass("hidden");
                    $("#showInviteStudentForm").removeClass("hidden");
                    $("#filterQuarter").addClass("hidden");
                    $("#downloadReport").addClass("hidden");
                    $(".remove-student").removeClass("hidden");
                    $(".attendance-buttons").addClass("hidden");
                }
            }


            $("#filterShowbuttons").on("change", function () {
                currentMode = $(this).val();
                currentPage = 1;
                applyModeUI();
                loadClassMembers(); // ✅ ADD THIS
            });


            // --------------------- MARKING STUDENT ATTENDANCE ---------------------

            $(document).on("click", ".present-student, .late-student", function () {
                if (currentMode !== "attendance") return;

                const studentId = $(this).data("id");
                const status = $(this).hasClass("present-student") ? "present" : "late";

                saveAttendance(studentId, status, null);
            });

            $(document).on("click", ".absent-student, .excuse-student", function () {
                if (currentMode !== "attendance") return;

                const studentId = $(this).data("id");
                const status = $(this).hasClass("absent-student") ? "absent" : "excuse";

                $("#attendanceStudentId").val(studentId);
                $("#attendanceStatus").val(status);
                $("#attendanceReasonText").val("");

                $("#attendanceReasonTitle").text(
                    status === "absent" ? "Reason for Absence" : "Reason for Excuse"
                );

                showModal("attendance-reason-modal");
            });


            $("#saveAttendanceReason").on("click", function () {
                const studentId = $("#attendanceStudentId").val();
                const status = $("#attendanceStatus").val();
                const reason = $("#attendanceReasonText").val().trim();

                if (!reason) {
                    Swal.fire("Required", "Please enter a reason.", "warning");
                    return;
                }

                saveAttendance(studentId, status, reason);
                hideModals();
            });


            function saveAttendance(studentId, status, reason) {
                $.post("db/request.php", {
                    action: "saveAttendance",
                    class_id: classId,
                    student_id: studentId,
                    date: $("#attendanceDate").val(),
                    status: status,
                    reason: reason
                }, function (res) {
                    if (res.status === "success") {
                        loadClassMembers();
                    } else {
                        Swal.fire("Error", res.message, "error");
                    }
                }, "json");
            }


        });




        $(document).ready(function () {

            let classId = new URLSearchParams(window.location.search).get("id");

$("#downloadReport").on("click", function () {

    const classId = new URLSearchParams(window.location.search).get("id");

    if (!classId) {
        Swal.fire("Error", "Invalid class ID", "error");
        return;
    }

    const form = $("<form>", {
        method: "POST",
        action: "db/request.php"
    });

    form.append($("<input>", { name: "action", value: "downloadAttendanceReport", type: "hidden" }));
    form.append($("<input>", { name: "class_id", value: classId, type: "hidden" }));
    form.append($("<input>", { name: "quarter", value: $("#filterQuarter").val(), type: "hidden" }));
    form.append($("<input>", { name: "search", value: $("#classMember_searchInput").val(), type: "hidden" }));

    $("body").append(form);
    form.submit();
    form.remove();
});



        });




        document.addEventListener("click", function (e) {

            // Check if click happened inside ANY table
            const cell = e.target.closest("td, th");
            if (!cell) return;

            const table = cell.closest("table");
            if (!table) return;

            // Ignore action column (last column)
            const row = cell.parentNode;
            if (!row || !row.cells) return;

            const lastIndex = row.cells.length - 1;
            if (cell.cellIndex === lastIndex) return;

            // Collapse previously expanded cell (global)
            const expanded = document.querySelector("td.expanded, th.expanded");
            if (expanded && expanded !== cell) {
                expanded.classList.remove("expanded");
            }

            // Toggle expansion
            cell.classList.toggle("expanded");
        });

    </script>




</body>

</html>