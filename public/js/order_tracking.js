let oldStatus = null;

function checkOrderStatus() {
  fetch('/api_order_status')
    .then(response => response.json())
    .then(data => {
      const status = data["status"];
      console.log(status, oldStatus);
      if (status === null || status == oldStatus) return;

      oldStatus = status;

      // Update the stepper based on the new status
      let elements = document.getElementsByClassName("step");
      for (let i = 0; i < Math.min(status, elements.length); i++) {
        elements[i].classList.add("active");
      }

      // Fetch the updated page content to reflect changes in delivery info and actions
      fetch(window.location.href)
        .then(r => r.text())
        .then(html => {
          const parser = new DOMParser();
          const doc = parser.parseFromString(html, "text/html");

          const newDeliveryInfo = doc.querySelector('.delivery-info');
          const currentDeliveryInfo = document.querySelector('.delivery-info');

          if (newDeliveryInfo) {
            if (currentDeliveryInfo) {
              currentDeliveryInfo.innerHTML = newDeliveryInfo.innerHTML;
            } else {
              const trackingActions = document.querySelector('.tracking-actions');
              if (trackingActions) {
                trackingActions.insertAdjacentHTML('beforebegin', newDeliveryInfo.outerHTML);
              }
            }
          } else if (currentDeliveryInfo) {
            currentDeliveryInfo.remove();
          }

          const newActions = doc.querySelector('.tracking-actions');
          const currentActions = document.querySelector('.tracking-actions');
          if (newActions && currentActions) {
            currentActions.innerHTML = newActions.innerHTML;
          }
        })
        .catch(err => console.error("Failed to fetch updated page", err));
    })
    .catch(err => console.error("Failed to fetch order status", err));
}

// Check immediately, then every 5 seconds
checkOrderStatus();
setInterval(checkOrderStatus, 5000);
