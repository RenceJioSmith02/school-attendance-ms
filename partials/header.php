<header class="flex jus-between al-center">

    <a href="admin.php" class="logo-container-header flex jus-center al-center" style="gap: 10px;">
        <img src="./assets/images/system_image/logo-nobg.png" width="60" alt="Logo">
        <h1>DKB Fitness Gym</h1>
    </a>

    <div class="links">
        <a class="link" href="dashboard.php">Dashboard</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a class="link"
            href="users.php">Members</a>
    </div>

    <button class="btn-secondary">Logout</button>
</header>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        let links = document.querySelectorAll(".links .link");
        let current = window.location.pathname.split("/").pop(); // e.g., dashboard.php

        links.forEach(link => {
            if (link.getAttribute("href") === current) {
                link.classList.add("active");
            }
        });
    });


    // =============================
    // Logout process
    // =============================
    $(document).on("click", ".btn-secondary", function (e) {
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