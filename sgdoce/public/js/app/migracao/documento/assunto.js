var procedencia;
var tipoPessoaOrigem;
var sqPessoaOrigem;
var Assunto = {
    _tpDocWDuplicityVerification: [],
    
    config:{
        cep: $('#cep')
    },

    validaDados: function(){
        $('#noResponsavelAssinatura').val('');
        $('#noResponsavelAssinatura_hidden').val('');
        $('#noPessoaFuncaoAssinante').val('');
        $('#cb_PessoaFuncaoAssinante').val('');
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

        Assunto.autoComplete();
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
                 '/migracao/vinculo/search-pessoa-fisica' +'/procedencia/'+procedencia
            : '/artefato/pessoa/search-pessoa-externa/tipoPessoa/' + tipoPessoaOrigem;
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
    
    handleDisableSignature: function() {
        $("#noResponsavelAssinatura").removeClass('required');
        $("label[for='noResponsavelAssinatura']").find('span').remove();
    },    
    handleEnableSignature: function(){
        $("#noResponsavelAssinatura").addClass('required');
        if( $("label[for='noResponsavelAssinatura']").find('span').length <= 0 ) {
            $("label[for='noResponsavelAssinatura']").prepend($('<span>').addClass('required').attr('id', 'spanRequiredSignature').html('*'));
        }
    }
};

$(document).ready(function(){
    $('#sqPessoaIcmbio, #sqPessoaOrigem, #sqTipoPessoaOrigem, #nuCNPJOrigem, #nuCPFOrigem').blur(function(){
        var sqTipoDocumento = parseInt($("#sqTipoDocumento_hidden").val()),
            checkDuplicity = false,
            optionsCheckbox2 = $("#optionsCheckbox2");
            
        if( $("#chekProcedenciaInterno").prop('checked') && $.inArray(sqTipoDocumento, Assunto._tpDocWDuplicityVerification) > -1 ) {
            checkDuplicity = true;            
        }
        
        if( optionsCheckbox2.prop('checked') ) {
            checkDuplicity = true;
        }        
        
        if( $("#chekProcedenciaExterno").prop('checked') ) {
            checkDuplicity = false;
        }
        
        if( checkDuplicity ) {
            setTimeout(function(){Assunto.verificaDuplicidade()}, 500);
        }
    });
    
    $("#sqTipoDocumento").blur(function(){
        var sqTipoDocumentoHidden = parseInt($("#sqTipoDocumento_hidden").val());
        if( $("#chekProcedenciaInterno").prop('checked') && $.inArray(sqTipoDocumentoHidden, Assunto._tpDocWDuplicityVerification) > -1 ) {            
            $("#optionsCheckbox1").attr('disabled', true).parent('label').addClass('hidden').hide();
            if( !$("#optionsCheckbox2").is(":checked") ) {
                $("#optionsCheckbox2").attr('checked', true);
            }
        } else {
            if( $("#optionsCheckbox1").parent('label').hasClass('hidden') ) {
                $("#optionsCheckbox1").attr('disabled', false).parent('label').removeClass('hidden').show();
            }
        }
    });
    
    $('#btnFecharDuplicidade').click(function(){
        $('#sqPessoaIcmbio_hidden').val('');
        $('#sqPessoaIcmbio').val('');
        $('#modalDuplicidade').modal('hide');
    });

    Assunto.initAssinatura();
});