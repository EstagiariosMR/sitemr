// @ts-check

document.addEventListener('DOMContentLoaded', () => {
    const alertBox = /** @type {HTMLElement} */ (document.querySelector('#status-alert'))
    const closeBtn = /** @type {HTMLButtonElement} */ (document.querySelector('#close-alert'))

    if(alertBox && closeBtn){
        const closeAlert = () => {
            alertBox.style.transition = 'opacity 0.4s ease, transform 0.4s ease'
            alertBox.style.opacity = '0'
            alertBox.style.transform = 'translateY(-10px)'

            setTimeout(() => alertBox.remove(), 400)
        }

        closeBtn.addEventListener('click', closeAlert)

        setTimeout(closeAlert, 6000)
    }
})