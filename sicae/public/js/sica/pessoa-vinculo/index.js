PessoaVinculo = {

    initGrid: function(){
        Grid.loadNoPagination($('#form-pessoa-vinculo'), $('#table-pessoa-vinculo'));
    },

    adicionar: function(){
        $('#btn-add-pessoa-vinculo').click(function(){
            $.get('/principal/pessoa-vinculo/create', {
                sqPessoa: $('#sqPessoa').val()
            }, function(data){
                $('#modal-pessoa-vinculo').html(data).modal({'backdrop': 'static','keyboard': false});
                $('#modal-pessoa-vinculo #sqPessoa').val($('#sqPessoa').val());
            });
        });
    },

    alterar: function(id, sqPessoa){
        $.get('/principal/pessoa-vinculo/edit', {
            id: id,
            sqPessoa: sqPessoa
        }, function(data){
            $('#modal-pessoa-vinculo').html(data).modal({'backdrop': 'static','keyboard': false});
        });
    },

    alterarStatus: function(sqPessoaVinculo, stRegistroAtivo){
        var callBack = function(){
            PessoaForm.saveFormWebService(
                'app:PessoaVinculo', 'libCorpUpdatePessoaVinculo',
                [{
                    name: 'sqPessoaVinculo',
                    value: sqPessoaVinculo
                },
                {
                    name: 'stRegistroAtivo',
                    value: stRegistroAtivo == '1' ? 'true': 'false'
                },
                {
                    name: 'toogleStatus',
                    value: stRegistroAtivo == '1' ? MessageUI.get('MN143'): MessageUI.get('MN145')
                }],
                $('#form-pessoa-vinculo'));
        }

        var msg = MessageUI.get('MN144');

        if(stRegistroAtivo == '1'){
            msg = MessageUI.get('MN142');
        }

        Message.showConfirmation({
            'body': msg,
            'yesCallback': callBack
        });
    },

    init: function(){
        PessoaVinculo.initGrid();
        PessoaVinculo.adicionar();
    }
}

$(document).ready(function(){
    PessoaVinculo.init();
});