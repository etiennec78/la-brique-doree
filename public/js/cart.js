// Intercept the form sent when adding an item to cart, and do it with js to avoid reloading the page
document.addEventListener("DOMContentLoaded", () => {
    const updateCartForms = document.querySelectorAll('form[action="/update_cart"]');
    const cartItemsBubble = document.getElementById("cart_items");

    updateCartForms.forEach(form => {
        form.addEventListener("submit", (e) => {
            e.preventDefault();

            const formData = new FormData(form);

            fetch("/update_cart", {
                method: "POST",
                body: formData,
                headers: {
                    "Accept": "application/json"
                }
            })
            .then(response => {
                if (response.redirected) {
                    window.location.href = response.url;
                    return null;
                }
                return response.json();
            })
            .then(data => {
                if (data && data.cart_count !== undefined) {
                    if (cartItemsBubble) {
                        cartItemsBubble.textContent = data.cart_count;
                        
                        // Animation on the cart icon to reflect amount update
                        cartItemsBubble.animate([
                            { transform: 'scale(1)' },
                            { transform: 'scale(2)' },
                            { transform: 'scale(1)' }
                        ], { duration: 300 });
                    }
                }
            })
            .catch(error => {
                console.error("Error while updating cart :", error);
            });
        });
    });
});
