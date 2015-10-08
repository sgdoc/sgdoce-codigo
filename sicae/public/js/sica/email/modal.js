EmailModal = {
    validationEmail: function() {
        $('#txEmail').blur(function() {
            EmailModal.validationMailInstitucional();
        });
        $('#sqTipoEmail').change(function() {
            if ($('#sqTipoEmail').val() == 4) {
                $('#txEmail').removeClass('email');
            } else {
                $('#txEmail').addClass('email');
            }
            EmailModal.validationMailInstitucional();
        });
    },
    concluir: function() {
        $('.btnAdicionarEmail').click(function() {
            if ($('#form-email-modal').valid()) {
                if (EmailModal.validationMailInstitucional()) {
                    if ($('#form-email-modal #sqEmail').val()) {
                        PessoaForm.saveFormWebService(
                                'app:Email',
                                'libCorpUpdateEmail',
                                $('#form-email-modal'),
                                $('#form-email')
                        );
                    } else {
                        PessoaForm.saveFormWebService(
                                'app:Email',
                                'libCorpSaveEmail',
                                $('#form-email-modal'),
                                $('#form-email')
                        );
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        });
        PessoaForm.validateType('#modal-email', '#sqTipoEmail', 'e-mail');
    },
    init: function() {
        EmailModal.validationEmail();
        EmailModal.concluir();
    },
    validationMailInstitucional: function() {
        if ($('#txEmail').val() != "") {
            $("#txEmail").closest('.control-group').removeClass('error').find('.mail-error').remove();
            if (/INSTITUCIONAL/im.test($('select#sqTipoEmail').find(":selected").text().toUpperCase())) {
                if (!(/\w(@icmbio\.gov\.br)$/im.test( $.trim($('#txEmail').val()) ))) {
                    $('#txEmail').parent()
                                 .append('<p class="help-block mail-error">E-mail institucional inválido.</p>')
                                 .closest('.control-group')
                                 .addClass('error');
                    return false;
                }
            }
            return true;
        }
    }
}

$(document).ready(function() {
    EmailModal.init();
});