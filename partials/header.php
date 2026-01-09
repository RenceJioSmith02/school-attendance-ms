<!-- Header -->
<header class="flex jus-between al-center">

    <!-- Logo -->
    <a href="admin.php" class="logo-container-header flex jus-center al-center" style="gap: 10px;">
        <img src="./assets/images/system_image/school-logo.png" width="40" alt="Logo">
        <h1>JOLNHS</h1>
    </a>

    <!-- Desktop Links -->
    <div class="links">
        <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {?>
            <a class="link" href="users.php">Users</a>
            &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
        <?php } ?>
        <?php if(isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'teacher' || $_SESSION['user_role'] === 'student')) {?>
            <a class="link" href="classrooms.php">Classrooms</a>
            &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
        <?php } ?>
        <a class="link" href="profile.php"><?php echo (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin')
            ? 'Settings'
            : 'Profile'; ?></a>
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

    <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {?>
        <a class="link" href="users.php">Users</a>
    <?php } ?>
    <?php if(isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'teacher' || $_SESSION['user_role'] === 'student')) {?>
        <a class="link" href="classrooms.php">Classrooms</a>
    <?php } ?>
        <a class="link" href="profile.php"><?php echo (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin')
        ? 'Settings'
        : 'Profile'; ?></a>

    <button class="btn-secondary" style="margin-top:20px;">Logout</button>

</div>


<script>
    // =============================
    // Logout process
    // =============================
    $(document).on("click", ".logout-btn", function (e) {
        e.preventDefault();

        $.ajax({
            url: "db/request.php",
            type: "POST",
            data: { action: "logout" },
            dataType: "json",
            success: function (response) {
                if (response.status === "success") {
                    // Redirect to login page
                    window.location.href = "index.php";
                } else {
                    alert(response.message || "Logout failed.");
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX Logout Error:", error);
                alert("Something went wrong during logout.");
            }
        });
    });
</script>