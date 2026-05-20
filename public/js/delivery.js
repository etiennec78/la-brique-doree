function getAddress(delivery) {
    if (!delivery) return '';
    const nbSuf = delivery.street_nb_suf ? delivery.street_nb_suf : '';
    const town = delivery.town ? delivery.town : '';
    return `${delivery.street}, ${delivery.zip_code} ${town}`;
}

function checkDeliveryStatus() {
    fetch('/api_delivery')
    .then(response => response.json())
    .then(data => {
        const deliveriesContainer = document.querySelector('main');
        if (!deliveriesContainer || !data.deliveries) return;

        // Find where the deliveries start (after the map)
        const mapSection = document.querySelector('.map-box');
        const headingList = document.querySelectorAll('.section-title');
        const deliveriesHeading = headingList.length > 1 ? headingList[1] : null;

        if (!deliveriesHeading) return;

        // Remove old delivery cards entirely
        const oldCards = document.querySelectorAll('.delivery-card');
        oldCards.forEach(card => card.remove());

        // Insert new cards after the heading
        let insertAfterNode = deliveriesHeading;

        if (data.deliveries.length === 0) {
            const emptyCard = document.createElement('div');
            emptyCard.className = 'delivery-card';
            emptyCard.innerHTML = `
                <div class="card-header">
                    <span class="client-name">En attente d'un nouveau client...</span>
                </div>
            `;
            insertAfterNode.parentNode.insertBefore(emptyCard, insertAfterNode.nextSibling);
        } else {
            // Need to insert in reverse order to keep them in correct sequence after the header
            const reversedDeliveries = [...data.deliveries].reverse();
            reversedDeliveries.forEach(delivery => {
                const name = `${delivery.first_name || ''} ${delivery.last_name || ''}`;
                const address = getAddress(delivery);
                const googleMapsUrl = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(address)}`;
                
                const intercomHTML = delivery.intercom_code ? `<p class="access">🔑 Code ${delivery.intercom_code}</p>` : '';

                const card = document.createElement('div');
                card.className = 'delivery-card';
                card.innerHTML = `
                    <div class="card-header">
                        <span class="order-id">#${delivery.id}</span>
                        <span class="client-name">${name.trim()}</span>
                        ${window.isAdmin ? `<span class="delivery-staff"> / Livreur : ${delivery.driver_first_name || 'Non assigné'}</span>` : ''}
                    </div>
                    <button onclick="location.href='${googleMapsUrl}'" class="basic-btn action-btn">Ouvrir dans Google Maps</button>
                    <div class="card-body">
                        <p class="address">📍 ${address}</p>
                        ${intercomHTML}
                    </div>
                    <div class="delivery-actions">
                        <form action="/confirm_delivery" method="POST">
                            <input type="hidden" name="order_id" value="${delivery.id}">
                            <button type="submit" class="basic-btn action-btn btn-confirm">Confirmer</button>
                        </form>
                        <form action="/cancel_delivery" method="POST">
                            <input type="hidden" name="order_id" value="${delivery.id}">
                            <button type="submit" class="basic-btn action-btn btn-cancel">Annuler</button>
                        </form>
                    </div>
                `;
                insertAfterNode.parentNode.insertBefore(card, insertAfterNode.nextSibling);
            });
        }
    })
    .catch(err => console.error("Failed to fetch updated deliveries", err));
}

// Check every 5 seconds
setInterval(checkDeliveryStatus, 5000);
