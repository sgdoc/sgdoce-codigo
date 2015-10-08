var Documento = {
    init: function () {
        Documento.events();
    },
    grid: function () {
        if (Documento.isFormEmpty($('#form-pesquisa-documento')) == false) {
            if ($('#grid-documento-container').is(':hidden')) {
                $('#grid-documento-container').show();
                Grid.load($('#form-pesquisa-documento'), $('#table-grid-documento'));
            } else {
                $("#form-pesquisa-documento").submit();
            }
        } else {
            Validation.addMessage("Informe pelo menos um campo para realizar a pesquisa.");
        }
        return Documento;
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
            Documento.grid();
            return false;
        });

        $("#btnLimpar").click(function () {
            $('input', '#form-pesquisa-documento').each(function () { $(this).val(''); });
        });

        $('#sqAssunto').simpleAutoComplete("/auxiliar/assunto/searchassunto/", {
            extraParamFromInput: '#extra',
            attrCallBack: 'rel',
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel'
        });
        return Documento;
    },
    fnc: {
//        vinculos: function (sqArtefato) {
//            Documento.windowOpen('/artefato/vinculo/motrar-arvore/sqArtefato/' + sqArtefato);
//        },
        imagem: function (sqArtefato) {
            Documento.windowOpen(sprintf('/artefato/imagem/view/id/%d', sqArtefato));
        },
        detalhar: function (sqArtefato) {
            Documento.windowOpen('/artefato/visualizar-artefato/index/sqArtefato/' + sqArtefato + '#liDadosArtefato');
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

};

$(Documento.init);