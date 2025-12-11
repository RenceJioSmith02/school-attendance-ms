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
                    <input class="btn-primary" style="width: 100%; font-weight:500; font-size: 15px;" type="submit" name="add_member" value="Create" style="max-width: 80px;">
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
                    <select class="btn-primary" style="max-width: 150px;" name="" id="">
                        <option class="text-left" value="">Sort By</option>
                        <option class="text-left" value="grade-asc">Grade Level (Asc)</option>
                        <option class="text-left" value="grade-desc">Grade Level (Desc)</option>
                        <option class="text-left" value="section-asc">Section Name (A-Z)</option>
                        <option class="text-left" value="section-desc">Section Name (Z-A)</option>
                    </select>
                </div>
            </div>



            <div class="card-container">

                <div class="class-card">

                    <div class="card-header">
                        <p class="teacher-name">Jhero S. Antonio</p>

                        <!-- Teacher Image -->
                        <img class="teacher-img" src="assets/images/user_image/default.png" alt="">
                    </div>

                    <a href="asd" class="card-main">
                        <h2 class="subject-title">ENGLISH</h2>
                        <p class="section-name">10 – Apitong</p>

                        <div class="students-chip">
                            <img src="assets/images/system_image/svg/student-count.svg" alt="">
                            <span>45 students</span>
                        </div>
                    </a>

                    <div class="card-footer">
                        <div class="card-menu">⋮</div>

                        <div class="card-dropdown">
                            <div class="dropdown-item archive">Archive</div>
                            <div class="dropdown-item delete">Delete</div>
                        </div>
                    </div>

                </div>

            </div>


        </div>

    </div>



<script src="assets/js/main.js"></script>
<script>

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


