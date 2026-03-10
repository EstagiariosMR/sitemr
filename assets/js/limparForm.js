const limparFormulario = () => {
    const form = document.getElementById('form-trabalhos')
    
    form.reset()

    document.getElementById('ano').selectedIndex = 0
    document.getElementById('turma').selectedIndex = 0

    const submitEvent = new Event('submit', {
        bubbles: true,
        calcelable: true
    })

    form.dispatchEvent(submitEvent)
}