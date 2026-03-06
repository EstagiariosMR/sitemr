document.addEventListener('DOMContentLoaded', () => {
    const trilho = document.querySelector('.carrossel-trilho')
    const containerDots = document.querySelector('.controles-dots')
    let slidesIniciais = document.querySelectorAll('.carrossel-item')

    if(slidesIniciais.length <= 1){
        document.querySelector('.controles-setas').style.display = 'none'
        containerDots.style.display = 'none'

        if(slidesIniciais.length === 1){
            trilho.style.transform = 'translateX(0)'
        }

        throw new Error("Carrossel com itens insuficientes para animação")
    }

    let index = 1
    const tempoIntervalo = 5000
    let autoplay

    slidesIniciais.forEach((_, i) => {
        const dot = document.createElement('button')
        dot.classList.add('dot')
        if(i === 0) dot.classList.add('ativo')
    
        dot.addEventListener('click', () => {
            index = i + 1
            moverCarrossel()
            reiniciarAutoplay()
        })

        containerDots.appendChild(dot)
    })

    const primeiroClone = slidesIniciais[0].cloneNode(true)
    const ultimoClone = slidesIniciais[slidesIniciais.length - 1].cloneNode(true)

    primeiroClone.classList.add('clone')
    ultimoClone.classList.add('clone')

    trilho.appendChild(primeiroClone)
    trilho.insertBefore(ultimoClone, slidesIniciais[0])

    const slides = document.querySelectorAll('.carrossel-item')
    const dots = document.querySelectorAll('.dot')

    const moverCarrossel = () => {
        trilho.style.transition = "transform 0.5s ease-in-out"
        trilho.style.transform = `translateX(-${index * 100}%)`

        dots.forEach((dot, i) => {
            let dotIndex = index - 1
        
            if(index === 0) dotIndex = dots.length - 1
            if(index === slides.length - 1) dotIndex = 0

            dot.classList.toggle('ativo', i === dotIndex)
        })
    }

    trilho.addEventListener('transitionend', () => {
        if (index === slides.length - 1) {
            trilho.style.transition = "none"
            index = 1
            trilho.style.transform = `translateX(-${index * 100}%)`
        }

        if (index === 0) {
            trilho.style.transition = "none"
            index = slides.length - 2
            trilho.style.transform = `translateX(-${index * 100}%)`
        }
    })

    const btnProximo = document.querySelector('.seta-direita')
    const btnAnterior = document.querySelector('.seta-esquerda')

    btnProximo.addEventListener('click', () => {
        index++
        moverCarrossel()
        reiniciarAutoplay()
    })

    btnAnterior.addEventListener('click', () => {
        index--
        moverCarrossel()
        reiniciarAutoplay()
    })

    const iniciarAutoplay = () => {
        autoplay = setInterval(() => {
            index++
            moverCarrossel()
        }, tempoIntervalo)
    }

    const reiniciarAutoplay = () => {
        clearInterval(autoplay)
        iniciarAutoplay()
    }

    trilho.style.transform = `translateX(-${index * 100}%)`
    iniciarAutoplay()
})