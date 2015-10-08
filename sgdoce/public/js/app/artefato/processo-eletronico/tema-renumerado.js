$(document).ready(function(){
    $('.btnConcluirTermo').click(function() {
        window.location = '/artefato/visualizar-artefato/index/sqArtefato/' + $('#sqArtefato').val() + '/update/0/view/1';
    });
});