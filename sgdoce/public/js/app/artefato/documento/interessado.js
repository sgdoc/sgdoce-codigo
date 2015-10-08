$(document).ready(function(){
    $('#nuCpf').setMask('999.999.999-99');
    $('#nuCnpj').setMask('99.999.999/9999-99');

    Interessado.interessadoAutoComplete();
    Interessado.interessadoModal();
    Interessado.interessadoFuncoes();

    //só carrega a grid de maneira automatica se for edição
    if (parseInt($('#sqArtefato').val())) {
        Interessado.grid();
    }
});