function formatTime(datetimeStr) {
    if (!datetimeStr) return 'Au plus vite';
    const date = new Date(datetimeStr);
    const h = date.getHours().toString().padStart(2, '0');
    const m = date.getMinutes().toString().padStart(2, '0');
    return `${h}h${m}`;
}

function checkCookStatus() {
    fetch('/api_cook')
    .then(response => response.json())
    .then(data => {
        const pendingContainer = document.querySelector('#pending-content table');
        const deliveryContainer = document.querySelector('#delivery-content table');

        if (pendingContainer && data.pending) {
            let pendingHTML = `
                <tr>
                    <td><h3>COMMANDE</h3></td>
                    <td><h3 id="state">ETAT</h3></td>
                </tr>
            `;
            if (data.pending.length === 0) {
                pendingHTML += `<tr><td colspan="2" style="text-align:center;">Aucune commande en attente.</td></tr>`;
            } else {
                data.pending.forEach(order => {
                    const name = `${order.first_name || ''} ${(order.last_name || '').charAt(0)}.`;
                    let deliveryInfo = order.is_takeaway ? `<br><small>Retrait : ${formatTime(order.takeaway_time)}</small>` : `<br><small>Livraison</small>`;
                    
                    pendingHTML += `
                    <tr>
                        <td>
                            <span>Commande #${order.id} (${name.trim()})</span>
                            ${deliveryInfo}
                        </td>
                        <td>
                            <form action="/assign_order" method="POST">
                                <input type="hidden" name="order_id" value="${order.id}">
                                <button id="manage" type="submit" class="basic-btn">Prêt !</button>
                            </form>
                        </td>
                    </tr>
                    `;
                });
            }
            pendingContainer.innerHTML = pendingHTML;
        }

        if (deliveryContainer && data.delivery) {
            let deliveryHTML = `
                <tr>
                    <td><h3>COMMANDE</h3></td>
                    <td><h3 id="state">ETAT</h3></td>
                </tr>
            `;
            if (data.delivery.length === 0) {
                deliveryHTML += `<tr><td colspan="2" style="text-align:center;">Aucune livraison en cours.</td></tr>`;
            } else {
                data.delivery.forEach(order => {
                    const name = `${order.first_name || ''} ${(order.last_name || '').charAt(0)}.`;
                    let deliveryInfo = order.is_takeaway ? `<br><small>Retrait : ${formatTime(order.takeaway_time)}</small>` : `<br><small>Livraison</small>`;
                    
                    let actionHTML = order.is_takeaway ? `
                        <form action="/finish_takeaway" method="POST">
                            <input type="hidden" name="order_id" value="${order.id}">
                            <button type="submit" class="basic-btn">Remis</button>
                        </form>
                    ` : `
                        <label class="selection">
                            <span>En attente du retour d'un livreur</span>
                            <input type="checkbox" checked disabled/> 
                        </label>
                    `;

                    deliveryHTML += `
                    <tr>
                        <td>
                            <span>Commande #${order.id} (${name.trim()})</span>
                            ${deliveryInfo}
                        </td>
                        <td>
                            ${actionHTML}
                        </td>
                    </tr>
                    `;
                });
            }
            deliveryContainer.innerHTML = deliveryHTML;
        }
    })
    .catch(err => console.error("Failed to fetch updated orders", err));
}

// Check every 5 seconds
setInterval(checkCookStatus, 5000);


