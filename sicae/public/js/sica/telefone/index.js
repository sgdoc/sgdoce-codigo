Telefone = {
    initGrid: function(){
        Grid.loadNoPagination($('#form-telefone'), $('#table-telefone'));
    },

    adicionar: function(){
        $('#btn-add-telefone').click(function(){
            $.get('/principal/telefone/create', {
                sqPessoa: $('#sqPessoa').val()
                }, function(data){
                $('#modal-telefone').html(data).modal({'backdrop': 'static','keyboard': false});
            });
        });
    },

    alterar: function(id, sqPessoa){
        $.get('/principal/telefone/edit', {
            id: id,
            sqPessoa: sqPessoa
        }, function(data){
            $('#modal-telefone').html(data).modal({'backdrop': 'static','keyboard': false});
        });
    },

    deletar: function(codigo){
        var callBack = function(){
            PessoaForm.saveFormWebService('app:Telefone', 'libCorpDeleteTelefone',
                [{
                    name: 'sqTelefone',
                    value: codigo
                }],
                $('#form-telefone'));
        }

        Message.showConfirmation({
            'body': 'Confirma exclusão do registro?',
            'yesCallback': callBack
        });

    },

    init: function(){
        Telefone.initGrid();
        Telefone.adicionar();
    }

}

$(document).ready(function(){
    Telefone.init();
});