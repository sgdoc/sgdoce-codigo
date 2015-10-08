$(document).ready(function(){
    $('#btnRecoverPass').click(function(){
        if ($('[name=form-revover-pass]').valid()) {
            $.post('/usuario/recover-pass',
            {
                txEmail : $('#txEmailRecover').val(),
                nuCpf   : $('#nuCpfRecover').val()
            },
            function(response){
                if(response.error){
                    $('#error-pass').html('<div class="alert alert-error campos-obrigatorios">'+
                        '<button class="close" data-dismiss="alert">×</button>'+
                        response.msg+'</div>');
                    return false;
                }
                window.location = '/usuario/login';
            });
        }

        return false;
    });

    $('a[href=#esqueci]').click(function(){
        $('[name=form-login]').hide();
    });

    $('#btnCancelar, .modal-header > .close').click(function(){
        $(".campos-obrigatorios > .close").click();

        $("#form-revover-pass").each(function(){
            this.reset();
        });

        $("#esqueci").modal('hide');
        $('[name=form-login]').show();

        $("form[name=form-revover-pass]").validate().resetForm();
        $("form[name=form-revover-pass]").validate().elements().each(function(elment){
            $(this).parents('.error').removeClass('error');
            $(this).val('');
        });
    });

    (function() {
        var supremeRegExp = /^\s+|×|\n\s+|\s+$/g,
            alertErrorElement = $('.alert.alert-error'),
            alertError = alertErrorElement.text().replace(supremeRegExp, '');
        if (alertError !== '') {
            var changePasswordTrigger = '{{changePasswordTrigger}}';
            if (alertError.indexOf(changePasswordTrigger) === 0) {
                alertErrorElement.find('.close').trigger('click');
                $('a[href=#esqueci]').trigger('click');
                var modalTitleElement = $('#esqueci').find('.modal-header h3'),
                    closableElements = $('#btnCancelar, .modal-header > .close'),
                    modalNewTitle = alertErrorElement.find('span').html(),
                    modalOldTitle = modalTitleElement.text();
                modalTitleElement.html( modalNewTitle );
                closableElements.one('click',function(){
                    modalTitleElement.text( modalOldTitle );
                });
            }
        }
        if (window.Egg) {
            (new Egg()).addCode("left, right, up, down, a, a", function () {
            if (window.console) {
                console.log(
                    "\n" +
                    " ___ ____ __  __ ____  _       ____             _____" +
                    "                    ____            _        _ \n" +
                    "|_ _/ ___|  \\/  | __ )(_) ___ |  _ \\  _____   _|_  " +
                    " ____  __ _ _ __ ___ |  _ \\ ___   ___| | _____| |\n" +
                    " | | |   | |\\/| |  _ \\| |/ _ \\| | | |/ _ \\ \\ /  " +
                    " | |/ _ \\/ _` | '_ ` _ \\| |_) / _ \\ / __| |/ / __| |\n" +
                    " | | |___| |  | | |_) | | (_) | |_| |  __/\\ V /  | |" +
                    "  __| (_| | | | | | |  _ | (_) | (__|   <\\__ |_|\n" +
                    "|___\\____|_|  |_|____/|_|\\___/|____/ \\___| \\_/   " +
                    "|_|\\___|\\__,_|_| |_| |_|_| \\_\\___/ \\___|_|\\_|___(_)\n" +
                    "\n"
                );
            }
          }).listen();
        }
    })();
});