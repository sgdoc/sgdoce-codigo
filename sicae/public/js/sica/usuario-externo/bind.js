var CheckedPerfil = {
    validate: function() {
        var total = $('#perfis input[type=checkbox]').not('#profile-all').length;
        var totalChecked = $('#perfis input[type=checkbox]:checked').not('#profile-all').length;

        $('#profile-all').attr('checked', total == totalChecked);
    },
    clickCheckBoxes: function() {
        $('#perfis input[type=checkbox]').not('#profile-all').click(function() {
            CheckedPerfil.validate();
        });
    }
};

$(document).ready(function() {

    var checksTrue = '';
    var checksFalse = '';

    $('#sqSistema').unbind('change').change(function() {
        $.each($('#modal-view [name*=perfil]'), function() {
            $(this).attr('checked', false);
        });

        var url = BASE + '/usuario-externo/perfis/sqSistema/' + $('#sqSistema').val() + '/sqUsuarioExterno/' + $('#sqUsuarioExterno').val();
        $('#perfis').load(url, function(data) {
            if ($(data).find('tr').size() == 1) {
                $('#perfis').hide();
                Validation.addMessage("Não existe perfil disponível para este sistema.");
            } else {
                $('#perfis').show();
            }

            CheckedPerfil.validate();
            CheckedPerfil.clickCheckBoxes();

            $('#perfis input[type=checkbox]').not('#profile-all').each(function() {
                if ($(this).is(':checked')) {
                    checksTrue = checksTrue + $(this).val();
                } else {
                    checksFalse = checksFalse + $(this).val();
                }
            });
        });

        $(document).ajaxStop(function() {
            if (!$('#sqSistema').val()) {
                $('#perfis').hide();
            }
        });
    });

    $('.btn-concluir').unbind('click').click(function() {
        $('#form-bind-profile').submit();
        return false;
    });

    $('#form-bind-profile').unbind('submit').submit(function() {
        var $form = $('#form-bind-profile'),
        dataForm = $form.serializeArray();

        if ($('#form-bind-profile :checkbox').size() && 0 === $('#form-bind-profile :checkbox:checked').size()) {
            Validation.addMessage('No mínimo 01 (um) Perfil deve ser selecionado.');
            return false;
        }

        var checksTrueSubmit = '';
        var checksFalseSubmit = '';

        $('#perfis input[type=checkbox]').not('#profile-all').each(function() {
            if ($(this).is(':checked')) {
                checksTrueSubmit = checksTrueSubmit + $(this).val();
            } else {
                checksFalseSubmit = checksFalseSubmit + $(this).val();
            }
        });

        if (checksTrueSubmit == checksTrue && checksFalseSubmit == checksFalse) {
            Validation.addMessage(MessageUI.get('MN165'));
            return false;
        }

        dataForm.push({
            'name': 'usuario',
            'value': $('#sqUsuarioExterno').val()
        });

        if ($form.valid() && $('#perfis table tr').size() > 1) {
            $.post($form.attr('action'), dataForm, function() {
                $('#modal-view').modal('hide');
                window.location = BASE + 'usuario-externo/bind/id/' + $('#sqUsuarioExterno').val();
            });
        }

        return false;
    });

    $('#form-bind-profile').validate();

    $('#profile-all').die('click').live('click', function() {
        var $all = $(this);
        $.each($('#form-bind-profile [name*=perfil]'), function() {
            $(this).attr('checked', $all.is(':checked'));
        });
    });

    $('.btnModal').live('click', function(event) {
        $('#modal-view > .modal-header h3').text($(this).attr('titleModal'));
    });

    $('.campos-obrigatorios:visible:last').hide();
    $('.modal-footer a:last').html('<i class="icon-remove"></i> Cancelar');

    $('#perfis input[type=checkbox]').not('#profile-all').each(function() {
        if ($(this).is(':checked')) {
            checksTrue = checksTrue + $(this).val();
        } else {
            checksFalse = checksFalse + $(this).val();
        }
    });

    CheckedPerfil.clickCheckBoxes();
    CheckedPerfil.validate();
});