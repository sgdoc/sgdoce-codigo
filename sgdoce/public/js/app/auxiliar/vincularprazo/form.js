$(document).ready(function(){
 	$('#sqAssunto').simpleAutoComplete("/auxiliar/assunto/searchassunto/", {
        extraParamFromInput: '#extra',
        attrCallBack: 'rel',
        autoCompleteClassName: 'autocomplete',
        selectedClassName: 'sel'
 	});

 	$('#sqTipoDocumento').simpleAutoComplete("/auxiliar/tipodoc/search-tipo-documento", {
        extraParamFromInput: '#extra',
        attrCallBack: 'rel',
        autoCompleteClassName: 'autocomplete',
        selectedClassName: 'sel'
 	});

    if ($('#inPrazoObrigatorio').val()=='FALSE') {
        $('#divPrazo').show();
    } else {
        $('#divPrazo').hide();
    }

    $('#inPrazoObrigatorio').click(function () {
        if ($('#inPrazoObrigatorio').val()=='FALSE') {
            $('#divPrazo').show();
        } else {
            $('#divPrazo').hide();
        }
    });

    $('#cancelar').click(function(){
        window.location = '/auxiliar/vincularprazo';
    });

    $("#nuDiasPrazo").css("text-align", "right");
    $('#nuDiasPrazo').keyup(function() {
        var carc = /[^0-9]/gi;
        var obj = $('#nuDiasPrazo').val();
        obj = obj.replace(carc, "");
        $('#nuDiasPrazo').val(obj);
    }); 

    $('#form-manter-vincular-prazo').submit(function() {
        var params = $('#form-manter-vincular-prazo').serialize();
        if (FormIndicacaoPrazo.checkDuplicatePrazo(params)) {
            var callBack = function() {
                FormIndicacaoPrazo.unLinkPrazo(params);
            }
            Message.showConfirmation({
                'body': 'Já existe vinculação de prazo para os dados selecionados. Deseja desvincular o prazo ?',
                'yesCallback': callBack
            });
            return false;
        } else {
            return true;
        }
    });
})

FormIndicacaoPrazo = {
    checkDuplicatePrazo: function(params){
        var result = $.ajax({
            type: 'post',
            url: '/auxiliar/vincularprazo/check-duplicate-prazo',
            data: params,
            async: false,
            global: false
            }).responseText;
        return (result > 0);
    },

    unLinkPrazo: function(params){
        $.ajax({
            type: 'post',
            url: '/auxiliar/vincularprazo/unlink-prazo',
            data: params,
            async: false,
            global: false
        });
        $('#form-manter-vincular-prazo').submit();
    }
}