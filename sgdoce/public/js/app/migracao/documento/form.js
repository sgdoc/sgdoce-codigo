ProcessoDoc = {
    _isSIC              : 0,
    _disabledProcedencia: '',
    _tpDocWithoutSignature: [],
    _urlCancel          : '/artefato/documento/index',
    _urlCancelSIC       : '/artefato/area-trabalho/index',
    initTable: function () {},
    init: function () {

        ProcessoDoc.handleTaggleDisableBtnConcluir().trocaObrigatoriedade();

        var nuArtefato              = $('#nuArtefato'),
            optionsCheckbox2        = $('#optionsCheckbox2'),
            optionsCheckbox1        = $('#optionsCheckbox1'),
            chekProcedenciaInterno  = $('#chekProcedenciaInterno'),
            chekProcedenciaExterno  = $('#chekProcedenciaExterno'),
            nuArtefatoHidden        = $('#nuArtefatoHidden');

        //na inicialização da tela caso esteja selecionado a opção "Doc sem numero"
        //força a retirada da classe "required" do campo
        if (optionsCheckbox1.prop('checked') || chekProcedenciaInterno.prop('checked')) {
            nuArtefato.removeClass('required');
        }

        if( chekProcedenciaInterno.prop('checked') ) {
            Assunto.handleEnableSignature();
        }

        if( chekProcedenciaExterno.prop('checked') ) {
            Assunto.handleDisableSignature();
        }

        nuArtefato.data('originalValue',nuArtefato.val());

        if (ProcessoDoc._disabledProcedencia) { //SIC não entra aki pois já vem desabilitado
            //desabilita a procedencia não permitida devido ao tipo de digital
            $('#'+ProcessoDoc._disabledProcedencia).prop('disabled','disabled');
        }
        var clicked = false;

        $('.tab').click(function () {
            var nuCurrTab = $(this).parents("li").index(); //1 = origem/destino, 2 = assunto, 3 = interessado
            if (nuCurrTab == 1) {
                //caso desabilitar procedencia interna efetua trigger para procedencia externa
                if (!clicked && ProcessoDoc._disabledProcedencia === 'chekProcedenciaInterno' && $('#sqArtefato').val() == '0') {
                    clicked = true;
                    var interval = setInterval(function(){
                        if (!$('#sqTipoPessoaOrigem').is(':visible')){
                            $('#chekProcedenciaExterno').trigger('click');
                        }else{
                            clearInterval(interval);
                        }
                    },100);
                }
            }
        });

        chekProcedenciaInterno.on('click', function(){
            var isChecked = $(this).is(":checked");
            if( isChecked ) {
                var sqTipoDocumento = $("#sqTipoDocumento_hidden").val();
                sqTipoDocumento = parseInt(sqTipoDocumento);
                if( sqTipoDocumento > 0 ) {
                    ProcessoDoc.handleSqTipoDocumento($("#sqTipoDocumento_hidden").val());
                } else {
                    Assunto.handleEnableSignature();
                }
            }
        });

        chekProcedenciaExterno.on('click', function(){
            var isChecked = $(this).is(":checked");
            if( isChecked ) {
                Assunto.handleDisableSignature();
            }
        });
    },

    trocaObrigatoriedade: function () {

        var nuArtefato      = $('#nuArtefato'),
            nuArtefatoHidden = $('#nuArtefatoHidden');

        $('input[name="numeracao"]').off().on('click', function () {
            var procedencia = $('input[name="procedenciaInterno"]:checked').val()
            var option = $(this).val();
            var checked = $(this).is(':checked');

            if (checked) {
                nuArtefato.val('');
                nuArtefato.removeAttr('class');
                nuArtefato.attr('class', 'span4 inline');
                nuArtefato.prop('disabled', 'disabled');
                nuArtefato.parents('.control-group').removeClass('error');
                nuArtefato.siblings('p').hide();

                if (option === 'option1') {
                    nuArtefatoHidden.val('')
                                    .attr('name', 'nuArtefato');
                    nuArtefato.removeAttr('name');
                } else {
                    $('#optionsCheckbox1').removeProp('checked');
                    nuArtefatoHidden.val(nuArtefato.data('originalValue'));
                    nuArtefato.val(nuArtefato.data('originalValue'));
                }
            } else {
                //não pode os 2 desabilitados quando procedencia for interna
                if (procedencia === 'interno'
                    && $('#optionsCheckbox2').length > 0) {
                    return false;
                }
                nuArtefato.removeAttr('class');
                nuArtefato.removeProp('disabled');
                nuArtefato.attr('class', 'span4 inline required');

                if (nuArtefatoHidden.length){
                    nuArtefatoHidden.removeAttr('name').val('');
                    nuArtefato.attr('name', 'nuArtefato');
                }
            }
        });
    },
    check: function () {
        if ($("#optionsCheckbox1:checked").length) {
            $('#nuArtefato').val('');
            $('#nuArtefato').removeAttr('class');
            $('#nuArtefato').attr('class', 'span4 inline');
            $('#nuArtefato').prop('readonly', 'readonly');
        } else {
            $('#nuArtefato').removeAttr('class');
            $('#nuArtefato').removeProp('readonly');
            $('#nuArtefato').attr('class', 'span4 inline required');
        }
    },
    initCampos: function () {

        $('#cancelarDoc').click(function () {
            Message.showConfirmation({
                body: UI_MSG.MN011, //'Tem certeza que deseja cancelar o cadastro?'
                yesCallback: function () {
                    window.location = (ProcessoDoc._isSIC) ? ProcessoDoc._urlCancelSIC : ProcessoDoc._urlCancel;
                }
            });
            return false;
        });

        /*
         * controla o campo de assinatura retirando a obrigatoriedade caso procedencia seja externa
         */
        var isProcedenciaExterna = (ProcessoDoc._disabledProcedencia == 'chekProcedenciaInterno');
        if (isProcedenciaExterna) {
            var objNoRespAssina = $('#noResponsavelAssinatura');
            var objSpanRequired = objNoRespAssina.parent('.controls')
                                                 .siblings('.control-label')
                                                 .find('span');
                objNoRespAssina.removeClass('required');
                objSpanRequired.hide();
        }

        $('#sqTipoDocumento').simpleAutoComplete("/artefato/documento/search-tipo-documento", {
            extraParamFromInput: '#sqTipoDocumento',
            attrCallBack: 'id',
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel'
        },function(data){
            ProcessoDoc.handleSqTipoDocumento(data[1]);
        });
    },
    handleTaggleDisableBtnConcluir: function () {
        var btnConcluir = $('.btn-concluir');

        //se for cadastro
        if ($('#divEdit').val() == 0) {
            btnConcluir.prop('disabled', 'disabled');

            $('.tab').click(function () {
                var nuCurrTab = $(this).parents("li").index(); //2 = assunto, 3 = interessado
                if (ProcessoDoc._isSIC) {
                    if (nuCurrTab == 3 ) {
                        //quando chegar na aba interessado habilita o concluir caso esteja desabilitado
                        if (btnConcluir.is(':disabled')) {
                            btnConcluir.removeProp('disabled');
                        }
                    }
                }else{
                    if (nuCurrTab == 2) {
                        //quando chegar na aba assunto habilita o concluir caso esteja desabilitado
                        if (btnConcluir.is(':disabled')) {
                            btnConcluir.removeProp('disabled');
                        }
//                    } else if (nuCurrTab < 2) {
//                        //quando chegar na aba assunto habilita o concluir caso esteja desabilitado
//
//                        var enableFinishButton = true;
//                        //não dá pra saber quais os .required estão preenchidos pois tem campos .required ocultos não preenchidos
//                        $('#dadosAssunto :input.required').each(function (index, elem) {
//                            if ($(elem).val() == '') {
//                                enableFinishButton = false;
//                                return false;
//                            }
//                        });
//                        btnConcluir.prop('disabled', !enableFinishButton);
                    }
                }
            });
        }
        return ProcessoDoc;
    },
    reloadDivImagem: function () {
        $('#dadosImagem').html('');
        ProcessoDoc.assingContentImage();
    },
    assingContentImage: function () {
        $.get('artefato/imagem/list', {
            id: $('#sqArtefato').val(),
            obrigatoriedade: false
        },
        function (data) {
            $('#dadosImagem').html(data);
            $('.thumbnail').css('height', 276);
        });
    },
    handleSqTipoDocumento: function( sqTipoDocumento ){
        var chekProcedenciaInterno = $('#chekProcedenciaInterno');

        if( chekProcedenciaInterno.prop('checked') ){
            //tipo selecionado
            var sqTipoDocumento = parseInt(sqTipoDocumento);
            var isProcedenciaExterna = (ProcessoDoc._disabledProcedencia == 'chekProcedenciaInterno');
            var objNoRespAssina = $('#noResponsavelAssinatura');
            var objSpanRequired = objNoRespAssina.parent('.controls')
                                                 .siblings('.control-label')
                                                 .find('span');
            if ((-1 !== $.inArray(sqTipoDocumento,ProcessoDoc._tpDocWithoutSignature)) || isProcedenciaExterna ) {
                Assunto.handleDisableSignature();
            }else{
                Assunto.handleEnableSignature();
            }
        } else {
            Assunto.handleDisableSignature();
        }
    }
}

$(document).ready(function () {
    ProcessoDoc.init();
    ProcessoDoc.initCampos();

    loadJs('js/components/modal.js', function () {
        Menu.init();
    }); // load cdn

    if ($('#divEdit').val() == 1) {
        if ($('#nuArtefato').val() == '') {
            $('#optionsCheckbox1').attr('checked', 'checked');
        }
    }

    $('.btn-concluir').click(function () {
        $('.tab').each(function () {
            $(this).click();
            if ($('form').valid()) {
                valid = true;
            } else {
                valid = false;
            }
        });

        if (ProcessoDoc._isSIC && valid && $('#table-interessado tbody tr:not(.mensagemInteressado').length == 0) {
            $('#liDadosInteressado a').trigger('click');
            Message.showAlert('Nenhum interessado foi informado.');
            valid = false;
        }

        if (valid) {
            Message.wait();
            $.get('artefato/documento/verifica-documento', {
                nuArtefato: $('#nuArtefato').val(),
                sqArtefato: $('#sqArtefato').val(),
                sqTipoDocumento: $('#sqTipoDocumento_hidden').val(),
                sqPessoaIcmbio: $('#sqPessoaIcmbio_hidden').val(),
                sqPessoaOrigem: $('#sqPessoaOrigem_hidden').val(),
                divEdit: $('#divEdit').val()
            },
            function (data) {
                //neste caso o retorno é padrão de sessão expirada
                if (data.status != 'undefined' && data.status == false ) {
                    Message.waitClose();
                    Message.showError(data.message,function(){
                        window.location.reload();
                    });
                    return false;
                }
                if (data.sucess == 'true') {
                    $.post('/migracao/documento/save/',
                            $('#form-cadastro-documento').serialize(),
                            function (result) {
                                if (result.error) {
                                    Message.waitClose();
                                    Message.show(result.errorType, result.msg);
                                } else {
                                    if (result == '') {
                                        var url = $(".btn-cancelar").attr('href');
                                        if ($('#divEdit').val() == 0) {
                                            window.location = url;
                                        } else {
                                            switch ($('#redirect').val()) {
                                                case '2':
                                                    window.location = url;
                                                    break;
                                                case '3':
                                                    window.location = url;
                                                    break;
                                            }
                                        }
                                    } else {
                                        Message.waitClose();
                                        Message.show('Erro no sistema', result);
                                    }
                                }
                            });
                }
                if (data.sucess == 'false') {
                    Message.waitClose();
                    Message.showAlert('Ja existe Documento cadastrado com essas informações.');
                }
            });
        }

        return false;
    });
});
