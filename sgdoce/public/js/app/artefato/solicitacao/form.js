var Form = {
    init: function () {
        Form.events();
    },
    events: function () {
        $(".sqTipoArtefato").click(function () {
            var sqTipoArtefato = $(this).val();
            if (sqTipoArtefato > 0) {
                if ($("#sqArtefato").parents(".control-group").not(":visible").length > 0) {
                    $("#sqArtefato").parents(".control-group").show();
                    $("#sqArtefato_hidden").removeAttr('disabled');
                }
            } else {
                $("#sqArtefato_hidden").attr('disabled', 'disabled');
                $("#sqArtefato").parents(".control-group").hide();
                $("#sqTipoAssuntoSolicitacao").parents(".control-group").show();
            }
            $("#sqTipoAssuntoSolicitacao").val("");
            $("#sqArtefato").val("");
        });

        $('#sqArtefato').simpleAutoComplete("/artefato/solicitacao/search-artefato", {
            extraParamFromInput: '.sqTipoArtefato:checked',
            attrCallBack: 'rel',
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel'
        });

        $("#btnSubmit").click(function () {
            if ($('#formModalSolicitacao').valid()) {
                Message.wait();
                $('#formModalSolicitacao').submit();
            }
        });

        $(".sqTipoArtefato").click(function () {
            var sqTipoArtefatoProcesso = $(this).val();
            $.post('/artefato/solicitacao/combo-tipo-solicitacao-assunto', {
                inTipoParaArtefato: sqTipoArtefatoProcesso
            },
            function (data) {
                Form.loadCombo(data, $('#sqTipoAssuntoSolicitacao'), false);
            });
        });
        
        /*$('#sqTipoAssuntoSolicitacao').change(function(){
            if ($('#demandaVolume').val() == $('#sqTipoAssuntoSolicitacao').val()) {
                $('#msg-alterar-volume').removeClass('hide');
            } else {
                $('#msg-alterar-volume').addClass('hide');
            }
        });*/
    },
    loadCombo: function (data, combo, selectedValue) {
        var html = ['<option value="" title="Selecione uma opção">Selecione uma opção</option>'];
        var firstItem = true;
        $.each(data, function (index, value, i) {
            if (firstItem && !selectedValue) {
                firstItem = false;
                html.push('<option selected="selected" value="' + index + '" title="' + value + '">' + value + '</option>');
            } else {
                html.push('<option value="' + index + '" title="' + value + '">' + value + '</option>');
            }
        });
        combo.html(html.join(''));
        if (selectedValue) {
            combo.val(selectedValue).change();
        }
    },
}