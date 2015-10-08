var Destino = {
    getGroup: function(vl) {
        switch (vl) {
            case 1 :
                return $('.divDestinoTipoPessoaIcmbio, .divDestinoSqPessoaIcmbio, .divDestinoTipoPessoa, .divNacionalidadeDestino, .divDestinoSqPessoa, .divDestinoCPF, .divDestinoCNPJ, .divDestinoCNPJ, .divDestinoPassaporte, .divDestinoSqPessoaEncaminhado, .divDestinoNoCargoEncaminhado, .divDestinoSqPessoaEncaminhadoExterno');
                break;
            case 2 :
                return $('.divDestinoTipoPessoaIcmbio, .divDestinoSqPessoaIcmbio, .divDestinoTipoPessoa, .divNacionalidadeDestino, .divDestinoSqPessoa, .divDestinoCPF, .divDestinoCNPJ, .divDestinoCNPJ, .divDestinoPassaporte, .divDestinoSqPessoaEncaminhado, .divDestinoNoCargoEncaminhado');
                break;
            case 3 :
                return $('.divNacionalidadeDestino,.divDestinoSqPessoaIcmbio,.divDestinoCPF, .divDestinoSqPessoa, .divDestinoCNPJ,.divDestinoPassaporte,  .divDestinoSqPessoaEncaminhado, .divDestinoNoCargoEncaminhado');
                break;
        }
    },
    getElement: function(vl) {
        switch (vl) {
            case 1:
                return $('#sqTipoPessoaDestinoIcmbio, #sqTipoPessoaDestino, #sqPessoa, #nuCPFDestino, #nuCNPJDestino, #nuPassaporteDestino, #sqPessoaEncaminhado, #noCargoEncaminhado');
                break;
            case 2:
                return $('#sqTipoPessoaDestinoIcmbio, #sqPessoa, #nuCPFDestino, #nuCNPJDestino, #nuPassaporteDestino, #sqPessoaEncaminhado, #noCargoEncaminhado');
                break;
            case 3:
                return $('#sqPessoaIcmbioDestino, #sqPessoa, #nuCPFDestino, #nuCNPJDestino, #nuPassaporteDestino');
                break;
        }
    },
    limpaCampo: function(vl) {
        var obj = Destino.getElement(vl);
        obj.val('');
    },
    hideCampo: function(vl) {
        var obj = Destino.getGroup(vl);
        obj.hide();
    },
    removeObrigatoriedade: function(vl) {
        var obj = Destino.getElement(vl);
        obj.not('#nuCPFDestino') //excluindo cpf da não obrigatoridade
           .removeClass('required');
    },
    tipoPessoaDestino: function() {

        if ($("input[name='destinoInterno']:checked").val() == 'interno') {
            $('.divDestinoTipoPessoaIcmbio').show();
            $('#sqTipoPessoaDestinoIcmbio').addClass('required');
        }

        if ($("input[name='destinoInterno']:checked").val() == 'externo') {
            $('.divDestinoTipoPessoa').show();
            $('#sqTipoPessoaDestino').addClass('required');
        }
    },
    tipoDestino: function() {
        switch ($('#sqTipoPessoaDestino').val()) {
            case '1':
                $('.divDestinoSqPessoaEncaminhadoExterno').hide();
            case '3':
                if ($("input[name='tpNacionalidadeDestino']:checked").val() == '0') {
                    Destino.setaAutocompletePessoa(3);
                    $('.divNacionalidadeDestino, .divDestinoSqPessoa,.divDestinoPassaporte').show();
                    $('.divDestinoNoCargoEncaminhado').show();
                    $('#sqPessoa, #nuPassaporteDestino').addClass('required');
                } else {
                    Destino.setaAutocompletePessoa(1);
                    $('.divNacionalidadeDestino, .divDestinoSqPessoa, .divDestinoCPF').show();
                    $('.divDestinoNoCargoEncaminhado').show();
                    $('#sqPessoa').addClass('required');
                }
                $('.divCadastraPessoa').show();
                break;
            case '2':
                Destino.setaAutocompletePessoa(2);
                $('.divDestinoTipoPessoa,.divDestinoCNPJ,.divDestinoSqPessoa').show();
                $('.divDestinoNoCargoEncaminhado').show();
                $('.divDestinoSqPessoaEncaminhadoExterno').show();
                $('#sqPessoa, #nuCNPJDestino').addClass('required');
                $('#sqEndereco').removeAttr('disabled');
                if ($("#chekDestinoExterno").is(":checked")) {
                    $('#pesquisaEncaminhamento').val('1');
                }
                $('.divCadastraPessoa').show();
                break;
            case '4':
            case '5':
                Destino.setaAutocompletePessoa(5);
                $('.divDestinoTipoPessoa,.divDestinoSqPessoa').show();
                $('.divDestinoNoCargoEncaminhado').show();
                $('.divDestinoSqPessoaEncaminhadoExterno').show();
                $('#sqPessoa').addClass('required');
                $('#sqEndereco').removeAttr('disabled');
                $('.divCadastraPessoa').hide();
                break;
            case '':
                $('.divDestinoTipoPessoa').show();
                $('#sqEndereco').removeAttr('disabled');
                break;
        }
        return true;
    },
    tipoDestinoIcmbio: function() {
        if ($('#sqTipoPessoaDestinoIcmbio').val() == 4) {
            $('.divDestinoSqPessoaIcmbio').show();
            $('#sqPessoaIcmbioDestino').addClass('required');
            $('.divDestinoNoCargoEncaminhado').show();
            $('.divDestinoSqPessoaEncaminhado').show();
            $('.divDestinoSqPessoaEncaminhadoExterno').hide();
        } else if ($('#sqTipoPessoaDestinoIcmbio').val() == 1) {
            $('.divDestinoSqPessoaIcmbio').show();
            $('#sqPessoaIcmbioDestino').addClass('required');
            $('.divDestinoNoCargoEncaminhado').show();
            $('.divDestinoSqPessoaEncaminhado').hide();
            $('.divDestinoSqPessoaEncaminhadoExterno').hide();
        }
    },
    camposInternosExternos: function() {
        if ($("input[name='destinoInterno']:checked").val() == 'interno') {
            $('.divDestinoTipoPessoaIcmbio').show();
            $('#sqTipoPessoaDestinoIcmbio').addClass('required');

//            $('#sqPessoaEncaminhado').removeClass('required');
//            $('#noCargoEncaminhado,#cb_noCargoEncaminhado').removeClass('required');
            $('#sqPessoaEncaminhado').parent().parent().removeClass('error');
            $('#noCargoEncaminhado' ).parent().parent().removeClass('error');
            $('#sqTipoPessoaDestino').parent().parent().removeClass('error');
            $('#sqPessoaEncaminhado').removeAttr('readonly');

            $('.help-block').hide();

            $('#lbCargo').text('Cargo');
            
            $('.divDestinoCargoFuncao').show();

            Destino.tipoDestinoIcmbio()
        }

        if ($("input[name='destinoInterno']:checked").val() == 'externo') {
            $('.divDestinoTipoPessoa').show();

            $('#sqTipoPessoaDestino').addClass('required');
//            $('#sqPessoaEncaminhado').addClass('required');

            $('#noCargoEncaminhado').removeAttr('readonly');
            $('#sqPessoaEncaminhado').removeAttr('readonly');

            $('#lbCargo').text('Cargo/Função');
            
            $('.divDestinoCargoFuncao').hide();
            $('#stCargoFuncao1').trigger('click');
            $("#cb_noFuncaoEncaminhado").val('');

            Destino.tipoDestino();
        }
    },
    autocomplete: function() {
        $('#sqPessoaIcmbioDestino').simpleAutoComplete("/artefato/pessoa/autocomplete", {
            extraParamFromInput: '#sqTipoPessoaDestinoIcmbio',
            attrCallBack: 'id',
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel'
        },function(){
            $('#sqPessoaEncaminhado_hidden').val('');
            $('#sqPessoaEncaminhado').val('');
            $('#noCargoEncaminhado_hidden').val('');
            $('#noCargoEncaminhado').val('');
        });
    },
    autocompleteCargo : function(){
        Destino.zeraComandoCargo();
        /**
         * Juliano  24-06-2015 (Planilha de erro item 221 )
         *
         * autocomplente comentado pois quando destino for "interno" apresenta combo e quando for "externo"
         * o campo fica livre
         */
//        $('#noCargoEncaminhado').simpleAutoComplete('/artefato/pessoa/search-nome-cargo',{
//            autoCompleteClassName: 'autocomplete',
//            selectedClassName: 'sel',
//            attrCallBack: 'rel',
//            clearInput: true
//        });

    },
    zeraComandoCargo: function() {
        $('#noCargoEncaminhado').val('');
        $('#noCargoEncaminhado').removeAttr('autocomplete');
        $('#noCargoEncaminhado').removeAttr('name');
        $('#noCargoEncaminhado').attr('name', 'noCargoEncaminhado');
        $('#noCargoEncaminhado_hidden').remove();
    },
    zeraComando: function() {
        $('#sqPessoa').val('');
        $('#sqPessoa').removeAttr('autocomplete');
        $('#sqPessoa').removeAttr('name');
        $('#sqPessoa').attr('name', 'sqPessoa');
        $('#sqPessoa_hidden').remove();
        Destino.acaoFocus();
    },
    setaAutocompletePessoa: function(vl) {
        Destino.zeraComando();
        $('#sqPessoa').simpleAutoComplete("/artefato/pessoa/autocomplete/extraParam/" + vl, {
            extraParamFromInput: vl.toString(),
            attrCallBack: 'id',
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel'
        },function(){
            Destino.searchDadosPessoa();
        });
    },

    searchDadosPessoa: function() {
        var tipoPessoa = $("#sqTipoPessoaDestino").val();
        var nacionalidade = $("input[name=tpNacionalidadeDestino]:checked").val();
        if (tipoPessoa == '1') {
            var nuCpfCnpjPassaporte = $('#nuCPFDestino').val();
            var label = 'CPF';
            if (nacionalidade != 1) {
                nuCpfCnpjPassaporte = $('#nuPassaporteDestino').val();
                label = 'Nº do Passaporte';
            }
        } else {
            var nuCpfCnpjPassaporte = $('#nuCNPJDestino').val();
            var label = 'CNPJ';
        }

        if (!/Unidade/.test($('#sqTipoPessoaDestino option:selected').text())) {
            if ($('#sqPessoa_hidden').val() || $('#nuCNPJDestino').val() || $('#nuCPFDestino').val() ||
                    $('#nuCNPJOrigem').val() || $('#nuCPFOrigem').val()
             ) {
                var params = {
                        sqPessoaCorporativo: $('#sqPessoa_hidden').val(),
                        sqTipoPessoa: tipoPessoa,
                        sqNacionalidade: nacionalidade,
                        nuCpfCnpjPassaporte: nuCpfCnpjPassaporte
                };
    
                $.post('/artefato/pessoa/get-dados-pessoa', params, function(data) {
                    if ($.isArray(data) && data.length == 0) {
                        Message.showAlert(sprintf(UI_MSG.MN128, label));
                        $('#sqPessoa,#sqPessoa_hidden').val('');
                    } else {
                        Destino.setaValorDocumento(data, tipoPessoa);
                    }
                });
            }
        }
    },

    setaValorDocumento: function(doc, tipoPessoa) {
        if ($('#sqPessoa').val() != '') {
            switch (tipoPessoa) {
                case '1':
                    if(doc.nuPassaporte) {
                        $('#nuPassaporteDestino').val(doc.nuPassaporte);
                    } else {
                        $('#nuCPFDestino').val(Destino.cpfCnpj(doc.nuCpf));
                    }
                    break;
                case '2':
                    $('#nuCNPJDestino').val(Destino.cpfCnpj(doc.nuCnpj));
                    break;
            }
        }
        if ($('#sqPessoa').val() == '') {
            $('#sqPessoa').val(doc.noPessoa);
            $('#sqPessoa_hidden').val(doc.sqPessoa);
        }
    },

    acaoFocus : function(){
        $('#sqPessoa').keyup(function() {
            $('#nuCPFDestino, #nuCNPJDestino, #nuPassaporteDestino').val('');
        });
    },
    buscaCargoProfissional : function(){
        if($("input[name='destinoInterno']:checked").val() == 'interno'){
            return $('#sqPessoaEncaminhado_hidden').val();
        }

        if($("input[name='destinoInterno']:checked").val() == 'externo'){
            if( $('#sqTipoPessoaDestino').val() == '1' || $('#sqTipoPessoaDestino').val() == '3' ){
                return $('#sqPessoa_hidden').val();
            } else {
                return $('#sqPessoaEncaminhado_hidden').val();
            }
        }
    },
    inicializaFormulario: function() {
        Destino.autocomplete();
        Destino.camposInternosExternos();
        Destino.autocompleteCargo();
    },
    carregaNoCargo: function(){
        if(!$('#sqPessoaEncaminhado').hasClass('disabled')){
            var sqPessoa = $('#sqPessoaEncaminhado_hidden').val() ?
                $('#sqPessoaEncaminhado_hidden').val() :
                $('#sqPessoaIcmbioDestino_hidden').val();
//            if(sqPessoa != ''){
//                $.ajax({
//                    type : 'post',
//                    url : '/artefato/pessoa/cargo-pessoa-interna',
//                    data : 'sqPessoa=' + sqPessoa,
//                    async : false,
//                    global : false,
//                    dataType : 'json',
//                    success : function(data) {
//                        $('#noCargoEncaminhado_hidden').val(data['noCargo'])
//                        $('#noCargoEncaminhado').val(data['noCargo'])
//                    },
//                    error : function(){
//                        $('#noCargoEncaminhado_hidden').val('');
//                        $('#noCargoEncaminhado').val('');
//                    }
//                });
//            }
        }
    },
    limpaNoCargo: function(){
        if(!$('#sqPessoaEncaminhado').hasClass('disabled')){
            $('#noCargoEncaminhado_hidden').val('');
            $('#noCargoEncaminhado').val('');
        }
    },
    initCampos: function() {
        //        Destino.removeObrigatoriedade(3); // remove a obrigatoriedade de todos os campos de destinatario
        Destino.hideCampo(1);             // esconde todos os campos do formulario de destinatario
        Destino.inicializaFormulario();   // inicializando o formulario, setando acoes, exibindo grupos e etc.

        $("input[name='destinoInterno']").addClass('required');

        $("input[name='destinoInterno']").click(function() {
            Destino.limpaCampo(1);

            $('#sqPessoaIcmbioDestino_hidden').val('');
            $('#sqPessoaEncaminhado_hidden').val('');
            $('#noCargoEncaminhado_hidden').val('');
            $('#sqPessoa_hidden').val('');
            $('#cb_noCargoEncaminhado').val('');

            Destino.hideCampo(2);
            Destino.camposInternosExternos();
            Encaminhado.verificaCondicao();

            if($(this).val() == 'interno'){
                $('#noCargoEncaminhado,#noCargoEncaminhado_hidden').prop('disabled','disabled').hide();

                $('.divDestinoSqPessoaEncaminhadoExterno').hide();

                $('#cb_noCargoEncaminhado').removeProp('disabled').show();
            }else{
                $('#cb_noCargoEncaminhado').prop('disabled','disabled').hide();
                $('#noCargoEncaminhado,#noCargoEncaminhado_hidden').removeProp('disabled').show();
                $(this).parent('.controls').parent('.control-group').removeClass('error');
            }
        });

        $('#sqTipoPessoaDestino').change(function() {
            Destino.removeObrigatoriedade(3);
            Destino.limpaCampo(2);
            Destino.hideCampo(3);
            Destino.tipoDestino();
            Encaminhado.verificaCondicao();
            Destino.autocompleteCargo();
        });

        $('input[name=tpNacionalidadeDestino]').click(function() {
            Destino.removeObrigatoriedade(3);
            Destino.limpaCampo(2);
            Destino.hideCampo(3);
            Destino.tipoDestino();
        });

        $('#sqTipoPessoaDestinoIcmbio').change(function() {
            Destino.removeObrigatoriedade(3);
            Destino.limpaCampo(3);
            Destino.hideCampo(3);
            Destino.tipoDestinoIcmbio();
            Encaminhado.verificaCondicao();

            $('#sqPessoaEncaminhado_hidden').val('');
            $('#sqPessoaEncaminhado').val('');

            $('#noCargoEncaminhado_hidden').val('');
            $('#noCargoEncaminhado').val('');
        });

        
        $('#stCargoFuncao1').on('click', Destino.handleOnCargo);
        $('#stCargoFuncao2').on('click', Destino.handleOnFuncao);
    },
    init: function(){
//        $('#sqPessoaIcmbioDestino').blur(function(){
//            if ($('#sqTipoPessoaDestinoIcmbio').val() == 1){
//                Destino.carregaNoCargo();
//            }
//        });
    },

    cpfCnpj: function(v)
    {
        if( v ) {
            v=v.replace(/\D/g,"")
            if (v.length <= 14) { //CPF
                v=v.replace(/(\d{3})(\d)/,"$1.$2")
                v=v.replace(/(\d{3})(\d)/,"$1.$2")
                v=v.replace(/(\d{3})(\d{1,2})$/,"$1-$2")
            } else { //CNPJ
                v=v.replace(/^(\d{2})(\d)/,"$1.$2")
                v=v.replace(/^(\d{2})\.(\d{3})(\d)/,"$1.$2.$3")
                v=v.replace(/\.(\d{3})(\d)/,".$1/$2")
                v=v.replace(/(\d{4})(\d)/,"$1-$2")
            }
        }
        return v;
    },
    
    handleOnCargo: function(){
        var isChecked = $(this).is(':checked');
        
        if( isChecked ) {
            $("#divCargo").removeClass('hidden').show();
            $("#divFuncao").addClass('hidden').hide();
            $("#cb_noFuncaoEncaminhado").val('');
        } else {
            $("#divFuncao").addClass('hidden').hide();
            $("#divCargo").removeClass('hidden').show();
        }
    },
    
    handleOnFuncao: function(){
        var isChecked = $(this).is(':checked');
        
        if( isChecked ) {
            $("#divFuncao").removeClass('hidden').show();
            $("#divCargo").addClass('hidden').hide();
            $("#cb_noCargoEncaminhado").val('');
        } else {
            $("#divCargo").addClass('hidden').hide();
            $("#divFuncao").removeClass('hidden').show();
        }
    }
};
