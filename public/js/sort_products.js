document.addEventListener('DOMContentLoaded', function () {

    const select = document.getElementById('sort-price');
    const bentos = document.querySelectorAll('.bento');

    bentos.forEach(function (bento) {
        Array.from(bento.children).forEach((item, index) => {
            item.setAttribute('data-initial-order', index);
        });
    });

    select.addEventListener('change', function () {

        bentos.forEach(function (bento) {

            const items = Array.from(bento.children);

            items.sort(function (a, b) {

                const priceA = parseFloat(a.getAttribute('data-raw-price'));
                const priceB = parseFloat(b.getAttribute('data-raw-price'));

                if (select.value === 'asc') {
                    return priceA - priceB;
                } else if (select.value === 'desc') {
                    return priceB - priceA;
                } else if (select.value === 'default') {
                    const orderA = parseInt(a.getAttribute('data-initial-order'));
                    const orderB = parseInt(b.getAttribute('data-initial-order'));
                    return orderA - orderB;
                }

                return 0;
            });

            items.forEach(function (item) {
                bento.appendChild(item);
            });

        });

    });

});