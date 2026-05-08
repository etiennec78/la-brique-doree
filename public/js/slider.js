const slideshow = document.getElementById('slideshow');
const slides = document.querySelectorAll('.slide');
let index = 0;

document.getElementById('next').onclick = () => {
    index = (index + 1) % slides.length;
    slideshow.style.transform = `translateX(-${index * 25}%)`;
};

document.getElementById('prev').onclick = () => {
    index = (index - 1 + slides.length) % slides.length;
    slideshow.style.transform = `translateX(-${index * 25}%)`;
};