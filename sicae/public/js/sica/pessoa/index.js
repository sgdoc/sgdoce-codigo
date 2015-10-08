PessoaPesquisar = {

    fieldsForm: $('.fisica, .juridica, .semClass'),

    actionOfChangeSqTipoPessoa: function(){

        $('#sqTipoPessoa').change(function(){

            PessoaPesquisar.clearFields();
            PessoaPesquisar.fieldsForm.hide();
            $("#form-pesquisar-pessoa").validate().resetForm();
            $("#form-pesquisar-pessoa .error").removeClass('error')

            $(".campos-obrigatorios").hide();

            switch ($(this).val()){
                case '1':
                    $('.fisica').show();
                    $('#sqTipoSociedade').val('').change();
                    break;

                case '2':
                    $('.juridica').show();
                    break;

                case '3':
                    $('.semClass').show();
                    break;

                default:
                    $('.fisica, .juridica, .semClass').hide();
                    break;
            }
        });
    },

    clearFields: function(){
        $('input[type=text], input[type=textarea]').val('').blur();
    },

    initActionButtons: function(){
        $('.limpar').click(function(){
            PessoaPesquisar.fieldsForm.hide();
        });

        $('.filtro').click(function(){
            $(document).scrollTop(0);
        });
    },

    alterar: function(id, pessoaJuridica){
        var url = '/principal/pessoa-fisica/edit/id/' + id;

        if(pessoaJuridica){
            url = '/principal/pessoa-juridica/edit/id/' + id;
        }

        window.location = url;
    },

    visualizar: function(id, pessoaJuridica){
        if(pessoaJuridica){
            window.location = '/principal/pessoa-juridica/view/id/' + id;
            return false;
        }

        $.get('/principal/pessoa-fisica/view', {
            id: id
        }, function(data){
            $('#modal-pessoa-fisica').html(data).modal({
                backdrop:'static'
            });
        });
    },

    alterarStatus: function(sqPessoa, noPessoa,nuCpfCnpj, stRegistroAtivo){
        var callBack = function(){
        	var metodo = 'libCorpUpdatePessoaFisica';
        	if($('#sqTipoPessoa').val() == 2){
        		metodo = 'inativarPessoaJuridica'
        	}
            PessoaForm.saveFormWebService(
                'app:Pessoa', metodo,
                [{
                    name: 'sqPessoa',
                    value: sqPessoa
                },
                {
                    name: 'noPessoa',
                    value: noPessoa
                },
                {
                    name: $('#sqTipoPessoa').val() == 2 ? 'nuCnpj' : 'nuCpf',
                    value: nuCpfCnpj
                },
                {
                    name: 'stRegistroAtivo',
                    value: stRegistroAtivo == '1' ? '1': '0'
                },
                {
                    name: 'toogleStatus',
                    value: stRegistroAtivo == '1' ? 'Pessoa reativada com sucesso.': 'Pessoa inativada com sucesso.'
                }],
                $('#form-pesquisar-pessoa'));
        }

        var msg = 'Tem certeza que deseja inativar esta pessoa?';

        if(stRegistroAtivo == '1'){
            msg = 'Tem certeza que deseja reativar esta pessoa?';
        }

        Message.showConfirmation({
            'body': msg,
            'yesCallback': callBack
        });
    },

    initGrid: function(){
        Grid.load($('#form-pesquisar-pessoa'), $('#table-pesquisar-pessoa'));

        $('#form-pesquisar-pessoa').bind('submit', function(){
            var sqTipoPessoa = $('#sqTipoPessoa').val();

            $(document).ajaxStop(function(){
                switch (sqTipoPessoa){
                    case '1':
                        $('.nome').html('Nome');
                        $('.cpfGrid').html('CPF');
                        $('.data').html('Data de Nascimento');

                        $('tr[role=row] th.header').each(function(){
                            if($(this).text() == 'Sexo'){
                                $(this).removeClass('hidden')
                            }
                        });
                        $('p.column').parent('td').removeClass('hidden');
                        break;
                    case '2':
                        $('.nome').html('Nome Fantasia');
                        $('.cpfGrid').html('CNPJ');
                        $('.data').html('Razão Social');

                        $('tr[role=row] th.header').each(function(){
                            if($(this).text() == 'Sexo'){
                                $(this).addClass('hidden')
                            }
                        });
                        $('p.column').parent('td').addClass('hidden');
                        break;

                    default:
                        $('.nome').html('Tipo de Pessoa');
                        $('.cpfGrid').html('Nome');
                        $('.data').html('CPF/CNPJ');

                        $('tr[role=row] th.header').each(function(){
                            if($(this).text() == 'Sexo'){
                                $(this).addClass('hidden')
                            }
                        });
                        $('p.column').parent('td').addClass('hidden');
                        break;
                }
            });

            $('#pesquisa-pdf').val($(this).find(':not(#pesquisa-pdf)').serialize());
        });
    },

    initNaturezaJuridica: function() {
        $('#sqNaturezaJuridicaPai').change(function() {
            if (parseInt($(this).val()) > 0) {
                $.get("/principal/pessoa-juridica/find-natureza-juridica", {
                    sqNaturezaJuridicaPai: $('#sqNaturezaJuridicaPai').val()
                },function(data) {
                    $('#sqNaturezaJuridica').html(data);
                });
            } else {
                $('#sqNaturezaJuridica').html('<option value="">Selecione uma opção</option>');
                $('#sqNaturezaJuridica').val('').change();
            }
        });
    },

    init: function(){
        PessoaPesquisar.initNaturezaJuridica();
        PessoaPesquisar.actionOfChangeSqTipoPessoa();
        PessoaPesquisar.initActionButtons();
        PessoaPesquisar.initGrid();
    }
}

$(document).ready(function(){
    PessoaPesquisar.init();
});

