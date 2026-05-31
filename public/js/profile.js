document.addEventListener("DOMContentLoaded", function() {
    /*
        
     INPUT :
             
        None
      
     OUTPUT :

        None

      
     SUMMARY :
        
        Initializes custom form listeners after structure building cycles, intercepting adjustments to trigger asynchronous save commands while giving immediate opacity pulsing feedback to the user.

    */
    const form = document.getElementById("profile-form");

    if (form) {
        form.addEventListener("submit", function(event) {
            /*
                
             INPUT :
                     
                (Event) event : variable tracking the native submission execution loop parameters
              
             OUTPUT :

                None

            */
            event.preventDefault();

            const formData = new FormData(form);

            fetch("/profile", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    form.style.opacity = "0.5";
                    setTimeout(() => form.style.opacity = "1", 500);
                }

                if (result.error) {
                    toast(result.error);
                }
            })
            .catch(err => {
                toast("Une erreur inattendue est survenue.");
            });
        });
    }
});