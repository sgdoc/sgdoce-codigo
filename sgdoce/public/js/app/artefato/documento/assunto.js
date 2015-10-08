var procedencia;
var tipoPessoaOrigem;
var sqPessoaOrigem;
var Assunto = {
    _tpDocWDuplicityVerification: [],
    _stMigracao: false,
    config:{
        cep: $('#cep')
    },

    validaDados: function( setEmpty ) {
        if( setEmpty == undefined || setEmpty != false ) {
            if( !Assunto._stMigracao ) {
                $('#noResponsavelAssinatura').val('');
                $('#noResponsavelAssinatura_hidden').val('');
                $('#noPessoaFuncaoAssinante').val('');
                $('#cb_PessoaFuncaoAssinante').val('');
            }
        }
        if ($("input[name='procedenciaInterno']:checked").val() == 'interno') {
            sessionStorage.setItem('procedencia','interna');
            sessionStorage.setItem('tipoPessoaOrigem',$('#sqTipoPessoaOrigemIcmbio').val());
            sessionStorage.setItem('sqPessoaOrigem',$('#sqPessoaIcmbio_hidden').val());
            sessionStorage.setItem('sqTipoDocumento',$('#sqTipoDocumento_hidden').val());
            sessionStorage.setItem('nuArtefato',$('#nuArtefato').val());

            $('#btnCadastraPessoa').hide();
            $('#divCargoResponsavelAssinatura').show();
            Assunto.carregaNoCargo(true);
        } else if ($("input[name='procedenciaInterno']:checked").val() == 'externo') {
            $('#btnCadastraPessoa').show();
            sessionStorage.setItem('procedencia','externa');
            sessionStorage.setItem('tipoPessoaOrigem',$('#sqTipoPessoaOrigem').val());
            sessionStorage.setItem('sqPessoaOrigem',$('#sqPessoaOrigem_hidden').val());
            sessionStorage.setItem('sqTipoDocumento',$('#sqTipoDocumento_hidden').val());
            sessionStorage.setItem('nuArtefato',$('#nuArtefato').val());
            $('#divCargoResponsavelAssinatura').hide();
            if ($('#sqTipoPessoaOrigem').val() == 5) {
                $('#btnCadastraPessoa').hide();
            }
            Assunto.carregaNoCargo(false);
        }
        Assunto.getSessionStorage();
        if( !Assunto._stMigracao ) {
            Assunto.autoComplete();
        }
    },

    getSessionStorage: function() {
        procedencia      = sessionStorage.getItem('procedencia');
        tipoPessoaOrigem = sessionStorage.getItem('tipoPessoaOrigem');
        sqPessoaOrigem   = sessionStorage.getItem('sqPessoaOrigem');
    },

    autoComplete: function(){
        //altera de 'noResponsavelAssinatura_autocomplete' para 'noResponsavelAssinatura' novamente
        //para aplicar o autocomplete novamente
        $('#noResponsavelAssinatura').unbind();
        $('#noResponsavelAssinatura').attr('name','noResponsavelAssinatura');
        $('#noResponsavelAssinatura').removeAttr('autocomplete');

        var url = procedencia == 'interna' ?
                 '/artefato/pessoa/search-pessoa-interna/tipoPessoa/'+tipoPessoaOrigem
                +'/sqPessoaOrigem/'+sqPessoaOrigem
                +'/procedencia/'+procedencia
            : '/artefato/pessoa/search-pessoa-externa/tipoPessoa/'+tipoPessoaOrigem
                +'/sqPessoaOrigem/'+sqPessoaOrigem;
        $('#noResponsavelAssinatura').simpleAutoComplete(url);
    },

    init: function(){
        Assunto.initCampos();
    },

    carregaNoCargo: function(combo){
        if (combo) {
            $('#cb_noPessoaFuncaoAssinante').removeProp('disabled').show();
            $('#noPessoaFuncaoAssinante').hide().prop('disabled','disabled');
        }else{

            $('#noPessoaFuncaoAssinante').removeProp('disabled');
            $('#cb_noPessoaFuncaoAssinante').hide().prop('disabled','disabled');
        }
    },

    loadPrioridade : function(selectedValue){
        if(selectedValue == ''){
            $('#sqTipoPrioridade').html('');
            $('#sqTipoPrioridade').html('<option value="">Selecione uma opção</option>');
            $('#sqTipoPrioridade').prop('disabled','disabled');
            return false;
        }
        if ($('#sqPrioridade').val()) {
            $.post('/artefato/documento/combo-descricao-prioridade', {
                    sqPrioridade: $('#sqPrioridade').val()
                },
                function(data){
                    Assunto.loadCombo(data, $('#sqTipoPrioridade'), selectedValue);
                    //variavel definida em form.js;
                    if (!ProcessoDoc._isSIC) {
                        $('#sqTipoPrioridade').removeProp('disabled');
                    }
                });
        }else{
            $('#sqTipoPrioridade').html('');
            $('#sqTipoPrioridade').html('<option value="">Selecione uma opção</option>');
            $('#sqTipoPrioridade').prop('disabled','disabled');
            return false;
        }
    },

    loadCombo: function(data, combo, selectedValue){
        var html = ['<option value="" title="Selecione uma opção">Selecione uma opção</option>'];
        var firstItem = true;
        $.each(data, function(index, value, i) {
            if (firstItem && !selectedValue) {
                firstItem = false;
                html.push('<option selected="selected" value="' + index + '" title="' + value + '">' + value + '</option>');
            }else{
                html.push('<option value="' + index + '" title="' + value + '">' + value + '</option>');
            }
        });
        combo.html(html.join(''));
        if (selectedValue) {
            combo.val(selectedValue).change();
        }
    },
    initCampos: function(){
        $('#nuDiasPrazo').attr('disabled', 'disabled');
        $('#inDiasCorridos').change(function(){
            $('#nuDiasPrazo').val('');
            if( $('#inDiasCorridos').val() == 0 ){
                $('#nuDiasPrazo').attr('disabled', 'disabled');
                $('.dvDataPrazo').removeClass('required');
            } else {
                $('#nuDiasPrazo').removeAttr('disabled');
                $('.dvDataPrazo').addClass('required');
            }
        });

        if($('#sqPrazo').val() == 1) {
            $('.dvDataPrazo').show();
            $('#dtPrazo').addClass('required');

            $('.dvDiasPrazo').hide();
            $('#inDiasCorridos').removeClass('required');
            $('#nuDiasPrazo').removeClass('required');
        }

        if($('#sqPrazo').val() == 2){
            $('.dvDataPrazo').hide();
            $('#dtPrazo').removeClass('required');

            $('.dvDiasPrazo').show();
            $('#inDiasCorridos').addClass('required');
            $('#nuDiasPrazo').addClass('required');
        }

        $('#sqPrazo').change(function(){

            $('#inDiasCorridos').val('');
            $('#nuDiasPrazo').val('');
            $('#dtPrazo').val('');

            if($('#sqPrazo').val() == 1) {
                $('.dvDataPrazo').show();
                $('#dtPrazo').addClass('required');

                $('.dvDiasPrazo').hide();
                $('#inDiasCorridos').removeClass('required');
                $('#nuDiasPrazo').removeClass('required');
            } else if($('#sqPrazo').val() == 2){
                $('.dvDataPrazo').hide();
                $('#dtPrazo').removeClass('required');

                $('.dvDiasPrazo').show();
                $('#inDiasCorridos').addClass('required');
                $('#nuDiasPrazo').addClass('required');
            } else {
                $('.dvDiasPrazo').hide();
                $('.dvDataPrazo').hide();
            }
        });

        $('#sqAssunto').simpleAutoComplete("/auxiliar/assunto/searchassunto", {
            extraParamFromInput: '#sqAssunto',
            attrCallBack: 'rel',
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel'
        });

        $('#sqPessoaRecebimento').simpleAutoComplete("/artefato/documento/search-pessoa-unidade", {
            extraParamFromInput: '#sqPessoaRecebimento',
            attrCallBack: 'rel',
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel'
        });
    },

    initAssinatura: function() {
        if ($("input[name='procedenciaInterno']:checked").val() == 'interno') {
            sessionStorage.setItem('procedencia','interna');
            sessionStorage.setItem('tipoPessoaOrigem',$('#sqTipoPessoaOrigemIcmbio').val());
            sessionStorage.setItem('sqPessoaOrigem',$('#sqPessoaIcmbio_hidden').val());
            sessionStorage.setItem('sqTipoDocumento',$('#sqTipoDocumento_hidden').val());
            sessionStorage.setItem('nuArtefato',$('#nuArtefato').val());
            $('#btnCadastraPessoa').hide();
            $('#divCargoResponsavelAssinatura').show();
            $('#noPessoaFuncaoAssinante').prop('disabled','disabled');
        } else if ($("input[name='procedenciaInterno']:checked").val() == 'externo') {
            $('#btnCadastraPessoa').show();
            sessionStorage.setItem('procedencia','externa');
            sessionStorage.setItem('tipoPessoaOrigem',$('#sqTipoPessoaOrigem').val());
            sessionStorage.setItem('sqPessoaOrigem',$('#sqPessoaOrigem_hidden').val());
            sessionStorage.setItem('sqTipoDocumento',$('#sqTipoDocumento_hidden').val());
            sessionStorage.setItem('nuArtefato',$('#nuArtefato').val());
            $('#divCargoResponsavelAssinatura').hide();
            if ($('#sqTipoPessoaOrigem').val() == 5) {
                $('#btnCadastraPessoa').hide();
            }
        }
    },
    verificaDuplicidade:function() {
        var sqOrigem = $('#sqPessoaOrigem_hidden').val();
        if($('#sqPessoaIcmbio_hidden').val()){
            sqOrigem = $('#sqPessoaIcmbio_hidden').val();
        }
        var params = {
            sqArtefato: $('#sqArtefato').val(),
            tipo: sessionStorage.getItem('sqTipoDocumento'),
            numero: sessionStorage.getItem('nuArtefato'),
            origem: sqOrigem
        };

        if ((sessionStorage.getItem('nuArtefato') && $('#sqPessoaIcmbio_hidden').val()
            || sessionStorage.getItem('sqTipoDocumento') && $('#sqPessoaOrigem_hidden').val())) {
            $.post('artefato/documento/verifica-duplicidade',params,function(data){
                if (data.success) {
                    var sqPessoa = $('#sqPessoaIcmbio').val();
                    if($('#sqPessoaOrigem').val()){
                        var sqPessoa = $('#sqPessoaOrigem').val();
                    }
                    $('#sqPessoaOrigem').val('');
                    $('#md-tipo').val($('#sqTipoDocumento').val());
                    $('#md-origem').val(sqPessoa);
                    $('#md-numero').val($('#nuArtefato').val());
                    $('#modalDuplicidade').modal();
                    $('#btnProximo,.btn-concluir').attr('disabled',true);
                } else {
                    $('#btnProximo,.btn-concluir').removeAttr('disabled');
                }
            });
        }
    },
    nuArtefato: {
        vars: {
            ckSemNumero: null,
            ckNuAutomatico: null,
            inNuArtefato: null,
        },
        init: function(){        
            Assunto.nuArtefato.vars.ckSemNumero     = $("#optionsCheckbox1");
            Assunto.nuArtefato.vars.ckNuAutomatico  = $("#optionsCheckbox2");
            Assunto.nuArtefato.vars.inNuArtefato    = $("#nuArtefato");
        },
        handlers : {
            isMigracao : function(){
                Assunto.nuArtefato.vars.ckNuAutomatico.prop('disabled', 'disabled');
                Assunto.nuArtefato.vars.ckSemNumero.prop('disabled', 'disabled');

                // Sem número desmarcado
                if( Assunto.nuArtefato.vars.ckSemNumero.is(':checked') ) {
                    Assunto.nuArtefato.vars.ckSemNumero.attr('checked', false);
                }
                // Número automático marcado
                if( Assunto.nuArtefato.vars.ckNuAutomatico.is(':checked') ){
                    Assunto.nuArtefato.vars.ckNuAutomatico.attr('checked', false);
                }
                
                if( Assunto.nuArtefato.vars.inNuArtefato.val() != '' && !Assunto.nuArtefato.vars.inNuArtefato.is(':disabled') ){
                    Assunto.nuArtefato.vars.inNuArtefato.attr('disabled', true).removeClass('required');
                } else {
                    Assunto.nuArtefato.vars.inNuArtefato.attr('disabled', false).addClass('required');
                }
            },
            isInternoOficial: function( isTipoOuOrigemChange ) {
                if( isTipoOuOrigemChange != true ){
                    isTipoOuOrigemChange = false;
                }
                // Sem número desabilitado
                if( !Assunto.nuArtefato.vars.ckSemNumero.is(':disabled') ) {
                    Assunto.nuArtefato.vars.ckSemNumero.attr('disabled', true);
                }
                // Sem número desmarcado
                if( Assunto.nuArtefato.vars.ckSemNumero.is(':checked') ) {
                    Assunto.nuArtefato.vars.ckSemNumero.attr('checked', false);
                }
                // Se o Documento for alterado no tipo ou origem limpa númeração.
                if( isTipoOuOrigemChange ){
                    // Limpa número
                    Assunto.nuArtefato.vars.inNuArtefato.val("");
                    // Número automático habilitado
                    if( Assunto.nuArtefato.vars.ckNuAutomatico.is(':disabled') ){
                        Assunto.nuArtefato.vars.ckNuAutomatico.attr('disabled', false);
                    }
                    // Número automático marcado
                    if( !Assunto.nuArtefato.vars.ckNuAutomatico.is(':checked') ){
                        Assunto.nuArtefato.vars.ckNuAutomatico.attr('checked', true);
                    }
                // Se não volta a numeração antiga.
                } else {
                    Assunto.nuArtefato.vars.inNuArtefato.val(sessionStorage.getItem('nuArtefato'));
                    // Número automático habilitado
                    if( !Assunto.nuArtefato.vars.ckNuAutomatico.is(':disabled') ){
                        Assunto.nuArtefato.vars.ckNuAutomatico.attr('disabled', true);
                    }
                    // Número automático marcado
                    if( Assunto.nuArtefato.vars.ckNuAutomatico.is(':checked') ){
                        Assunto.nuArtefato.vars.ckNuAutomatico.attr('checked', false);
                    }
                }

                if (!Assunto.nuArtefato.vars.inNuArtefato.is(':disabled')) {
                    Assunto.nuArtefato.vars.inNuArtefato.attr('disabled', true).removeClass('required');
                } 
            },
            isInternoNaoOficial: function( isTipoOuOrigemChange ){
                if( isTipoOuOrigemChange != true ){
                    isTipoOuOrigemChange = false;
                }
                // Número automático habilitado
                if( Assunto.nuArtefato.vars.ckNuAutomatico.is(':disabled') ){
                    Assunto.nuArtefato.vars.ckNuAutomatico.attr('disabled', false);
                }
                // Número automático marcado
                if( !Assunto.nuArtefato.vars.ckNuAutomatico.is(':checked') ){
                    Assunto.nuArtefato.vars.ckNuAutomatico.attr('checked', true);
                }
                // Sem número desabilitado
                if( Assunto.nuArtefato.vars.ckSemNumero.is(':disabled') ) {
                    Assunto.nuArtefato.vars.ckSemNumero.attr('disabled', false);
                }
                // Sem número desmarcado
                if( Assunto.nuArtefato.vars.ckSemNumero.is(':checked') ) {
                    Assunto.nuArtefato.vars.ckSemNumero.attr('checked', false);
                }
                // Se o Documento for alterado no tipo ou origem limpa númeração.
                if( isTipoOuOrigemChange ){
                    // Limpa número
                    Assunto.nuArtefato.vars.inNuArtefato.val("");
                // Se não volta a numeração antiga.
                } else {
                    Assunto.nuArtefato.vars.inNuArtefato.val(sessionStorage.getItem('nuArtefato'));
                }

                if (!Assunto.nuArtefato.vars.inNuArtefato.is(':disabled')) {
                    Assunto.nuArtefato.vars.inNuArtefato.attr('disabled', true).removeClass('required');
                } 
            },
            isExternoDefault: function() {
                // Número automático desabilitado
                if( !Assunto.nuArtefato.vars.ckNuAutomatico.is(':disabled') ){
                    Assunto.nuArtefato.vars.ckNuAutomatico.attr('disabled', true);
                }
                // Número automático desmarcado
                if( Assunto.nuArtefato.vars.ckNuAutomatico.is(':checked') ){
                    Assunto.nuArtefato.vars.ckNuAutomatico.attr('checked', false);
                }
                // Sem número desabilitado
                if( Assunto.nuArtefato.vars.ckSemNumero.is(':disabled') ) {
                    Assunto.nuArtefato.vars.ckSemNumero.attr('disabled', false);
                }
                // Sem número desmarcado
                if( Assunto.nuArtefato.vars.ckSemNumero.is(':checked') ) {
                    Assunto.nuArtefato.vars.ckSemNumero.attr('checked', false);
                }
                // Campo número habilitado
                if( Assunto.nuArtefato.vars.inNuArtefato.is(':disabled') ){
                    Assunto.nuArtefato.vars.inNuArtefato.attr('disabled', false).addClass('required');
                }
            },
            isInternoDefault: function(){
                // Sem número desabilitado
                if( !Assunto.nuArtefato.vars.ckSemNumero.is(':disabled') ) {
                    Assunto.nuArtefato.vars.ckSemNumero.attr('disabled', true);
                }
                // Sem número desmarcado
                if( Assunto.nuArtefato.vars.ckSemNumero.is(':checked') ) {
                    Assunto.nuArtefato.vars.ckSemNumero.attr('checked', false);
                }    
                // Campo número desabilitado
                if (!Assunto.nuArtefato.vars.inNuArtefato.is(':disabled')) {
                    Assunto.nuArtefato.vars.inNuArtefato.attr('disabled', true).removeClass('required');
                } 
                
                if( Assunto.nuArtefato.vars.inNuArtefato.val() !=  "" ){
                    // Limpa número
                    Assunto.nuArtefato.vars.inNuArtefato.val("");
                    // Número automático habilitado
                    if( Assunto.nuArtefato.vars.ckNuAutomatico.is(':disabled') ){
                        Assunto.nuArtefato.vars.ckNuAutomatico.attr('disabled', false);
                    }
                    // Número automático marcado
                    if( !Assunto.nuArtefato.vars.ckNuAutomatico.is(':checked') ){
                        Assunto.nuArtefato.vars.ckNuAutomatico.attr('checked', true);
                    }
                // Se não volta a numeração antiga.
                } else {
                    Assunto.nuArtefato.vars.inNuArtefato.val(sessionStorage.getItem('nuArtefato'));
                    // Número automático habilitado
                    if( !Assunto.nuArtefato.vars.ckNuAutomatico.is(':disabled') ){
                        Assunto.nuArtefato.vars.ckNuAutomatico.attr('disabled', true);
                    }
                    // Número automático marcado
                    if( Assunto.nuArtefato.vars.ckNuAutomatico.is(':checked') ){
                        Assunto.nuArtefato.vars.ckNuAutomatico.attr('checked', false);
                    }
                }
            }
        }
    }
};
// Dependente do form.js
$(document).ready(function(){
    $('#btnFecharDuplicidade').click(function(){
        $('#sqPessoaIcmbio_hidden').val('');
        $('#sqPessoaIcmbio').val('');
        $('#modalDuplicidade').modal('hide');
    });

    $("#sqTipoDocumento,#sqPessoaIcmbio,#sqPessoaOrigem").blur(function(){
        var sqTipoDocumentoHidden = parseInt($("#sqTipoDocumento_hidden").val());
        var nuArtefato   = $("#nuArtefato");
        var sqPessoaOrigem = ( $('#sqPessoaOrigem_hidden').val() != "" ) ? $('#sqPessoaOrigem_hidden').val() : $('#sqPessoaIcmbio_hidden').val(),
            isTipoOuOrigemChange = false;
        nuArtefato.addClass('required');
        // Se documento for alterado o tipo ou origem
        if( ProcessoDoc._sqTipoDocumentoOriginal != $("#sqTipoDocumento_hidden").val()
            || ProcessoDoc._sqPessoaOrigemOriginal != sqPessoaOrigem  ) { 
            isTipoOuOrigemChange = true;
            //se for migração não pode ter numeração automática
            //pois a migração pode ser de anos anteriores
            if ( ProcessoDoc._isMigracao ) {                
                Assunto.nuArtefato.handlers.isMigracao();
            } else  {
                if( $("#chekProcedenciaInterno").prop('checked') ) {
                    if( $.inArray(sqTipoDocumentoHidden, Assunto._tpDocWDuplicityVerification) > -1 ) {                   
                        Assunto.nuArtefato.handlers.isInternoOficial(isTipoOuOrigemChange);
                    } else {                                        
                        Assunto.nuArtefato.handlers.isInternoNaoOficial(isTipoOuOrigemChange);
                    }
                } else if( $("#chekProcedenciaExterno").prop('checked') ) {
                    Assunto.nuArtefato.handlers.isExternoDefault(isTipoOuOrigemChange);
                }
            }
        } else {        
            if( $("#chekProcedenciaInterno").prop('checked') ) {    
                Assunto.nuArtefato.handlers.isInternoDefault();                  
            } else  if( $("#chekProcedenciaExterno").prop('checked') ) {
                Assunto.nuArtefato.handlers.isExternoDefault();
            }
            // Preenche novamente
            nuArtefato.val(ProcessoDoc._nuArtefatoOriginal);
        }
    });

    Assunto.initAssinatura();    
    Assunto.nuArtefato.init();
});