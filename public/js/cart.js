// Intercept the form sent when adding an item to cart, and do it with js to avoid reloading the page
document.addEventListener("DOMContentLoaded", () => {
  /*

    INPUT :

    None

    OUTPUT :

    (void) : No return value


    SUMMARY :

    Initializes cart update interception behavior when the DOM is loaded.

  */
    const updateCartForms = document.querySelectorAll('form[action="/update_cart"]');
    const cartItemsBubble = document.getElementById("cart_items");

    updateCartForms.forEach(form => {
  /*

    INPUT :

    (Element) $form : The individual cart update form element.

    OUTPUT :

    (void) : No return value


    SUMMARY :

    Attaches a submit event listener to each matching cart form element.

  */
        form.addEventListener("submit", (e) => {
  /*

    INPUT :

    (Event) $e : The submit event object.

    OUTPUT :

    (void) : No return value


    SUMMARY :

    Prevents default submission, intercepts the request via Fetch API to asynchronously update the cart.

  */
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
  /*

    INPUT :

    (Response) $response : The Fetch response object.

    OUTPUT :

    (Promise|null) $json : Returns the parsed JSON data promise or null if redirected.


    SUMMARY :

    Handles HTTP redirect responses or parses the incoming response body as JSON data.

  */
                if (response.redirected) {
                    window.location.href = response.url;
                    return null;
                }
                return response.json();
            })
            .then(data => {
  /*

    INPUT :

    (Object|null) $data : The parsed JSON data response from the server.

    OUTPUT :

    (void) : No return value


    SUMMARY :

    Updates the cart count badge with animation and triggers a notification toast if an error occurs.

  */
                if (data && data.cart_count !== undefined) {
                    if (cartItemsBubble) {
                        cartItemsBubble.textContent = data.cart_count;
                        
                        // Animation on the cart icon to reflect amount update
                        cartItemsBubble.animate([
                            { transform: 'scale(1)' },
                            { transform: 'scale(1.5)' },
                            { transform: 'scale(1)' }
                        ], { duration: 300 });
                    }
                }

                if (data && data.error && typeof toast === "function") {
                    toast(data.error);
                }
            })
            .catch(error => {
  /*

    INPUT :

    (Error) $error : The thrown error object.

    OUTPUT :

    (void) : No return value


    SUMMARY :

    Logs any runtime or fetch-related errors during the cart update operation to the console.

  */
                console.error("Error while updating cart :", error);
            });
        });
    });
});
