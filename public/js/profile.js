document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("profile-form");

    if (form) {
        form.addEventListener("submit", function(event) {
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