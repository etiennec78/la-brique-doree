const slideshow = document.getElementById('slideshow');
const slides = document.querySelectorAll('.slide');
let index = 0;

function NextSlide() {
  /*

    INPUT :

    None

    OUTPUT :

    (void) : No return value


    SUMMARY :

    Increments the slide index and updates the slideshow transform property to display the next slide.

  */
    index = (index + 1) % slides.length;
    slideshow.style.transform = `translateX(-${index * 25}%)`;
}

setInterval(NextSlide, 5000); //Repeats the function every 5 seconds

document.getElementById('next').onclick = NextSlide;

document.getElementById('prev').onclick = () => {
  /*

    INPUT :

    None

    OUTPUT :

    (void) : No return value


    SUMMARY :

    Decrements the slide index and updates the slideshow transform property to display the previous slide.

  */
    index = (index - 1 + slides.length) % slides.length;
    slideshow.style.transform = `translateX(-${index * 25}%)`;
};
