ProcessoDoc = {
    _isSIC                : 0,
    _isMigracao           : false,
    _disabledProcedencia  : '',
    _tpDocWithoutSignature: [],
    _sqTipoDocumentoOriginal: false,
    _sqPessoaOrigemOriginal: false,
    _nuArtefatoOriginal   : false,
    _urlCancel            : '/artefato/documento/index',
    _urlCancelSIC         : '/artefato/area-trabalho/index',

    initTable: function () {},
    init: function () {
        ProcessoDoc._isMigracao = ($('#stMigracao').val() == '1');
        ProcessoDoc.handleTaggleDisableBtnConcluir().trocaObrigatoriedade();

        var nuArtefato              = $('#nuArtefato'),
            optionsCheckbox2        = $('#optionsCheckbox2'),
            optionsCheckbox1        = $('#optionsCheckbox1'),
            chekProcedenciaInterno  = $('#chekProcedenciaInterno'),
            chekProcedenciaExterno  = $('#chekProcedenciaExterno'),
            nuArtefatoHidden        = $('#nuArtefatoHidden'),
            sqTipoDocumentoHidden   = parseInt($("#sqTipoDocumento_hidden").val());

        nuArtefato.data('originalValue',nuArtefato.val());

        chekProcedenciaInterno.bind('change', function () {
            ProcessoDoc.nuArtefato.init();
            if ($(this).is(':checked')) {
                if (ProcessoDoc._isMigracao) {
                    ProcessoDoc.nuArtefato.handlers.isMigracao();
                } else {
                    ProcessoDoc.nuArtefato.handlers.isInternoDefault();
                }

                if (nuArtefatoHidden.length){
                    nuArtefato.removeAttr('name').val(nuArtefato.data('originalValue'));
                    nuArtefatoHidden.attr('name', 'nuArtefato').val(nuArtefato.data('originalValue'));
                } else {
                    nuArtefato.val('');
                }
            }
        });

        chekProcedenciaExterno.bind('change', function () {
            ProcessoDoc.nuArtefato.init();
            if ($(this).is(':checked')) {
                if( ProcessoDoc._isMigracao ) {
                    ProcessoDoc.nuArtefato.handlers.isMigracao();
                } else {
                    ProcessoDoc.nuArtefato.handlers.isExternoDefault();
                    nuArtefatoHidden.attr('name', 'nuArtefatoHidden').val('');
                }
            }
        });

        //na inicialização da tela caso esteja selecionado a opção "Doc sem numero"
        //força a retirada da classe "required" do campo
        if (optionsCheckbox1.prop('checked') || chekProcedenciaInterno.prop('checked')) {
            nuArtefato.removeClass('required');
        }

        if (! ProcessoDoc._isMigracao) {
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
        }
    },

    trocaObrigatoriedade: function () {

        var nuArtefato       = $('#nuArtefato'),
            nuArtefatoHidden = $('#nuArtefatoHidden');

        $('input[name="numeracao"]').off().on('click', function () {
            var procedencia = $('input[name="procedenciaInterno"]:checked').val();
            var option  = $(this).val();
            var checked = $(this).is(':checked');

            if (ProcessoDoc._isMigracao) {
                $('#optionsCheckbox2').prop('disabled', 'disabled');

                if (checked) {
                    nuArtefato.removeAttr('class')
                              .removeAttr('name')
                              .attr('class', 'span4 inline')
                              .prop('disabled', 'disabled')
                              .val('');
                    nuArtefato.parents('.control-group').removeClass('error');
                    nuArtefato.siblings('p').hide();

                    nuArtefatoHidden.val('')
                                    .attr('name', 'nuArtefato');
                } else {
                    nuArtefato.val(nuArtefato.data('originalValue'));
                    nuArtefatoHidden.val(nuArtefato.data('originalValue'));

                    if (procedencia != 'interno') {
                        nuArtefato.removeProp('disabled');
                        nuArtefato.addClass('required');
                    }

                    //só libera o campo de numeração se não tiver valor original
                    if (! nuArtefato.data('originalValue')) {
                        nuArtefato.removeProp('disabled')
                                  .attr('name', 'nuArtefato');
                        nuArtefatoHidden.attr('name','nuArtefatoHidden');
                    }
                }
            } else {
                if (checked) {
                    nuArtefato.val('');
                    nuArtefato.removeAttr('class');
                    nuArtefato.attr('class', 'span4 inline');
                    nuArtefato.prop('disabled', 'disabled');
                    nuArtefato.parents('.control-group').removeClass('error');
                    nuArtefato.siblings('p').hide();

                    if (option === 'option1') {
                        $('#optionsCheckbox2').removeProp('checked');
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
                    if (procedencia === 'interno') {
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
            //tipo selecionado
            var sqTipoDocumento = parseInt(data[1]);
            var isProcedenciaExterna = (ProcessoDoc._disabledProcedencia == 'chekProcedenciaInterno');
            var objNoRespAssina = $('#noResponsavelAssinatura');
            var objSpanRequired = objNoRespAssina.parent('.controls')
                                                 .siblings('.control-label')
                                                 .find('span');
            if ((-1 !== $.inArray(sqTipoDocumento,ProcessoDoc._tpDocWithoutSignature)) || isProcedenciaExterna ) {
                objNoRespAssina.removeClass('required');
                objSpanRequired.hide();
            }else{
                objNoRespAssina.addClass('required');
                objSpanRequired.show();
            }
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
    nuArtefato: {
        vars: {
            ckSemNumero: null,
            ckNuAutomatico: null,
            inNuArtefato: null,
        },
        init: function(){        
            ProcessoDoc.nuArtefato.vars.ckSemNumero     = $("#optionsCheckbox1");
            ProcessoDoc.nuArtefato.vars.ckNuAutomatico  = $("#optionsCheckbox2");
            ProcessoDoc.nuArtefato.vars.inNuArtefato    = $("#nuArtefato");
        },
        handlers : {
            isMigracao : function(){
                ProcessoDoc.nuArtefato.vars.ckNuAutomatico.prop('disabled', 'disabled');
                ProcessoDoc.nuArtefato.vars.ckSemNumero.prop('disabled', 'disabled');

                // Sem número desmarcado
                if( ProcessoDoc.nuArtefato.vars.ckSemNumero.is(':checked') ) {
                    ProcessoDoc.nuArtefato.vars.ckSemNumero.attr('checked', false);
                }
                // Número automático marcado
                if( ProcessoDoc.nuArtefato.vars.ckNuAutomatico.is(':checked') ){
                    ProcessoDoc.nuArtefato.vars.ckNuAutomatico.attr('checked', false);
                }
                
                if( ProcessoDoc.nuArtefato.vars.inNuArtefato.val() != '' && !nuArtefato.vars.inNuArtefato.is(':disabled') ){
                    ProcessoDoc.nuArtefato.vars.inNuArtefato.attr('disabled', true).removeClass('required');
                } else {
                    ProcessoDoc.nuArtefato.vars.inNuArtefato.attr('disabled', false).addClass('required');
                }
            },
            isExternoDefault: function() {
                // Número automático desabilitado
                if( !ProcessoDoc.nuArtefato.vars.ckNuAutomatico.is(':disabled') ){
                    ProcessoDoc.nuArtefato.vars.ckNuAutomatico.attr('disabled', true);
                }
                // Número automático desmarcado
                if( ProcessoDoc.nuArtefato.vars.ckNuAutomatico.is(':checked') ){
                    ProcessoDoc.nuArtefato.vars.ckNuAutomatico.attr('checked', false);
                }
                // Sem número desabilitado
                if( ProcessoDoc.nuArtefato.vars.ckSemNumero.is(':disabled') ) {
                    ProcessoDoc.nuArtefato.vars.ckSemNumero.attr('disabled', false);
                }
                // Sem número desmarcado
                if( ProcessoDoc.nuArtefato.vars.ckSemNumero.is(':checked') ) {
                    ProcessoDoc.nuArtefato.vars.ckSemNumero.attr('checked', false);
                }
                // Campo número habilitado
                if( ProcessoDoc.nuArtefato.vars.inNuArtefato.is(':disabled') ){
                    ProcessoDoc.nuArtefato.vars.inNuArtefato.attr('disabled', false).addClass('required');
                }
            },
            isInternoDefault: function(){
                // Sem número desabilitado
                if( !ProcessoDoc.nuArtefato.vars.ckSemNumero.is(':disabled') ) {
                    ProcessoDoc.nuArtefato.vars.ckSemNumero.attr('disabled', true);
                }
                // Sem número desmarcado
                if( ProcessoDoc.nuArtefato.vars.ckSemNumero.is(':checked') ) {
                    ProcessoDoc.nuArtefato.vars.ckSemNumero.attr('checked', false);
                }    
                // Campo número desabilitado
                if (!ProcessoDoc.nuArtefato.vars.inNuArtefato.is(':disabled')) {
                    ProcessoDoc.nuArtefato.vars.inNuArtefato.attr('disabled', true).removeClass('required');
                } 
                
                if( ProcessoDoc.nuArtefato.vars.inNuArtefato.val() !=  "" ){
                    // Limpa número
                    ProcessoDoc.nuArtefato.vars.inNuArtefato.val("");
                    // Número automático habilitado
                    if( ProcessoDoc.nuArtefato.vars.ckNuAutomatico.is(':disabled') ){
                        ProcessoDoc.nuArtefato.vars.ckNuAutomatico.attr('disabled', false);
                    }
                    // Número automático marcado
                    if( !ProcessoDoc.nuArtefato.vars.ckNuAutomatico.is(':checked') ){
                        ProcessoDoc.nuArtefato.vars.ckNuAutomatico.attr('checked', true);
                    }
                // Se não volta a numeração antiga.
                } else {
                    ProcessoDoc.nuArtefato.vars.inNuArtefato.val(sessionStorage.getItem('nuArtefato'));
                    // Número automático habilitado
                    if( !ProcessoDoc.nuArtefato.vars.ckNuAutomatico.is(':disabled') ){
                        ProcessoDoc.nuArtefato.vars.ckNuAutomatico.attr('disabled', true);
                    }
                    // Número automático marcado
                    if( ProcessoDoc.nuArtefato.vars.ckNuAutomatico.is(':checked') ){
                        ProcessoDoc.nuArtefato.vars.ckNuAutomatico.attr('checked', false);
                    }
                }
            }
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
                divEdit: $('#divEdit').val(),
                stNumeroAutomatico: $("#optionsCheckbox2").is(":checked"),
                stSemNumero: $("#optionsCheckbox1").is(":checked"),
                stProcedencia: $("#chekProcedenciaInterno").is(":checked"),
            },
            function (data) {
                if (data.sucess == 'true') {
                    $.post('/artefato/documento/save/',
                            $('#form-cadastro-documento').serialize(),
                            function (result) {
                                if (result.error) {
                                    Message.waitClose();
                                    Message.show(result.errorType, result.msg);
                                } else {
                                    if (result == '') {
                                        var controllerBack = $("#controllerBack").val(),
                                            caixaBack = $("#caixa").val();
                                    
                                        if( controllerBack == undefined || controllerBack == '' ){
                                            controllerBack = 'area-trabalho';
                                        }
                                        if( caixaBack == undefined || caixaBack == ''  ){
                                            caixaBack = 'minhaCaixa';
                                        }
                                    
                                        if ($('#divEdit').val() == 0) {
                                            window.location = '/artefato/' + controllerBack + '/index/tipoArtefato/1/caixa/' + caixaBack;
                                        } else {
                                            switch ($('#redirect').val()) {
                                                case '2':
                                                    window.location = '/artefato/' + controllerBack + '/index/tipoArtefato/1/caixa/' + caixaBack;
                                                    break;
                                                case '3':
                                                    window.location = '/artefato/' + controllerBack + '/index/tipoArtefato/1/caixa/' + caixaBack;
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
                    var msgId = data.msg;
                    Message.waitClose();
                    Message.showAlert(eval('UI_MSG.' + msgId));
                }
            });
        }

        return false;
    });
});
