var detail = {

    /* atua como construtor do "objeto" */
    init: function () {
        detail.event();
    },

    event: function () {
        $('.btn-voltar').click(function () {
            $("#modal_container_medium").modal('hide').html('').css('display', 'none');
        });
    }
};
$(document).ready(function () { detail.init(); });