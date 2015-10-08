var VincularDocumento = {
    keyVinculo: 0,
    _msgDuplicidade: 'Item já incluído na lista.',

    Validation: function() {
        loadJs('js/components/validation.js', function() {
            Validation.init();
        }); //
   },
    zeraComando: function() {
        $('#nuArtefatoVinculacao').val('');
        $('#nuArtefatoVinculacao').removeAttr('autocomplete');
        $('#nuArtefatoVinculacao').removeAttr('name');
        $('#nuArtefatoVinculacao').attr('name', 'nuArtefatoVinculacao');
        $('#nuArtefatoVinculacao_hidden').remove();

        $('#nuArtefatoVinculo').val('');
        $('#nuArtefatoVinculo').removeAttr('autocomplete');
        $('#nuArtefatoVinculo').removeAttr('name');
        $('#nuArtefatoVinculo').attr('name', 'nuArtefatoVinculo');
        $('#nuArtefatoVinculo_hidden').remove();
    },
    setaAutocompletePessoa: function(vl) {
        VincularDocumento.zeraComando();

        if (vl == 1) {
            $('#nuArtefatoVinculacao').simpleAutoComplete("/artefato/dossie/find-numero-digital/sqTipoDocumento/" +
                    $('#sqTipoDocumentoReferencia_hidden').val(), {
                extraParamFromInput: '#sqTipoArtefatoVinculacao',
                autoCompleteClassName: 'autocomplete',
                selectedClassName: 'sel'
            });

            $('#nuArtefatoVinculo').simpleAutoComplete("/artefato/dossie/find-numero-artefato/sqTipoDocumento/" +
                    $('#sqTipoDocumentoReferencia_hidden').val(), {
                extraParamFromInput: '#sqTipoArtefatoVinculacao',
                autoCompleteClassName: 'autocomplete',
                selectedClassName: 'sel'
            });
        }
        if (vl == 2 || vl == 3) {
            $('#nuArtefatoVinculacao').simpleAutoComplete("/artefato/dossie/find-numero-digital/", {
                extraParamFromInput: '#sqTipoArtefatoVinculacao',
                autoCompleteClassName: 'autocomplete',
                selectedClassName: 'sel'
            });

            $('#nuArtefatoVinculo').simpleAutoComplete("/artefato/dossie/find-numero-artefato/", {
                extraParamFromInput: '#sqTipoArtefatoVinculacao',
                autoCompleteClassName: 'autocomplete',
                selectedClassName: 'sel'
            });
        }
        $('#noPessoaFantasia,#noPessoaFantasia_hidden').val('');
    },
    VincularDocumentoAutoComplete: function() {

        var noPessoaFantasia          = $('#noPessoaFantasia').prop('disabled','disabled')
            ,noPessoaFantasia_hidden  = $('#noPessoaFantasia_hidden')
            ,sqTipoArtefatoVinculacao = $("#sqTipoArtefatoVinculacao")
            ,sqTipoDocumentoReferencia= $("#sqTipoDocumentoReferencia")
            ,sqTipoDocumentoReferencia_hidden = $('#sqTipoDocumentoReferencia_hidden');

        sqTipoDocumentoReferencia.blur(function() {
            if ($(this).val() == '') {
                sqTipoDocumentoReferencia_hidden.val('');
            }
            VincularDocumento.setaAutocompletePessoa(1);
        });

        sqTipoArtefatoVinculacao.change(function() {
            sqTipoDocumentoReferencia.val('');
            sqTipoDocumentoReferencia_hidden.val('');
            switch ($(this).val()) {
                case '1':
                    VincularDocumento.setaAutocompletePessoa(1);
                    break;
                case '2':
                    VincularDocumento.setaAutocompletePessoa(2);
                    break;
                case '3':
                    VincularDocumento.setaAutocompletePessoa(3);
                    break;
                default:
                    break;
            }
        });

        sqTipoDocumentoReferencia.simpleAutoComplete("/auxiliar/tipodoc/search-tipo-documento/", {
            extraParamFromInput: '#extra',
            attrCallBack: 'class',
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel'
        });

        $('#nuArtefatoVinculacao').focusin(function() {

            if ($('#nuArtefatoVinculacao').val() != '') {
                $.ajax({
                    data: '',
                    type: "POST",
                    dataType: "json",
                    url: '/artefato/documento/auto-complete-vinculacao/nuDigital/' + $('#nuArtefatoVinculacao_hidden').val()
                          + '/sqTipoDocumento/' + sqTipoDocumentoReferencia_hidden.val()
                          + '/sqTipoArtefatoVinculacao/' + sqTipoArtefatoVinculacao.val(),
                    success: function(response) {
                        noPessoaFantasia.val(response.noPessoa);
                        noPessoaFantasia_hidden.val(response.sqPessoa);
                        $('#nuArtefatoVinculo_hidden').val(response.nuArtefato);
                        sqTipoDocumentoReferencia.val(response.noTipoDocumento);
                        sqTipoDocumentoReferencia_hidden.val(response.sqTipoDocumento);
                        if (response.nuArtefato) {
                            $('#nuArtefatoVinculo').val(response.nuArtefato).attr('disabled', false);
                        } else {
                            $('#nuArtefatoVinculo').attr('disabled', true);
                        }
                    }
                });
            }
        });
        $('#nuArtefatoVinculo').unbind('focusin').focusin(function() {
            var artefato = $('#nuArtefatoVinculo_hidden').val();
            var nuArtefato = artefato.replace('/', '!');
            if ($('#nuArtefatoVinculo').val() != '') {
                $.ajax({
                    data: '',
                    type: "POST",
                    dataType: "json",
                    url: '/artefato/documento/auto-complete-vinculacao/nuArtefato/' + nuArtefato
                          + '/sqTipoDocumento/' + sqTipoDocumentoReferencia_hidden.val()
                          + '/sqTipoArtefatoVinculacao/' + sqTipoArtefatoVinculacao.val(),
                    success: function(response) {
                        noPessoaFantasia.val(response.noPessoa);
                        noPessoaFantasia_hidden.val(response.sqPessoa);
                        $('#nuArtefatoVinculacao').val(response.nuDigital);
                        $('#nuArtefatoVinculacao_hidden').val(response.nuDigital);
                        sqTipoDocumentoReferencia.val(response.noTipoDocumento);
                        sqTipoDocumentoReferencia_hidden.val(response.sqTipoDocumento);
                    }
                });
            }
        });

        $('#nuArtefatoVinculacao').keyup(function(e) {
            if (e.keyCode == 8 || e.keyCode == 46) {
                noPessoaFantasia.val(null);
                noPessoaFantasia_hidden.val(null);
                $('#nuArtefatoVinculo').val(null);
                $('#nuArtefatoVinculo_hidden').val(null);
            }
        });
        $('#nuArtefatoVinculo').keyup(function(e) {
            if (e.keyCode == 8 || e.keyCode == 46) {
                noPessoaFantasia.val(null);
                noPessoaFantasia_hidden.val(null);
                $('#nuArtefatoVinculacao').val(null);
                $('#nuArtefatoVinculacao_hidden').val(null);
            }
        });

        //autocomplete para as unidade organizacionais do ICMBio
        $('#unidIcmbio').simpleAutoComplete("/artefato/processo-eletronico/unidade-org-icmbio", {
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel'
        });

        // autocomplete para os funcionario do ICMBio
        $('#funcIcmbio').simpleAutoComplete("/artefato/processo-eletronico/funcionario-icmbio", {
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel'
        });

        // autocomplete para os funcionario do ICMBio
        noPessoaFantasia.simpleAutoComplete("/artefato/pessoa/search-pessoa", {
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel'
        });

        $('#btn-modal-vinculacao').click(function() {
            if (!$('form').valid()) {
                $('.campos-obrigatorios').remove();
                return false;
            }
            
            var sqArtefato = null;
            
            if( $('#stMigracao').val() != true ) {
                sqArtefato = parseInt($('#sqArtefato').val())
            }

            var dataPost = {
                    nuDigital           : $('#nuArtefatoVinculacao').val(),
                    nuArtefatoVinculacao: $('#nuArtefatoVinculo_hidden').val(),
                    sqTipoArtefato      : sqTipoArtefatoVinculacao.val(),
                    sqArtefato          : sqArtefato,
                    inOriginal          : $('#inOriginal').val()
                };

            if (dataPost.sqArtefato) {
                $.post('/artefato/documento/add-documento-eletronico', dataPost, function(data) {
                    if (data.sucess == 'true') {
                        Message.showSuccess(UI_MSG['MN013']);
                        VincularDocumento.reloadGrid()
                    } else if (data.sucess == 'false') {
                        Message.showAlert(VincularDocumento._msgDuplicidade);
                        $(".bootbox .btn").click(function() {
                            $("#modal-vincular-documento").modal();
                        });
                        return false;
                    }
                });
            } else {
                dataPost.tipoDocumento = sqTipoArtefatoVinculacao.find(':selected').text();
                dataPost.nomeOrigem    = noPessoaFantasia.val();

                if(!VincularDocumento.populateTable(dataPost)){
                    return false;
                }
            }
        });
    },

    populateTable:function(data){
        var table = $('.tableVinculo')
           ,tbody = table.find('tbody')
           ,go = true;

        /*verifica duplicidade na grid */
        if(data.sqTipoArtefato != 2){ //2 = processo
            tbody.find('.hdn_nuDigital').each(function(i){
                if($(this).val() == data.nuDigital){
                    go = false;
                    Message.showAlert(VincularDocumento._msgDuplicidade);
                    return false;
                }
            });
        }else{
            tbody.find('.hdn_nuArtefato').each(function(i){
                if($(this).val() == data.nuArtefatoVinculacao){
                    go = false;
                    Message.Alert(VincularDocumento._msgDuplicidade);
                    return false;
                }
            });
        }

        if(!go){
            return false;
        }

        var newTr       = $('<tr />')
           ,tdTypeDoc   = $('<td />',{text:data.tipoDocumento}       ).appendTo(newTr)
           ,tdOrigem    = $('<td />',{text:data.nomeOrigem}          ).appendTo(newTr)
           ,tdNumber    = $('<td />',{text:data.nuArtefatoVinculacao}).appendTo(newTr)
           ,tdDigital   = $('<td />',{text:data.nuDigital}           ).appendTo(newTr)
           ,tdAction    = $('<td />').appendTo(newTr)
           ,btnAction   = $('<button />',{type:'button', class:'btn btn-mini btnExcluirVinculo',title:'Excluir'});

            btnAction.click(function(){
                var btn = $(this);
                Message.showConfirmation({
                    body: sprintf(UI_MSG.MN051, data.nuDigital),
                    yesCallback: function(){
                        btn.parents('tr').remove();
                        if (tbody.find('tr').length === 1) {
                            tbody.find('tr.mensagemVinculo').show();
                        }
                    }
                });
            });
        $('<i />',{class:'icon-trash'}).appendTo(btnAction);

        btnAction.appendTo(tdAction);

        VincularDocumento.keyVinculo++;

        $('<input />',{
            type:'hidden',
            name:'dataVinculo['+VincularDocumento.keyVinculo+'][nuDigital]',
            value:data.nuDigital,
            class:'hdn_nuDigital'
        }).appendTo(tdDigital);
        $('<input />',{
            type:'hidden',
            name:'dataVinculo['+VincularDocumento.keyVinculo+'][nuArtefatoVinculacao]',
            value:data.nuArtefatoVinculacao,
            class:'hdn_nuArtefato'
        }).appendTo(tdNumber);
        $('<input />',{
            type:'hidden',
            name:'dataVinculo['+VincularDocumento.keyVinculo+'][sqTipoArtefato]',
            value:data.sqTipoArtefato
        }).appendTo(tdTypeDoc);
        $('<input />',{
            type:'hidden',
            name:'dataVinculo['+VincularDocumento.keyVinculo+'][inOriginal]',
            value:data.inOriginal
        }).appendTo(tdOrigem);

        tbody.find('tr.mensagemVinculo').hide();
        tbody.append(newTr);
        return true;
    },

    VincularDocumentoModal: function() {
        $('#btnAdicionarVincularDocumento').click(function() {
            var sqArtefato = "";
            if( $('#stMigracao').val() != true ){
                sqArtefato = $('#sqArtefato').val();
            }
            $("#modal-vincular-documento").load('/artefato/documento/modal-vinculacao/sqArtefato/' + sqArtefato).modal();
        });
    },
    VincularDocumentoFuncoes: function() {
        $("input[name=tpInternoExterno]").click(function() {
            if ($(this).val() == 'interno') {
                $('.interno').removeClass('hidden');
                $('.externo').addClass('hidden');
                return true;
            }

            $('.externo').removeClass('hidden');
            $('.interno').addClass('hidden');
        });

        // evento de tela ao selecionar a opçao de funcionario
        $("input[name=unidFuncionario]").click(function() {
            if ($(this).val() == 'unidade') {
                $('.unidade').removeClass('hidden');
                $('.funcionario').addClass('hidden');
                return true;
            }

            $('.funcionario').removeClass('hidden');
            $('.unidade').addClass('hidden');
        });

        //acao ao clicar no botao de cancelar na modal
        $("#btnCancelarVincularDocumento").click(function() {
            $('.externo, .interno, .funcionario, .unidade').addClass('hidden');
            return true;
        });

        $("#sqTipoPessoaMaterial").change(function() {
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

        $('#sqTipoArtefatoVinculacao').change(function() {
            if ($('#sqTipoArtefatoVinculacao').val() == '') {
                $('#divNuDigital').hide();
                $('.divTipoDocumento').hide();
                $('#divNuartefato').hide();
                $('#divOrigem').hide();
            }
            switch ($('#sqTipoArtefatoVinculacao').val()) {
                case '2':
                    $('#divNuDigital').hide();
                    $('#nuArtefatoVinculo').addClass('required');
                    $('#labelNumero').removeClass('hidden');
                    $('.divTipoDocumento').hide();
                    $('#divNuartefato').show();
                    $('#divOrigem').show();
                    break;
                case '1':
                    $('.divTipoDocumento').show();
                    $('#divNuDigital').show();
                    $('#nuArtefatoVinculo').removeClass('required');
                    $('#labelNumero').addClass('hidden');
                    $('#divNuartefato').show();
                    $('#divOrigem').show();
                    break;
                case '3':
                    $('#divNuDigital').show();
                    $('#nuArtefatoVinculo').removeClass('required');
                    $('#labelNumero').addClass('hidden');
                    $('.divTipoDocumento').hide();
                    $('#divNuartefato').show();
                    $('#divOrigem').show();
                    break;
            }
        });
    },
    deletar: function(sqArtefatoVinculo) {
        var callBack = function() {
            $.get('artefato/artefato/delete-referencia/id/' + sqArtefatoVinculo, function() {
                VincularDocumento.reloadGrid();
                Message.showSuccess(UI_MSG['MN013']);
            });
        }
        Message.showConfirmation({
            'body': UI_MSG['MN018'],
            'yesCallback': callBack
        });
    },
    grid: function() {
        Grid.load('/artefato/documento/list-vincular-documento/sqArtefato/' + $('#sqArtefato').val(), $('#table-vincular-documento'));
    },
    reloadGrid: function() {
        $('#table-vincular-documento').dataTable().fnDraw(false);
    }
};