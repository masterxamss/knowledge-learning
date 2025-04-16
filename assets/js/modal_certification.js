document.addEventListener("DOMContentLoaded", () => {
  const modal = document.getElementById("course-completed-modal");
  const closeBtn = modal?.querySelector(".close");

  if (modal && closeBtn) {
    closeBtn.addEventListener("click", () => {
      modal.style.display = "none";
    });

    // Opcional: clicar fora do modal também o fecha
    window.addEventListener("click", (event) => {
      if (event.target === modal) {
        modal.style.display = "none";
      }
    });
  }
});
