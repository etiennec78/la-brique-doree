// Show a notification in the upper-right corner
function toast(message = null, duration = 5000) {
	/*
	 	
	  INPUT :
	         
		(str|null) $message : The notification message text to display.
		(int) $duration : The time in milliseconds before the toast is removed.
	  
	  OUTPUT :

		(void) : No return value

	  
	  SUMMARY :
	 	
		Displays a toast notification with the given message, or shows an existing pre-rendered toast, then removes it after a duration.

	*/
  let toast_div;
  if (message == null) {
    // PHP already has set a message, so we just need to display it
    toast_div = document.getElementById('toast');

  } else {
    // Create the div containing the message
    const container = document.getElementById('toast-container');

    toast_div = document.createElement('div');
    toast_div.id = 'toast';
    toast_div.innerText = message;

    container.appendChild(toast_div);
  }

  setTimeout(() => {
	/*
	 	
	  INPUT :
	         
		None
	  
	  OUTPUT :

		(void) : No return value

	  
	  SUMMARY :
	 	
		Adds the visibility class to trigger the CSS show animation for the toast.

	*/
    toast_div.classList.add('show');
  }, 10);

  // Remove the class then the div at the end of the transition
  setTimeout(() => {
	/*
	 	
	  INPUT :
	         
		None
	  
	  OUTPUT :

		(void) : No return value

	  
	  SUMMARY :
	 	
		Removes the show class to fade out the toast and sets up the removal listener.

	*/
    toast_div.classList.remove('show');
    toast_div.addEventListener('transitionend', () => {
	/*
	 	
	  INPUT :
	         
		None
	  
	  OUTPUT :

		(void) : No return value

	  
	  SUMMARY :
	 	
		Removes the toast DOM element completely once the fade transition has finished.

	*/
      toast_div.remove();
    });
  }, duration);
}


// Search the page for a php div signaling the need for a toast
document.addEventListener("DOMContentLoaded", () => {
	/*
	 	
	  INPUT :
	         
		None
	  
	  OUTPUT :

		(void) : No return value

	  
	  SUMMARY :
	 	
		Checks for an existing pre-rendered toast container on page load and initializes it.

	*/
  const errorContainer = document.getElementById("toast");

  if (errorContainer) {
    toast();
  }
});
