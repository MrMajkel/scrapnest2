document.addEventListener("DOMContentLoaded", () => {
  const avatarBtn = document.getElementById("avatarBtn");
  const dropdownMenu = document.getElementById("dropdownMenu");

  avatarBtn.addEventListener("click", () => {
    dropdownMenu.style.display = dropdownMenu.style.display === "block" ? "none" : "block";
  });

  window.addEventListener("click", function(e) {
    if (!avatarBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
      dropdownMenu.style.display = "none";
    }
  });
});

function logout() {
  window.location.href = "backend/logout.php";
}
