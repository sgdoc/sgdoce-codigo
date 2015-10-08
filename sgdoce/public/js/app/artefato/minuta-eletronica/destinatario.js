ModalMinuta = {

	initVisualizaPessoa : function () {

    	$('#alterarPF,#alterarPJ,#visualizarPFDest,#visualizarPJDest').parent().addClass('disabled');
    	$('#sqPessoaDestinatarioPf,#sqPessoaDestinatario,#nuCPF').blur(function(){
	    	if($('#sqPessoaDestinatarioPf').val() || $('#nuCPF').val())
	    	{
	            $('#alterarPF').attr('href','/auxiliar/pessoa-fisica/edit/id/' + $('#sqPessoaDestinatarioPf_hidden').val()+'/form/form-destinatario-modal/campoPessoa/sqPessoaDestinatarioPf/campoCpf/nuCPF');
	    		$('#alterarPF,#visualizarPFDest').parent().removeClass('disabled');

	    	}else{
	    		$('#alterarPF,#visualizarPFDest').parent().addClass('disabled');
	    	}
	    	if($('#sqPessoaDestinatario').val() || $('#nuCPF').val())
	    	{
	            $('#alterarPJ').attr('href','/auxiliar/pessoa-juridica/edit/id/' + $('#sqPessoaDestinatario_hidden').val()+'/form/form-destinatario-modal/campoPessoa/sqPessoaDestinatarioPf/campoCnpj/nuCnpj');
	    		$('#alterarPJ,#visualizarPJDest').parent().removeClass('disabled');
	    	}else{
	    		$('#alterarPJ,#visualizarPJDest').parent().addClass('disabled');

	    	}
    	});
    	
    	$('#sqPessoaDestinatarioPf,#sqPessoaDestinatario').click(function(){
    		$(this).closest('.input-append').removeClass('open');
    	});
    	
    	$('#visualizarPJDest').on('click', function() {
            $("#visualizarPJ").load('/auxiliar/pessoa-juridica/visualizar-matriz-filial/visualizar/true/sqPessoa/'+$('#sqPessoaDestinatario_hidden').val());
    	});

    	$('#visualizarPFDest').on('click', function() {
    		$("#visualizarPF").load('/auxiliar/pessoa-fisica/visualizar-pessoa-fisica/sqPessoa/'+$('#sqPessoaDestinatarioPf_hidden').val());   
    	});
    	 
    	$('#alterarPF,#alterarPJ').on('click', function(e) {
    		$('#alterarPF,#alterarPJ').closest('.input-append').removeClass('open');
			$('#alterarPJ,#visualizarPJDest').parent().addClass('disabled');
			$('#alterarPF,#visualizarPFDest').parent().addClass('disabled');
    		$('#btnLimpar').trigger('click');
    	});	
	},
    initModalDestinatario : function() {
    	
        $('#coCep').setMask('99.999-999');
        ModalMinuta.tipoOrigem();
        $('#sqTipoPessoa').change(function() {
            ModalMinuta.tipoOrigem();
        });
        
        $("input[name=TipoNacionalidadeDest]").click(function(){     
            $('#sqPessoaDestinatarioPf_hidden').val('');
            $('#sqPessoaDestinatarioPf').val('');
            $('#nuCPF').val('');
            $('.dvLabelNome').hide();
            $('.tpInteressado').show();
            if($("#pessoaBrasilieiraDest").is(":checked"))
            {
                $('#dvLabelCPF').text('CPF');
                $('#nuCPF').setMask('999.999.999-99');
                $('#divGeralCPF').show();
            }else{
                $('#dvLabelCPF').text('Nº do Passaporte');
	            $('#nuCPF').removeClass('cpf').removeClass('cnpj');
                $('#nuCPF').unsetMask();
                $('#dvLabelCPF').show();
            }
        })
        $('#sqPessoaDestinatarioPf').simpleAutoComplete("/artefato/pessoa/search-pessoa/save/true", {
            extraParamFromInput: 'input[name=TipoNacionalidadeDest]:checked',
            attrCallBack: 'rel',
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel'
            }); 
        
        
        $('#sqPessoaDestinatario').simpleAutoComplete(
                "/artefato/pessoa/search-pessoa/save/true", {
                    extraParamFromInput : '#sqTipoPessoa',
                    attrCallBack : 'rel',
                    autoCompleteClassName : 'autocomplete',
                    selectedClassName : 'sel'
                });

        $('#sqPessoaInterno').simpleAutoComplete(
                "/artefato/pessoa/search-pessoa", {
                    extraParamFromInput : '#sqTipoPessoa',
                    attrCallBack : 'rel',
                    autoCompleteClassName : 'autocomplete',
                    selectedClassName : 'sel'
                });

        $('#sqMunicipioDestinatario').simpleAutoComplete(
                "/auxiliar/municipio/search-municipio", {
                    extraParamFromInput : '#sqEstadoDestinatario',
                    attrCallBack : 'rel',
                    autoCompleteClassName : 'autocomplete',
                    selectedClassName : 'sel'
                });

        $(document).ajaxStop(function() {
            $('#btnCadastrarPessoa').hide();
        });

        $('#sqTratamento').change(
                function() {
                    if ($('#sqTratamento').val()) {
                        $('#divVocativo').load(
                                '/auxiliar/tratamento/combo-tratamento/sqTratamento/'
                                        + $('#sqTratamento').val());
                    }
                });

        $('#sqPessoaDestinatario,#sqPessoaDestinatarioPf,#btnLimpar').click(function() {
//            if ($('#sqPessoaDestinatario').val() == '') {
        	   $('#modal-destinatario').find('.help-block').hide();
               $('#radioCorporativo').removeAttr('checked');
               $('#radioSgdoce').removeAttr('checked');
   	    	   $('#radioCorporativo').addClass('required');
	    	   $('#radioCorporativo').addClass('required');
               
        	    $('#sqPessoaDestinatario').val('');
                $('#sqPessoaDestinatario_hidden').val('');
                $('#sqPessoaDestinatario_autocomplete').val('');
                $('#sqPessoaDestinatarioPf').val('');
                $('#sqPessoaDestinatarioPf_hidden').val('');
                $('#sqPessoaDestinatarioPf_autocomplete').val('');
                $('#listCorporativo').html('');                
                $('.idDadosCorporativo').hide();
                $('#listSgdoce').html('');
                $('.idDadosSgdoce').hide();
                $('#nuCPF').val('');
//                $('#coCep').val('');
//                $('#txEndereco').val('');
//                $('#sqEstadoDestinatarioName').val('');
//                $('#sqMunicipioDestinatario').val('');
//            }
        });
    },

    concluirExterno : function() {
    	
        $('.btnConcluirDestinatarioExterno').click(function() {
            if(!($("input[name=radioDadosCorporativo]").is(":checked") || $("input[name=radioDadosSgdoce]").is(":checked")))
            {
                $("#radioDadosCorporativo").attr('style', 'color: #FFFFFF;');
                $("#radioDadosSgdoce").attr('style', 'color: #FFFFFF;');
             	$('#radioCorporativo').closest('.control-group').addClass('error');
             	$('#radioSgdoce').closest('.control-group').addClass('error');
            }
            if ($('#form-destinatario-modal').valid()) {
                
                if($('#sqPessoaDestinatarioPf_hidden').val()){
                    sqPessoaCorporativo = $('#sqPessoaDestinatarioPf_hidden').val();
                }
                if($('#sqPessoaDestinatario_hidden').val()){
                    sqPessoaCorporativo = $('#sqPessoaDestinatario_hidden').val();
                }
                var arrDados = ModalMinuta.validaDados($('#sqArtefato').val(),sqPessoaCorporativo,3);
               if(arrDados == 'true'){
                   Message.showAlert('O usuário informado já foi incluido.');
                   return false;
               }else{
                   if($("input[name=radioDadosCorporativo]").is(":checked") || $("input[name=radioDadosSgdoce]").is(":checked"))
                    {
                        ModalMinuta.formDestinatarioExterno();
                        Message.showAlert('Operação realizada com sucesso.');    
                   }else{
//                        Message.showAlert('Escolha uma opção.');
                        return false;
                   }
               }
            } else {
                $('.campos-obrigatorios').remove();
                return false;
            }
        });
    },

    tipoOrigem : function() {
    	
        $('#listCorporativo').html('');                
        $('.idDadosCorporativo').hide();
        $('#listSgdoce').html('');
        $('.idDadosSgdoce').hide();
        
        $('#btnCadastrarPessoa').hide();
        $('#sqPessoaDestinatario').val('');
        $('#nuCPF').val('');
        $('#coCep').val('');
        $('#txEndereco').val('');
        $('#sqEstadoDestinatarioName').val('');
        $('#sqMunicipioDestinatario').val('');
        $('#sqMunicipioDestinatario_hidden').val('');
        $('#coCep,#txEndereco,#sqEstadoDestinatarioName,#sqMunicipioDestinatario').addClass('required');
        var all = $('.dvGeralNome, .divGeralCPF, .divOutrosDados,.dvGeralNomePF,.divGeralEstrangeiroDes');
        $(all).hide();
        $('.labelEstrangeiro').text('*');
        switch ($('#sqTipoPessoa').val()) {
	        case '1':
	            $(all).show();
	            $('#pessoaBrasilieiraDest').trigger('click');
	            $('.dvGeralNome').hide();
	            $('.divGeralEstrangeiroDes').show();
	            $('.dvGeralNome').hide();
	            $('#dvLabelNomePF').text('* Nome');
	            $('#dvLabelCPF').text('CPF');
	            $('#nuCPF').setMask('999.999.999-99');
	            $('#nuCPF').removeClass('cnpj').addClass('cpf');
	            $('#nuCPF').next().hide();
	        	$('.multiplaPessoa').show();
	            break;
	        case '2':
	            $(all).show();
	            $('.divGeralEstrangeiroDes').hide();
	            $('.dvGeralNomePF').hide();
	            $('#dvLabelNome').text('* Razão Social');
	            $('#dvLabelCPF').text('CNPJ');
	            $('#nuCPF').setMask('99.999.999/9999-99');
	            $('#nuCPF').removeClass('cpf').addClass('cnpj');
	            $('#nuCPF').next().hide();
	        	$('.multiplaPessoa').show();
	            break;
	        case '4':
	            $(all).show();
	            $('.divGeralEstrangeiroDes').hide();
	            $('.dvGeralNomePF').hide();
	            $('.divGeralCPF').hide();
	            $('#dvLabelNome').text('* Nome');
	        	$('.multiplaPessoa').hide();
	            break;
	        case '5':
	            $(all).show();
	            $('.divGeralEstrangeiroDes').hide();
	            $('.dvGeralNomePF').hide();
	            $('.divGeralCPF').hide();
	            $('#dvLabelNome').text('* Nome');
	            $('.multiplaPessoa').hide();
	            break;
        }
    },

    formDestinatarioExterno : function() {
        
        var corporativo = '';
        var sqEndereco  = '';
        var sqPessoa    = '';
        var noPessoa    = '';
        var sqPessoaCorporativo = '';
        if($("input[name=radioDadosCorporativo]").is(":checked"))
        {
            sqEndereco = $("input[name=radioDadosCorporativo]:checked").val();
            corporativo = '1';
        }
        if($("input[name=radioDadosSgdoce]").is(":checked"))
        {
            sqEndereco = $("input[name=radioDadosSgdoce]:checked").val();
            corporativo = '2';
        }

        if($('#sqPessoaDestinatarioPf_hidden').val()){
            sqPessoaCorporativo = $('#sqPessoaDestinatarioPf_hidden').val();
            noPessoa = $('#sqPessoaDestinatarioPf').val();
        }
        if($('#sqPessoaDestinatario_hidden').val()){
            sqPessoaCorporativo = $('#sqPessoaDestinatario_hidden').val();
            noPessoa = $('#sqPessoaDestinatario').val();
        }
        
        $.post('/artefato/minuta-eletronica/add-destinatario-artefato', {
        // tipo externo
        sqArtefato               : $('#sqArtefato').val(),
        noPessoa              : noPessoa,
        checkCorporativo      : corporativo,
        sqPessoaCorporativo   : sqPessoaCorporativo,
        sqEndereco            : sqEndereco,
        tipoPessoaAba: '1',
        sqTratamento           : $('#sqTratamento option:selected').val(),
        sqTratamentoVocativo  : $('#sqVocativo option:selected').val(),
        sqTipoPessoa           : $('#sqTipoPessoa').val(),
        nuCpfCnpjPassaporte   : $('#nuCPF').val(),
        txPosTratamento       : $('#txPosTratamento').val(),
        txPosVocativo         : $('#txPosVocativo').val(),
        sqNacionalidade : $("input[name=TipoNacionalidadeDest]:checked").val(),
        }, function() {
            $('#sqTratamento').val('');
            $('#sqTipoPessoa').val('');
            ModalMinuta.tipoOrigem();
            $('#table-destinatario-externo').dataTable().fnDraw(false);
            ModalMinuta.fnDrawCallback();
        });
    },
    fnDrawCallback : function() {
        setTimeout(function() {
            if ($('#unicoDestino').val()) {
                var totalDestinos = $('#table-destinatario-externo').dataTable().fnSettings().fnRecordsTotal();
                if (totalDestinos == 0) {
                    //habilita o botao
                    $('.btnAdicionarDestinFake').hide();
                    $('.btnAdicionarDestin').show();
                } else {
                    //desabilita o botao
                    $('.btnAdicionarDestin').hide();
                    $('.btnAdicionarDestinFake').show();
                }
            }
        }, 1000);
    },

    initCampo : function() {
        var interessado = false;
        var rodape = false;
        var tratamento = false;
        var params = {
            sqTipoDocumento : $('#sqTipoDocumento').val(),
            sqAssunto : $('#sqAssunto').val(),
            sqModeloDocumento : $('#sqModeloDocumento').val()
        };
        var arrCampoModeloDocumento = MinutaPasso
                .getCampoModeloDocumento(params);
        var j = $.parseJSON(arrCampoModeloDocumento);

        if (j.length > 0) {
            $
                    .each(j,
                            function(i) {
                                if (j[i].noCampo == 'Interessado') {
                                    interessado = true;
                                    $('#botao_aba7').show();
                                }

                                if (j[i].noCampo == 'Tratamento') {
                                    tratamento = true;
                                }

                                $(
                                        '.dv' + j[i].sqGrupoCampo + '-'
                                                + j[i].noColunaTabela).show();
                                if (j[i].inObrigatorio) {
                                    $('#' + j[i].noColunaTabela).addClass(
                                            'required');
                                }
                                tipo[j[i].sqGrupoCampo] = 1;
                            });
        }

        if (!tratamento) {
            $('.tpDestinatario').show();
        }

        for (i in tipo) {
            $('#botao_aba' + i).show();
        }
    },

    initResponsavelDestinatario : function() {
        $('input[id=sqPessoaDestinatario],input[id=sqPessoaDestinatarioPf],input[id=nuCPF]').blur(
                function() {    
                    var value = '';
                    if($('#sqPessoaDestinatarioPf_hidden').val()){
                        value = $('#sqPessoaDestinatarioPf_hidden').val();
                    }
                    if($('#sqPessoaDestinatario_hidden').val()){
                        value = $('#sqPessoaDestinatario_hidden').val();
                    }
                    ModalMinuta.getDados($('#sqTipoPessoa option:selected').val(), value, $('#nuCPF').val(),$("input[name=TipoNacionalidadeDest]:checked").val());
                });
    },

    getDados : function(sqTipoPessoa, sqPessoa, nuCPF, sqNacionalidade) {

        if (sqPessoa || nuCPF) {
            var result;
            var sqArtefato = $('#sqArtefato').val();
            $.ajax({
                type : 'post',
                url : '/artefato/pessoa/get-pessoa-dados',
                data : 'sqArtefato=' + sqArtefato + '&sqTipoPessoa=' + sqTipoPessoa + '&sqPessoaCorporativo=' + sqPessoa
                        + '&nuCpfCnpjPassaporte=' + nuCPF + '&sqNacionalidade=' + sqNacionalidade  + '&nuCPFDestinatario=' + nuCPF,
                async : false,
                global : false,
                dataType : 'json',
                success : function(data) {

//                	if(data == ''){
//                        var callBack = function(){
//                        	var url = '/modelo-minuta/modelo-minuta';
//                        	window.open(url, '_blank');
//                        }
//                                
//                        Message.showConfirmation({
//                            'body': 'Nenhum registro encontrado. Deseja cadastrar?',
//                            'yesCallback': callBack
//                        });
//                		
//                		return false;
//                	}
               	
                    if($('#sqTipoPessoa').val() == '1'){      
                        $('#sqPessoaDestinatarioPf_hidden').val(data['sqPessoa']);
                        $('#sqPessoaDestinatarioPf').val(data['noPessoa']);
                        if($("#pessoaBrasilieiraDest").is(":checked"))
                        {
                        	$('#nuCPF').val(data['nuCpf']);
                        }else{
                        	$('#nuCPF').val(data['nuPassaporte']);
                        }
                    }else{
                        $('#nuCPF').val(data['nuCnpj']);
                        $('#sqPessoaDestinatario_hidden').val(data['sqPessoa']);
                        $('#sqPessoaDestinatario').val(data['noPessoa']);
                    }
                    result = data;
                }
            });

            ModalMinuta.populatePessoaDestinatario(result);
            return true;
        }
        return false;
    },

    validaDados: function(sqArtefato,sqPessoaCorporativo,sqPessoaFuncao){
        var result = $.ajax({
            type: 'post',
            url: '/artefato/pessoa/valida-pessoa-destinatario',
            data: 'sqArtefato='+sqArtefato+'&sqPessoaCorporativo='+sqPessoaCorporativo+'&sqPessoaFuncao='+sqPessoaFuncao,
            async: false,
            global: false
            }).responseText;
        return result;
    }, 
    
    populatePessoaDestinatario : function(data) {

//    	var emptyEndereco = true;
    	var cont = false;

        if ($(data).size()) {
            if(data['corporativo'] != ''){
                $('#listCorporativo').html('');
                $('.idDadosCorporativo').show();
            	jQuery.each(data['corporativo'], function(index, result) {
                cont = true;
//        		emptyEndereco = false;
                 var cep = result.coCep ? result.coCep : 'não cadastrado';
                 var sqEndereco = result.sqEndereco ? result.sqEndereco : 'não cadastrado';
                 var endereco = result.txEndereco ? result.txEndereco : 'não cadastrado';
                 var estado = result.sqEstadoDestinatario ? result.sqEstadoDestinatario : 'não cadastrado';
                 var municipio = result.sqMunicipioDestinatario ? result.sqMunicipioDestinatario : 'não cadastrado';
                 var tipoEndereco = result.noTipoEndereco ? result.noTipoEndereco : 'não cadastrado';
                 var opcao = '<input type="radio"  id="radioDadosCorporativo" name="radioDadosCorporativo" value="'+result.sqEndereco+'">';

                 if(endereco == '' || endereco == ' ' ){
                	 endereco = 'não cadastrado';
                 }
                 if((sqEndereco == 'não cadastrado' || cep == 'não cadastrado') || (endereco == 'não cadastrado') || ( estado == 'não cadastrado') || (municipio == 'não cadastrado') ){
                	 opcao = '';
                 }
                 
                 var tr = '<tr>';
                 
                 tr = tr + '<td>' + opcao + '</td>'
                 tr = tr + '<td>' + tipoEndereco + '</td>';
                 tr = tr + '<td>' + cep + '</td>';
                 tr = tr + '<td>' + endereco +'</td>';
                 tr = tr + '<td>' + estado +'</td>';
                 tr = tr + '<td>' + municipio +'</td>';
                 tr = tr + '</tr>';
                 $('#listCorporativo').append(tr);;
                 
                });
            }

            if(data['sgdoce'] != ''){
            	$('#listSgdoce').html('');
                $('.idDadosSgdoce').show();
            	jQuery.each(data['sgdoce'], function(index, result) {
            		
            		if(result.sqEnderecoSgdoce && index <= 4){

            			var end = result.txEndereco;
            			var noEstado = result.noEstado;
            			var noMunicipio = result.noMunicipio;
            			var coCep = result.coCep;
            			var sqEnderecoSgdoce = result.sqEnderecoSgdoce;
            			var tipoEndereco = result.noTipoEndereco;
            			var nuEndereco = result.nuEndereco;
//                    	if(cont == false){
//                    		emptyEndereco = false;
                   		
                    		 if(coCep == null){
                    			 coCep = 'não cadastrado';
                    		 }else{
                    			 coCep = new String(result.coCep) ;
                        		 coCep = coCep.substring(0, 2) + "." + coCep.substring(2, 5) + "-" + coCep.substring(8, 5);
                    		 }
                    		 if(end == null){
                    			 end = 'não cadastrado';
                    		 }
                    		 if(noEstado == null){
                    			 noEstado = 'não cadastrado';
                    		 }
                    		 if(noMunicipio == null){
                    			 noMunicipio = 'não cadastrado';
                    		 }
                    		 if(nuEndereco == null){
                    			 nuEndereco = '';
                    		 }
                             var opcao = '<input type="radio"  id="radioDadosSgdoce" name="radioDadosSgdoce" value="'+result.sqEnderecoSgdoce+'">';
                    		 if(sqEnderecoSgdoce == null){
                    			 opcao = '';
                    		 }
                    		 

                             var tr = '<tr>';
                             tr = tr + '<td>'+ opcao + '</td>'
                             tr = tr + '<td>' + tipoEndereco + '</td>';
                             tr = tr + '<td>' + coCep + '</td>';
                             tr = tr + '<td>' + end + ' ' + nuEndereco +'</td>';
                             tr = tr + '<td>' + noEstado +'</td>';
                             tr = tr + '<td>' + noMunicipio +'</td>';
                             tr = tr + '</tr>';
                             $('#listSgdoce').append(tr);
                         }
                    });
            }
        } else {
            $('#listCorporativo').html('');
            $('#listSgdoce').html('');
        }
//        if(emptyEndereco){
//        	
//        	console.debug('não existe endereco cadastrado');
//        }
        ModalMinuta.setaValidacao();
    },

    // destinatario interno
    formDestinatarioInterno : function() {
        $.post('/artefato/pessoa/add-destinatario-interno', {
            // tipo externo
            sqArtefato : $('#sqArtefato').val(),
            sqPessoaCorporativo : $('#sqPessoaDestInterno_hidden').val(),
            noPessoa : $('#sqPessoaDestInterno').val(),
            sqTratamentoVocativo : $('#sqTratamento').val(),
            txPosVocativo : $('#noCargo').val()
            // ,sqPessoaCorporativo: $('#sqPessoaInterno_hidden').val()
            ,
            sqPessoaFuncao : 3,
            sqTipoPessoa : 1,
            sqTipoUnidadeOrg : $('#sqTipoUnidadeOrg_hidden').val(),
            sqUnidadeOrg : $('#sqUnidadeOrg_hidden').val(),
            noUnidadeOrg : $('#sqUnidadeOrg').val()
        }, function(data) {
            $('#noCargo').val('');
            $('#sqTratamento').val('');
            $('#sqTipoPessoa').val('');
            $('#sqPessoaInterno').val('');
            $('#table-destinatario-interno').dataTable().fnDraw(false);
//            if(data == 'false'){
//                Message.showAlert('O usuário informado já foi incluido.');
//                return false;
//            }else{
//                Message.showAlert('Operação realizada com sucesso.');
//            }
        });
    },

    setaValidacao : function() {

    	$('#radioDadosSgdoce,#radioDadosCorporativo').click(function() {
            if($("input[name=radioDadosCorporativo]").is(":checked") || $("input[name=radioDadosSgdoce]").is(":checked"))
            {
         	    $('#modal-destinatario').find('.help-block').hide();
                $('#radioSgdoce').attr('checked', 'checked');
    	    	$('#radioSgdoce').removeClass('required');

             	$('#radioCorporativo').closest('.control-group').removeClass('error');
             	$('#radioSgdoce').closest('.control-group').removeClass('error');
//             	
                $('#radioCorporativo').attr('checked', 'checked');
    	    	$('#radioCorporativo').removeClass('required');
            }
    	});
    	
    	$("input[name=radioDadosCorporativo]").click(function(){ 
    		if($("#radioDadosCorporativo").is(":checked"))
    		{
    		    $("#radioDadosSgdoce").removeAttr('checked');
            }
    	});
    	$("input[name=radioDadosSgdoce]").click(function(){ 
    		if($("#radioDadosSgdoce").is(":checked"))
    		{
    		    $("#radioDadosCorporativo").removeAttr('checked');
    		}
    	});
    },
    
    concluirInterno : function() {

        $('.btnConcluirDestinatarioInterno').click(function() {
        	ModalMinuta.setaValidacao();
            if ($('#form-destinatario-interno-modal').valid()) {
                sqTipoUnidadeOrg = $('#sqTipoUnidadeOrg_hidden').val();
                sqUnidadeOrg     = $('#sqUnidadeOrg_hidden').val();
                
                var result = $.ajax({
                    type: 'post',
                    url: '/artefato/pessoa/add-destinatario-interno',
                    data: 'sqArtefato='+ $('#sqArtefato').val()+'&sqPessoaCorporativo='+ $('#sqPessoaDestInterno_hidden').val()+
                          '&sqPessoaFuncao='+3+'&sqTipoUnidadeOrg='+sqTipoUnidadeOrg+'&sqUnidadeOrg='+sqUnidadeOrg+'&noUnidadeOrg='+'',
                    async: false,
                    global: false
                    }).responseText;
                
                if(result == 'false'){
                    Message.showAlert('O usuário informado já foi incluido.');
                    return false;
                }else{
                    ModalMinuta.formDestinatarioInterno();
                    Message.showAlert('Operação realizada com sucesso.');
                }
            } else {
//            	;
                $('.campos-obrigatorios').remove();
                return false;
            }

        });
    },

    init : function() {
        ModalMinuta.initModalDestinatario();
        ModalMinuta.concluirExterno();
        ModalMinuta.initCampo();
        ModalMinuta.initResponsavelDestinatario();
        ModalMinuta.concluirInterno();
    }
}

DestinatarioInterno = {
    deletar : function(sqArtefato,sqPessoaSgdoce,sqPessoaFuncao) {
        $.post('/artefato/pessoa/delete-destinatario', {
            sqArtefato: sqArtefato,
            sqPessoaSgdoce: sqPessoaSgdoce,
            sqPessoaFuncao: sqPessoaFuncao
            
        }).done(function(){
            DestinatarioInterno.reloadGrid()
        });
    },
    reloadGrid : function() {
        $('#table-destinatario-interno').dataTable().fnDraw(false);
    }
}

$(document).ready(function() {
    ModalMinuta.init();
	ModalMinuta.initVisualizaPessoa();
    ModalMinuta.fnDrawCallback();
    loadJs('js/components/modal.js', function() {
        Menu.init();
    }); // load cdn
});
