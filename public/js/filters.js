document.addEventListener('DOMContentLoaded', () => {
    const filters = document.querySelectorAll('.filter');
    const products = document.querySelectorAll('article.description');

    function applyFilters() {
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