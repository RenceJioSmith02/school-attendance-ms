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
    <link rel="stylesheet" href="assets/css/main.css">
    <script type="text/javascript" src="assets/js/jquery.min.js"></script>
    <script src="assets/js/sweetalert2.min.js"></script>
</head>

<body>

    <div class="container flex flex-col jus-start ">

        <!-- Header -->
        <?php include 'partials/header.php'; ?>


        <!-- Overlay -->
        <div id="overlay" style="display:none;"></div>

        <!-- Add form -->
        <div class="form-container modal scroller-format" id="add-class">
            <div class="header flex jus-center al-center flex-col">
                <h2>Add New Class</h2>
                <p>Please fill up the required information.</p>
                <span class="close-btn" onclick="hideModals()">&times;</span>
            </div>

            <form method="POST" id="addClassForm" enctype="multipart/form-data">

                <div class="form-input-container flex jus-center al-center">
                    <div class="form-group flex jus-center al-center">
                        <div class="input-group">
                            <label>Section Name</label>
                            <input type="text" name="section_name" required placeholder="Rosal"><br>
                        </div>

                        <div class="input-group">
                            <label>Grade Level</label>
                            <input type="number" name="grade_level" required placeholder="7"><br>
                        </div>
                    </div>
                    <div class="form-group flex jus-center al-center">
                        <div class="input-group">
                            <label>Subject</label>
                            <input type="text" name="subject_name" required placeholder="English"><br>
                        </div>
                        <div class="input-group" style="width: 100px;">
                            <label>Class Color</label>
                            <input type="color" name="class_color"><br>
                        </div>
                    </div>
                    <div class="input-group">
                        <label>Subject Description</label>
                        <input type="text" name="subject_description"><br>
                    </div>
                </div>

                <div class="form-buttons">
                    <input class="btn-primary" style="width: 100%; font-weight:500; font-size: 15px;" type="submit"
                        name="add_class" value="CREATE" style="max-width: 80px;">
                </div>
            </form>
        </div>


        <!-- Edit form -->
        <div class="form-container modal scroller-format" id="edit-class">
            <div class="header flex jus-center al-center flex-col">
                <h2>Edit Class</h2>
                <p>Please fill up the required information.</p>
                <span class="close-btn" onclick="hideModals()">&times;</span>
            </div>

            <form method="POST" id="editClassForm" enctype="multipart/form-data">

                <div class="form-input-container flex jus-center al-center">
                    <div class="form-group flex jus-center al-center">
                        <div class="input-group">
                            <label>Section Name</label>
                            <input type="text" name="section_name" required placeholder="Rosal"><br>
                        </div>

                        <div class="input-group">
                            <label>Grade Level</label>
                            <input type="number" name="grade_level" required placeholder="7"><br>
                        </div>
                    </div>
                    <div class="form-group flex jus-center al-center">
                        <div class="input-group">
                            <label>Subject</label>
                            <input type="text" name="subject_name" required placeholder="English"><br>
                        </div>
                        <div class="input-group" style="width: 100px;">
                            <label>Class Color</label>
                            <input type="color" name="class_color"><br>
                        </div>
                    </div>
                    <div class="input-group">
                        <label>Subject Description</label>
                        <input type="text" name="subject_description"><br>
                    </div>
                </div>

                <div class="form-buttons">
                    <input class="btn-primary" style="width: 100%; font-weight:500; font-size: 15px;" type="submit"
                        name="edit_class" value="UPDATE" style="max-width: 80px;">
                </div>
            </form>
        </div>


        <!-- Main Content -->
        <div class="content dashboard">
            <div class="top-controls flex jus-between al-center pt10 pb10">
                <div class="search-container" style="order: 2;">
                    <input type="text" id="searchInput" placeholder="Search...">
                    <img src="./assets/images/system_image/svg/search-icon.svg" class="search-icon" />
                </div>
                <div class="flex al-center jus center gap-10">
                    <button class="btn-primary" style="max-width: 150px;" id="showAddClassForm">+ Add Class</button>
                    <select id="filterStatus" class="btn-primary custom-filter" style="max-width: 150px;">
                        <option class="text-left" value="active">Active</option>
                        <option class="text-left" value="archived">Archived</option>
                    </select>
                </div>
            </div>



            <div class="card-container">

                <!-- CLASS CARDS WILL BE LOADED HERE -->

            </div>


        </div>

    </div>



    <script src="assets/js/main.js"></script>
    <script>

        $(document).ready(function () {
            loadClassrooms();
        });

        // ===============================
        // LOAD ALL CLASSROOMS
        // ===============================
        function loadClassrooms() {

            let search = $("#searchInput").val();
            let status = $("#filterStatus").val();

            $.ajax({
                url: "db/request.php",
                type: "POST",
                data: { 
                    action: "get_classrooms",
                    search: search,
                    status: status
                },
                dataType: "json",

                success: function (res) {
                    if (!res.data || res.data.length === 0) {
                        $(".card-container").html("<div class='noRecord'><p>No classes found.</p></div>");
                        return;
                    }

                    let html = "";

                    res.data.forEach(c => {

                        let actionBtn = `
                            ${c.status === "active" 
                                ? `<div class="dropdown-item archive" data-id="${c.id}">Archive</div>` 
                                : `<div class="dropdown-item unarchive" data-id="${c.id}">Unarchive</div>`
                            }
                        `;

                        html += `
                        <div class="class-card" style="border-color:${c.background_color}">
                            
                            <div class="card-header">
                                <p class="teacher-name">${c.teacher_name}</p>
                                <img class="teacher-img" src="assets/images/user_image/${c.teacher_photo}" alt="">
                            </div>

                            <a href="classroom_view.php?id=${c.id}" class="card-main" style="background-color: ${c.background_color}33;">
                                <h2 class="subject-title">${c.subject_name.toUpperCase()}</h2>
                                <p class="section-name">${c.grade_level} – ${c.section_name}</p>

                                <div class="students-chip">
                                    <img src="assets/images/system_image/svg/student-count.svg" alt="">
                                    <span>${c.total_students} students</span>
                                </div>
                            </a>

                            <div class="card-footer">
                                <div class="card-menu">⋮</div>

                                <div class="card-dropdown">

                                    ${actionBtn}

                                    <div class="dropdown-item delete" data-id="${c.id}">Delete</div>
                                    <div class="dropdown-item edit" data-id="${c.id}">Edit</div>
                                </div>
                            </div>

                        </div>`;

                    });

                    $(".card-container").html(html);
                }
            });
        }


        $("#searchInput").on("input", function () {
            loadClassrooms();
        });

        $("#filterStatus").on("change", function () {
            loadClassrooms();
        });


        // ===============================
        // LOAD SINGLE CLASSROOM DATA FOR EDITING
        // ===============================
        function loadClassroomData(classId) {
            $.ajax({
                url: "db/request.php",
                type: "POST",
                data: { action: "get_single_classroom", id: classId },
                dataType: "json",

                success: function (res) {
                    if (!res.success) {
                        Swal.fire("Error!", res.message, "error");
                        return;
                    }

                    let c = res.data;

                    // Autofill edit form
                    $("#editClassForm input[name='section_name']").val(c.section_name);
                    $("#editClassForm input[name='grade_level']").val(c.grade_level);
                    $("#editClassForm input[name='subject_name']").val(c.subject_name);
                    $("#editClassForm input[name='class_color']").val(c.background_color);
                    $("#editClassForm input[name='subject_description']").val(c.subject_description);

                    $("#editClassForm").attr("data-id", c.id);

                    showModal("edit-class");
                }
            });
        }


        $(document).on("click", ".dropdown-item.edit", function () {
            let classId = $(this).data("id");
            loadClassroomData(classId);  
        });


        // ===============================
        // ADD CLASSROOM FORM SUBMIT
        // ===============================
        $("#addClassForm").submit(function (e) {
            e.preventDefault();

            let formData = new FormData(this);
            formData.append("action", "add_classroom");

            $.ajax({
                url: "db/request.php",
                type: "POST",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                dataType: "json",

                success: function (res) {
                    if (res.status === "success") {
                        Swal.fire("Success!", "Classroom created successfully!", "success");
                        loadClassrooms();
                        hideModals();
                    } else {
                        Swal.fire("Error!", res.message, "error");
                    }
                },

                error: function (xhr, status, error) {
                    console.error(error);
                    Swal.fire("Error!", "Something went wrong!", "error");
                }
            });
        });


        // ===============================
        // EDIT CLASSROOM FORM SUBMIT
        // ===============================
        $("#editClassForm").submit(function (e) {
            e.preventDefault();

            let classId = $("#editClassForm").attr("data-id");
            let formData = new FormData(this);

            formData.append("action", "update_classroom");
            formData.append("class_id", classId);

            $.ajax({
                url: "db/request.php",
                type: "POST",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                dataType: "json",

                success: function (res) {
                    if (res.status === "success") {
                        Swal.fire("Updated!", "Classroom updated successfully!", "success");
                        loadClassrooms();
                        hideModals();
                    } else {
                        Swal.fire("Error!", res.message, "error");
                    }
                },

                error: function (xhr, status, error) {
                    console.error(error);
                    Swal.fire("Error!", "Something went wrong!", "error");
                }
            });
        });


        // ===============================
        // ARCHIVE CLASSROOM (with confirmation)
        // ===============================
        $(document).on("click", ".dropdown-item.archive", function () {

            let id = $(this).data("id");

            Swal.fire({
                title: "Archive this class?",
                text: "Students will no longer see this class until it is restored.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, archive it",
                cancelButtonText: "Cancel",
                confirmButtonColor: '#15285C',
                cancelButtonColor: '#e74c3c'
            }).then((result) => {

                if (result.isConfirmed) {

                    $.ajax({
                        url: "db/request.php",
                        type: "POST",
                        data: { action: "archive_classroom", class_id: id },
                        dataType: "json",

                        success: function (res) {
                            if (res.status === "success") {
                                Swal.fire("Archived!", "Classroom has been archived.", "success");
                                loadClassrooms();
                            } else {
                                Swal.fire("Error!", res.message, "error");
                            }
                        }
                    });

                }

            });

        });


        // ===============================
        // UNARCHIVE CLASSROOM (with confirmation)
        // ===============================
        $(document).on("click", ".dropdown-item.unarchive", function () {

            let id = $(this).data("id");

            Swal.fire({
                title: "Restore this class?",
                text: "Students will regain access to this class.",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Yes, restore it",
                cancelButtonText: "Cancel",
                confirmButtonColor: '#15285C',
                cancelButtonColor: '#e74c3c'
            }).then((result) => {

                if (result.isConfirmed) {

                    $.ajax({
                        url: "db/request.php",
                        type: "POST",
                        data: { action: "unarchive_classroom", class_id: id },
                        dataType: "json",

                        success: function (res) {
                            if (res.status === "success") {
                                Swal.fire("Restored!", "Classroom has been restored.", "success");
                                loadClassrooms();
                            } else {
                                Swal.fire("Error!", res.message, "error");
                            }
                        }
                    });

                }

            });

        });


        // ===============================
// DELETE CLASSROOM (with confirmation)
// ===============================
$(document).on("click", ".dropdown-item.delete", function () {

    let id = $(this).data("id");

    Swal.fire({
        title: "Delete this class?",
        text: "This action is permanent and cannot be undone!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, delete it",
        cancelButtonText: "Cancel",
        confirmButtonColor: '#15285C',
        cancelButtonColor: '#e74c3c'
    }).then((result) => {

        if (result.isConfirmed) {

            $.ajax({
                url: "db/request.php",
                type: "POST",
                data: { action: "delete_classroom", class_id: id },
                dataType: "json",

                success: function (res) {
                    if (res.status === "success") {
                        Swal.fire("Deleted!", "Classroom has been deleted.", "success");
                        loadClassrooms();
                    } else {
                        Swal.fire("Error!", res.message, "error");
                    }
                },

                error: function (xhr, status, error) {
                    console.error(error);
                    Swal.fire("Error!", "Something went wrong!", "error");
                }
            });

        }

    });

});



        // ===============================
        // DROPDOWN MENU FUNCTIONALITY
        // ===============================

        document.addEventListener("click", function (e) {
            const allDropdowns = document.querySelectorAll(".card-dropdown");

            // If click is on a card menu icon
            if (e.target.classList.contains("card-menu")) {
                const dropdown = e.target.nextElementSibling;

                // If this dropdown is already open → close it
                if (dropdown.style.display === "block") {
                    dropdown.style.display = "none";
                }
                else {
                    // Close all others
                    allDropdowns.forEach(d => d.style.display = "none");
                    dropdown.style.display = "block";
                }

                e.stopPropagation();
                return;
            }

            // If click is outside → close all dropdowns
            allDropdowns.forEach(drop => drop.style.display = "none");
        });

    </script>

</body>

</html>