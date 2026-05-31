document.addEventListener('DOMContentLoaded', function () {
	/*
	 	
	  INPUT :
	         
		None
	  
	  OUTPUT :

		(void) : No return value

	  
	  SUMMARY :
	 	
		Initializes product sorting functionality once the DOM content is fully loaded.

	*/

    const select = document.getElementById('sort-price');
    const bentos = document.querySelectorAll('.bento');

    bentos.forEach(function (bento) {
	/*
	 	
	  INPUT :
	         
		(Element) $bento : The current bento grid element container.
	  
	  OUTPUT :

		(void) : No return value

	  
	  SUMMARY :
	 	
		Iterates through each bento grid container to store the initial order of its child items.

	*/
        Array.from(bento.children).forEach((item, index) => {
	/*
	 	
	  INPUT :
	         
		(Element) $item : The current product item element.
		(int) $index : The current index of the item.
	  
	  OUTPUT :

		(void) : No return value

	  
	  SUMMARY :
	 	
		Sets a custom data attribute on each child item to record its initial order.

	*/
            item.setAttribute('data-initial-order', index);
        });
    });

    select.addEventListener('change', function () {
	/*
	 	
	  INPUT :
	         
		None
	  
	  OUTPUT :

		(void) : No return value

	  
	  SUMMARY :
	 	
		Triggers the sorting process for all bento layout items when the selection option changes.

	*/

        bentos.forEach(function (bento) {
	/*
	 	
	  INPUT :
	         
		(Element) $bento : The current bento container being sorted.
	  
	  OUTPUT :

		(void) : No return value

	  
	  SUMMARY :
	 	
		Sorts and re-appends the items within each individual bento container based on the selected sorting order.

	*/

            const items = Array.from(bento.children);

            items.sort(function (a, b) {
	/*
	 	
	  INPUT :
	         
		(Element) $a : The first element for comparison.
		(Element) $b : The second element for comparison.
	  
	  OUTPUT :

		(int) $result : A negative, zero, or positive value indicating sorting order.

	  
	  SUMMARY :
	 	
		Compares two elements based on their price attribute or initial order depending on the active sort option.

	*/

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
	/*
	 	
	  INPUT :
	         
		(Element) $item : The sorted product item element.
	  
	  OUTPUT :

		(void) : No return value

	  
	  SUMMARY :
	 	
		Re-appends the sorted item back to the bento parent element to update the DOM order.

	*/
                bento.appendChild(item);
            });

        });

    });

});