const slidesContainer = document.querySelector('.slides');
let slides = document.querySelectorAll('.slide');
const totalSlides = slides.length;

// Clona o primeiro e o último slide
const firstClone = slides[0].cloneNode(true);
const lastClone = slides[slides.length - 1].cloneNode(true);

firstClone.classList.add('clone');
lastClone.classList.add('clone');

// Adiciona os clones
slidesContainer.appendChild(firstClone);
slidesContainer.insertBefore(lastClone, slides[0]);

slides = document.querySelectorAll('.slide'); // Atualiza a lista de slides

let currentIndex = 1; // Começa no "verdadeiro" primeiro slide

function setTransition(enable) {
    slidesContainer.style.transition = enable ? "transform 1s cubic-bezier(0.77, 0, 0.175, 1)" : "none";
}

function updateSlidePosition() {
    slidesContainer.style.transform = `translateX(-${currentIndex * 100}%)`;
}

function nextSlide() {
    if (currentIndex >= slides.length - 1) return;
    setTransition(true);
    currentIndex++;
    updateSlidePosition();

    slidesContainer.addEventListener('transitionend', handleNextClone, { once: true });
}

function handleNextClone() {
    if (slides[currentIndex].classList.contains('clone')) {
        setTransition(false);
        currentIndex = 1;
        updateSlidePosition();
    }
}

function prevSlide() {
    if (currentIndex <= 0) return;
    setTransition(true);
    currentIndex--;
    updateSlidePosition();

    slidesContainer.addEventListener('transitionend', handlePrevClone, { once: true });
}

function handlePrevClone() {
    if (slides[currentIndex].classList.contains('clone')) {
        setTransition(false);
        currentIndex = slides.length - 2;
        updateSlidePosition();
    }
}

function autoSlide() {
    nextSlide();
    setTimeout(autoSlide, 7000);
}

document.addEventListener("DOMContentLoaded", () => {
    // Ajusta slides para layout horizontal
    slides.forEach(slide => {
        slide.style.width = '100%';
        slide.style.flexShrink = '0';
        slide.style.position = 'relative';
    });

    setTransition(false);
    updateSlidePosition();
    setTimeout(autoSlide, 5000);
});
