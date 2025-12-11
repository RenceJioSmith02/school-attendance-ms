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

$("#overlay").on("click", hideModals);




document.addEventListener("DOMContentLoaded", () => {
  const table = document.querySelector("table");
  let expandedCell = null; // store currently expanded cell

  table.addEventListener("click", (e) => {
    // Find the closest td or th
    let cell = e.target.closest("td, th");

    if (!cell) return;

    // Ignore last column (actions)
    const lastIndex = cell.parentNode.cells.length - 1;
    if (cell.cellIndex === lastIndex) return;

    // Collapse previously expanded cell if different
    if (expandedCell && expandedCell !== cell) {
      expandedCell.classList.remove("expanded");
    }

    // Toggle the clicked cell
    cell.classList.toggle("expanded");

    // Store it if expanded, otherwise clear
    expandedCell = cell.classList.contains("expanded") ? cell : null;
  });
});
