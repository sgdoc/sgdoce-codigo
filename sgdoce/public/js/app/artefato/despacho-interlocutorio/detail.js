var Detail = {

    /* atua como construtor do "objeto" */
    init: function () {
        Detail.event();
    },

    /* carrega os elementos do formulario que sera validados */
    event: function () {
        $('.btn-voltar').click(function () {
            /*if ($('#backToModal').val()) {
                var _url = sprintf(
                        '/artefato/despacho-interlocutorio/index/id/%d/back/%s',
                        $('#sqArtefato').val(),
                        AreaTrabalho.getUrlBack()
                );
                AreaTrabalho.initModal(_url);
            } else {
                $("#modal_container_medium").modal('hide').html('').css('display', 'none');
            }*/
            $("#modal_container_medium").modal('hide').html('').css('display', 'none');
        });
    }
};
$(document).ready(function () { Detail.init(); });