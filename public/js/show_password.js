function showPwd() {
    /*
        
     INPUT :
             
        None
      
     OUTPUT :

        None

      
     SUMMARY :
        
        Alternates target input attributes between generic unmasked characters and protected password masking variables while switching eye and padlock symbols accordingly.

    */
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
