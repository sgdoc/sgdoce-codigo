ModalAssinatura = {           

    initModalDestinatario: function(){

        $('#sqTipoMotivacao').val('1');
        $('#sqTipoMotivacao').change(function(){
            if($('#sqTipoMotivacao').val() != '1'){
                $('.deMotivacao').attr('disabled', 'disabled');
            }else{
                $('.deMotivacao').removeAttr('disabled');
            }
        });    
        $('.deMotivacao').blur(function(){
            $('#sqTipoMotivacao').attr('disabled', 'disabled');
            if( $('.deMotivacao').val() == ''){
                $('#sqTipoMotivacao').removeAttr('disabled');
            }
        });    
    },
    
    concluirAssinatura: function(){
        $('.btnConcluirAssinatura').click(function(){
            if($('#form-assinatura-modal').valid()){
                var arrDados = AssinaturaUnica.validaDados($('#sqArtefato').val()
                         ,$('#sqPessoaAssinatura_hidden').val()
                          ,6);

                if(arrDados == 'true'){
                    Message.showAlert('O usuário informado já foi incluido.');
                    return false;
                }else{
                    ModalAssinatura.formAssinatura();
                }

            }else{
                   $('.campos-obrigatorios').remove();
                return false;
            }

        });
    },
    
    tipoOrigem: function(){
        $('#btnCadastrarPessoa').hide();
        $('#sqPessoaDestinatario').val('');
        $('#nuCPF').val('');            
        $('#coCep').val('');
        $('#txEndereco').val('');
        $('#sqEstadoDestinatario').val('');
        $('#sqMunicipioDestinatario').val('');
        $('#sqMunicipioDestinatario_hidden').val('');
        var all = $('.dvGeralNome, .divGeralCPF, .divOutrosDados');
        $(all).hide();
        switch ($('#sqTipoPessoa').val()) {
        case '1':
            $(all).show();
            $('#dvLabelNome').text('* Nome');
            $('#dvLabelCPF').text('CPF');
            break;
        case '2':
            $(all).show();
            $('#dvLabelNome').text('* Razão Social');
            $('#dvLabelCPF').text('CNPJ');
            break;
        case '3':
            $(all).show();
            $('#dvLabelNome').text('* Nome');
            $('#dvLabelCPF').text('Nº do Passaporte');
            break;
        case '4':
            $(all).show();
            $('.divGeralCPF').hide();
            $('#dvLabelNome').text('* Unidade do Ministério Público');
            break;
        case '5':
            $(all).show();
            $('.divGeralCPF').hide();
            $('#dvLabelNome').text('* Unidade de Outros Órgãos');
            break;
        } 
    }, 
    
    formAssinatura: function (){
        var sqTipoMotivacao = $('#sqTipoMotivacao option:selected').val();
    	if(!$('#sqTipoMotivacao').is(':visible'))
		{
    		sqTipoMotivacao = '';
		}
        $.post('/artefato/minuta-eletronica/add-destinatario-artefato', {
            tipoPessoaAba: '3'
            ,sqArtefato: $('#sqArtefato').val()
            ,sqTipoUnidadeOrg : $('#sqTipoUnidadeOrgg_hidden').val()
            ,noTipoUnidadeOrg : $('#sqTipoUnidadeOrgg').val()    
            ,sqPessoaUnidadeOrgCorp : $('#sqUnidadeOrgg_hidden').val()
            ,noUnidadeOrg : $('#sqUnidadeOrgg').val()    
            ,sqPessoaCorporativo: $('#sqPessoaAssinatura_hidden').val()
            ,noPessoa     : $('#sqPessoaAssinatura').val()
            ,sqTipoMotivacao : sqTipoMotivacao
            ,deMotivacao: $('#deMotivacao').val()
            ,sqEnderecoSgdoce : ''
            ,sqTratamentoVocativo : ''
    	    ,nuCpfCnpjPassaporte : ''
    	    ,checkCorporativo : '1'
    	    ,sqTipoPessoa : '1'
        }, 
        function(data){
            $('#sqTipoMotivacao').val('');
            ModalAssinatura.tipoOrigem();
            $('#table-assinatura').dataTable().fnDraw(false);
            console.log(data);
            if (data === 'true') {
                Message.showAlert('Operação realizada com sucesso.');
            }
        });
    },
    
    initCampo: function (){
        var interessado  =  false;
        var rodape          = false;
        var tratamento      = false;
        var params = {
                sqTipoDocumento : $('#sqTipoDocumento').val() , 
                sqAssunto : $('#sqAssunto').val(),
                sqModeloDocumento : $('#sqModeloDocumento').val()
            };
            var arrCampoModeloDocumento = MinutaPasso.getCampoModeloDocumento(params);
            var j = $.parseJSON(arrCampoModeloDocumento);

            if(j.length > 0){
                $.each(j, function(i) {
                    if (j[i].noCampo == 'Interessado'){
                        interessado = true;
                        $('#botao_aba7').show();                        
                    }
                    
                    if (j[i].noCampo == 'Tratamento'){
                        tratamento = true;
                    }
                    
                    $('.dv'+j[i].sqGrupoCampo+'-'+j[i].noColunaTabela).show();                    
                    if (j[i].inObrigatorio) {
                        $('#'+j[i].noColunaTabela).addClass('required');
                    }
                    tipo[j[i].sqGrupoCampo] = 1;
                });
            }
            
            if (!tratamento){
                $('.tpDestinatario').show();
            }
            
            for (i in tipo) {
                $('#botao_aba' + i).show();
            }
    },
    init: function(){
        ModalAssinatura.initModalDestinatario();
        ModalAssinatura.concluirAssinatura();
        ModalAssinatura.initCampo();
        AssinaturaUnica.initResponsavelAssinatura();
        Search.initSearch();
    }
}

AssinaturaUnica = {           
        
    initResponsavelAssinatura: function(){

        $('#sqResponsavel').simpleAutoComplete("/artefato/pessoa/search-pessoa", {
            extraParamFromInput: '#extra',
            attrCallBack: 'rel',
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel'
        });
        

       	$('input[name=sqTipoAssinante]').click(function(){
            var arrDados = AssinaturaUnica.getDados($(this).val());
            AssinaturaUnica.populatePessoaAssinatura(arrDados);
        });   
    },
    
    getDados: function(sqTipoPessoa){
        var result = $.ajax({
            type: 'post',
            url: '/artefato/pessoa/get-pessoa-assinatura',
            data: 'sqTipoPessoa='+sqTipoPessoa,
            async: false,
            global: false
            }).responseText;
        return result;
    },  
    
    validaDados: function(sqArtefato,sqPessoaCorporativo,sqPessoaFuncao){
        var result = $.ajax({
            type: 'post',
            url: '/artefato/pessoa/valida-pessoa-assinatura',
            data: 'sqArtefato='+sqArtefato+'&sqPessoaCorporativo='+sqPessoaCorporativo+'&sqPessoaFuncao='+sqPessoaFuncao,
            async: false,
            global: false
            }).responseText;
        return result;
    },  
    
    populatePessoaAssinatura: function(data){        
        
        var j = $.parseJSON(data);

          if(j.length > 0){
            $.each(j, function(i) {
              $('#sqResponsavel_hidden').val(j[i].sqPessoa); 
              $('#sqResponsavel').val(j[i].noPessoa);                
              $('#noProfissao').val(j[i].noCargo);
              $('#sqSetorResponsavel').val(j[i].noUnidadeOrg);
            })
        } else {
            Message.showAlert('Nenhum resultado encontrado!');
            $('#sqResponsavel_hidden').val(''); 
            $('#sqResponsavel').val('');                
            $('#noProfissao').val('');
            $('#sqSetorResponsavel').val('');
        }
    },     
}

Search = {
        
        initSearch: function(){
            $('#sqTipoUnidadeOrgg').simpleAutoComplete("auxiliar/tipo-unidade-org/search-tipo-unidade-org/", {
                extraParamFromInput: '#extra',
                attrCallBack: 'id',
                autoCompleteClassName: 'autocomplete',
                selectedClassName: 'sel'
            },function(){
                $('#sqUnidadeOrgg').val('');
                $('#sqPessoaAssinatura').val('');
            });
        
            $('#sqUnidadeOrgg').simpleAutoComplete("auxiliar/tipo-unidade-org/search-unidade-org/", {
                extraParamFromInput: '#sqTipoUnidadeOrgg_hidden',
                attrCallBack: 'id',
                autoCompleteClassName: 'autocomplete',
                selectedClassName: 'sel'
            },function(){
                $('#sqPessoaAssinatura').val('');
            });
            
            $('#sqPessoaDestInternoo').simpleAutoComplete("auxiliar/tipo-unidade-org/search-pessoa/", {
                extraParamFromInput: '#sqUnidadeOrgg_hidden',
                attrCallBack: 'rel',
                autoCompleteClassName: 'autocomplete',
                selectedClassName: 'sel'
            });
            
            $('#sqPessoaAssinatura').simpleAutoComplete("auxiliar/tipo-unidade-org/search-pessoa/", {
                extraParamFromInput: '#sqUnidadeOrgg_hidden',
                attrCallBack: 'rel',
                autoCompleteClassName: 'autocomplete',
                selectedClassName: 'sel'
            });
        }
}

$(document).ready(function(){
    ModalAssinatura.init();
    loadJs('js/components/modal.js', function() {
        Menu.init();
    }); // load cdn
});
