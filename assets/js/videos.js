document.addEventListener("turbo:load", function () {
  // Carregar a API do YouTube de forma assíncrona
  var tag = document.createElement("script");
  tag.src = "https://www.youtube.com/iframe_api";
  var firstScriptTag = document.getElementsByTagName("script")[0];
  firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

  function onYouTubeIframeAPIReady() {
    var playerElement = document.getElementById("player");
    if (!playerElement) return;

    var videoId = playerElement.getAttribute("data-video-id");
    if (!videoId) return;

    new YT.Player("player", {
      height: "315",
      width: "560",
      videoId: videoId,
      playerVars: {
        autoplay: 1,
        controls: 1,
      },
    });
  }

  window.onYouTubeIframeAPIReady = onYouTubeIframeAPIReady;
});
