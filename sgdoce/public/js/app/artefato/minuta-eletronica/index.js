$(document).ready(function(){
    if ($('#semUnidadeExercicio').val()){
        Message.showError('Não é possível o cadastro ou alteração das informações. Favor procurar a coordenação para o cadastro da sua unidade de exercício.');
    }
    $('a[data-dismiss="modal"]').click(function(){
       location.href = '/';
    });
 	$('#sqTipoDocumento').simpleAutoComplete("/auxiliar/tipodoc/search-tipo-documento", {
        extraParamFromInput: '#extra',
        attrCallBack: 'rel',
        autoCompleteClassName: 'autocomplete',
        selectedClassName: 'sel'
 	});
    
 	$('#sqAssunto').simpleAutoComplete("/auxiliar/assunto/searchassunto/", {
        extraParamFromInput: '#extra',
        attrCallBack: 'rel',
        autoCompleteClassName: 'autocomplete',
        selectedClassName: 'sel'
 	});

    $('#form-artefato').submit(function() {
        var params = $('#form-artefato').serialize();

        if (!FormArtefato.checkModeloCadastrado(params) && $('#sqTipoDocumento').val() && $('#sqAssunto').val()) {
            Message.showAlert('Não existe modelo de minuta para esse tipo de documento , entre em contato com o gerenciador do sistema.');
            return false;
        } else {
            return true;
        }
    });
});

FormArtefato = {
    checkModeloCadastrado: function(params){
        var result = $.ajax({
                type: 'post',
                url: '/artefato/minuta-eletronica/check-modelo-cadastrado',
                data: params,
                async: false,
                global: false
            }).responseText;
        return (result > 0);
    }
}