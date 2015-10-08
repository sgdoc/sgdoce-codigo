var Form = {
    init : function() {
        Form.events();
        Form.tabs();
    },
    events : function() {

        $("#chekProcedenciaExterno,#chekProcedenciaInterno").attr('disabled', true).unbind('click');
        $('<input>').attr('type', 'hidden')
                    .attr('name', 'procedenciaInterno')
                    .attr('id', 'procedenciaHidden')
                    .val($("input[name='procedenciaInterno']:checked").val()).appendTo('form');

        $('#sqPrazo').change(function(){
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
                $('#sqPrazo').addClass('required');
                $('.dvDiasPrazo').hide();
                $('.dvDataPrazo').hide();
            }
        });

        $('.btn-concluir').click(function() {
            $('.tab').each(function(){
                $(this).click();
                if ($('#sqPrazo').val() == '2' && $('#inDias').val() == '2' && $('#nuDiasPrazo').val() == '') {
                    $('#nuDiasPrazo').val('30');
                }
                if($('form').valid()){
                    valid = true;
                } else {
                    valid = false;
                }
            });

            // Verifica se existe pelo menos um interessados.
            if($("input[name*='dataInteressado']").length <= 0 ) {
                Validation.addMessage("É necessário pelo menos um interessado!");
                return false;
            }
    	});

        $('#sqAssunto').simpleAutoComplete("/auxiliar/assunto/searchassunto/", {
            extraParamFromInput: '#extra',
            attrCallBack: 'rel',
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel'
 	});


        var sqPrazo = $("#sqPrazo").val();

        if( sqPrazo != '' ) {
            $("#sqPrazo").trigger('change');
            var dtPrazo = $(".dtPrazoDiv").attr('data-date');
            $("#dtPrazo").val(dtPrazo);
        }
        var table = $('.tableInteressado')
           ,tbody = table.find('tbody')
           ,go = true;

        $(".btnExcluirInteressado").click(function(){
            var btn = $(this);
            Message.showConfirmation({
                body: UI_MSG.MN018,
                yesCallback: function(){
                    btn.parents('tr').remove();
                    if (tbody.find('tr').length === 1) {
                        tbody.find('tr.mensagemInteressado').show();
                    }
                }
            });
            return false;
        });
        
        // Prioridade        
    	$('.TipoPrioridade').hide();
        $('#sqPrioridade').change(function() {
            $('.TipoPrioridade').show();
            if ($('#sqPrioridade').val() != '') {
                $('#divTipoPrioridade').load(
                    '/auxiliar/tipo-prioridade/combo-tipo-prioridade/sqPrioridade/'
                        + $('#sqPrioridade').val());
            } else {
                $('#sqTipoPrioridade').val('');
                $('.TipoPrioridade').hide();
            }
        });

        if($('#sqPrioridade').val()){
            $('.TipoPrioridade').show();
        }

    },
    tabs : function() {
        var valid = false;
        $('.tab').click(function(){

            var nuActiTab = $("li.active").index(),
                nuCurrTab = $(this).parents("li").index();

            if($('ul.tabsForm li').length > 1){

                var isValid = true;

                if( nuActiTab < nuCurrTab ) {
                    isValid = $('form').valid();
                }

                if( isValid ) {
                    if($(this).attr('href') == $('.tab:first').attr('href')){
                        $('#btnAnterior').attr('disabled', true);
                    }

                    if($(this).attr('href') != $('.tab:first').attr('href') && $(this).attr('href') != $('.tab:last').attr('href')){
                        $('#btnAnterior, #btnProximo').removeAttr('disabled');
                            $('.btn-concluir').removeClass('btn-primary');
                        $('#btnSalvar').addClass('hidden');
                    }

                    $('.campos-obrigatorios').addClass('hidden');
                    $(this).tab('show');
                }

                if($('ul.tabsForm li:last').hasClass('active')){
                    $('#btnAnterior').removeAttr('disabled');
                    $('.btn-concluir').addClass('btn-primary');
                    $('#btnProximo').removeClass('btn-primary');
                    $('#btnProximo i').removeClass('icon-white');
                    $('#btnProximo').attr('disabled', true);
                    $('#btnSalvar').removeClass('hidden');
                }else{
                    $('.btn-concluir').removeClass('btn-primary');
                    $('#btnProximo').removeAttr('disabled');
                    $('#btnProximo').addClass('btn-primary');
                    $('#btnProximo i').addClass('icon-white');
                }
            }

            return false;
        });

        $('#btnProximo').click(function(){
            $('li.active').next('li').children().click();
        });

        $('#btnAnterior').attr('disabled', true);

        $('#btnAnterior').click(function(){
            $('li.active').prev('li').children().click();
            $('#btnProximo').addClass('btn-primary');
            $('#btnProximo i').addClass('icon-white');
        });

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            if($(this).attr('href') == $('.tab:last').attr('href')){
                $('#btnProximo').attr('disabled', true);
            }else{
                $('#btnProximo').attr('disabled', false);
            }
            if ($(this).attr('href') == $('.tab:first').attr('href')){
                $('#btnAnterior').attr('disabled', true);
            }else{
                $('#btnAnterior').attr('disabled', false);
            }
        });
    }
};

$(Form.init);