document.addEventListener("DOMContentLoaded", () => {
  const menuToggle = document.querySelector(".menu-toggle");
  const closeBtn = document.querySelector(".close-btn");
  const sidebar = document.querySelector(".sidebar-menu");
  const overlay = document.querySelector(".overlay");

  // if (!menuToggle || !closeBtn || !sidebar || !overlay) {
  //   console.error("Erro: Um ou mais elementos não foram encontrados.");
  //   return;
  // }
  //
  console.log(menuToggle);

  // Abrir menu
  menuToggle.addEventListener("click", () => {
    console.log("Abrir menu");
    sidebar.classList.add("active");
    overlay.classList.add("active");
  });

  // Fechar menu
  closeBtn.addEventListener("click", () => {
    sidebar.classList.remove("active");
    overlay.classList.remove("active");
  });

  // Fechar ao clicar fora
  overlay.addEventListener("click", () => {
    sidebar.classList.remove("active");
    overlay.classList.remove("active");
  });

  // Fechar ao apertar ESC
  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") {
      sidebar.classList.remove("active");
      overlay.classList.remove("active");
    }
  });
});
