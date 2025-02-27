document.addEventListener("turbo:load", function () {
  const inputs = document.querySelectorAll(".input-image"); // Seleciona todos os inputs com a classe 'input-image'

  inputs.forEach(function (input) {
    input.addEventListener("change", function (event) {
      const file = event.target.files[0];
      const previewId = input.getAttribute("data-preview"); // Obtém o ID da pré-visualização associado
      const preview = document.getElementById(previewId); // Seleciona a pré-visualização correta

      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          if (preview) {
            preview.src = e.target.result; // Atualiza o `src` da imagem
            //preview.style.display = "block"; // Exibe a imagem
            //preview.style.width = "100%"; // Ajusta a largura
          }
        };
        reader.readAsDataURL(file); // Lê o arquivo como URL
      } else if (preview) {
        preview.src = ""; // Limpa o `src` se não houver arquivo
        preview.style.display = "none"; // Esconde a imagem
      }
    });
  });
});
