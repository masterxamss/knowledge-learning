document.addEventListener("turbo:load", () => {
  let timeout;
  const navItem = document.querySelector(".menu-item_profile");
  const dropdown = document.querySelector(".menu-item_profile__dropdown");
  const dropdownExplore = document.querySelector(
    ".menu-item_explore__dropdown",
  );

  if (navItem && dropdown) {
    navItem.addEventListener("mouseenter", () => {
      clearTimeout(timeout);
      dropdown.style.display = "block";
      dropdownExplore.style.display = "none";
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
