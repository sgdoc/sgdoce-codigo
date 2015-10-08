$(document).ready(function(){

    $('#modalMensagem').modal({show: false});

    function verifyAssuntoDocumento(){
        var sqAssunto = $('[name$="sqAssunto"]').val();
        var sqTipoDocumento = $('[name$="sqTipoDocumento"]').val();
        $('#sqTipoDocumentoModal').val(sqTipoDocumento);
        $('#sqAssuntoModal').val(sqAssunto);
        if (sqAssunto && sqTipoDocumento)
        {
            $.post("auxiliar/mensagem/findmessage",  $('#form-mensagem').serialize(), function(data) {
               if ($.isPlainObject(data) && parseInt(data.idMensagem) != parseInt($('#sqMensagem').val())){
                   canSubmit = false;
                   var titleModal = $('[name$="sqTipoDocumento"] option:selected').text() + ' - ' + $('#sqAssunto').val();
                   Message.showConfirmation({
                   'body': data.Mensagem,
                   'yesCallback': function(){
                        cMensagem.editar(data.idMensagem);
                   },
                   'noCallback' : function(){}
                   });
               }
               else
               {
                    $('#mensagemEspecifica').removeAttr("readonly");
               }
            });
        }
    }

    $('#sqTipoDocumento').change(function(e) {
        verifyAssuntoDocumento();
    })

    function verifyAssuntoCallbackAutocomplete(item) {
        verifyAssuntoDocumento();
    }

    $('#sqAssunto').simpleAutoComplete("auxiliar/assunto/searchAssunto/", {
            extraParamFromInput: '#extra',
            attrCallBack: 'class',
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel'
        },
        verifyAssuntoCallbackAutocomplete
    );

     $('#table-grid_length,#table-grid_info').css('margin-left','35px');

    $('#modalMensagem').on('hide', function () {
        window.location = '/auxiliar/mensagem';
    });

    $('#btn-submit').on('click', function(event){
        event.preventDefault();
        if ($('#form-mensagem').valid()) {
            var sqAssunto = $('[name$="sqAssunto"]').val();
            var sqTipoDocumento = $('[name$="sqTipoDocumento"]').val();
            $('#sqTipoDocumentoModal').val(sqTipoDocumento);
            $('#sqAssuntoModal').val(sqAssunto);
            if (sqAssunto && sqTipoDocumento)
            {
                $.post("auxiliar/mensagem/findmessage",  $('#form-mensagem').serialize(), function(data) {
                    if ($.isPlainObject(data) && parseInt(data.idMensagem) != parseInt($('#sqMensagem').val())){
                        canSubmit = false;
                        var titleModal = $('[name$="sqTipoDocumento"] option:selected').text() + ' - ' + $('#sqAssunto').val();
                        Message.showConfirmation({
                            'body': data.Mensagem,
                            'yesCallback': function(){
                                cMensagem.editar(data.idMensagem);
                            },
                            'noCallback' : function(){}
                        });
                    }
                    else
                    {
                        $('#form-mensagem').submit();
                    }
                });
            }
        }
    });

});
