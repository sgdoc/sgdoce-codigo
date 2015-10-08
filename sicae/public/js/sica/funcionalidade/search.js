$(document).ready(function() {
    var formPesquisa = $('#form-pesquisa-funcionalidade'),
        comboSistema = $('.sistema-load-menu-funcionalidade');

    $('button[type=reset]').click(function() {
        $('#menu-sistema select option:not(:first)').remove();
    });

    Grid.load(formPesquisa, $('#table-funcionalidade'));

    formPesquisa.submit(function() {
        var $this = $(this);
        $('#pesquisa-pdf').val($this.find(':not(#pesquisa-pdf)').serialize());
    });

    comboSistema.live('change', function() {
        var $this   =  $(this),
        $target =  $($this.attr('target')),
        value   =  $this.val();

        if (!value) {
            $('#sqMenu option').not('#sqMenu option:first').remove();
            return;
        }

        $target.load(BASE + '/principal/funcionalidade/find-menu/id/' + value);
    });

    if(!$('.menu-funcionalidade').val()) {
        comboSistema.trigger('change');
    } else {
        formPesquisa.trigger('submit');
    }
});

var Funcionalidade = {
    edit : function(id) {
        window.location = BASE + "/funcionalidade/edit/id/" + id;
    },
    remove : function(id) {
        Message.showConfirmation({
            body: 'Deseja realmente remover esta Funcionalidade?',
            yesCallback: function(){
                var failback = function( msg ) {
                    msg = msg || MessageUI.translate('MN176');
                    Message.showError(msg);
                };
                $.get(
                    BASE + "/funcionalidade/delete/id/" + id,
                    function(result) {
                        if (typeof result === 'object' && 'success' in result) {
                            if (result.success) {
                                Message.showSuccess( MessageUI.translate( result.message ) );
                                $('#form-pesquisa-funcionalidade').trigger('submit');
                            } else {
                                failback( MessageUI.translate( result.message ) );
                            }
                        } else {
                            failback();
                        }
                    }
                ).fail(function(){
                    failback();
                });
            }
        });
    }
};
$(document).ajaxComplete(function(e, xhr, settings) {
    if (settings.url.indexOf('funcionalidade/list') != -1) {
        var response = $.parseJSON(xhr.responseText) || {};
        if ('iTotalRecords' in response) {
            $('.gerar-pdf').removeClass('hide');
            if (parseInt(response.iTotalRecords) < 1) {
                $('.gerar-pdf').addClass('hide');
            }
        }
    }
});