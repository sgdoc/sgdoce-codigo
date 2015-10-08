EncaminharMinutaAnalise = {

        searchUnidadeOrg: function() {
            $("#btEncaminharAnalise").attr('disabled', '');
            $("#btEncaminharAssinatura").attr('disabled', '');
            
            $('#sqUnidadeOrg').simpleAutoComplete("/artefato/visualizar-caixa-minuta/search-unidade-orgs/sq-unidade-org/" + $("#sqUnidadeOrg").val(), {
                extraParamFromInput: '#noPessoa_hidden',
                attrCallBack: 'id',
                autoCompleteClassName: 'autocomplete'
            }, function() {
            	if(parseInt($('#sqUnidadeOrg_hidden').val()) && parseInt($('#noPessoa_hidden').val())) {
                    $("#btEncaminharAnalise").removeAttr('disabled');
                    $("#btEncaminharAssinatura").removeAttr('disabled');
            	}
            });

            $('#sqUnidadeOrg').click(function(){
                 $("#sqUnidadeOrg").val('');
            });

            $("#noPessoa").focus(function(){
                if($("#sqUnidadeOrg").val() != '' ){
                    $("#noPessoa").removeAttr('readonly');
                };
            });

        },
        
        searchPessoa: function() {
            $('#noPessoa').simpleAutoComplete(
        		"/artefato/visualizar-caixa-minuta/search-pessoas/sqUnidadeOrg/" + $("#sqUnidadeOrg_hidden").val() + 
        		'/inAssinatura/' +  $("#inAssinatura").val() + '/sqArtefato/' + $('#sqArtefatoAssinatura').val(),
    		{
                extraParamFromInput: '#sqUnidadeOrg_hidden',
                attrCallBack: 'id',
                autoCompleteClassName: 'autocomplete'
            }, function(){
            	if(parseInt($('#sqUnidadeOrg_hidden').val()) && parseInt($('#noPessoa_hidden').val())) {
                    $("#btEncaminharAnalise").removeAttr('disabled');
                    $("#btEncaminharAssinatura").removeAttr('disabled');
            	}
            });

            $('#noPessoa').click(function(){
                $("#noPessoa").val('');
            	$("#btEncaminharAnalise").attr('disabled', '');
                $("#btEncaminharAssinatura").attr('disabled', '');
            });

            $("#btEncaminharAnalise").click(function(){
                if(!parseInt($("#sqUnidadeOrg_hidden").val()) || !parseInt($("#noPessoa_hidden").val())) {
                    return false;
                }
            });

            $("#btEncaminharAssinatura").click(function(){
                if(!parseInt($("#sqUnidadeOrg_hidden").val()) || !parseInt($("#noPessoa_hidden").val())) {
                    return false;
                }
            });
        },
        
        init: function() {
            EncaminharMinutaAnalise.searchUnidadeOrg();
            EncaminharMinutaAnalise.searchPessoa();
            
            $('#sqUnidadeOrg, #noPessoa').off('blur').on('blur', function() {
            	if(!parseInt($('#sqUnidadeOrg_hidden').val()) || !parseInt($('#noPessoa_hidden').val())) {
                    $("#btEncaminharAnalise").attr('disabled', true);
                    $("#btEncaminharAssinatura").attr('disabled', true);
            	}
            });
        }
}

$(document).ready(function() {
    EncaminharMinutaAnalise.init();
});