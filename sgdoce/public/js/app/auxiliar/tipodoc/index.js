TipoDocumento = {
    deletar: function(codigo){
        var callBack = function(){
            window.location = ('/auxiliar/tipodoc/delete/id/' + codigo);
        }
                
        Message.showConfirmation({
            'body': 'Tem certeza que deseja excluir o tipo de documento?',
            'yesCallback': callBack
        });
    },
    
    alterar: function(codigo){
        window.location = ('/auxiliar/tipodoc/edit/id/' + codigo);
    },
    
    switchStatus: function(codigo,status) {
        var callBack = function()
        {
            $.get('/auxiliar/tipodoc/switch-status/sqTipoDocumento/' + codigo + '/stAtivo/' + status, function()
            {
                var msg = '';
                if (status == 'true'){
                    msg = UI_MSG['MN056'];
                }
                else {
                    msg = UI_MSG['MN055'];
                }
                $('#form-tipo-documento').submit();
                Message.showSuccess(msg);
            })
        }
        if(status == 'false') {
            Message.showConfirmation({
                'body': 'Tem certeza que deseja desativar o Tipo de Documento?',
                'yesCallback': callBack
            });
        }else {
            callBack();
        }
    }
}

$(function(){
    Grid.load($('#form-tipo-documento'), $('#table-grid-tipodoc'));
});
