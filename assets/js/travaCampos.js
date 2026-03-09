document.addEventListener('DOMContentLoaded', () => {
    const selectAno = document.querySelector('#ano')
    const selectTurma = document.querySelector('#turma')
    const inputAluno = document.querySelector('#aluno')

    const atualizarCampos = async () => {
        const anoSelecionado = selectAno.value

        selectTurma.disabled = !anoSelecionado
        inputAluno.disabled = !(anoSelecionado && selectTurma.value)

        if(!anoSelecionado){
            selectTurma.value = ""
            selectTurma.innerHTML = '<option value="" selected disabled>-- Selecione o ano --</option>'

            inputAluno.value = ""
            return
        }

        if(event && event.target === selectAno){
            try{
                const busca = await fetch(`includes/buscar_turmas.php?ano=${anoSelecionado}`)

                const listaTurmas = await busca.json()

                selectTurma.innerHTML = '<option value="" selected disabled>-- Selecione a turma --</option>'

                listaTurmas.forEach(item => {
                    const option = document.createElement('option')
                    option.value = item.turma
                    option.textContent = item.turma
                    selectTurma.appendChild(option)
                })
            }
            catch(erro){
                console.error("Erro ao carregar as turmas via Ajax:", erro)
            }
        }

        // if(!selectTurma.value){
        //     inputAluno.value = ""
        // }
    }

    selectAno.addEventListener('change', atualizarCampos)
    selectTurma.addEventListener('change', atualizarCampos)

    const btnLimpar = document.querySelector('button[onclick="limparFormulario()"]')

    if(btnLimpar){
        btnLimpar.addEventListener('click', () => {
            setTimeout(atualizarCampos, 10)
        })
    }
})