// Intercept the form sent when selecting delivery or takeaway, and show/hide the hour of takeaway
document.addEventListener("DOMContentLoaded", () => {
  const deliveryTimeForm = document.getElementById("delivery-type");
  deliveryTimeForm.addEventListener("submit", (e) => {
    e.preventDefault();

    // Get elements
    const isTakeaway = e.submitter.value === "1";
    const checkoutBtn = document.getElementById("checkout");
    const deliveryTimeForm = document.getElementById("delivery-time");

    // Update the UI
    document.querySelectorAll('#delivery-type button').forEach(button => {
      button.classList.toggle("selected");
    });
    deliveryTimeForm.classList.toggle("hidden", !isTakeaway);
    if (isTakeaway) {
      checkoutBtn.textContent = "Veuillez confirmer l'heure";
      checkoutBtn.disabled = true;
    } else {
      checkoutBtn.textContent = "Payer";
      checkoutBtn.disabled = false;
    }

    // Send form data that was intercepted
    const formData = new FormData(e.target);
    formData.append(e.submitter.name, e.submitter.value);
    fetch(e.target.action, {
      method: 'POST',
      body: formData
    })
    .catch(error => console.error("Error :", error));
  });
});
