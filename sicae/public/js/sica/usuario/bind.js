$(document).ready(function() {
    var checksTrue = '';
    var checksFalse = '';

    loadJs('js/library/jquery.simpleautocomplete.js', function() {
        $('#noUnidade').simpleAutoComplete(BASE + '/unidade-organizacional/ativas', {
            attrCallBack: 'class',
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel',
            hiddenName: 'unidade'
        }, function(object, li, hidden) {
            $.getJSON(BASE
            + '/usuario-interno/perfis-unidade/',
                    {
                        'unidade': hidden.val(),
                        'usuario': $('#sqUsuario').val()
                    },
            function(response) {
                $('#body-view [name*=perfil]').removeAttr('checked');

                $.each(response, function() {
                    $('#body-view [name*=perfil][value=' + this.sqPerfil + ']').attr('checked', true);
                });

                $('#perfis input[type=checkbox]').not('#profile-all').click(function() {
                    var total = $('#perfis input[type=checkbox]').not('#profile-all').size();
                    var totalChecked = $('#perfis input[type=checkbox]:checked').not('#profile-all').size();

                    if (total == totalChecked) {
                        $('#profile-all').attr('checked', true);
                    } else {
                        $('#profile-all').removeAttr('checked');
                    }
                });

                var total = $('#perfis input[type=checkbox]').not('#profile-all').size();
                var totalChecked = $('#perfis input[type=checkbox]:checked').not('#profile-all').size();

                if (total == totalChecked) {
                    $('#profile-all').attr('checked', true);
                } else {
                    $('#profile-all').removeAttr('checked');
                }

                $('#perfis input[type=checkbox]').not('#profile-all').each(function() {
                    if ($(this).is(':checked')) {
                        checksTrue = checksTrue + $(this).val();
                    } else {
                        checksFalse = checksFalse + $(this).val();
                    }
                });
            }
            );
        });
    });

    $('#sqSistema').unbind('change').change(function() {
        $.each($('#modal-view [name*=perfil]'), function() {
            $(this).attr('checked', false);
        });
        $('#noUnidade').val('');
        $('#noUnidade_hidden').val('');

        $('#perfis').load(BASE + '/usuario-interno/perfis/sqSistema/' + $('#sqSistema').val(), function(data) {
            if ($(data).find('tr').size() == 1) {
                Validation.addMessage("Não existe perfil disponível para este sistema.");
                $('#perfis').hide();
            } else {
                $('#perfis').show();
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
            Validation.addMessage(MessageUI.get('MN136'));
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
            'value': $('#sqUsuario').val()
        });
        $.post($form.attr('action'), dataForm, function() {
            $('#modal-view').modal('hide');
            window.location = BASE + '/usuario-interno/bind/id/' + $('#sqUsuario').val();
        });

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

    $('#perfis input[type=checkbox]').not('#profile-all').click(function() {
        var total = $('#perfis input[type=checkbox]').not('#profile-all').size();
        var totalChecked = $('#perfis input[type=checkbox]:checked').not('#profile-all').size();

        if (total == totalChecked) {
            $('#profile-all').attr('checked', true);
        } else {
            $('#profile-all').removeAttr('checked');
        }
    });

    var total = $('#perfis input[type=checkbox]').not('#profile-all').length;
    var totalChecked = $('#perfis input[type=checkbox]:checked').not('#profile-all').length;

    $('#perfis input[type=checkbox]').not('#profile-all').each(function() {
        if ($(this).is(':checked')) {
            checksTrue = checksTrue + $(this).val();
        } else {
            checksFalse = checksFalse + $(this).val();
        }
    });

    $('#profile-all').attr('checked', total == totalChecked);
});