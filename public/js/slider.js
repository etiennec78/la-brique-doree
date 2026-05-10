const slideshow = document.getElementById('slideshow');
const slides = document.querySelectorAll('.slide');
let index = 0;

function slideSuivant() {
    index = (index + 1) % slides.length;
    slideshow.style.transform = `translateX(-${index * 25}%)`;
}

setInterval(slideSuivant, 5000); //répète la fonction toutes les 5 secondes

document.getElementById('next').onclick = slideSuivant;

document.getElementById('prev').onclick = () => {
    index = (index - 1 + slides.length) % slides.length;
    slideshow.style.transform = `translateX(-${index * 25}%)`;
};