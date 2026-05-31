function formatTime(datetimeStr) {
    /*
        
     INPUT :
             
        (string) datetimeStr : variable representing the incoming target timestamp string
      
     OUTPUT :

        (string) : variable representing the clean formatted hour and minute layout text string

      
     SUMMARY :
        
        Evaluates a structured timestamp object, extracts hour and minute values into an aligned, zero-padded configuration pattern, or provides a default immediate text fallback statement.

    */
    if (!datetimeStr) return 'Au plus vite';
    const date = new Date(datetimeStr);
    const h = date.getHours().toString().padStart(2, '0');
    const m = date.getMinutes().toString().padStart(2, '0');
    return `${h}h${m}`;
}

function renderItems(items) {
    /*
        
     INPUT :
             
        (object) items : variable representing the composite dictionary holding menu arrays and individual food array objects
      
     OUTPUT :

        (string) : variable representing the generated HTML itemized list structure string

      
     SUMMARY :
        
        Loops across grouped selections and standalone items inside a specific request payload configuration to append structural labels into an HTML list block for dashboard layout inclusion.

    */
    const menusArray = items.menus || [];

    const menusEnHTML = [];
    for (const m of menusArray) {
        menusEnHTML.push(`<li><strong>${m.name}</strong> x${m.quantity}</li>`);
    }

    const menus = menusEnHTML.join('');

    const foodsArray = items.foods || [];

    const foodsEnHTML = [];
    for (const f of foodsArray) {
        foodsEnHTML.push(`<li>${f.name} x${f.quantity}</li>`);
    }

    const foods = foodsEnHTML.join('');

    return `<ul>${menus}${foods}</ul>`;
}

function checkCookStatus() {
    /*
        
     INPUT :
             
        None
      
     OUTPUT :

        None

      
     SUMMARY :
        
        Polls the unified preparation pipeline database status through network fetch operations, parsing pending line queues along with takeout schedules to update management panels asynchronously.

    */
    fetch('/api_cook')
    .then(response => response.json())
    .then(data => {
        const pendingContainer = document.querySelector('#pending-content table');
        const deliveryContainer = document.querySelector('#delivery-content table');

        if (pendingContainer && data.pending) {
            let pendingHTML = `
                <tr>
                    <td><h3>COMMANDE</h3></td>
                    <td><h3>ITEMS</h3></td>
                    <td><h3 id="state">ETAT</h3></td>
                </tr>
            `;
            if (data.pending.length === 0) {
                pendingHTML += `<tr><td colspan="3" style="text-align:center;">Aucune commande en attente.</td></tr>`;
            } else {
                data.pending.forEach(order => {
                    let name = `${order.first_name || ''} ${order.last_name || ''}`;
                    let deliveryInfo = order.is_takeaway ? `<br><small>Retrait : ${formatTime(order.takeaway_time)}</small>` : `<br><small>Livraison</small>`;

                    pendingHTML += `
                    <tr>
                        <td>
                            <span>Commande #${order.id} (${name.trim()})</span>
                            ${deliveryInfo}
                        </td>
                        <td class="order-items">
                            ${renderItems(order.items)}
                        </td>
                        <td>
                            <form action="/assign_order" method="POST">
                                <input type="hidden" name="order_id" value="${order.id}">
                                <button id="manage" type="submit" class="basic-btn action-btn">Prêt !</button>
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
                    let name = `${order.first_name || ''} ${order.last_name || ''}`;
                    
                    let delivererText = '';
                    if (!order.is_takeaway && order.delivery_first_name) {
                        delivererText = ` (Livreur : ${order.delivery_first_name} ${order.delivery_last_name})`;
                    }

                    let deliveryInfo = order.is_takeaway ? 
                        `<br><small>Retrait : ${formatTime(order.takeaway_time)}</small>` : 
                        `<br><small>Livraison${delivererText}</small>`;

                    let actionHTML = order.is_takeaway ? `
                        <form action="/finish_takeaway" method="POST">
                            <input type="hidden" name="order_id" value="${order.id}">
                            <button type="submit" class="basic-btn action-btn">Remis</button>
                        </form>
                    ` : `
                        <button class="waiting-delivery-btn" disabled>Attente retour livreur</button>
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


