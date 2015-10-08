var Processo = {
    init: function () {
        Processo.events();
    },
    grid: function () {
        if (Processo.isFormEmpty($('#form-pesquisa-processo')) == false) {
            if ($('#grid-processo-container').is(':hidden')) {
                $('#grid-processo-container').show();
                Grid.load($('#form-pesquisa-processo'), $('#table-grid-processo'));
            } else {
                $("#form-pesquisa-processo").submit();
            }
        } else {
            Validation.addMessage("Informe pelo menos um campo para realizar a pesquisa.");
        }
    },
    isFormEmpty: function (form)
    {
        var isAllNull = true;
        $.each($(form).find("input,select,textarea").not("#stTipoPesquisa"), function (index, value) {
            if ($(value).val() != '') {
                isAllNull = false;
            }
        });
        return isAllNull;
    },
    events: function () {
        $(document).on('mouseover', '.tooltip_grid', function () {
            $(this).tooltip('show');
        }).on('mouseout', '.interessados', function () {
            $(this).tooltip('destroy');
        });

        $("#btnPesquisar").click(function () {
            Processo.grid();
            return false;
        });

        $('#sqAssunto').simpleAutoComplete("/auxiliar/assunto/searchassunto/", {
            extraParamFromInput: '#extra',
            attrCallBack: 'rel',
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel'
        });
    },
    fnc: {
//        vinculos: function (sqArtefato) {
//            Processo.windowOpen('/artefato/vinculo/motrar-arvore/sqArtefato/' + sqArtefato);
//        },
        detalhar: function (sqArtefato) {
            Processo.windowOpen('/artefato/visualizar-artefato/index/sqArtefato/' + sqArtefato + '#liDadosArtefato');
        },
        imagem: function (sqArtefato) {
            Processo.windowOpen(sprintf('/artefato/imagem/view/id/%d', sqArtefato));
        }
    },
    windowOpen: function (url) {
        window.open(url, '_blank', 'fullscreen=yes,location=no,menubar=no,scrollbars=yes');
    },
    initModal: function (_url) {
        var modalContainer = $("#modal_container");
        modalContainer.empty();
        modalContainer.load(_url, function (responseText, textStatus) {
            if (textStatus === 'success') {
                modalContainer.modal();
            } else {
                Message.showError(responseText);
            }
        });
    }

}

$(document).ready(function () {
    Processo.init();
});