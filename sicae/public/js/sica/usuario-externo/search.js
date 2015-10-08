$(document).ready(function() {
    loadJs('js/library/jquery.simpleautocomplete.js', function() {
        $('#noUnidade').simpleAutoComplete(BASE + '/unidade-organizacional/ativas', {
            attrCallBack: 'class',
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel',
            hiddenName: 'sqUnidadeOrg'
        });
    });

    $('button[type=reset]').click(function() {
        $('#menu-sistema select option:not(:first)').remove();

        $('#form-pesquisa-usuario input[type=text]').each(function(a, b){
            $(b).attr('value', '');
        });

        $("#form-pesquisa-usuario").validate().resetForm();
        $("#form-pesquisa-usuario").validate().elements().each(function(elment){
            $(this).parents('.error').removeClass('error');
        });
    });

    if ($('#id').val()) {
        var callBackPerfil = function() {
            UsuarioExterno.bind($('#id').val());
        };

        Message.showConfirmation({
            'body': 'Deseja atribuir perfil ao usuário cadastrado?',
            'yesCallback': callBackPerfil
        });
    }

    Grid.load($('#form-pesquisa-usuario'), $('#table-usuario'));

    $('#form-pesquisa-usuario').unbind("submit").submit(function() {
        var $this = $(this);
        $('#pesquisa-pdf').val($this.find(':not(#pesquisa-pdf)').serialize());
        $('#search').val('1');

        if ($(':input[value=""]:not(:hidden,button)', this).size() === $(':input:not(:hidden,button)', this).size()) {
            Validation.addMessage('Informar no mínimo um dos parâmetros de consulta.');
            $(document).scrollTop(40);
            return false;
        }

        $('.campos-obrigatorios').addClass('hide');
        $('#table-usuario').dataTable().fnDraw(false);

        setTimeout(function(){
            $('#table-usuario').closest('div.hidden').removeClass('hidden');
        },1000);
        return false;
    });

    $('[id=sqPerfil]:last').attr('disabled', true);
    $('#sqSistema').change(function() {
        var value = $(this).val();

        if (value) {
            $('[id=sqPerfil]:last').parent('div').load('/perfil/combo-profile', {
                sqSistema             : $('#sqSistema').val(),
                inPerfilExterno : 1
            },function(){
                $('[id=sqPerfil]:last').addClass('span9').removeAttr('disabled');
            });

        }else{
            $('[id=sqPerfil]:last').attr('disabled', true).val('').change();
        }
    });

    $("#cpfCnpjUsuario")
    .keypress(function(e) {
        var len     = $(this).val().length;
        var key     = e.which;
        var keyCode = e.keyCode;
        var isCtrlC = e.ctrlKey && (key == 67 || key == 99);
        var isCtrlP = e.ctrlKey && (key == 80 || key == 112);
        var isCtrlR = e.ctrlKey && (key == 82 || key == 114);
        var isCtrlV = e.ctrlKey && (key == 86 || key == 118);
        var isCtrlX = e.ctrlKey && (key == 88 || key == 120);
        var isCtrlZ = e.ctrlKey && (key == 90 || key == 122);

        if (isCtrlV) {
            $('#cpfCnpjUsuario')
            .removeClass("cpf")
            .removeClass("cnpj")
            .unsetMask();
            len = 18;
        }

        if ((key < 48 || key > 57) &&
            key != 8 && !isCtrlC && !isCtrlP && !isCtrlV &&
            !isCtrlR && !isCtrlX && !isCtrlZ &&  keyCode != 46) {
            if (keyCode == 9) {
                $("#sqSistema").trigger("focus");
            }
            return false;
        }

        if (len >= 14) {
            $('#cpfCnpjUsuario')
            .setMask({
                mask : '99.999.999/9999-99',
                autoTab : false
            })
            .addClass("cnpj")
            .removeClass("cpf");
        } else {
            $('#cpfCnpjUsuario').setMask({
                mask : '999.999.999-99',
                autoTab : false
            })
            .addClass("cpf")
            .removeClass("cnpj");
        }
    })
    .keyup(function(e) {
        var len = $(this).val().length;

        if (len > 14) {
            $('#cpfCnpjUsuario')
            .setMask({
                mask : '99.999.999/9999-99',
                autoTab : false
            })
            .addClass("cnpj")
            .removeClass("cpf");
        } else {
            $('#cpfCnpjUsuario').setMask({
                mask : '999.999.999-99',
                autoTab : false
            })
            .addClass("cpf")
            .removeClass("cnpj");
        }
    });

    $('.limpar').click(function(){
        $("#form-pesquisa-usuario").validate().resetForm();
        $(":input").each(function(id, element){
            $(element).val('').change();
        });

        $('#cpfCnpjUsuario').unsetMask();
        $('#cpfCnpjUsuario').val('');
    });
});

var UsuarioExterno = {
    bind : function(id) {
        window.location = BASE + 'usuario-externo/bind/id/' + id;
    },
    resendMail : function(id) {
        Message.showConfirmation({
            'body': MessageUI.get('MN173'),
            'yesCallback': function(){
                $.get(
                    BASE + 'usuario-externo/resend-mail/id/' + id,
                    function(data) {
                        if( typeof data === 'object' && 'error' in data) { 
                            if (data.error === false ){
                                Message.showSuccess(MessageUI.get('MN174'));
                            } else {
                                Message.showAlert(data.message);
                            }
                        } else {
                            Message.showAlert(MessageUI.get('MN172'));        
                        }
                    }
                ).fail(function(){
                    Message.showAlert(MessageUI.get('MN172'));
                });
            }
        });
        
    }
};
