var DemandaInformacao = {
    Form : {
        Events: function(){            
            var options = {
                format: 'dd/mm/yyyy',
                language: 'pt-BR',
                autoclose: true
            };

            $('.date').datepicker(options);
                        
            $(".btnConcluir").click(function(){
                var form = $(".modal-body").find('form');
                if( form.valid() ) { 
                    form.submit();
                    return false;
                }                 
            });
            
            $(".sqTipoArtefato").on('change', function(){
                $("#sqArtefatoResposta").val("");
            });
            
        },        
        Gerar : function() {
            $('.form-di-gerar #sqUnidadeOrgPessoaDestino').simpleAutoComplete("/artefato/pessoa/search-unidade-interna/", {                
                attrCallBack: 'rel',
                autoCompleteClassName: 'autocomplete',
                selectedClassName: 'sel'
            }, function(data){    
                $('#sqPessoaDestino').unbind();                
                var sqTipoPessoaOrigem = $("#sqTipoPessoaOrigem").val(),
                    sqPessoaOrigem = $("input[name='sqUnidadeOrgPessoaDestino']").val(),
                    url = '/artefato/pessoa/search-pessoa-interna/tipoPessoa/' + sqTipoPessoaOrigem
                        + '/sqPessoaOrigem/' + sqPessoaOrigem
                        + '/procedencia/interna';
                
                $('#sqPessoaDestino').removeAttr('disabled');
                $('#sqPessoaDestino').simpleAutoComplete(url);                
            });
            
            $(".form-di-gerar #sqArtefatoResposta").simpleAutoComplete("/artefato/pessoa/search-unidade-interna/", {                
                attrCallBack: 'rel',
                autoCompleteClassName: 'autocomplete',
                selectedClassName: 'sel'
            });
        },        
        Resposta : function() {            
            $(".form-di-resposta .stDocumentoResposta").click(function(){
                var stDocumentoResposta = $(this).val();
                
                if( stDocumentoResposta == true ) {
                    $("#sqArtefatoRespostaDiv").removeClass('hide').show();
                    $("#sqArtefatoResposta").addClass('required');
                } else {
                    $("#sqArtefatoRespostaDiv").addClass('hide').hide();
                    $("#sqArtefatoResposta").removeClass('required');
                }
            });
                        
            $('#sqArtefatoResposta').simpleAutoComplete("/artefato/demanda-informacao/find-artefato-resposta/sqArtefatoPrazo/" + $("#sqArtefato").val(), {
                extraParamFromInput: '#sqTipoArtefato',
                autoCompleteClassName: 'autocomplete',
                selectedClassName: 'sel'
            });
            
            $(".sqTipoArtefato").on('click', function(){
                var sqTipoArtefatoChecked = $(".sqTipoArtefato:checked").val();                
                $("#sqTipoArtefato").val(sqTipoArtefatoChecked);
            });
        },
    }
}