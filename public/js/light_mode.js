let btn = document.getElementById("theme-toggle");
let body = document.body;

function majIcone() {
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
  majIcone();
});

btn.addEventListener("click", () => {
  body.classList.toggle("light-theme");
  majIcone();

  let theme;
  if (body.classList.contains("light-theme")) {
    theme = "light";
  } else {
    theme = "black";
  }

  document.cookie = "theme=" + theme + "; max-age=2592000; path=/";
});
