let btn = document.getElementById("theme-toggle");
let body = document.body;

function updateIcon() {
    /*
        
     INPUT :
             
        None
      
     OUTPUT :

        None

      
     SUMMARY :
        
        Examines structural document class parameters, alternating the toggler selector button text label representation value to present either moon or sun characters accurately.

    */
  if (body.classList.contains("light-theme")) {
    btn.innerText = "🌙";
  } else {
    btn.innerText = "☀️";
  }
}

window.addEventListener("DOMContentLoaded", () => {
    /*
        
     INPUT :
             
        None
      
     OUTPUT :

        None

      
     SUMMARY :
        
        Interrogates standard document cookie tracking strings to discover preset light styling choices, applying structural rule setups prior to initiating icon updates.

    */
  if (document.cookie.includes("theme=light")) {
    body.classList.add("light-theme");
  }
  updateIcon();
});

btn.addEventListener("click", () => {
    /*
        
     INPUT :
             
        None
      
     OUTPUT :

        None

      
     SUMMARY :
        
        Handles manual execution signals on layout switcher triggers, modifying cookie configuration strings while performing clean class adjustments.

    */
  body.classList.toggle("light-theme");
  updateIcon();

  let theme;
  if (body.classList.contains("light-theme")) {
    theme = "light";
  } else {
    theme = "black";
  }

  document.cookie = "theme=" + theme + "; max-age=2592000; path=/";
});
