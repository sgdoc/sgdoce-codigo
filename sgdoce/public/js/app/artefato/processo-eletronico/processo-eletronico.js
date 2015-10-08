var ProcessoEletronico = {
    init: function(){
        sessionStorage.setItem('origemExterna', $('#chekProcedenciaExterno').is(':checked'));
        if (sessionStorage.getItem('origemExterna') == 'true') {
            $('#dtPrazo').removeClass('required');
        } else {
            $('#dtPrazo').addClass('required');
        }

        $('input[name=procedenciaInterno]').click(function () {
            if ($('#chekProcedenciaExterno').is(':checked')) {
                sessionStorage.setItem('origemExterna', 'true');
                $('#dtPrazo').removeClass('required');
            } else {
                sessionStorage.setItem('origemExterna', 'false');
                $('#dtPrazo').addClass('required');
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
                    sessionStorage.clear();
                }else{
                    valid = false;
                }
            });

            // Verifica se existe pelo menos um interessados.
            if( $("input[name*='dataInteressado']").length <= 0
                && $("#table-interessado tbody tr:not(.mensagemInteressado)").length <= 0 ) {
                ProcessoEletronico.show._msgNoInteressado();
                return false;
            }

            // Verifica se existe pelo menos um interessados.
            if( $("input[name*='dataVolumes']").length <= 0
                && $("#table-interessado tbody tr:not(.mensagemVolume)").length <= 0 ) {
                ProcessoEletronico.show._msgNoVolume();
                return false;
            }
        });

        $('.tab').click(function(){
            var nuCurrTab = $(this).parents("li").index(),
                nuAbaVolume = 2,
                nuAbaInteressado = 3;

            if( $("a[href='#volume']").length == 0 ) {
                nuAbaVolume = -1;
                nuAbaInteressado = 2;
            }

            // VERIFICA SE É ABA VOLUME
            if( nuCurrTab == nuAbaVolume ) {
                if($("input[name*='dataVolume']").length <= 0
                   && $("#table-volume tbody tr:not(.mensagemVolume)").length <= 0  ) {
                    $('li.active').prev('li').children().click();
                    $('#btnProximo').addClass('btn-primary');
                    $('#btnProximo i').addClass('icon-white');
                    ProcessoEletronico.show._msgNoVolume();
                    return false;
                }
            }

            // VERIFICA SE É ABA INTERESSADO
            if( nuCurrTab == nuAbaInteressado ) {
                // Verifica se existe pelo menos um interessados.
                if($("input[name*='dataInteressado']").length <= 0
                   && $("#table-interessado tbody tr:not(.mensagemInteressado)").length <= 0  ) {
                $('li.active').prev('li').children().click();
                $('#btnProximo').addClass('btn-primary');
                $('#btnProximo i').addClass('icon-white');
                    ProcessoEletronico.show._msgNoInteressado();
                    return false;
                }
            }
        });

        $('.btn-cancelar').click(function() {
            ProcessoEletronico.sair();
        });

        $('.TipoPrioridade').hide();
        $('#sqPrioridade').change(function() {
                $('.TipoPrioridade').show();
                if ($('#sqPrioridade').val() != '') {
                    $('#divTipoPrioridade').load(
                            '/auxiliar/tipo-prioridade/combo-tipo-prioridade/sqPrioridade/'
                                    + $('#sqPrioridade').val());
                }else{
                    $('#sqTipoPrioridade').val('');
                     $('.TipoPrioridade').hide();
                }
        });

        if($('#sqPrioridade').val()){
             $('.TipoPrioridade').show();
        }

        if ($('#dtPrazo').val()){
            $('#sqPrazo').val('1');
        }
        if ($('#nuDiasPrazo').val()){
            $('#sqPrazo').val('2');
        }

        ProcessoEletronico.tipoData();
        $('#sqPrazo').change(function(){
            ProcessoEletronico.tipoData();
        });

        ProcessoEletronico.diaCorrido();
        $('#inDias').change(function(){
            ProcessoEletronico.diaCorrido();
        });

        if( $("#stBloqueioArtefato").val() ){
            $('input, select, textarea').attr('disabled', true);
            $('#btnAdicionarVincularDocumento').attr('disabled', true)
                                               .removeAttr('id', null)
                                               .unbind('click');
        }

        $("#nuPaginaProcesso").blur(function(){
            var nuPaginas = $(this).val(),
                nuVolume  = Math.ceil(nuPaginas/200);

            $("#nuVolume").attr('min', nuVolume);
        });

        $(".infolink").tooltip();

        if( $("#procedencia").length > 0 ) {
            var procedencia = $("#procedencia").val();
            $("input[name='procedenciaInterno'][value='" + procedencia + "']").attr('checked', 'true');
            $("input[name='procedenciaInterno']").attr('disabled', 'disabled');
            $('<input>').attr('type', 'hidden')
                        .attr('name', 'procedenciaInterno')
                        .attr('id', 'procedenciaHidden')
                        .val($("input[name='procedenciaInterno']:checked").val()).appendTo('form');
        }

        ProcessoEletronico.handleTaggleDisableBtnConcluir();
    },

    diaCorrido: function(){
        switch ($('#inDias').val()) {
        case '1':
            $('#nuDiasPrazo').addClass('required');
            $('#inDiasCorridos').val(false);
            $('#sqPrazo').val('2');
            $('.dvDiasPrazo').removeClass('hidden');
            $('.dv2-dtPrazo').removeClass('hidden');
            break;
        case '2':
            if (sessionStorage.getItem('origemExterna') == 'true'){
                $('#nuDiasPrazo').removeClass('required');
            }
            $('#sqPrazo').val('2');
            $('#inDiasCorridos').val(true);
            $('.dvDiasPrazo').removeClass('hidden');
            $('.dv2-dtPrazo').removeClass('hidden');
            break;
        }
    },

    tipoData: function(){
        switch ($('#sqPrazo').val()) {
        case '1':
            if (sessionStorage.getItem('origemExterna') == 'true') {
                $('#dtPrazo').removeClass('required');
            } else {
                $('#dtPrazo').addClass('required');
            }

            $('.dvDataPrazo').show();
            $('.dvDiasPrazo').hide();
            $('#inDiasCorridos').val(null);
            $('#nuDiasPrazo').val(null);
            break;
        case '2':
            $('.dvDataPrazo').hide();
            $('.dvDiasPrazo').show();
            $('#dtPrazo').val('');
            break;
        default:
            $('.dvDataPrazo').hide();
            $('.dvDiasPrazo').hide();
            $('#inDiasCorridos').val(null);
            break;
        }
    },

    sair: function() {
        var acao = ($('#sqArtefato').val()) ? ' a alteração do cadastro?' : ' o cadastro?';
        Message.showConfirmation({
            'body': 'Tem certeza que deseja cancelar' + acao,
            'yesCallback': function(){
                var sqTipoArtefato = $("#sqTipoArtefato").val();
                window.location = '/artefato/area-trabalho/index/tipoArtefato/' + sqTipoArtefato;
            }
        });
        return false;
    },

    reloadDivImagem : function() {
        $('#dadosImagem').html('');
        ProcessoEletronico.assingContentImage();
    },

    assingContentImage : function() {
        if($('#sqArtefato').val()){
            $.get('artefato/imagem/list',{
                id: $('#sqArtefato').val(),
                obrigatoriedade: true
                },
            function(data){
                $('#dadosImagem').html(data);
                $('.thumbnail').css('height', 276);
            });
        }
    },

    show : {
        _msgNoInteressado : function(){
            Validation.addMessage("É necessário pelo menos um interessado no cadastro do processo!");
        },
        _msgNoVolume : function(){
            Validation.addMessage("É necessário pelo menos um volume no cadastro do processo!");
        }
    },

    handleTaggleDisableBtnConcluir: function () {
        var btnConcluir = $('.btn-concluir');

        //se for cadastro
        if ($('#sqArtefato').val() == '') {
            $('.tab').click(function () {
                var nuCurrTab = $(this).parents("li").index(); //3 = assunto

                if (nuCurrTab == 3 && $('#sqAssunto').is(':visible')) {
                    //quando chegar na aba interessado habilita o concluir caso esteja desabilitado
                    if (btnConcluir.is(':disabled')) {
                        btnConcluir.removeProp('disabled');
                    }
                }
            });
        }
        return ProcessoEletronico;
    }

};

$(document).ready(function(){
    ProcessoEletronico.init();
//    ProcessoEletronico.assingContentImage();

    $('#sqAssunto').simpleAutoComplete("/auxiliar/assunto/searchassunto/", {
        extraParamFromInput: '#extra',
        attrCallBack: 'rel',
        autoCompleteClassName: 'autocomplete',
        selectedClassName: 'sel'
    });
});