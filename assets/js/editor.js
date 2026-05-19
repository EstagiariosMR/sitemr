function adicionarLink(){
    let url = prompt("Insira a URl do link: ")
    
    if(!url || url.trim() === ""){
        return
    }

    url = url.trim()

    if(!url.startsWith("http://") && !url.startsWith("https://")){
        url = "https://" + url
    }

    document.getElementById('editor').focus()
    document.execCommand('createLink', false, url)
}

function format(command, value = null){
    document.getElementById('editor').focus()
    document.execCommand(command, false, value)
}

function mostrarErro(msg){
    const msgErro = document.getElementById('msgErro')
    
    if(msgErro){
        msgErro.textContent = msg
        msgErro.style.display = 'block'
        setTimeout(() => {
            msgErro.style.display = 'none'
        }, 4000)
    }
}

document.addEventListener('DOMContentLoaded', function(){
    const form = document.getElementById('formEditor')
    const editor = document.getElementById('editor')
    const conteudoInput = document.getElementById('conteudo')

    if(!form || !editor || !conteudoInput){
        console.warn('Form, editor ou input hidden não foram encontrados.')
        return
    }

    form.addEventListener('submit', function(e){
        const conteudoHtml = editor.innerHTML;

        if(!conteudoHtml || conteudoHtml.trim() === '' || conteudoHtml === '<br>'){
            e.preventDefault();
            mostrarErro('Por favor, digite algo antes de enviar.')
            return
        }

        conteudoInput.value = conteudoHtml
    })
})