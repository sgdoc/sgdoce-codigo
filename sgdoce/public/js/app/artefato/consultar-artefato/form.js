consultaArtefato = {
    initCampo : function() {
    	if($('#update').val() == '0'){
        	$('.updateForm').parent().show();
        	$('.updateForm').text('Cadastro realizado com sucesso');
    	}else if($('#update').val() == '1'){
        	$('.updateForm').parent().show();
        	$('.updateForm').text('Alteração realizada com sucesso');
    	}

        $('#nuCpfCnpjPassaporte').keypress(function(){
        	var nuCpf =    $('#nuCpfCnpjPassaporte').val();
        	 $('#nuCpfCnpjPassaporte').val(nuCpf.replace(/(\.|\/|-|,)/g, ''));
        	switch ($(this).val().length) {

        	default:
				$('#nuCpfCnpjPassaporte').removeClass('cnpj');
				$('#nuCpfCnpjPassaporte').removeClass('cpf');
				$('#nuCpfCnpjPassaporte').unsetMask();
				break;
        	}
        });
        $('#nuCpfCnpjPassaporte').blur(function(){
    		switch ($('#nuCpfCnpjPassaporte').val().length) {
			case 11:
				 $('#nuCpfCnpjPassaporte').addClass('cpf');
				 $("#nuCpfCnpjPassaporte").setMask("999.999.999-99");
				break;
			case 14:
				$('#nuCpfCnpjPassaporte').addClass('cnpj');
				$("#nuCpfCnpjPassaporte").setMask("99.999.999/9999-99");
				break;
			default:
				$('#nuCpfCnpjPassaporte').val('');
				break;
			}
        });
    },

    escondeCamposTipoInformacao:function(tipoInformacao){
        if (tipoInformacao != 3 && tipoInformacao != '') {
            $('#divTexto').show();
            $('.campos-esconder').hide();
        } else {
            $('#divTexto').hide();
            $('.campos-esconder').show();
        }

         //Mostra data despacho ou comentario
        if(tipoInformacao == 2){
            $('#dataCombo option').hide();
            $('#dataCombo').val('6');
            $('#divAssinatura,#divData').show();
            consultaArtefato.verificaDataCombo();
        }else if(tipoInformacao == 1){
            $('#dataCombo option').hide();
            $('#divAssinatura,#divData').show();
            $('#dataCombo').val('7');
            consultaArtefato.verificaDataCombo();
        }else{
             $('#dataCombo').val('');
             $('#dataCombo option, #divAssinatura,#divData').hide();
             $('#divPeriodo').hide();
             $('#dataCombo').find("option[value='2'],option[value='4'],option[value='5']").show();
             consultaArtefato.escondeCamposTipoPesquisa();
        }
    },
    escondeCamposTipoPesquisa:function(tipoPesquisa) {
        if (tipoPesquisa == 2) {
            $('#divTipoDocumento, #divTituloDossie,#divNumDocumento, #divNumDocumento').hide();
            $('#divUnidadeAfetada, #divEmpreendimentos,#divTipoEspecie,#divCavernas,#divData').show();

        } else if(tipoPesquisa == 1){
            $('#divTipoDocumento, #divTituloDossie, #divNumDocumento,#divData').show();
            $('#divUnidadeAfetada, #divEmpreendimentos,#divTipoEspecie,#divCavernas').hide();
        }else {
            $('#divTituloDossie,#divNumDocumento').show();
            $('#divTipoDocumento,#divUnidadeAfetada, #divEmpreendimentos,#divTipoEspecie,#divCavernas,#divTituloDossie,#divNumDocumento,#divData').hide();
        }

        //Esconde campos Assunto, Assunto Complementar, Assinatura, Cargo, Destino, Encaminhado para, Recebido por, Endereçamento, Prioridade e Referencia.
        if(tipoPesquisa == 1 || tipoPesquisa == 2){
            $('#divAssunto, #divAssunotoComplementar, #divAssinatura, #divCargo, #divDestino, #divEncaminhamento, #divRecebido, #divEnderecamento,#divPrioridade, #divReferencia').show();
        }else{
            $('#divAssunto, #divAssunotoComplementar, #divAssinatura, #divCargo, #divDestino, #divEncaminhamento, #divRecebido, #divEnderecamento, #divPrioridade, #divReferencia').hide();
        }

        //Esconde data do documento se o dipo de pesquisa não for digital
        if(tipoPesquisa != 1 ){
            $('#dataCombo').find("option[value='3']").hide();
        }else{
            $('#dataCombo').find("option[value='3']").show();
        }

        //Esconde data da atuação se o dipo de pesquisa não for processo
        if(tipoPesquisa == 2){
            $('#dataCombo').find("option[value='1']").show();
        }else{
            $('#dataCombo').find("option[value='1']").hide();
        }
    },
    deletar:function(sqArtefato,controller,action,nuDigital){

        var callBack = function(){
                $.ajax({
                    data :{'id' : sqArtefato},
                    type: "POST",
                    dataType : "json",
                    url : 'artefato/' + controller + '/' + action,
                    success : function(data){
                        if(data == true){
                           Message.show('Sucesso','Exclusão realizada com sucesso.');
                        }else{
                               Message.show('Alerta','A digital ' + nuDigital + ' não pode ser excluída porque existem vinculações. Retire as vinculações para ser possível a exclusão.');
                        }
                    }
                });
            }

            Message.showConfirmation({
                'body': 'Tem certeza que deseja realizar a exclusão?',
                'yesCallback': callBack
            });
    },

    autuarProcesso:function(sqArtefato,view,update){
        window.location = 'artefato/autuar-processo/save-artefato-processo/id/'+sqArtefato+'/view/'+view+'/update/'+update;
    },

    verificaDataCombo:function(){
        if ($('#dataCombo').val() == '') {
            $('#divPeriodo').hide();
        } else {
            $('#divPeriodo').show();

        }
    }
};

$(document).ready(function() {
    consultaArtefato.initCampo();
    consultaArtefato.escondeCamposTipoInformacao($('#tipoInformacao').val());
    consultaArtefato.escondeCamposTipoPesquisa($('#tipoPesquisa').val());

    $('#divPeriodo').hide();
    $('#dataCombo').change(function(){
        consultaArtefato.verificaDataCombo();
    });
    $('#tipoInformacao').change(function() {
        consultaArtefato.escondeCamposTipoInformacao($('#tipoInformacao').val());
    });
    $('#tipoPesquisa').change(function() {
        consultaArtefato.escondeCamposTipoPesquisa($('#tipoPesquisa').val());
    });
    Grid.load($('#form-consultar-artefato-padrao'), $('#table-consultar-artefato-padrao'));

    //Verifica se a pesquisa e comentário ou despacho
    $('.btn_pesquisar').click(function(){
        //Carrega a pesquisa sem campo texto.
        if($('#tipoInformacao').val() == 3 || $('#tipoInformacao').val() == ''){
            Grid.load($('#form-consultar-artefato-avancado'), $('#table-consultar-artefato-avancado'));
            $('.rstComTipoInfo').hide();
            $('.rstSemTipoInfo').show();
        }else{
            //Carrega a pesquisa com o campo texto.
            Grid.load($('#form-consultar-artefato-avancado'), $('#table-consultar-artefato-avancado-tipo-info'));
            $('.rstSemTipoInfo').hide();
            $('.rstComTipoInfo').show();
        }
    });

    //Função para limpar campos formulario.
    $('#limpar').click(function(){
       $('form input, select').val('');
       return false;
    });

    $('#btnPesquisaAvancada').click(function() {
        window.location = "artefato/consultar-artefato/consultar-artefato-avancado";
    });
    $('#btnPesquisaPadrao').click(function() {
        window.location = "artefato/consultar-artefato/consultar-artefato-padrao";
    });

    $('#sqPessoaSgdoce').simpleAutoComplete("artefato/consultar-artefato/search-pessoa-interessada/", {
        extraParamFromInput: '#extra',
        attrCallBack: 'rel',
        autoCompleteClassName: 'autocomplete',
        selectedClassName: 'sel'
    });

    $('#sqPessoaFuncao').simpleAutoComplete("artefato/pessoa/search-pessoa/", {
        attrCallBack:'rel',
        autoCompleteClassName:'autocomplete',
        selectedClassName:'sel'
    });

    $('#txAssunto').simpleAutoComplete("auxiliar/assunto/searchassunto/", {
        extraParamFromInput: '#extra',
        attrCallBack: 'rel',
        autoCompleteClassName: 'autocomplete',
        selectedClassName: 'sel'
    });

    $("#txTituloDossie").simpleAutoComplete("artefato/consultar-artefato/search-titulo-dossie/", {
        attrCallBack:'rel',
        autoCompleteClassName:'autocomplete',
        selectedClassName: 'sel'
    });

    $('#txAssinatura').simpleAutoComplete("artefato/pessoa/search-pessoa/", {
        extraParamFromInput: '#extra',
        attrCallBack:'rel',
        autoCompleteClassName:'autocomplete',
        selectedClassName:'sel'
    });

    $('#txCargo').simpleAutoComplete("artefato/pessoa/search-Cargo-Corporativo/", {
        extraParamFromInput: '#extra',
        attrCallBack:'rel',
        autoCompleteClassName:'autocomplete',
        selectedClassName:'sel'
    });

    $('#txDestino').simpleAutoComplete("artefato/pessoa/search-pessoa/", {
        attrCallBack:'rel',
        autoCompleteClassName:'autocomplete',
        selectedClassName:'sel'
    });

    $('#txEncaminhadoPara').simpleAutoComplete("artefato/pessoa/search-pessoa/", {
        attrCallBack:'rel',
        autoCompleteClassName:'autocomplete',
        selectedClassName:'sel'
    });

    $('#txRecebidoPor').simpleAutoComplete("artefato/pessoa/search-pessoa/", {
        attrCallBack:'rel',
        autoCompleteClassName:'autocomplete',
        selectedClassName:'sel'
    });

    $('#txEnderecamento').simpleAutoComplete("artefato/pessoa/search-pessoa/", {
        attrCallBack:'rel',
        autoCompleteClassName:'autocomplete',
        selectedClassName:'sel'
    });

    $('#txReferencia').simpleAutoComplete("artefato/consultar-artefato/search-referencia/", {
        attrCallBack:'rel',
        autoCompleteClassName:'autocomplete',
        selectedClassName:'sel'
    });

    // autocomplete para o tema caverna selecionado
    $('#sqNomeUnidade').simpleAutoComplete("/artefato/processo-eletronico/canie-caverna-auto-complete/extraParam/3", {
        extraParamFromInput: '3',
    	autoCompleteClassName: 'autocomplete',
        selectedClassName: 'sel'
    });

        // autocomplete para o tema caverna selecionado
    $('#sqNomeEmpreendimento').simpleAutoComplete("/artefato/processo-eletronico/canie-caverna-auto-complete/extraParam/2", {
        extraParamFromInput: '2',
    	autoCompleteClassName: 'autocomplete',
        selectedClassName: 'sel'
    });

        // autocomplete para o tema caverna selecionado
    $('#sqNomeEspecie').simpleAutoComplete("/artefato/processo-eletronico/canie-caverna-auto-complete/extraParam/1", {
        extraParamFromInput: '1',
    	autoCompleteClassName: 'autocomplete',
        selectedClassName: 'sel'
    });

        // autocomplete para o tema caverna selecionado
    $('#sqNomeCaverna').simpleAutoComplete("/artefato/processo-eletronico/canie-caverna-auto-complete/extraParam/0", {
        extraParamFromInput: '0',
    	autoCompleteClassName: 'autocomplete',
        selectedClassName: 'sel'
    });

    // autocomplete para tipo documento
    $('#tipoDocumento').simpleAutoComplete("/artefato/consultar-artefato/search-tipo-documento/", {
        extraParamFromInput: '#tipoDocumento',
        autoCompleteClassName: 'autocomplete',
        selectedClassName: 'sel'
    });

    $('#btnFiltro').click(function(){
    	 $('html,body').animate({scrollTop:0},500);
    });
});

