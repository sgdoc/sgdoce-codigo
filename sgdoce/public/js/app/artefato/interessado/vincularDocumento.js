var VincularDocumento = {
    VincularDocumentoAutoComplete: function () {
        //autocomplete para as unidade organizacionais do ICMBio
        $('#unidIcmbio').simpleAutoComplete("/processo/processo/unidade-org-icmbio", {
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel'
        });

        // autocomplete para os funcionario do ICMBio
        $('#funcIcmbio').simpleAutoComplete("/processo/processo/funcionario-icmbio", {
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel'
        });
    },
    VincularDocumentoModal: function () {
        $('#btnAdicionarVincularDocumento').click(function () {
            $('#modal-vincular-documento').modal();
        });
    },
    VincularDocumentoFuncoes: function () {
        $("input[name=tpInternoExterno]").click(function () {
            if ($(this).val() == 'interno') {
                $('.interno').removeClass('hidden');
                $('.externo').addClass('hidden');
                return true;
            }

            $('.externo').removeClass('hidden');
            $('.interno').addClass('hidden');

        });

        // evento de tela ao selecionar a opçao de funcionario
        $("input[name=unidFuncionario]").click(function () {
            if ($(this).val() == 'unidade') {
                $('.unidade').removeClass('hidden');
                $('.funcionario').addClass('hidden');
                return true;
            }

            $('.funcionario').removeClass('hidden');
            $('.unidade').addClass('hidden');
        });

        //acao ao clicar no botao de cancelar na modal
        $("#btnCancelarVincularDocumento").click(function () {
            $('.externo, .interno, .funcionario, .unidade').addClass('hidden');
            return true;
        });

        $("#sqTipoPessoaMaterial").change(function () {

            switch ($(this).val()) {
                case Pessoa.PessoaFisica :
                    $('.div-externo').addClass('hidden');
                    $('.noPessoa').removeClass('hidden');
                    $('.nuCpf').removeClass('hidden');
                    break;
                case Pessoa.PessoaJuridica :
                    $('.div-externo').addClass('hidden');
                    $('.noPessoa').removeClass('hidden');
                    $('.nuCnpj').removeClass('hidden');
                    break;
                case Pessoa.Estrangeiro :
                    $('.div-externo').addClass('hidden');
                    $('.noPessoa').removeClass('hidden');
                    $('.nuPassaporte').removeClass('hidden');
                    break;
                case Pessoa.MinisterioPublico :
                    $('.div-externo').addClass('hidden');
                    $('.noPessoa').removeClass('hidden');
                    break;
                case Pessoa.OutrosOrgaos :
                    $('.div-externo').addClass('hidden');
                    $('.noPessoa').removeClass('hidden');
                    break;
            }
        });
    },
    grid: function ()
    {
        Grid.load(
                '/artefato/processo-eletronico/list-vincular-documento/sqArtefato/' + $('#sqArtefato').val(), $('#table-vincular-documento')
                );
    }
};