document.addEventListener('DOMContentLoaded', () => {
    /*
        
     INPUT :
             
        None
      
     OUTPUT :

        None

      
     SUMMARY :
        
        Registers dynamic item filter checkboxes on content assembly finish, attaching tracking callbacks that reconcile displayed items with checked values seamlessly.

    */
    const filters = document.querySelectorAll('.filter');
    const products = document.querySelectorAll('article.description');

    function applyFilters() {
        /*
            
         INPUT :
                 
            None
          
         OUTPUT :

            None

          
         SUMMARY :
            
            Iterates through the entirety of standard product description cards, hiding components whose class identities do not comply with active search parameters.

        */
        products.forEach(product => {
            let hide = false;

            filters.forEach(filter => {
                if (filter.checked === false && product.classList.contains(filter.id)) {
                    hide = true;
                }
            });

            if (hide) {
                product.style.display = 'none';
            } else {
                product.style.display = 'block';
            }
        });
    }

    filters.forEach(checkbox => {
        checkbox.addEventListener('change', applyFilters);
    });
});