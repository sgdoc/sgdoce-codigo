DadoBancario = {
    initGrid: function(){
        Grid.loadNoPagination($('#form-dado-bancario'), $('#table-dado-bancario'));
    },

    adicionar: function(){
        $('#btn-add-dado-bancario').click(function(){
            $.get('/principal/dado-bancario/create', {
                sqPessoa: $('#sqPessoa').val()
            }, function(data){
                $('#modal-dado-bancario').html(data).modal({'backdrop': 'static','keyboard': false});
            });
        });
    },

    alterar: function(id, sqPessoa){
        $.get('/principal/dado-bancario/edit', {
            id: id,
            sqPessoa: sqPessoa
        }, function(data){
            $('#modal-dado-bancario').html(data).modal({'backdrop': 'static','keyboard': false});
        });
    },

    deletar: function(codigo){
        var callBack = function(){
            PessoaForm.saveFormWebService('app:DadoBancario', 'libCorpDeleteDadoBancario',
                [{
                    name: 'sqDadoBancario',
                    value: codigo
                }],
                $('#form-dado-bancario'));
        }

        Message.showConfirmation({
            'body': 'Confirma exclusão do registro?',
            'yesCallback': callBack
        });
    },

    init: function(){
        DadoBancario.initGrid();
        DadoBancario.adicionar();
    }

}

$(document).ready(function(){
    DadoBancario.init();
});