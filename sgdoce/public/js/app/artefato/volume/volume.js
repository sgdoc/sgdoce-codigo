var Volume = {


    vars : {
        _keyVolume: -1,
        _urlFormVolume: '/artefato/volume/form',
        _lastNuVolume: 0,
        _lastNuFolhaFinal: 0,
        _hasVolumeAberto: false,
    },

    init : function()
    {
        $('#nuFolhaFinal,#nuFolhaInicial').setMask({
            mask: '9',
            type: 'repeat'
        });
        
        $('[name="dtAbertura"],[name="dtEncerramento"]').setMask('39/19/9999');
        
        Volume.events();
        Volume.autocomplete();
    },

    initModal : function(){
        $('#btnAdicionarVolume').click(function() {
            if( Volume.vars._hasVolumeAberto == false ) {
                var sqArtefato = (parseInt($('#sqArtefato').val())) ? parseInt($('#sqArtefato').val()) : null;

                $(".modal-backdrop, #modal-volume").show();

                if(sqArtefato){
                    $("#modal-volume").load(Volume.vars._urlFormVolume + "/id/" + sqArtefato).modal();
                } else {
                    $("#modal-volume").load(Volume.vars._urlFormVolume).modal();
                }
                $(document).on('shown.bs.modal', '#modal-volume', function (e) {
                    $('#nuFolhaFinal').setMask()
                });
            } else {
                Validation.addMessage("Não é possível adicionar um novo volume, com o último volume em aberto!");
            }
        });
    },

    events: function()
    {
        $(document).on('shown.bs.modal', '#modal-volume', function (e) {
            $(this).find(':input').filter('[mask]').setMask();
        });

        var options = {
            format: 'dd/mm/yyyy',
            language: 'pt-BR',
            autoclose: true
        };

        $('.date').datepicker(options);

        $(".btnAbriEncerrarVolume").on('click', function(){
            if( $("#form-volume-abrir:visible,#form-volume-encerrar:visible").valid() ){
                $("#form-volume-abrir:visible,#form-volume-encerrar:visible").submit();
                $(".modal:visible").modal('hide').html("");
            }
        });

        $(".btnConcluirVolume").on('click', function(){
            if( $("#form-volume-adicionar").valid() ){
                var dataPost = {
                        sqArtefato    : (parseInt($('#sqArtefato').val())) ? parseInt($('#sqArtefato').val()) : null,
                        nuVolume      : $('#nuVolumeInput').val(),
                        nuFolhaInicial: $('#nuFolhaInicial').val(),
                        nuFolhaFinal  : $('#nuFolhaFinal').val()
                };
                if ( !dataPost.sqArtefato ) {
                    if(!Volume.populateTable(dataPost)){
                        return false;
                    }else{
                        $("#modal-volume").modal('hide').html('');
                        Message.showSuccess(UI_MSG.MN013);
                        $('div.modal-footer:visible').find('a.btn-primary:visible').focus();
                    }
                }

                if( $(".form-processo-eletronico-principal #nuVolumeInput").length > 0 ) {
                    $(".form-processo-eletronico-principal #nuVolumeInput").val(Volume.vars._lastNuVolume);
                    $(".form-processo-eletronico-principal #nuPaginaProcesso").val(Volume.vars._lastNuFolhaFinal);
                }
            }
        });

        $(".isVolumeAberto").on('click', function(){
            var isAberto = $(this).val();

            if( isAberto == 1 ){
                $("#divNuFolhaFinal").hide();
                $("#nuFolhaFinal").val('');
                Volume.vars._hasVolumeAberto = true;
            } else {
                $("#divNuFolhaFinal").show();
                Volume.vars._hasVolumeAberto = false;
            }
        });

        if( $("#keyVolume").length > 0 ) {
            Volume.vars._keyVolume = $("#keyVolume").val();
        } else {
            Volume.vars._keyVolume = 0;
        }

        $('#stCargoFuncaoAbertura1,#stCargoFuncaoEncerramento1').on('click', Volume.handleOnCargo);
        $('#stCargoFuncaoAbertura2,#stCargoFuncaoEncerramento2').on('click', Volume.handleOnFuncao);
    },

    populateForm: function()
    {
        var table = $('#table-volume')
           ,tbody = table.find('tbody');

        if( $("#nuVolumeLast").length > 0
         || $("#nuFolhaFinalLast").length > 0 ) {
            Volume.vars._lastNuVolume       = $("#nuVolumeLast").val();
            Volume.vars._lastNuFolhaFinal   = $("#nuFolhaFinalLast").val();
         }

        Volume.vars._lastNuVolume++;
        Volume.vars._lastNuFolhaFinal++;
        $("#nuVolumeInput").val(Volume.vars._lastNuVolume)
                      .attr('min', Volume.vars._lastNuVolume)
                      .attr('max', Volume.vars._lastNuVolume)
                      .attr('disabled', true);
        $("#nuFolhaInicial").val(Volume.vars._lastNuFolhaFinal)
                            .attr('min', Volume.vars._lastNuFolhaFinal)
                            .attr('max', Volume.vars._lastNuFolhaFinal)
                            .attr('disabled', true);
        $("#nuFolhaFinal").attr('min', Volume.vars._lastNuFolhaFinal + 1)
                          .attr('max', Volume.vars._lastNuFolhaFinal + 199);
    },

    autocomplete: function()
    {
        $('#sqPessoaAssinaturaAbertura,#sqPessoaAssinaturaEncerramento').simpleAutoComplete("/artefato/despacho-interlocutorio/search-pessoa-unidade/", {
            attrCallBack: 'rel',
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel'
        });
    },

    populateTable:function(data){

        if( $("#keyVolume").length > 0 ) {
            Volume.vars._keyVolume = $("#keyVolume").val();
        } else {
            Volume.vars._keyVolume = 0;
        }

        var table = $('#table-volume')
           ,tbody = table.find('tbody')
           ,go = true;

        /*verifica duplicidade na grid */

        tbody.find('.hdn_nuVolume').each(function(i){
            if($(this).val() == data.nuVolume){
                go = false;
                Message.showAlert(Volume.show._msgDuplicidade);
                return false;
            }
        });

        if(!go){
            return false;
        }

        tbody.find('button.btnExcluirVolume:visible').hide();
        var nuFolhaFinalLabel = '';
        if( data.nuFolhaFinal == '' ) {
            data.nuFolhaFinal = 0;
        } else {
            nuFolhaFinalLabel = data.nuFolhaFinal;
        }

        var newTr            = $('<tr />')
           ,tdNuVolume       = $('<td />',{text:data.nuVolume}).appendTo(newTr)
           ,tdNuFolhaInicial = $('<td />',{text:data.nuFolhaInicial}).appendTo(newTr)
           ,tdNuFolhaFinal   = $('<td />',{text:nuFolhaFinalLabel}).appendTo(newTr)
           ,tdAction         = $('<td />').appendTo(newTr)
           ,btnAction        = $('<button />',{type:'button', class:'btn btn-mini btnExcluirVolume',title:'Excluir'});

            btnAction.click(function(){
                var btn = $(this);
                Message.showConfirmation({
                    body: UI_MSG.MN018,
                    yesCallback: function(){
                        var nuVolume = $("#nuVolumeLast").val() - 1,
                            nuFolhaFinal = btn.parents('tr').find("input[name*='nuFolhaInicial']").val() - 1;

                        btn.parents('tr').remove();
                        $('#table-volume > tbody').find('tr:first td:last').find('button.btnExcluirVolume').show();
                        $("#nuVolumeLast").val(nuVolume);
                        $("#nuFolhaFinalLast").val(nuFolhaFinal);
                        $("#keyVolume").val( $("#keyVolume").val() - 1 );
                        if (tbody.find('tr').length === 1) {
                            tbody.find('tr.mensagemVolume').show();
                        }
                        if( Volume.vars._hasVolumeAberto == true ) {
                            Volume.vars._hasVolumeAberto = false;
                        }
                    }
                });
            });
        $('<i />',{class:'icon-trash'}).appendTo(btnAction);

        btnAction.appendTo(tdAction);

        ++Volume.vars._keyVolume;

        var configVolume = {
            type:'hidden',
            name:'dataVolume['+Volume.vars._keyVolume+'][nuVolume]',
            value: data.nuVolume,
            class:'hdn_nuVolume'
        };

        $('<input />',configVolume).appendTo(tdNuVolume);
        $('<input />',{type:'hidden', name:'dataVolume['+Volume.vars._keyVolume+'][nuFolhaInicial]'  , value:data.nuFolhaInicial}).appendTo(tdNuFolhaInicial);
        $('<input />',{type:'hidden', name:'dataVolume['+Volume.vars._keyVolume+'][nuFolhaFinal]'    , value:data.nuFolhaFinal}  ).appendTo(tdNuFolhaFinal);

        tbody.find('tr.mensagemVolume').hide();
        tbody.prepend(newTr);

        Volume.vars._lastNuVolume = data.nuVolume;
        Volume.vars._lastNuFolhaFinal = data.nuFolhaFinal;
        Volume.setLastValues();

        return true;
    },

    setLastValues: function(){
        var table = $('#table-volume');

        if( $("#nuVolumeLast").length <= 0
            || $("#nuFolhaFinalLast").length <= 0 ) {
            $('<input />',{type:'hidden', name:'nuVolume', value: Volume.vars._lastNuVolume}).attr('id', 'nuVolumeLast').appendTo(table);
            $('<input />',{type:'hidden', name:'nuPaginaProcesso', value: Volume.vars._lastNuFolhaFinal}).attr('id', 'nuFolhaFinalLast').appendTo(table);
            $('<input />',{type:'hidden', name:'keyVolume', value: Volume.vars._keyVolume}).attr('id', 'keyVolume').appendTo(table);
        } else {
            $("#nuVolumeLast").val(Volume.vars._lastNuVolume);
            $("#nuFolhaFinalLast").val(Volume.vars._lastNuFolhaFinal);
            $("#keyVolume").val(Volume.vars._keyVolume);
        }
    },

    show : {
        _msgDuplicidade : "Item já incluído na lista."
    },

    fechar : function(){
        $(".modal:visible").modal('hide').html("");
        location.reload();
    },
    
    handleOnCargo: function()
    {
        var isChecked = $(this).is(':checked');
        var type      = ($(this).attr("id") == 'stCargoFuncaoAbertura1') ? 'Abertura' : 'Encerramento';
        
        if( isChecked ) {
            $('#divCargo') .removeClass('hidden').show();
            $('#divFuncao').addClass('hidden')   .hide();

            $('#sqCargoAssinatura' +type).addClass('required');
            $('#sqFuncaoAssinatura'+type).val('');
        }
    },
    
    handleOnFuncao: function()
    {
        var isChecked = $(this).is(':checked');
        var type      = ($(this).attr("id") == 'stCargoFuncaoAbertura2') ? 'Abertura' : 'Encerramento';
        
        if( isChecked ) {
            $('#divFuncao').removeClass('hidden').show();
            $('#divCargo') .addClass('hidden')   .hide();
            
            $('#sqFuncaoAssinatura'+type).addClass('required');
            $('#sqCargoAssinatura' +type).val('');
        }
    }
}

$(document).ready(function(){
    Volume.init();
});