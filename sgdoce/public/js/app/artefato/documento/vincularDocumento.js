$(document).ready(function(){
    VincularDocumento.VincularDocumentoModal();

    //só carrega a grid de vinculo de maneira automatica se for edição
    if(parseInt($('#sqArtefato').val())){
        VincularDocumento.grid();
    }

    VincularDocumento.Validation();
});