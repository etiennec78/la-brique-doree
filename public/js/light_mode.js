let btn = document.getElementById("theme-toggle");
let body = document.body;

function updateIcon() {
  if (body.classList.contains("light-theme")) {
    btn.innerText = "🌙";
  } else {
    btn.innerText = "☀️";
  }
}

window.addEventListener("DOMContentLoaded", () => {
  if (document.cookie.includes("theme=light")) {
    body.classList.add("light-theme");
  }
  updateIcon();
});

btn.addEventListener("click", () => {
  body.classList.toggle("light-theme");
  updateIcon();

  let theme;
  if (body.classList.contains("light-theme")) {
    theme = "light";
  } else {
    theme = "black";
  }

  document.cookie = "theme=" + theme + "; max-age=2592000; path=/";
});
