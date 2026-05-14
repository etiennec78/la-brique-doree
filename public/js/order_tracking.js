let statusInterval = null;

function checkOrderStatus() {
  const orderTrackers = document.querySelectorAll('.order-tracker');
  if (orderTrackers.length === 0) return;

  const orderIds = Array.from(orderTrackers).map(el => el.getAttribute('data-order-id'));

  fetch('/api_order_status?ids=' + orderIds.join(','))
    .then(response => response.json())
    .then(data => {
      const statuses = data["statuses"] || {};
      let needsRefresh = false;

      orderTrackers.forEach(tracker => {
        const id = tracker.getAttribute('data-order-id');
        const oldStatus = tracker.getAttribute('data-status');
        const newStatus = statuses[id];

        if (newStatus && newStatus != oldStatus) {
            needsRefresh = true;
        }
      });

      if (needsRefresh) {
        // Fetch the updated page content to reflect changes in delivery info and actions
        let url = new URL(window.location.href);
        url.searchParams.set('keep_ids', orderIds.join(','));
        fetch(url.toString())
          .then(r => r.text())
          .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, "text/html");

            orderTrackers.forEach(tracker => {
                const id = tracker.getAttribute('data-order-id');
                const newTracker = doc.getElementById('order-' + id);
                if (newTracker) {
                    tracker.innerHTML = newTracker.innerHTML;
                    tracker.setAttribute('data-status', newTracker.getAttribute('data-status'));
                }
            });
          })
          .catch(err => console.error("Failed to fetch updated page", err));
      }
    })
    .catch(err => console.error("Failed to fetch order status", err));
}

// Check immediately, then every 5 seconds
checkOrderStatus();
if (!statusInterval) {
    statusInterval = setInterval(checkOrderStatus, 5000);
}
