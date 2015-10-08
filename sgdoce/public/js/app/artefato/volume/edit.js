var VolumeEdit = 
{
    init : function()
    {
        $('#nuFolhaFinal,#nuFolhaInicial').setMask({
            mask: '9',
            type: 'repeat'
        });
        
        $('#dtAbertura,#dtEncerramento').setMask('39/19/9999');
        
        VolumeEdit.events();
        VolumeEdit.autocomplete();
        
        if ($('#isVolumeAberto').val()) {
            $('#isVolumeAbertoSim').prop('checked', true);
            $("#grupoEncerramento").hide();
        } else {
            $('#isVolumeAbertoNao').prop('checked', true).trigger('click');
        }
        
        if ($('#sqCargoAssinaturaAbertura_hidden').val()) {
            $('#sqCargoAssinaturaAbertura').val($('#sqCargoAssinaturaAbertura_hidden').val());
            $('#stCargoFuncaoAbertura1').prop('checked', true).trigger('click');
        } else {
            $('#sqFuncaoAssinaturaAbertura').val($('#sqFuncaoAssinaturaAbertura_hidden').val());
            $('#stCargoFuncaoAbertura2').prop('checked', true).trigger('click');
        }
        
        if ($('#sqCargoAssinaturaEncerramento_hidden').val()) {
            $('#sqCargoAssinaturaEncerramento').val($('#sqCargoAssinaturaEncerramento_hidden').val());
            $('#stCargoFuncaoEncerramento1').prop('checked', true).trigger('click');
        }
        if ($('#sqFuncaoAssinaturaEncerramento_hidden').val()) {
            $('#sqFuncaoAssinaturaEncerramento').val($('#sqFuncaoAssinaturaEncerramento_hidden').val());
            $('#stCargoFuncaoEncerramento2').prop('checked', true).trigger('click');
        }
    },

    events: function () 
    { 
        $('#btnSubmit').click(VolumeEdit.update);
        $('#btnCancel').click(VolumeEdit.closeModal);

        $(".isVolumeAberto")                                    .on('click', VolumeEdit.handleVolumeAberto);
        $('#stCargoFuncaoAbertura1,#stCargoFuncaoEncerramento1').on('click', VolumeEdit.handleOnCargo);
        $('#stCargoFuncaoAbertura2,#stCargoFuncaoEncerramento2').on('click', VolumeEdit.handleOnFuncao);
    
        $('#dtAbertura').datepicker({
            format: 'dd/mm/yyyy',
            language: 'pt-BR',
            autoclose: true,
            setDate: new Date($('#dtAbertura').val())
        });
        
        $('.btn-calendar-open').click(function(){
            $('#dtAbertura').focus();
            return false;
        });
        
        $('#dtEncerramento').datepicker({
            format: 'dd/mm/yyyy',
            language: 'pt-BR',
            autoclose: true,
            setDate: new Date($('#dtEncerramento').val())
        });
        
        $('.btn-calendar-close').click(function(){
            $('#dtEncerramento').focus();
            return false;
        });

    },
    
    closeModal:  function(event) 
    {
        event.preventDefault();
        var _url = sprintf(
                '/artefato/volume/grid/id/%d/back/%s',
                $('#sqArtefato').val(),
                AreaTrabalho.getUrlBack()
                );
        AreaTrabalho.initModal(_url);
        return false;
    },
    
    update:  function(event) 
    {
        event.preventDefault();
        try {
            if(VolumeEdit.valid() && $("#formVolume").valid())
            {
                $.ajax({
                    type: "POST",
                    url: "/artefato/volume/update",
                    data: $('#formVolume').serialize(),
                }).success(function(result) {
                    if (result == '') {
                        var _url = sprintf(
                                '/artefato/volume/grid/id/%d/back/%s',
                                $('#sqArtefato').val(),
                                AreaTrabalho.getUrlBack()
                                );
                        AreaTrabalho.initModal(_url);
                    } else {
                        Message.showError(result.message);
                    }
                    $('#btnSubmit').attr('disabled', false);
                }).error(function(err) {
                    Message.showError("Ocorreu um erro inesperado na execução.");
                    $('#btnSubmit').attr('disabled', false);
                });
            }
        } catch (e) {
            VolumeEdit.errorMessage(e.message);
            $("#modal_container_xl_size").scrollTop(0);
        }
    },

    valid: function () 
    {
        $('#formVolume').find('.control-group').each(function() {
            $(this).removeClass('error');
        });
        if (this.isEmpty($('#sqVolume'))) {
            throw {"code": 301, "message": "O identificador do volume não foi encontrado."};
        }
        if (this.isEmpty($('#dtAbertura'))) {
            $('#dtAbertura').parent('div').parents('div.control-group').addClass('error');
            throw {"code": 301, "message": "O campo Data de Abertura é de preenchimento obrigatório."};
        }
        if (VolumeEdit.isDtAberturaBeforeDtEncerramentoPreviousVolume()) {
            $('#dtAbertura').parent('div').parents('div.control-group').addClass('error');
            throw {"code": 301, "message": "A data de abertura deve ser igual ou posterior à Data de Encerramento do volume anterior."};
        }
        if ($('input[name="isVolumeAberto"]:checked').val() == '0') {
            if (this.isEmpty($('#nuFolhaFinal'))) {
                $('#nuFolhaFinal').parent('div').parents('div.control-group').addClass('error');
                throw {"code": 301, "message": "O campo Última Folha é de preenchimento obrigatório."};
            }
            var nuFolhaInicial = parseInt($('#nuFolhaInicial').val());
            var nuFolhaFinal   = parseInt($('#nuFolhaFinal').val());
            if (nuFolhaFinal <= nuFolhaInicial) {
                $('#nuFolhaFinal').parent('div').parents('div.control-group').addClass('error');
                throw {"code": 301, "message": "A Folha Final deve ser maior que a Folha Inicial."};
            }
            if ((nuFolhaFinal - nuFolhaInicial) > 200) {
                $('#nuFolhaFinal').parent('div').parents('div.control-group').addClass('error');
                throw {"code": 301, "message": "Volume não pode ter mais de 200 páginas."};
            }
            if (this.isEmpty($('#dtEncerramento'))) {
                $('#dtEncerramento').parent('div').parents('div.control-group').addClass('error');
                throw {"code": 301, "message": "O campo Data de Encerramento é de preenchimento obrigatório."};
            }
            if (VolumeEdit.isDtEncerramentoBeforeDtAbertura()) {
                $('#dtEncerramento').parent('div').parents('div.control-group').addClass('error');
                throw {"code": 301, "message": "A Data de Encerramento não pode ser anterior à Data de Abertura."};
            }
            if (VolumeEdit.isDtEncerramentoAfterDtAberturaNextVolume()) {
                $('#dtEncerramento').parent('div').parents('div.control-group').addClass('error');
                throw {"code": 301, "message": "A Data de Encerramento não pode ser posterior à Data de Abertura do próximo volume."};
            }
        }
        return true;
    },

    isEmpty: function (elm) { return !elm.val().trim(); },

    errorMessage: function (message) 
    {
        var container = $("#formVolume").closest("div");
        var divError  = $(".alert-error");

        if (!divError.length) {
            divError = $("<div>").addClass("alert alert-error campos-obrigatorios hidden");
            container.prepend(divError);
        }

        divError.removeClass("hidden")
                .html('<button class="close" data-dismiss="alert">×</button>' + message);
    },
    
    handleVolumeAberto: function()
    {
        if( $(this).val() == 1 ){
            $("#grupoEncerramento").hide();
            $("#nuFolhaFinal,#dtEncerramento,#sqPessoaAssinaturaEncerramento,#sqPessoaAssinaturaEncerramentoBD_hidden,#sqCargoAssinaturaEncerramento,#sqFuncaoAssinaturaEncerramento").val('');
        } else {
            $("#grupoEncerramento").show();
        }
    },

    autocomplete: function()
    {
        $('#sqPessoaAssinaturaAbertura').simpleAutoComplete("/artefato/despacho-interlocutorio/search-pessoa-unidade/", {
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel'
        });
        $('#sqPessoaAssinaturaEncerramento').simpleAutoComplete("/artefato/despacho-interlocutorio/search-pessoa-unidade/", {
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel'
        });
    },
    
    handleOnCargo: function()
    {
        var isChecked = $(this).is(':checked');
        var type      = ($(this).attr("id") == 'stCargoFuncaoAbertura1') ? 'Abertura' : 'Encerramento';

        if( isChecked ) {
            $('#divCargo' +type).removeClass('hidden').show();
            $('#divFuncao'+type).addClass('hidden')   .hide();

            $('#sqCargoAssinatura' +type).addClass('required');
            $('#sqFuncaoAssinatura'+type).val('');
        }
    },
    
    handleOnFuncao: function()
    {
        var isChecked = $(this).is(':checked');
        var type      = ($(this).attr("id") == 'stCargoFuncaoAbertura2') ? 'Abertura' : 'Encerramento';
        
        if( isChecked ) {
            $('#divFuncao'+type).removeClass('hidden').show();
            $('#divCargo' +type).addClass('hidden')   .hide();
            
            $('#sqFuncaoAssinatura'+type).addClass('required');
            $('#sqCargoAssinatura' +type).val('');
        }
    },
    
    isDtAberturaBeforeDtEncerramentoPreviousVolume: function ()
    {
        if (!$('#dataMin').val()) {
            return false;
        }
        var dtEncerramentoPreviousVolume = VolumeEdit.convertStringToDate($('#dataMin').val());
        var dtAbertura                   = VolumeEdit.convertStringToDate($('#dtAbertura').val());

        if (dtAbertura >= dtEncerramentoPreviousVolume) {
            return false;
        } else {
            return true;
        }
    },

    isDtEncerramentoBeforeDtAbertura: function ()
    {
        var dtAbertura     = VolumeEdit.convertStringToDate($('#dtAbertura').val());
        var dtEncerramento = VolumeEdit.convertStringToDate($('#dtEncerramento').val());

        if (dtEncerramento >= dtAbertura) {
            return false;
        } else {
            return true;
        }
    },

    isDtEncerramentoAfterDtAberturaNextVolume: function ()
    {
        if (!$('#dataMax').val()) {
            return false;
        }
        
        var dtEncerramento       = VolumeEdit.convertStringToDate($('#dtEncerramento').val());
        var dtAberturaNextVolume = VolumeEdit.convertStringToDate($('#dataMax').val());

        if (dtEncerramento <= dtAberturaNextVolume) {
            return false;
        } else {
            return true;
        }
    },

    convertStringToDate: function (d)
    {
        var dtArray = d.match(/^(\d{1,2})(\/|-)(\d{1,2})(\/|-)(\d{4})$/);

        dtMonth = dtArray[3];
        dtDay   = dtArray[1];
        dtYear  = dtArray[5];

        return new Date(dtYear,dtMonth-1, dtDay);
    }
}

$(document).ready(function() {
    VolumeEdit.init();
});