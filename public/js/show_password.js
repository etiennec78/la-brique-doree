function showPwd() {
  let input = document.getElementById("password");
  let oeil = document.getElementById("toggleEye");

  if (input.type === "password") {

    input.type = "text";
    oeil.innerText = "🔒";
  } else {
    input.type = "password";
    oeil.innerText = "👁️";
  }
}
