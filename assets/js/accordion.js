document.addEventListener("turbo:load", function () {
  // script.js
  document.querySelectorAll(".accordion-header").forEach((button) => {
    button.addEventListener("click", function () {
      const content = this.nextElementSibling;

      // Fecha outros painéis abertos
      document.querySelectorAll(".accordion-content").forEach((item) => {
        if (item !== content) {
          item.style.maxHeight = null;
          item.style.paddingTop = "0";
          item.style.paddingBottom = "0";
        }
      });

      // Alterna o painel atual
      if (content.style.maxHeight) {
        content.style.maxHeight = null;
        content.style.paddingTop = "0";
        content.style.paddingBottom = "0";
      } else {
        content.style.maxHeight = content.scrollHeight + "px";
        content.style.paddingTop = "10px";
        content.style.paddingBottom = "10px";
      }
    });
  });
});
