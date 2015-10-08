$(document).ready(function(){
    $('#btnRecoverPassExterno').click(function(){
        if ($('#form-revover-pass-externo').valid()) {
            $.post('/usuario-externo/recover-pass', {
                tpValidacao : $('#tpValidacao').val(),
                txEmail : $('#txEmail').val(),
                txLogin   : $('#txLogin').val()
            },
            function(response){
                if(response.error){
                    $('#error-pass-externo').html('<div class="alert alert-error campos-obrigatorios">'+
                        '<button class="close" data-dismiss="alert">×</button>'+
                        response.msg+'</div>');

                    return false;
                }
                window.location = '/usuario-externo/login';
            });
        }

        return false;
    });

    $('a[href=#esqueci-externo]').click(function(){
        $('[name=form-login]').hide();
    });

    $('#tpValidacao').change(function(){
        $('#txLogin').val('').blur();
        $('#txEmail, #senha').val('');

        switch($(this).val()){
            case 'cpf':
                $('#txLogin').
                val('').
                removeAttr('maxlength').
                removeClass().
                addClass('input-xlarge').
                addClass('required').
                addClass('cpf').
                setMask('cpf').
                parent('div').
                parent('div').
                find('label').
                html('<span class="required">* </span> CPF');

                break;
            case 'cnpj':
                $('#txLogin').
                val('').
                removeAttr('maxlength').
                removeClass().
                addClass('input-xlarge').
                addClass('required').
                addClass('cnpj').
                setMask('cnpj').
                parent('div').
                parent('div').
                find('label').
                html('<span class="required">* </span> CNPJ');
                break;
            case 'passaporte':
                $('#txLogin').
                val('').
                setMask('numeric').
                attr('maxlength', 50).
                removeClass().
                addClass('input-xlarge').
                addClass('required').
                parent('div').
                parent('div').
                find('label').
                html('<span class="required">* </span> Passaporte');
                break;
        }
    });

    $('#btnCancelar, .modal-header > .close').click(function(){
        $(".campos-obrigatorios > .close").click();

        $("#form-revover-pass-externo").each(function(){
            this.reset();
        });

        $('#tpValidacao').val('cpf').change();
        $("#esqueci-externo").modal('hide');

        $('[name=form-login]').show();

        $("#form-revover-pass-externo").validate().resetForm();
        $("#form-revover-pass-externo").validate().elements().each(function(elment){
            $(this).parents('.error').removeClass('error');
        });
    });

});