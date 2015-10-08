AreaTrabalho = {
    /* constantes carregas em index.phtml */
    _TIPO_ARTEFATO_DOCUMENTO: null,
    _TIPO_ARTEFATO_PROCESSO: null,
//    _TIPO_ARTEFATO_DOSSIE: null,
    _tipoArtefatoCurrent: 1,
    _currentBox: 'minhaCaixa',
    _urlIndex: '/artefato/area-trabalho/index/tipoArtefato/%d',
    _tabMigracaoEvents: false,
    T_GRID: {
        "minhaCaixa": "minha",
        "caixaUnidade": "unidade",
        "caixaExterna": "externa",
        "caixaArquivo": "arquivo",
        "caixaMigracao": "migracao",
        "caixaArquivoSetorial": "arquivo-setorial"
    },
    init: function () {
        AreaTrabalho.ativaAba();
        AreaTrabalho._tipoArtefatoCurrent = $('.nav.nav-tabs li.active').data('tipo-artefato');

        //caso seja alterado o tipo de artefato limpa o sessionStorage para limpar última pesquisa
        var localTpArtCur = localStorage.getItem('tipoArtefatoCurrent');
        if (localTpArtCur && localTpArtCur != AreaTrabalho._tipoArtefatoCurrent) {
            sessionStorage.clear();
        }
        localStorage.setItem('tipoArtefatoCurrent', AreaTrabalho._tipoArtefatoCurrent);

        var currentBox = AreaTrabalho.T_GRID[AreaTrabalho._currentBox];
        var sessionValues = eval(sessionStorage.getItem('SGDOC_table-grid-area-trabalho-' + currentBox));


        if (sessionValues) {
            sessionStorage.clear();
            //limpa todos os inputs de pesquisa
            $('.search').val('');
            //recupera as informações do input de pesquisa
            var name = sessionValues[2].name;
            var value = sessionValues[2].value;
            //set as informações no compo de pesquisa
            $('#form-visualizar-area-trabalho-'+currentBox).find('input[name="'+name+'"]').val(value);
        }

        $(document).on('show', '.accordion-group', function () {
            if ($(this).find("#caixaMigracao").length > 0) {
                if (!AreaTrabalho._tabMigracaoEvents) {
                    AreaTrabalho.setMigracaoBoxEvents();
                }
            }
            AreaTrabalho.Grid.initGrid($(this).find('.accordion-body').data('grid'));
        });

        AreaTrabalho.Grid.initGrid(currentBox).initSelectAll().events();

        if (!AreaTrabalho._tabMigracaoEvents) {
            AreaTrabalho.setMigracaoBoxEvents();
        }
    },
    Grid: {
        events: function () {
            $('input[name="search"]').on('blur', function () {
                if ($(this).val() == '') {
                    $(this).parents('form').submit();
                }
            });
            AreaTrabalho.setDropdownMenuBehavior();
            return AreaTrabalho.Grid;
        },
        initSelectAll: function () {
            $('.allSqArtefato').click(function () {
                var checkboxs = $(this).parents('table').find('tbody :checkbox.multipleAction').not(':disabled');
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

            $('.multipleAction').live('click', function () {
                if (!$(this).prop('checked')) {
                    $(this).parents('table')
                            .find('thead :checkbox.allSqArtefato')
                            .removeProp('checked');
                }
            });
            return AreaTrabalho.Grid;
        },
        initGrid: function (grid) {
            if ($('#table-grid-area-trabalho-' + grid).length != 0) {
                Grid.load($('#form-visualizar-area-trabalho-' + grid), $('#table-grid-area-trabalho-' + grid));
            }
            return AreaTrabalho.Grid;
        },
        reloadGrids: function () {
            $('.dataTable').each(function () {
                $(this).dataTable().fnDraw(false);
            });
            return AreaTrabalho.Grid;
        },
        reloadGridArquivo: function () {
            $('#form-visualizar-area-trabalho-arquivo').submit();
            return AreaTrabalho.Grid;
        },
        ajustaTitulosGrid: function () {
            //ajusta a 2 coluna das grids
            var tableDefaultBox = $('table.defaultBox');
            tableDefaultBox.find('tbody tr td:nth-child(2)').css({'text-align': 'center'});
            tableDefaultBox.find('td').css('word-break', 'break-all');

            //coluna de prioridade
            tableDefaultBox.find('tr td:nth-child(2)').css('width', '20px');
            if (AreaTrabalho._tipoArtefatoCurrent != AreaTrabalho._TIPO_ARTEFATO_PROCESSO) {
                tableDefaultBox.find('tr td:nth-child(3)').css('width', '77px'); //digital
                tableDefaultBox.find('tr td:nth-child(4)').css('width', '65px'); //cadastro
            } else {
                tableDefaultBox.find('tr td:nth-child(3)').css('width', '65px');//cadastro
                tableDefaultBox.find('tr td:nth-child(5)').css('width', '135px');//nr processo
            }

            return AreaTrabalho.Grid;
        }
    },
    updateView: function (tipoArtefato) {
        $(window.document.location).attr('href', sprintf(AreaTrabalho._urlIndex + '/caixa/minhaCaixa', tipoArtefato));
    },
    ativaAba: function () {
        var typeUnitBox = $('#type_unit_box').val();
        typeUnitBox = (typeUnitBox === '') ? 0 : typeUnitBox;

        switch (parseInt(typeUnitBox)) {
            case 0:
            case AreaTrabalho._TIPO_ARTEFATO_DOCUMENTO:
                $('#liDocumento').addClass('active');
                break;
            case AreaTrabalho._TIPO_ARTEFATO_PROCESSO:
                $('#liProcesso').addClass('active');
                break;
//            case AreaTrabalho._TIPO_ARTEFATO_DOSSIE:
//                $('#liDossie').addClass('active');
//                break;
        }
        return AreaTrabalho;
    },
    getUrlBack: function () {
        var __url = AreaTrabalho._urlIndex + '/caixa/%s';
        return sprintf(
                __url.replace(/\//g, '.'),
                AreaTrabalho._tipoArtefatoCurrent,
                $('.accordion .accordion-body.in.collapse').attr('id')
                );
    },
    setUrlBack: function (url) {
        AreaTrabalho._urlIndex = url;
    },
    initModal: function (_url, container) {
        var modalContainer = container || $("#modal_container_xl_size");

        //modalContainer.on('hidden.bs.modal', function (e) {
//            alert('Hide');
        //});

        modalContainer.modal('hide').html('').css('display', 'none');
        if (!container) {
            $('.modal-backdrop').remove();
        }

        modalContainer.empty();
        modalContainer.load(_url, function (responseText, textStatus) {
            Message.waitClose();
            if (textStatus === 'success') {
                modalContainer.modal({backdrop: 'static', keyboard: false});
            } else {
                Message.showError(responseText);
            }
        });
    },
    setDropdownMenuBehavior: function () {
        $("table tbody").on('click', '.dropdown-toggle', function () {
            var dropdown_menu = $(this).parent('div.btn-group').find(".dropdown-menu"),
                    dropdown_height = 0,
                    dropdown_container = null,
                    dropdown_parent = null,
                    // margem de erro do container
                    container_height = 0,
                    dropdown_new_offset = 0;

            setTimeout(function () {
                if (dropdown_menu != null) {
                    dropdown_height = dropdown_menu.offset().top + dropdown_menu.height();
                    dropdown_container = dropdown_menu.parents(".collapse");
                    dropdown_parent = dropdown_menu.parent('div.btn-group');

                    if (dropdown_container != null) {
                        container_height = dropdown_container.offset().top + dropdown_container.height() - 10;
                        if (dropdown_height > container_height) {
                            $(dropdown_container).height($(dropdown_container).height() + ((dropdown_height - container_height) + 25));
                        }
                    }
                }
            }, 1);
        });
    },
    setMigracaoBoxEvents: function ()
    {
        //Eventos para aba Migração.
        $(".infolink").tooltip();

        $(".tpNuArtefato").unbind();
        $(".tpNuArtefato").on('click', function () {
            var nuDigits = $(this).val();

            AreaTrabalho.setNuArtefatoMask(nuDigits);

            $("#nuArtefato").attr('minlength', nuDigits);
            $("#nuArtefato").attr('maxlength', nuDigits + 3);
        });

        var nuDigits = $(".tpNuArtefato:checked").val();
        AreaTrabalho.setNuArtefatoMask(nuDigits);

        $("#btnSearchMigracao").unbind();
        $("#btnSearchMigracao").on("click", function () {
            AreaTrabalho.loadGridMigracao();
        });

        $("#formSearchMigracaoBox").unbind();
        $("#formSearchMigracaoBox").submit(function () {
            return false;
        });
        $("#formSearchMigracaoBox").bind('keypress', function (e) {
            if (e.keyCode == 13) {
                AreaTrabalho.loadGridMigracao();
            }
        });
    },
    setNuArtefatoMask: function (nuDigits) {
        if (parseInt(nuDigits) == 17) {
            $("#nuArtefato").setMask('99999.999999/9999-99');
        } else if (parseInt(nuDigits) == 15) {
            $("#nuArtefato").setMask('99999.999999/99-99');
        }
    },
    loadGridMigracao: function () {
        if ($("#formSearchMigracaoBox").valid()) {
            var dataPost = $("#formSearchMigracaoBox").serializeArray();
            $("#caixaMigracao .accordion-inner").load("artefato/area-trabalho/box-migracao", dataPost, function (responseText, textStatus) {
                if (textStatus == 'success') {
                    //limpa dados das grids sessionStorage
                    sessionStorage.clear();
                    AreaTrabalho.Grid.initGrid(AreaTrabalho.T_GRID['caixaMigracao']);
                    $("#table-grid-area-trabalho-migracao_length:visible").addClass('hide');
                }
            });
        }
    },
    loadSearchMigracao: function () {
        var dataPost = $("#form-visualizar-area-trabalho-migracao").serializeArray();
        $("#caixaMigracao .accordion-inner").load("artefato/area-trabalho/box-migracao", dataPost, function (responseText, textStatus) {
            if (textStatus == 'success') {
                AreaTrabalho.setMigracaoBoxEvents();
            }
        });
    }

};

Artefato = {
    _urlTrace: 'http://websro.correios.com.br/sro_bin/txect01$.QueryList',
    _urlTraceList: 'artefato/tramite/modal-rastreamento/sqArtefato/%d',
    _urlEdit: 'artefato/documento/edit/a/b/id/%d/view/%d/back/%s',
    _urlEditArtefatoInconsistencia: '/migracao/vinculo/index/id/%d/back/%s',

    _urlEditProcess: 'artefato/processo-eletronico/form-data/id/%d/back/%s',

    _urlAutuar: 'artefato/autuar-documento/form/id/%d/back/%s',
    _urlImage: 'artefato/imagem/index/id/%d/back/%s',
    _urlImageView: '/artefato/imagem/view/id/%d',
    _urlClassificar: 'arquivo/artefato-classificacao/modal-classificacao/sqArtefato/%d/back/%s',
    _urlArquivar: 'arquivo/arquivamento/modal-arquivar/sqArtefato/%d',
    _urlPrint: 'etiqueta/gerar-etiqueta/processo/id/%d',
    _urlVolume: 'artefato/volume/form/id/%d',
    _urlDemandaInformacao: 'artefato/demanda-informacao/gerar/id/%d',
    _urlVisualizarArtefato: '/artefato/visualizar-artefato/index/sqArtefato/%d',
    _urlMigracao: 'migracao/vinculo/index/id/%d/back/%s',
    _urlEditVolume: 'artefato/volume/grid/id/%d',
    autuar: function (sqArtefato) {
        $(window.document.location).attr(
                'href',
                sprintf(Artefato._urlAutuar,
                        sqArtefato,
                        AreaTrabalho.getUrlBack())
                );
    },
    edit: function(sqArtefato, view, isInconsistente) {
        Message.wait();
        if (isInconsistente) {
            $(window.document.location).attr('href', sprintf(Artefato._urlEditArtefatoInconsistencia , sqArtefato, AreaTrabalho.getUrlBack()));
        }else{
            $.post('artefato/documento/pode-editar-artefato', {id: sqArtefato},function(data){

                if (! data.canEdit ) {
                    Message.waitClose();
                    Message.showAlert(data.msg);
                } else {
                    $(window.document.location).attr('href', sprintf(Artefato._urlEdit, sqArtefato, view, AreaTrabalho.getUrlBack()));
                }
            });
        }
        return;
    },
    migrar: function (sqArtefato) {
        Message.showConfirmation({
            'body': "Tem certeza que deseja corrigir estes dados de migração?",
            'yesCallback': function () {
                $(window.document.location).attr('href', sprintf(Artefato._urlMigracao, sqArtefato, AreaTrabalho.getUrlBack()));
            }
        });
        return;
    },
    editProcess: function (sqArtefato, isInconsistente) {
        Message.wait();
        if (isInconsistente) {
            $(window.document.location).attr('href', sprintf(Artefato._urlEditArtefatoInconsistencia , sqArtefato, AreaTrabalho.getUrlBack()));
        } else {
            $.post('artefato/documento/pode-editar-artefato', {id: sqArtefato}, function (data) {
                if (!data.canEdit) {
                    Message.waitClose();
                    Message.showAlert(data.msg);
                } else {
                    $(window.document.location).attr('href', sprintf(Artefato._urlEditProcess, sqArtefato, AreaTrabalho.getUrlBack()));
                }
            });
        }
        return;
    },
    imageView: function (sqArtefato) {
        var modal = window.open(sprintf(Artefato._urlImageView, sqArtefato), 'imageView' + sqArtefato, 'fullscreen=yes,location=no,menubar=no,scrollbars=yes');
        modal.focus();
    },
    image: function (sqArtefato) {
        $(window.document.location).attr(
                'href',
                sprintf(
                        Artefato._urlImage,
                        sqArtefato,
                        AreaTrabalho.getUrlBack()
                        )
                );
    },
    imageAlert: function (sqArtefato) {
        Message.showAlert('Este artefato possui demanda(s) em aberto. Não pode inserir imagem no momento.');
    },
    addPeca: function (sqArtefato) {
        Message.show('Adicionar Peça', 'peça: ' + sqArtefato);
    },
    classificar: function (sqArtefato) {
        var __url = sprintf(
                Artefato._urlClassificar,
                sqArtefato,
                AreaTrabalho.getUrlBack()
                );
        AreaTrabalho.initModal(__url);
    },
    arquivar: function (sqArtefato) {
        AreaTrabalho.initModal(sprintf(Artefato._urlArquivar, sqArtefato));
    },
    prazo: function (sqArtefato) {
        Message.show('Prazo', 'prazo: ' + sqArtefato);
    },
    printTicket: function (sqArtefato) {
        var modal = window.open(sprintf(Artefato._urlPrint, sqArtefato), 'printTicket' + sqArtefato);
        modal.focus();
    },
    traceList: function (sqArtefato) {
        AreaTrabalho.initModal(sprintf(Artefato._urlTraceList, sqArtefato));
    },
    trace: function (txCodigoRastreamento) {
        $('<form />', {
            action: sprintf(Artefato._urlTrace, txCodigoRastreamento),
            method: 'get',
            target: '_blank',
            id: 'form_rastreamento'
        }).append($('<input />', {type: 'hidden', name: 'P_LINGUA', value: '001'}))
                .append($('<input />', {type: 'hidden', name: 'P_TIPO', value: '001'}))
                .append($('<input />', {type: 'hidden', name: 'P_COD_UNI', value: txCodigoRastreamento}))
                .appendTo('body').submit();

        $('#form_rastreamento').remove();
    },
    volume: function (sqArtefato) {
        Message.wait();
        AreaTrabalho.initModal(sprintf(Artefato._urlVolume, sqArtefato), $("#modal_container_medium"));
    },
    gerarDemandaInformacao: function (sqArtefato) {
        Message.wait();
        AreaTrabalho.initModal(sprintf(Artefato._urlDemandaInformacao, sqArtefato));
    },
    visualizarArtefato: function (sqArtefato) {
        var modal = window.open(sprintf(Artefato._urlVisualizarArtefato, sqArtefato), 'visualizarArtefato' + sqArtefato, 'fullscreen=yes,location=no,menubar=no,scrollbars=yes');
        modal.focus();
    },
    editVolume: function (sqArtefato) {
        Message.wait();
        AreaTrabalho.initModal(sprintf(Artefato._urlEditVolume, sqArtefato));
    }
};

Vinculo = {
    _url: '/artefato/vinculo/index/id/%d/back/%s',
    _urlFirstPiece: '/artefato/vinculo/modal-first-piece/id/%d',
    go: function (sqArtefato) {
        $(window.document.location).attr('href',
                sprintf(
                        Vinculo._url,
                        sqArtefato,
                        AreaTrabalho.getUrlBack()
                        )
                );
    },
    firstPiece: function (sqArtefato) {
        AreaTrabalho.initModal(
                sprintf(Vinculo._urlFirstPiece, sqArtefato),
                $('#modal_container_medium')
                );
    }
};

Tramite = {
    _urlCancel: '/artefato/tramite/cancel',
    _urlReceive: '/artefato/tramite/receive',
    _urlSend: '/artefato/tramite/index/back/%s',
    _urlValidateSigilo: '/artefato/tramite/validate-sigilo',
    _urlValidateBackSigilo: '/artefato/tramite/validate-external-back-sigilo',
    _urlBack: '/artefato/tramite/return',
    _urlBackSigiloso: '/artefato/tramite/return-modal/id/%d',
    _urlRescue: 'artefato/tramite/rescue',
    init: function () {
        $('.acaoMultTram').on('click', Tramite.multipleTramit);
        $('.acaoMultRecTram').on('click', Tramite.multipleReceive);
        $('.acaoMultCanTram').on('click', Tramite.multipleCancel);
        $('.acaoMultRetTram').on('click', Tramite.multipleReturn);
    },
    rescue: function (sqArtefato) {
        Message.showConfirmation({
            'body': "Este registro será devolvido à Unidade. Deseja Continuar?",
            'yesCallback': function () {
                Message.wait();
                $.post(Tramite._urlRescue, {sqArtefato: sqArtefato}, function (result) {
                    Message.waitClose();
                    if (!result.error) {
                        $('#table-grid-area-trabalho-unidade').dataTable().fnDraw(false);
                        $('#table-grid-area-trabalho-minha').dataTable().fnDraw(false);
                    }
                    Message.show(result.type, result.msg);
                });
            }
        });
        return;
    },
    send: function (sqArtefato) {
        Tramite.tramitSend("sqArtefato[]=" + sqArtefato);
    },
    receive: function (sqArtefato) {
        //envia array "[sqArtefato]" pois o metodo deve estar preparado para receber
        //varios sq devido a opção "multiplo"
        Tramite.submitMultipeAction(Tramite._urlReceive, {sqArtefato: [sqArtefato]}, 'MN109');
    },
    cancel: function (sqArtefato) {
        //envia array "[sqArtefato]" pois o metodo deve estar preparado para receber
        //varios sq devido a opção "multiplo"

        Tramite.submitMultipeAction(Tramite._urlCancel, {sqArtefato: [sqArtefato]}, 'MN108');
    },
    externalBack: function (sqArtefato) {
        Message.wait();

        //valida se artefato é sigiloso
         $.post(Tramite._urlValidateBackSigilo, {sqArtefato: sqArtefato}, function (result) {
            if (result.hasVinculoSigiloso) {
                //abrir modal de direcionamento do tramite sigiloso
                AreaTrabalho.initModal(sprintf(Tramite._urlBackSigiloso, sqArtefato), $("#modal_container_medium"));
                return;
            }else{
                Message.waitClose();
                //envia array "[sqArtefato]" pois o metodo deve estar preparado para receber
                //varios sq devido a opção "multiplo"
                Tramite.submitMultipeAction(Tramite._urlBack, {sqArtefato: [sqArtefato]}, 'MN123');
            }
        });
    },
    back: function (sqArtefato) {
        //envia array "[sqArtefato]" pois o metodo deve estar preparado para receber
        //varios sq devido a opção "multiplo"
        Tramite.submitMultipeAction(Tramite._urlBack, {sqArtefato: [sqArtefato]}, 'MN123');
    },
    multipleTramit: function () {
        var table = $(this).parents('form').find('table');
        if (table.find('input:checkbox:checked').length > 0) {
            var dataPost = table.find('input:checkbox');
            if (table.find('input:checkbox:checked').length > 1) {
                Message.wait();
                $.post(Tramite._urlValidateSigilo, dataPost.serialize(), function (result) {
                    Message.waitClose();
                    if ( typeof result !== 'object') {
                        Message.showError('Ocorreu um erro. Tente mais tarde.');
                        return;
                    }
                    if (! result.error) {
                        Tramite.tramitSend(dataPost);
                    } else {
                        Message.show(result.type, result.msg);
                    }
                });
            } else {
                Tramite.tramitSend(dataPost);
            }
        } else {
            Message.show("Erro", 'Selecione pelo menos um artefato para tramitar');
        }
    },
    tramitSend: function (data) {
        data = (data.jquery) ? data.serialize() : data;
        data += '&tipoArtefato=' + AreaTrabalho._tipoArtefatoCurrent;
        $(window.document.location).attr('href', sprintf(Tramite._urlSend, AreaTrabalho.getUrlBack()) + '?' + data);
    },
    multipleCancel: function () {
        var table = $(this).parents('form').find('table');

        if (table.find('input:checkbox:checked').length > 0) {
            Tramite.submitMultipeAction(Tramite._urlCancel, table.find('input:checkbox'), 'MN108');
        } else {
            Message.show("Erro", 'Selecione pelo menos um artefato para cancelar o trâmite');
        }
    },
    multipleReceive: function () {
        var table = $(this).parents('form').find('table');

        if (table.find('input:checkbox:checked').length > 0) {
            Tramite.submitMultipeAction(Tramite._urlReceive, table.find('input:checkbox'), 'MN109');
        } else {
            Message.showAlert('Selecione pelo menos um artefato para receber.');
        }
    },
    multipleReturn: function () {
        var table = $(this).parents('form').find('table');

        if (table.find('input:checkbox:checked').length > 0) {
            Tramite.submitMultipeAction(Tramite._urlBack, table.find('input:checkbox'), 'MN123');
        } else {
            Message.show("Erro", 'Selecione pelo menos um artefato para retornar');
        }
    },
    submitMultipeAction: function (url, data, msgCode) {
        Message.showConfirmation({
            body: (UI_MSG[msgCode]) ? UI_MSG[msgCode] : "Tem certeza que deseja realizar a operação?",
            yesCallback: function () {
                Message.wait();
                data = (data.jquery) ? data.serialize() : data;
                if (typeof data == 'object') {
                    data['tipoArtefato'] = AreaTrabalho._tipoArtefatoCurrent
                } else {
                    data += '&tipoArtefato=' + AreaTrabalho._tipoArtefatoCurrent;
                }

                $.post(url, data, function (data) {
                    Message.waitClose();

                    //neste caso o retorno é padrão de sessão expirada
                    if (data.status != 'undefined' && data.status == false ) {
                        Message.showError(data.message,function(){
                            window.location.reload();
                        });
                    } else {
                        Message.show(data.type, data.msg);
                        AreaTrabalho.Grid.reloadGrids();
                    }
                });
            }
        });
    },
    printGuia: function (print, endereco) {
        if (!print) {
            return false;
        }

        $('<iframe/>', {
            src: '/artefato/tramite/print-guia/data/' + print + '/endereco/' + endereco,
            style: 'display:none',
            load: function () {
//                var error_code = $('body', $(this).contents() ).find('.alert-error').text().replace('×', '');
//                if (error_code) { Message.showError(UI_MSG[error_code]); }
            }
        }).appendTo('body');
    }
};

Despacho = {
    _url: '/artefato/despacho-interlocutorio/index/id/%d/back/%s',
    go: function (sqArtefato) {
        /*$(window.document.location).attr('href',
                sprintf(
                        Despacho._url,
                        sqArtefato,
                        AreaTrabalho.getUrlBack()
                        )
                );*/
        var __url = sprintf(
                Despacho._url,
                sqArtefato,
                AreaTrabalho.getUrlBack()
                );
        AreaTrabalho.initModal(__url);
    }
};

Comentario = {
    _url: '/artefato/comentario/index/id/%d/back/%s',
    go: function (sqArtefato) {
        //$(window.document.location).attr('href', sprintf(Comentario._url, sqArtefato, AreaTrabalho.getUrlBack()));
        var __url = sprintf(
                Comentario._url,
                sqArtefato,
                AreaTrabalho.getUrlBack()
                );
        AreaTrabalho.initModal(__url);
    }
};

Arquivo = {
    _urlLend: '/arquivo/emprestimo/modal-emprestimo?%s',
    _urlReturning: '/arquivo/emprestimo/devolver?%s',
    _urlUnarchive: '/arquivo/arquivamento/desarquivar',
    init: function () {
        $('.acaoMultDesarq').on('click', Arquivo.multipleUnarchive);
        $('.acaoMultEmpres').on('click', Arquivo.multipleLend);
    },
    view: function (sqArtefato) {
        Artefato.visualizarArtefato(sqArtefato);
    },
    lend: function (sqArtefato) { //emprestimo
        Arquivo.lendAction(encodeURI("sqArtefato[]=" + sqArtefato));
    },
    unarchive: function (sqArtefato) {
        Arquivo.unarchiveAction(encodeURI("sqArtefato[]=" + sqArtefato));
    },
    returning: function (sqArtefato) {
        Arquivo.returningAction(encodeURI("sqArtefato[]=" + sqArtefato));
    },
    lendAction: function (data) {
        data = (data.jquery) ? data.serialize() : data;
        AreaTrabalho.initModal(sprintf(Arquivo._urlLend, data));
    },
    unarchiveAction: function (data) {
        Message.showConfirmation({
            body: sprintf(UI_MSG.MN124, 'desarquivar'),
            yesCallback: function () {
                data = (data.jquery) ? data.serialize() : data;
                $.post(Arquivo._urlUnarchive, data, function (result) {
                    if (!result.error) {
                        AreaTrabalho.Grid.reloadGrids();
                    }
                    Message.show(result.type, result.msg);
                });
            }
        });
    },
    returningAction: function (data) {
        Message.showConfirmation({
            body: sprintf(UI_MSG.MN124, 'retornar'),
            yesCallback: function () {
                data = (data.jquery) ? data.serialize() : data;
                $.post(Arquivo._urlReturning, data, function (result) {
                    if (!result.error) {
                        AreaTrabalho.Grid.reloadGridArquivo();
                    }
                    Message.show(result.type, result.msg);
                });
            }
        });
    },
    multipleLend: function () {
        var table = $(this).parents('form').find('table');
        if (table.find('input:checkbox:checked').length > 0) {
            Arquivo.lendAction(table.find('input:checkbox'));
        } else {
            Message.show("Alerta", 'Selecione pelo menos um registro para emprestar');
        }
    },
    multipleUnarchive: function () {
        var table = $(this).parents('form').find('table');

        if (table.find('input:checkbox:checked').length > 0) {
            Arquivo.unarchiveAction(table.find('input:checkbox'));
        } else {
            Message.show("Alerta", 'Selecione pelo menos um registro para desarquivar');
        }
    }
};

ArquivoSetorial = {
    _urlArchive: '/arquivo/arquivamento-setorial/archive',
    _urlUnarchive: '/arquivo/arquivamento-setorial/unarchive',

    init: function () {
        $('.acaoMultDesarqSetorial').on('click', ArquivoSetorial.multipleUnarchive);
        $('.acaoMultArqSetorial'   ).on('click', ArquivoSetorial.multipleArchive);
    },
    view: function (sqArtefato) {
        Artefato.visualizarArtefato(sqArtefato);
    },

    archive: function (sqArtefato) {
        ArquivoSetorial.archiveAction(encodeURI("sqArtefato[]=" + sqArtefato));
    },
    archiveAction: function (data) {
        Message.showConfirmation({
            body: sprintf(UI_MSG.MN130, 'arquivar'),
            yesCallback: function () {
                try {
                    Message.wait();
                    data = (data.jquery) ? data.serialize() : data;
                    $.ajax({
                        type: "POST",
                         url: ArquivoSetorial._urlArchive,
                        data: data
                    }).success(function (result) {
                        Message.waitClose();

                        if ( typeof result !== 'object') {
                            Message.showError('Ocorreu um erro. Tente mais tarde.');
                            return;
                        }
                        //neste caso o retorno é padrão de sessão expirada
                        if (result.status != 'undefined' && result.status == false ) {
                            Message.showError(result.message,function(){
                                window.location.reload();
                            });
                          return;
                        }

                        $('#table-grid-area-trabalho-minha').dataTable().fnDraw(false);

                        if (!result.error) {

                            if ($('#table-grid-area-trabalho-arquivo-setorial.dataTable').length) {
                                $('#table-grid-area-trabalho-arquivo-setorial').dataTable().fnDraw(false);
                            }
                        }
                        Message.show(result.type, result.msg);
                    }).error(function (err) {
                        Message.waitClose();
                        Message.showError("Ocorreu um erro inesperado na execução");
                    });

                } catch (e) {
                    Message.waitClose();
                    Message.showError(e.message);
                }
            }
        });
    },
    multipleArchive: function () {
        var table = $(this).parents('form').find('table');

        if (table.find('input:checkbox:checked').length > 0) {
            ArquivoSetorial.archiveAction(table.find('input:checkbox'));
        } else {
            Message.show("Alerta", 'Selecione pelo menos um registro para arquivar');
        }
    },
    unarchive: function (sqArtefato) {
        ArquivoSetorial.unarchiveAction(encodeURI("sqArtefato[]=" + sqArtefato));
    },
    unarchiveAction: function (data) {
        Message.showConfirmation({
            body: sprintf(UI_MSG.MN124, 'desarquivar'),
            yesCallback: function () {
                try {
                    Message.wait();
                    data = (data.jquery) ? data.serialize() : data;
                    $.ajax({
                        type: "POST",
                         url: ArquivoSetorial._urlUnarchive,
                        data: data
                    }).success(function (result) {
                        Message.waitClose();

                        if ( typeof result !== 'object') {
                            Message.showError('Ocorreu um erro. Tente mais tarde.');
                            return;
                        }

                        $('#table-grid-area-trabalho-arquivo-setorial').dataTable().fnDraw(false);

                        if (!result.error) {
                            if ($('#table-grid-area-trabalho-minha.dataTable').length) {
                                $('#table-grid-area-trabalho-minha').dataTable().fnDraw(false);
                            }
                        }
                        Message.show(result.type, result.msg);
                    }).error(function (err) {
                        Message.waitClose();
                        Message.showError("Ocorreu um erro inesperado na execução");
                    });

                } catch (e) {
                    Message.waitClose();
                    Message.showError(e.message);
                }
            }
        });
    },

    multipleUnarchive: function () {
        var table = $(this).parents('form').find('table');

        if (table.find('input:checkbox:checked').length > 0) {
            ArquivoSetorial.unarchiveAction(table.find('input:checkbox'));
        } else {
            Message.show("Alerta", 'Selecione pelo menos um registro para desarquivar');
        }
    }
};

$(function () {
    AreaTrabalho.init();
    Tramite.init();
    ArquivoSetorial.init();

    if ($('#caixaArquivo').length === 1) {
        Arquivo.init();
    }
    $(document).ajaxStop(AreaTrabalho.Grid.ajustaTitulosGrid);
});