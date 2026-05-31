// Intercept the forms and update the cart-bar UI asynchronously
document.addEventListener("DOMContentLoaded", () => {

  function updateCartBarFromResponse(responsePromise, openCouponDetails = false) {
    responsePromise
      .then(res => res.text())
      .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, "text/html");
        const newCartBar = doc.getElementById("cart-bar");
        const oldCartBar = document.getElementById("cart-bar");
        if (newCartBar && oldCartBar) {
          oldCartBar.innerHTML = newCartBar.innerHTML;
          
          if (openCouponDetails) {
            const newDetails = document.querySelector(".coupon-details");
            if (newDetails) newDetails.open = true;
          }
        }
        const backgroundToast = doc.getElementById("toast");
        if (backgroundToast && typeof toast === "function") {
          toast(backgroundToast.innerText);
        }
      })
      .catch(error => console.error("Error:", error));
  }

  // Use event delegation because elements inside #cart-bar will be replaced
  document.body.addEventListener("submit", (e) => {
    if (e.target.id === "delivery-type") {
      e.preventDefault();
      const formData = new FormData(e.target);
      formData.append(e.submitter.name, e.submitter.value);
      updateCartBarFromResponse(fetch(e.target.action, {
        method: 'POST',
        body: formData
      }));
    }

    if (e.target.classList.contains("coupon-form")) {
      e.preventDefault();
      const formData = new FormData(e.target);
      updateCartBarFromResponse(fetch(e.target.action, {
        method: 'POST',
        body: formData
      }), true);
    }
  });

  document.body.addEventListener("change", (e) => {
    if (e.target.id === "takeaway_time" || e.target.name === "coupon") {
      const form = e.target.closest("form");
      const formData = new FormData(form);
      const isCouponUpdate = e.target.name === "coupon";
      updateCartBarFromResponse(fetch(form.action, {
        method: 'POST',
        body: formData
      }), isCouponUpdate);
    }
  });
});
