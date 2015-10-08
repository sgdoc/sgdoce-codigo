var Solicitacao = {
    vars: {
        _urlDevolver: "artefato/solicitacao/devolver?%s",
        _urlTriar: "artefato/solicitacao/triar/id/%d",
        _urlTriarMult: "artefato/solicitacao/triar-mult?%s",
        _urlFinalizar: "artefato/solicitacao/finalizar/id/%d",
        _urlVisualizar: "artefato/solicitacao/visualizar/id/%d",
        _urlIndex: '/artefato/solicitacao/index/caixa/%s',
        _urlImageView: '/artefato/imagem/view/id/%d',
        _urlDeleteImage: '/artefato/solicitacao/excluir-imagem/id/%d',
    },
    init: function () {
        Solicitacao.grid('#demands_box-form', '#table-grid-demands')
                .events();

        $(document).on('show', '.accordion-group', function () {
            var grid = $(this).find('.accordion-body').data('grid');
            Solicitacao.grid('#' + grid + '_box-form', '#table-grid-' + grid);
        });

        AreaTrabalho.setUrlBack("/artefato/solicitacao/index/tipo/%d");
    },
    events: function () {
        AreaTrabalho.setDropdownMenuBehavior();

        $("#btnPesquisar").click(function () {
            $("#demands-history_box-form").submit();
        });

        $('#btnClear').on('click', function () {
            $('#dtSolicitacao').val('');
            $('#sqTipoAssuntoSolicitacao,#sqTipoAssuntoSolicitacao_hidden').val('');
            $('#nuArtefato').val('').removeProp('disabled');
            $('#sqTipoArtefatoProcesso').trigger('click');
        });

        $('#sqTipoAssuntoSolicitacao').simpleAutoComplete("/artefato/solicitacao/search-tipo-assunto-solicitacao", {
            extraParamFromInput: 'input[name="sqTipoArtefato"]:checked',
            attrCallBack: 'rel',
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel'
        });

        $('input[name="sqTipoArtefato"]').on('change', function () {
            if ($(this).val() == 0) {
                $('#nuArtefato').val('').prop('disabled', 'disabled');
                $('#sqTipoAssuntoSolicitacao,#sqTipoAssuntoSolicitacao_hidden').val('');
            } else {
                $('#nuArtefato').removeProp('disabled');
            }
        });


        $('.allSqSolicitacao').click(function () {
            var checkboxs = $(this).parents('table').find('tbody :checkbox').not(':disabled');
            if ($(this).is(':checked')) {
                checkboxs.each(function () {
                    $(this).prop('checked', 'checked');
                });
            } else {
                checkboxs.each(function () {
                    $(this).removeProp('checked');
                });
            }
        });

        $(".cboxAll").click(function () {
            var cboxId = $(this).attr('id'),
                    table = null,
                    thParent = $(this).parent('th'),
                    isAllChecked = $(this).is(":checked");

            thParent.unbind();

            switch (cboxId) {
                case "cboxGridMyDemands":
                    table = $("#table-grid-my-demands");
                    break;
                default:
            }

            if (table != null) {
                if (isAllChecked) {
                    table.find("tbody input[type='checkbox']:not(:checked,:disabled)").attr('checked', 'checked');
                } else {
                    table.find("tbody input[type='checkbox']:checked").removeAttr('checked');
                }
            }
        });

        $(".acaoMultRecDevolver").click(function () {
            var cboxData = $("table tbody input[type='checkbox']:checked").serialize();
            Solicitacao.acoes.multi.devolver(cboxData);
        });

        $(".acaoTriarMult").click(function () {
            var cboxData = $("table tbody input[type='checkbox']:checked").serialize();
            Solicitacao.acoes.multi.triar(cboxData);
        });
    },
    grid: function (form, tablegrid) {
        Grid.load($(form), $(tablegrid));

        return Solicitacao;
    },
    acoes: {
        imageView: function (sqArtefato) {
            var modal = window.open(sprintf(Solicitacao.vars._urlImageView, sqArtefato), 'imageView' + sqArtefato, 'fullscreen=yes,location=no,menubar=no,scrollbars=yes');
            modal.focus();
        },
        devolver: function (sqSolicitacao) {
            var sqSolicitacaParam = "id[]=" + sqSolicitacao;
            Solicitacao.initModal(sprintf(Solicitacao.vars._urlDevolver, sqSolicitacaParam));
        },
        triar: function (sqSolicitacao) {
            Solicitacao.initModal(sprintf(Solicitacao.vars._urlTriar, sqSolicitacao));
        },
        finalizar: function (sqSolicitacao) {
            Solicitacao.initModal(sprintf(Solicitacao.vars._urlFinalizar, sqSolicitacao));
        },
        visualizar: function (sqSolicitacao) {
            Solicitacao.initModal(sprintf(Solicitacao.vars._urlVisualizar, sqSolicitacao));
        },
        multi: {
            devolver: function (sqSolicitacao) {
                sqSolicitacao = sqSolicitacao.replace(/sqSolicitacao/gi, "id");
                Solicitacao.initModal(sprintf(Solicitacao.vars._urlDevolver, sqSolicitacao));
            },
            triar: function (sqSolicitacao) {
                sqSolicitacao = sqSolicitacao.replace(/sqSolicitacao/gi, "id");
                Solicitacao.initModal(sprintf(Solicitacao.vars._urlTriarMult, sqSolicitacao));
            }
        },
        excluirImagem: function (sqSolicitacao) {
            Solicitacao.initModal(sprintf(Solicitacao.vars._urlDeleteImage, sqSolicitacao));
        },
        editarDocumento: function (sqArtefato, view, isInconsistente) {
            Message.wait();
            if (isInconsistente) {
                $(window.document.location).attr('href', sprintf(Artefato._urlEditArtefatoInconsistencia, sqArtefato, Solicitacao.getUrlBack()));
            } else {
                $.post('artefato/documento/pode-editar-artefato', {id: sqArtefato}, function (data) {

                    if (!data.canEdit) {
                        Message.waitClose();
                        Message.showAlert(data.msg);
                    } else {
                        $(window.document.location).attr('href', sprintf(Artefato._urlEdit, sqArtefato, view, Solicitacao.getUrlBack()));
                    }
                });
            }
            return;
        },
    },
    getUrlBack: function () {
        var __url = Solicitacao.vars._urlIndex;
        return sprintf(__url.replace(/\//g, '.'), $('.accordion .accordion-body.in.collapse').attr('id'));
    },
    initModal: function (_url, container) {
        Message.wait();
        var modalContainer = container || $("#modal_container");
        modalContainer.empty();
        modalContainer.load(_url, function (responseText, textStatus) {
            Message.waitClose();
            Limit.init();

            try {
                var jsonResponse = $.parseJSON(responseText);
                if (jsonResponse.status == false) {
                    Message.showError(jsonResponse.message);
                } else {
                    Message.showAlert(jsonResponse.message);
                }
            } catch (e) {
                if (textStatus === 'success') {
                    modalContainer.modal();
                } else {
                    Message.showError("Sistema temporariamente indisponível, tente novamente.");
                }
            }
        });
    }
};

$(Solicitacao.init);