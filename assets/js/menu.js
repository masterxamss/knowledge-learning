document.addEventListener("DOMContentLoaded", () => {
  const menuToggle = document.querySelector(".menu-toggle");
  const closeBtn = document.querySelector(".close-btn");
  const sidebar = document.querySelector(".sidebar-menu");
  const overlay = document.querySelector(".overlay");

  // Open menu
  menuToggle.addEventListener("click", () => {
    console.log("Abrir menu");
    sidebar.classList.add("active");
    overlay.classList.add("active");
  });

  // Close menu
  closeBtn.addEventListener("click", () => {
    sidebar.classList.remove("active");
    overlay.classList.remove("active");
  });

  // Close when click outside
  overlay.addEventListener("click", () => {
    sidebar.classList.remove("active");
    overlay.classList.remove("active");
  });

  // Close when press escape
  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") {
      sidebar.classList.remove("active");
      overlay.classList.remove("active");
    }
  });
});
