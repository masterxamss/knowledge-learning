document.addEventListener("turbo:load", () => {
  let timeout;
  const navItem = document.querySelector(".menu-item__explore");
  const dropdown = document.querySelector(".menu-item_explore__dropdown");

  if (navItem && dropdown) {
    navItem.addEventListener("mouseenter", () => {
      clearTimeout(timeout);
      dropdown.style.display = "block";
    });

    navItem.addEventListener("mouseleave", () => {
      timeout = setTimeout(() => {
        dropdown.style.display = "none";
      }, 200);
    });

    dropdown.addEventListener("mouseenter", () => {
      clearTimeout(timeout);
    });

    dropdown.addEventListener("mouseleave", () => {
      timeout = setTimeout(() => {
        dropdown.style.display = "none";
      }, 200);
    });
  }
});
