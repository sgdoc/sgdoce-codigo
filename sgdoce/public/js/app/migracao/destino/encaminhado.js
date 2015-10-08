var Encaminhado = {
    /**
     * @param vl é o nome da pessa, blz?
     * @param co é o codigo da pessoa, sacou?
     */
    init : function(vl, co){
        // iniciando a verificacao dos campos
        Encaminhado.verificaCondicao();

        $('#sqPessoaEncaminhado').val(vl);
        $('#sqPessoaEncaminhado_hidden').val(co);
    },

    verificaCondicao : function(){

        if ($("input[name='destinoInterno']:checked").val() == 'interno') {
            Encaminhado.zeraComando();

            switch ( $('#sqTipoPessoaDestinoIcmbio').val() ){
                case '1':
                    $('#sqPessoaEncaminhado').removeClass('disabled');
                    $('#sqPessoaEncaminhado').removeAttr('readonly');

                    /*colocar a habilitação do combo de cargo*/
                    $('#noCargoEncaminhado,#noCargoEncaminhado_hidden').prop('disabled','disabled').hide();
                    $('#cb_noCargoEncaminhado').removeProp('disabled').show();


                    break;
                case '2':
                    $('#sqPessoaEncaminhado').removeClass('disabled');
                    $('#sqPessoaEncaminhado').removeAttr('readonly');
                    break;
                default :
                    /*colocar a habilitação do combo de cargo*/
                    /*colocar a habilitação do combo de cargo*/
                    $('#noCargoEncaminhado,#noCargoEncaminhado_hidden').prop('disabled','disabled').hide();
                    $('#cb_noCargoEncaminhado').removeProp('disabled').show();
            }
            
            
            $('#sqPessoaEncaminhado').simpleAutoComplete("/migracao/vinculo/search-pessoa-fisica", {
                extraParamFromInput: '#sqPessoaIcmbioDestino_hidden',
                attrCallBack: 'rel',
                autoCompleteClassName: 'autocomplete',
                selectedClassName: 'sel'
            });
            
            return true;
        }

        if ($("input[name='destinoInterno']:checked").val() == 'externo') {

            Encaminhado.zeraComando();

            switch ( $('#sqTipoPessoaDestino').val() ){
                case '1':
                case '3':
//                    $('#sqPessoaEncaminhado').removeClass('required');
                    $('#sqPessoaEncaminhado').addClass('disabled');
                    $('#sqPessoaEncaminhado').attr('readonly','readonly');
                    $('#praConstar').hide();
                    break;
                case '2':
                case '5':
//                    $('#sqPessoaEncaminhado').addClass('required');
                    $('#sqPessoaEncaminhado').removeClass('disabled');
                    $('#sqPessoaEncaminhado').removeAttr('readonly');
                    $('#praConstar').show();

                    $('#sqPessoaEncaminhado').simpleAutoComplete("/migracao/vinculo/search-pessoa-fisica", {
                        attrCallBack: 'rel',
                        autoCompleteClassName: 'autocomplete',
                        selectedClassName: 'sel'
                    });
                    break;
            }
        }
        return true;
    },

    zeraComando: function() {
        $('#sqPessoaEncaminhado_hidden').remove();
        $('#sqPessoaEncaminhado').removeAttr('autocomplete');
        $('#sqPessoaEncaminhado').removeAttr('name');
        $('#sqPessoaEncaminhado').attr('name', 'sqPessoaEncaminhado');
        return true;
    },

    clearData : function(){
        return true;
    },

    autocomplete : function(){
        $('#sqPessoaEncaminhadoExterno').simpleAutoComplete("/migracao/vinculo/search-pessoa-fisica", {
            extraParamFromInput: '#pesquisaEncaminhamento',
            attrCallBack: 'rel',
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel'
        });
    }
};