Documento = {
    initGrid: function(){
        Grid.loadNoPagination($('#form-documento'), $('#table-documento'));
    },

    adicionar: function(){
        $('#btn-add-documento').click(function(){
            $.get('/principal/documento/create', {
                sqPessoa: $('#sqPessoa').val()
            }, function(data){
                $('#modal-documento').html(data).modal({'backdrop': 'static','keyboard': false});
            });
        });
    },

    alterar: function(sqTipoDocumento, sqPessoa){
        $.get('/principal/documento/edit', {
            id: sqTipoDocumento,
            sqPessoa: sqPessoa,
            sqTipoDocumento: sqTipoDocumento
        }, function(data){
            $('#modal-documento').html(data).modal({'backdrop': 'static','keyboard': false});
        });
    },

    deletar: function(sqTipoDocumento, sqPessoa){
        var callBack = function(){
            var config = {
                url: '/principal/documento/delete',
                type: 'post',
                data: {
                    sqPessoa: sqPessoa,
                    sqTipoDocumento: sqTipoDocumento
                },
                dataType: 'json',
                success: function(data){
                    Message.showMessage(data);

                    if(Message.isSuccess(data)){
                        $('#form-documento').submit();
                    }
                }
            };

            $.ajax(config);
        }

        Message.showConfirmation({
            'body': 'Confirma exclusão do registro?',
            'yesCallback': callBack
        });
    },

    init: function(){
        Documento.initGrid();
        Documento.adicionar();
    }

}

$(document).ready(function(){
    Documento.init();
});