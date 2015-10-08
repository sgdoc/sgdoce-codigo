VisualizarArtefato = {
    _urlGridInteressado     : '/artefato/visualizar-artefato/list-visualizar-interessado/sqArtefato/%d',
    _urlGridReferencia      : '/artefato/visualizar-artefato/list-visualizar-referencia/sqArtefato/%d',
    _urlGridHistorico       : '/artefato/visualizar-artefato/list-visualizar-historico/sqArtefato/%d',
    _urlGridHistoricoFisico : '/artefato/visualizar-artefato/list-visualizar-historico-fisico/sqArtefato/%d',
    _urlGridVolume          : '/artefato/visualizar-artefato/list-visualizar-volume/sqArtefato/%d',
    _urlPrintHistoric       : '/artefato/visualizar-artefato/print-historic/sqArtefato/%d',
    _urlGridDespacho        : '/artefato/visualizar-artefato/list-visualizar-despacho/sqArtefato/%d',
    _urlGridComentario      : '/artefato/visualizar-artefato/list-visualizar-comentario/sqArtefato/%d',
    _urlViewDespacho        : '/artefato/despacho-interlocutorio/detail/id/%d/backToModal/0',
    _urlViewComentario      : '/artefato/comentario/detail/sqComentario/%d/backToModal/0',
    _urlViewVolume          : 'artefato/volume/detail/id/%d',


    init: function() {
        $('#btn_imprimir').hide();
        VisualizarArtefato.initGrid();

        var nuCurrTab = null;
        $('.tab').click(function () {
            nuCurrTab = $(this).parents("li").index();
            if (nuCurrTab == 4 && !$('#tree').hasClass('treeview')) {
                setTimeout(function(){
                    $('#tree').treeview({
                        collapsed: false,
                        animated: "fast",
                        control: "#sidetreecontrol",
                        persist: "location"
                    });
                },100);
            }
            if (nuCurrTab != 6 && nuCurrTab != 7) {
                $('#btn_imprimir').hide();
            } else {
                $('#btn_imprimir').show();
            }
        });

        $('#btnPrintHistorico').click(VisualizarArtefato.printHistoric);

        $('#btn_imprimir').unbind('click').bind('click').click(function() {
            /** aba de despachos */
            if (nuCurrTab == 6) {
                Message.showConfirmation({
                   body: UI_MSG.MN112,
                   yesCallback: function () {
                       window.location = sprintf('/artefato/despacho-interlocutorio/print/id/%d', $('#sqArtefato').val());
                   }
               });
            }
            /** aba de comentários */
            if (nuCurrTab == 7) {
                window.location = sprintf('/artefato/comentario/print/sqArtefato/%d', $('#sqArtefato').val());
            }

        });
    },

    initGrid: function() {
        var sqArtefato = $('#sqArtefato').val();
//        Grid.load('/artefato/visualizar-artefato/list-tema-tratado/sqArtefato/'+ $('#sqArtefato').val() , $('#table-tema-tratado'));
        Grid.load(sprintf(VisualizarArtefato._urlGridInteressado    , sqArtefato), $('#table-visualizar-interessado'));
        Grid.load(sprintf(VisualizarArtefato._urlGridVolume         , sqArtefato), $('#table-visualizar-volume'));
        Grid.load(sprintf(VisualizarArtefato._urlGridHistorico      , sqArtefato), $('#table-visualizar-historico'), 'Nenhuma ação executada no novo sistema.');
        Grid.load(sprintf(VisualizarArtefato._urlGridHistoricoFisico, sqArtefato), $('#table-visualizar-historico-fisico'));
        Grid.load(sprintf(VisualizarArtefato._urlGridReferencia     , sqArtefato), $('#table-visualizar-referencia'));
        Grid.load(sprintf(VisualizarArtefato._urlGridDespacho       , sqArtefato), $('#table-visualizar-despacho'));
        Grid.load(sprintf(VisualizarArtefato._urlGridComentario     , sqArtefato), $('#table-visualizar-comentario'));

        return VisualizarArtefato;
    },

    printHistoric: function() {
        window.location = sprintf(VisualizarArtefato._urlPrintHistoric, $('#sqArtefato').val());
    },

    viewDespacho: function (sqDespacho) {
        var target = sprintf(VisualizarArtefato._urlViewDespacho, sqDespacho);
        VisualizarArtefato.initModal(target);
    },
    viewComentario: function(sqComentario) {
        var target = sprintf(VisualizarArtefato._urlViewComentario, sqComentario);
        VisualizarArtefato.initModal(target);
    },

    initModal: function (_url) {
        var modalContainer = $("#modal_container_medium");

        modalContainer.empty();
        modalContainer.load(_url, function (responseText, textStatus) {
            if (textStatus === 'success') {
                modalContainer.modal();
            } else {
                Message.showError(responseText);
            }
        });
    },

    viewVolume: function (sqVolume) {
        var target = sprintf(VisualizarArtefato._urlViewVolume, sqVolume);
        VisualizarArtefato.initModal(target);
    }
};

$(VisualizarArtefato.init);
