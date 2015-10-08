MenuProcesso = {
    initPassos: function() {

        var valid = false;

        $('.tab').click(function() {

            var nuActiTab = $("#nav-tabs li.active").index(),
                    nuCurrTab = $(this).parents("li").index();

            if ($('ul.tabsForm li').length > 1) {

                var isValid = true;

                if (nuActiTab < nuCurrTab) {
                    isValid = $('form').valid();
                }

                if (isValid) {
                    if ($(this).attr('href') == $('.tab:first').attr('href')) {
                        $('#btnAnterior').attr('disabled', true);
                    }

                    if ($(this).attr('href') != $('.tab:first').attr('href') && $(this).attr('href') != $('.tab:last').attr('href')) {
                        $('#btnAnterior, #btnProximo').removeAttr('disabled');
                        $('.btn-concluir').removeClass('btn-primary');
                        $('#btnSalvar').addClass('hidden');
                    }

                    $('.campos-obrigatorios').addClass('hidden');
                    $(this).tab('show');
                }

                if ($('ul.tabsForm li:last').hasClass('active')) {
                    $('#btnAnterior').removeAttr('disabled');
                    $('.btn-concluir').addClass('btn-primary');
                    $('#btnProximo').removeClass('btn-primary');
                    $('#btnProximo i').removeClass('icon-white');
                    $('#btnProximo').attr('disabled', true);
                    $('#btnSalvar').removeClass('hidden');
                } else {
                    $('.btn-concluir').removeClass('btn-primary');
                    $('#btnProximo').removeAttr('disabled');
                    $('#btnProximo').addClass('btn-primary');
                    $('#btnProximo i').addClass('icon-white');
                }
                // Bloqueio
                if ($("#stBloqueioArtefato").val()) {
                    $("#table-interessado tbody tr a, #table-vincular-documento tbody tr a").attr('href', null).attr('disabled', true);
                }
            }

            return false;
        });

        $('#btnProximo').click(function() {
            $('#nav-tabs li.active').next('li').children().click();
            // Bloqueio
            if ($("#table-interessado:visible").length > 0
                    && $("#stBloqueioArtefato").val()) {
                $("#table-interessado tbody tr a, #table-vincular-documento tbody tr a").attr('href', null).attr('disabled', true);
            }
        });

        $('#btnAnterior').attr('disabled', true);

        $('#btnAnterior').click(function() {
            $('#nav-tabs li.active').prev('li').children().click();
            $('#btnProximo').addClass('btn-primary');
            $('#btnProximo i').addClass('icon-white');
        });

        $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
            if ($(this).attr('href') == $('.tab:last').attr('href')) {
                $('#btnProximo').attr('disabled', true);
            } else {
                $('#btnProximo').attr('disabled', false);
            }
            if ($(this).attr('href') == $('.tab:first').attr('href')) {
                $('#btnAnterior').attr('disabled', true);
            } else {
                $('#btnAnterior').attr('disabled', false);
            }
        });
    },
    initButton: function() {


    },
    init: function() {
        MenuProcesso.initPassos();
        MenuProcesso.initButton();
    }
}

$(document).ready(function() {
    MenuProcesso.init();
});