// Append overlay container once
if (!document.getElementById("imgOverlay")) {
  document.body.insertAdjacentHTML(
    "beforeend",
    `
        <div id="imgOverlay" class="img-overlay">
            <img src="" alt="Preview">
        </div>
    `
  );
}

const overlay = document.getElementById("imgOverlay");
const overlayImg = overlay.querySelector("img");

// Open overlay on click (logsTable)
$(document).on("click", "#logsTable .log-img", function () {
  overlayImg.src = this.src;
  overlay.style.display = "flex";
});

// Open overlay on click (membersTable)
$(document).on("click", "#membersTable .member-img", function () {
  overlayImg.src = this.src;
  overlay.style.display = "flex";
});

// Close overlay when clicking outside image
overlay.addEventListener("click", function (e) {
  if (e.target === overlay) {
    overlay.style.display = "none";
    overlayImg.src = "";
  }
});
