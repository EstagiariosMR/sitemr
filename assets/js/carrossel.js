document.addEventListener('DOMContentLoaded', () => {
    const slidesContainer = document.querySelector('.carrossel-slides')
    const slides = document.querySelectorAll('.carrossel-slides .slide')
    const btnAnterior = document.querySelector('.carrossel-anterior')
    const btnProximo = document.querySelector('.carrossel-proximo')
    
    let indiceAtual = 0
    const totalSlidesReais = slides.length - 1
    const intervaloTroca = 5000
    let timer

    function mostrarSlide(indice){
        slidesContainer.style.transition = 'transform 0.5s ease-in-out'
        slidesContainer.style.transform = `translateX(-${indice * 100}%)`
    }

    function proximoSlide(){
        indiceAtual++
        mostrarSlide(indiceAtual)

        if(indiceAtual === slides.length - 1){
            setTimeout(() => {
                slidesContainer.style.transition = 'none'
                slidesContainer.style.transform = `translateX(0%)`
                indiceAtual = 0
            }, 500)
        }
    }

    function slideAnterior(){
        if(indiceAtual === 0){
            indiceAtual = totalSlidesReais
            slidesContainer.style.transition = 'none'
            slidesContainer.style.transform = `translateX(-${indiceAtual * 100}%)`

            void slidesContainer.offsetWidth

            slidesContainer.style.transition = 'transform 0.5s ease-in-out'
            indiceAtual--
        }
        else{
            indiceAtual--
            
        }

        mostrarSlide(indiceAtual)
    }

    function reiniciarTimer(){
        clearInterval(timer)
        timer = setInterval(proximoSlide, intervaloTroca)
    }

    btnProximo.addEventListener('click', () => {
        proximoSlide()
        reiniciarTimer()
    })

    btnAnterior.addEventListener('click', () => {
        slideAnterior()
        reiniciarTimer()
    })

    slidesContainer.style.width = `${slides.length * 100}%`
    mostrarSlide(indiceAtual)
    timer = setInterval(proximoSlide, intervaloTroca)
})