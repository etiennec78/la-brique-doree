function count_char() {
  let zone = document.getElementById("review-comm");
  let chiffre = document.getElementById("nb-caracteres");
  let bouton = document.getElementById("submit-avis");
  let longueur = zone.value.length;

  chiffre.innerText = longueur;

  if (longueur > 255) {

    chiffre.style.color = "red";
    bouton.disabled = true;
    bouton.style.opacity = "0.5";
    bouton.style.cursor = "not-allowed";
  } else {
    chiffre.style.color = "(--solid-gold)";
    bouton.disabled = false;
    bouton.style.opacity = "1";
    bouton.style.cursor = "pointer";
  }
}
