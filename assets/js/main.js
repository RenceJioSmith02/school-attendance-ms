// =============================
// Burger Menu & Mobile Sidebar
// =============================

$(document).ready(function () {
  const burger = $("#burgerMenu");
  const sidebar = $("#mobileSidebar");

  // Toggle sidebar
  burger.on("click", function () {
    sidebar.toggleClass("open");
    burger.toggleClass("active");

    // disable body scroll when sidebar is open
    if (sidebar.hasClass("open")) {
      $("body").css("overflow", "hidden");
    } else {
      $("body").css("overflow", "auto");
    }
  });

  // Close sidebar when clicking outside (on mobile)
  $(document).on("click", function (e) {
    if (
      !sidebar.is(e.target) &&
      sidebar.has(e.target).length === 0 &&
      !burger.is(e.target) &&
      burger.has(e.target).length === 0
    ) {
      if (sidebar.hasClass("open")) {
        sidebar.removeClass("open");
        burger.removeClass("active");
        $("body").css("overflow", "auto");
      }
    }
  });
});

// =============================
// Modals
// =============================
function showModal(id) {
  $("#overlay").show();
  $(`#${id}`).show();
}

function hideModals() {
  $("#overlay").hide();
  $(".modal").hide();
  imagePreview.src = DEFAULT_IMAGE; // reset to default
  imageInput.value = ""; // clear file input
}

// Show Modals
$("#showAddClassForm").on("click", function (e) {
  e.preventDefault();
  showModal("add-class");
});

$("#showAddTeacherForm").on("click", function (e) {
  e.preventDefault();
  showModal("add-teacher");
});

$("#showAddStudentForm").on("click", function (e) {
  e.preventDefault();
  showModal("add-student");
});

$("#showUpdateStudentForm").on("click", function (e) {
  e.preventDefault();
  showModal("update-student");
});

$("#showUpdateTeacherForm").on("click", function (e) {
  e.preventDefault();
  showModal("update-teacher");
});

$("#showUpdateQuarterForm").on("click", function (e) {
  e.preventDefault();
  showModal("update-quarter");
});

$("#showUpdatePasswordForm").on("click", function (e) {
  e.preventDefault();
  showModal("update-admin-password");
});

$("#showInviteStudentForm").on("click", function (e) {
  e.preventDefault();
  showModal("invite-student");
});



$("#overlay").on("click", hideModals);




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
