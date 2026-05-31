// Intercept the forms and update the cart-bar UI asynchronously
document.addEventListener("DOMContentLoaded", () => {
    /*
        
     INPUT :
             
        None
      
     OUTPUT :

        None

      
     SUMMARY :
        
        Establishes master document listeners during application setup to filter cart parameter changes, intercepting transmission signals to enable asynchronous layout modifications smoothly.

    */

  function updateCartBarFromResponse(responsePromise, openCouponDetails = false) {
    /*
        
     INPUT :
             
        (Promise) responsePromise : variable representing the unresolved fetch action stream reference
        (boolean) openCouponDetails : variable specifying whether discount parameter submenus open automatically
      
     OUTPUT :

        None

      
     SUMMARY :
        
        Extracts HTML string data segments upon network pipeline resolution to completely reassemble checkout description boxes while initializing application toast notices.

    */
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
    /*
        
     INPUT :
             
        (Event) e : variable tracking active transaction configuration contexts
      
     OUTPUT :

        None

      
     SUMMARY :
        
        Blocks classic page transitions for logistics configurations and promotional forms, capturing raw elements to stream background updates securely.

    */
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
