document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('#form-trabalhos')
    const divConteudo = /** @type {HTMLElement} */ (document.querySelector('#resultado'))

    if(!form) return

    form.addEventListener('submit', async (event) => {
        event.preventDefault()

        const { value: ano } = document.querySelector('#ano')
        const { value: turma } = document.querySelector('#turma')
        const { value: aluno } = document.querySelector('#aluno')

        divConteudo.classList.add('loading')
        divConteudo.style.opacity = '0.5'

        await buscarResultados({ ano, turma, aluno })
    })

    const buscarResultados = async ({ ano, turma, aluno }) => {
        const params = new URLSearchParams({
            ano,
            turma,
            aluno: aluno.trim()
        })

        const url = `includes/trabalhos_integrado_ajax.php?${params.toString()}`

        try{
            const response = await fetch(url)

            if(!response) throw new Error('Falha na requisição')
            
            const html = await response.text()

            divConteudo.innerHTML = html

            const yOffset = -20
            const y = form.getBoundingClientRect().top + window.pageYOffset + yOffset

            window.scrollTo({ top: y, behavior: 'smooth'})
        }
        catch(error){
            console.error('Erro na busca:', error)
            divConteudo.innerHTML = `<p style="color: red;">Erro ao carregar os dados. Verifique a conexão.</p>`
        }
        finally{
            divConteudo.style.opacity = '1'
            divConteudo.classList.remove('loading')
        }
    }
})