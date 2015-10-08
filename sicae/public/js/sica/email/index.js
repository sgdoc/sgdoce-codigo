Email = {
    initGrid: function(){
        Grid.loadNoPagination($('#form-email'), $('#table-email'));
    },

    adicionar: function(){
        $('#btn-add-email').click(function(){
            $.get('/principal/email/create', {
                sqPessoa: $('#sqPessoa').val()
            }, function(data){
                $('#modal-email').html(data).modal({'backdrop': 'static','keyboard': false});
            });
        });
    },

    alterar: function(id, sqPessoa){
        $.get('/principal/email/edit', {
            id: id,
            sqPessoa: sqPessoa
        }, function(data){
            $('#modal-email').html(data).modal({'backdrop': 'static','keyboard': false});
        });
    },

    deletar: function(codigo){
        var callBack = function(){
            PessoaForm.saveFormWebService('app:Email', 'libCorpDeleteEmail',
                [{
                    name: 'sqEmail',
                    value: codigo
                }],
                $('#form-email'));
        }

        Message.showConfirmation({
            'body': 'Confirma exclusão do registro?',
            'yesCallback': callBack
        });
    },

    init: function(){
        Email.initGrid();
        Email.adicionar();
    }

}

$(document).ready(function(){
    Email.init();
});