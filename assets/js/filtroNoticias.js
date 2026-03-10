function carregarPagina(pagina = 1){
    fetch('includes/noticias_ajax.php?pagina=' + pagina)
    .then(response => {
        if(!response.ok){
            throw new Error('Erro na resposta do servidor')
        }
        return response.text();
    })
    .then(html => {
        document.getElementById('noticia-box').innerHTML = html
    })
    .catch(error => {
        console.error('Erro ao carregar notícias:', error)
    })
}

document.addEventListener('DOMContentLoaded', () => {
    carregarPagina()
})