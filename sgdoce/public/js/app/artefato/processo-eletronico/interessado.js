$(document).ready(function(){
    $('#nuCpf').setMask('999.999.999-99');
    $('#nuCnpj').setMask('99.999.999/9999-99');
       
    Interessado.interessadoAutoComplete();
    Interessado.interessadoModal();
    Interessado.interessadoFuncoes();
    if($("#sqArtefato").val() != ""){
        Interessado.grid();
    }
});