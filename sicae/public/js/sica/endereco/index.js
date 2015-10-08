Endereco = {
    initGrid: function(){
        Grid.loadNoPagination($('#form-endereco'), $('#table-endereco'));
    },

    adicionar: function(){
        $('#btn-add-endereco').click(function(){
            $.get('/principal/endereco/create', {
                sqPessoa: $('#sqPessoa').val()
            }, function(data){
                $('#modal-endereco').html(data).modal({'backdrop': 'static','keyboard': false});
            });
        });
    },

    alterar: function(id, sqPessoa){
        $.get('/principal/endereco/edit', {
            id: id,
            sqPessoa: sqPessoa
        }, function(data){
            $('#modal-endereco').html(data).modal({'backdrop': 'static','keyboard': false});
        });
    },

    deletar: function(codigo){
        var callBack = function(){
            PessoaForm.saveFormWebService('app:Endereco', 'libCorpDeleteEndereco',
                [{
                    name: 'sqEndereco',
                    value: codigo
                }],
                $('#form-endereco'));
        }

        Message.showConfirmation({
            'body': 'Confirma exclusão do registro?',
            'yesCallback': callBack
        });
    },

    init: function(){
        Endereco.initGrid();
        Endereco.adicionar();
    }
}

$(document).ready(function(){
    Endereco.init();
});
