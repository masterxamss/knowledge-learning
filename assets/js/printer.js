document.addEventListener("turbo:load", () => {
  window.printSection = function (id) {
    const content = document.getElementById(id).innerHTML;
    const original = document.body.innerHTML;

    document.body.innerHTML = content;
    window.print();
    document.body.innerHTML = original;
  };
});
