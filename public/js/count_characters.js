function count_char() {
    /*
        
     INPUT :
             
        None
      
     OUTPUT :

        None

      
     SUMMARY :
        
        Tracks text lengths within the evaluation comments block real-time, modifying count tracker elements styling attributes and locking submission buttons dynamically when bounds cross a 255-character threshold constraint.

    */
  let zone = document.getElementById("review-comm");
  let number = document.getElementById("nb-characters");
  let button = document.getElementById("submit-review");
  let length = zone.value.length;

  number.innerText = length;

  if (length > 255) {

    number.style.color = "red";
    button.disabled = true;
    button.style.opacity = "0.5";
    button.style.cursor = "not-allowed";
  } else {
    number.style.color = "(--solid-gold)";
    button.disabled = false;
    button.style.opacity = "1";
    button.style.cursor = "pointer";
  }
}
