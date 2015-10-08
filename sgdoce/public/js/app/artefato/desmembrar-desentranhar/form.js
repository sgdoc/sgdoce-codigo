ProcessoDesentranhar = {

    init: function(){
    	ProcessoDesentranhar.events().mask().autocomplete();
    },

    events: function(){
        $("#btn_cancelar").click(function(){
            Message.showConfirmation({
                'body': "Tem certeza que deseja cancelar a operação?",
                'yesCallback': function(){
                    window.location = '/artefato/desmembrar-desentranhar';
                }
            });
        });

        $(".btnCancelar").on('click', function(){
            $(".modal:visible").modal('hide').html("");
        });

        $(".btnConcluir").on('click', function(){
            var form = $(this).parents().find('form');
            if(form.valid()){
                form.submit();
                $(".modal:visible").modal('hide').html("");
            }
            return false;
        });

        $('.date').datepicker({
            format: 'dd/mm/yyyy',
            language: 'pt-BR',
            autoclose: true
        });

        return ProcessoDesentranhar;
    },

    mask: function(){
    	$(".txNuPecas").keypress(function(event){
    		var value = event.key.replace(/[^\d,-]+/g, '');
    		if( value == "" && $.inArray(event.keyCode, [8,9,13,16,17,18,19,20,27,33,34,35,36,37,38,39,40,45,46]) < 0 ) { return false; }
    	});

        return ProcessoDesentranhar;
    },

    autocomplete: function(){
     	$('#sqUnidadeSolicitacao').simpleAutoComplete("/artefato/desmembrar-desentranhar/search-unidade-org/", {
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel'
     	});

     	$('#sqPessoaAssinatura').simpleAutoComplete("/artefato/desmembrar-desentranhar/search-pessoa-assinatura/", {
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel'
     	});

     	$('#sqArtefatoDestino').simpleAutoComplete("/artefato/desmembrar-desentranhar/search-artefato-destino/", {
            extraParamFromInput: '#sqArtefato',
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel'
     	});

        return ProcessoDesentranhar;
    }
};

$(ProcessoDesentranhar.init);

