function limparFormulario() {
    const form = document.getElementById('form-trabalhos');
    form.reset();

    // Forçar os selects a voltarem para o primeiro option com "disabled selected"
    document.getElementById('ano').selectedIndex = 0;
    document.getElementById('turma').selectedIndex = 0;
}