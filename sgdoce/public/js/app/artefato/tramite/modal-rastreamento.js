ModalRastremanto = {
    _objFormGrid: $('#form_rastreamento'),
    _objGridCaixa: $('#grid_rastreamento'),
    _urlTrace: 'http://websro.correios.com.br/sro_bin/txect01$.QueryList',

    init: function() {
        ModalRastremanto.events().grid();
    },
    events: function() {

        return ModalRastremanto;
    },
    grid: function() {
        Grid.load(ModalRastremanto._objFormGrid, ModalRastremanto._objGridCaixa);
        return ModalRastremanto;
    },
    trace: function(txCodigoRastreamento) {
        $('<form />', {
            action: sprintf(ModalRastremanto._urlTrace, txCodigoRastreamento),
            method: 'get',
            target: '_blank',
            id: 'modal_form_rastreamento'
        }).append($('<input />', {type: 'hidden', name: 'P_LINGUA', value: '001'}))
                .append($('<input />', {type: 'hidden', name: 'P_TIPO', value: '001'}))
                .append($('<input />', {type: 'hidden', name: 'P_COD_UNI', value: txCodigoRastreamento}))
                .appendTo('body').submit();

        $('#modal_form_rastreamento').remove();
    }
};

$(ModalRastremanto.init);