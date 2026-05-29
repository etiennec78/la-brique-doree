document.addEventListener('DOMContentLoaded', function () {

    const select = document.getElementById('sort-price');
    const bentos = document.querySelectorAll('.bento');

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
                }

                return 0;
            });

            items.forEach(function (item) {
                bento.appendChild(item);
            });

        });

    });

});