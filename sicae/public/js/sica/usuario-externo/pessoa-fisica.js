var PessoaFisicaExterna = {
    initNacionalidade: function() {

        $('.campoPassaporte').hide();
        $('input[name=inNacionalidadeBrasileira]').click(function() {
            if ($(this).val() == 1) {
                $('.campoPassaporte').hide();
                $('.campoRG').show();
                $('#nuCpf').addClass('required');
                $('.cpf').html('*');
                $('.campoPassaporte').hide();
                $('.avisoCpf').show();
                $('.avisoPassaporte').hide();

                $('#sqPaisOrigem').val('');
                $('#nuPassaporte').val('');

            } else {
                $('#nuCpf').removeClass('required').blur().blur();
                $('.cpf').html('');
                $('.campoPassaporte').show();
                $('.campoRG').hide();
                $('.avisoCpf').hide();
                $('.avisoPassaporte').show();
            }
        });

        if ($('input[name=inNacionalidadeBrasileira]:last').is(':checked')) {
            $('input[name=inNacionalidadeBrasileira]:last').click();

            if ($('#nuCpf').val() == '') {
                $('#nuCpf').removeAttr('disabled');
            }
        }
    },
    initMask: function() {
        $('#nuCpf').setMask('cpf');
        $('#nuRegistroGeral').setMask({mask: '9', type: 'repeat'});

        if ($('#sqUsuarioExterno').val() == 0 || $('#sqUsuarioExterno').val() == '') {
            $('#complementar-sqPais').val('1').change();
        }
    },
    initValidate: function() {
        Validation.init();

        $("#nuCpf").rules("add", {
            remote: {
                url: '/usuario-externo/check-credencials',
                type: 'post',
                data: {
                    nuCpf: function() {
                        return $("#nuCpf").val();
                    },
                    sqUsuarioExterno: function() {
                        return $("#sqUsuarioExterno").val() ? $("#sqUsuarioExterno").val() : '';
                    }
                }
            },
            messages: {
                remote: 'Usuário já cadastrado na base de dados.'
            }
        });

        $(['#nuPassaporte', '#noUsuarioExterno']).each(function(index, value) {
            PessoaFisicaExterna.rulesPf(value);
        });

        UsuarioExternoForm.initValidate();
    },
    rulesPf: function(value) {
        $(value).rules("add", {
            remote: {
                url: '/usuario-externo/check-credencials',
                type: 'post',
                data: {
                    noUsuarioExterno: function() {
                        return $("#noUsuarioExterno").val();
                    },
                    nuPassaporte: function() {
                        return $("#nuPassaporte").val();
                    },
                    inNacionalidadeBrasileira: function() {
                        return $("[name=inNacionalidadeBrasileira]:checked").val();
                    },
                    sqUsuarioExterno: function() {//sqUsuariExterno: function() {
                        return $("#sqUsuarioExterno").val();
                    }
                }
            },
            messages: {
                remote: "Usuário já cadastrado na base de dados."
            }
        });
    },
    init: function() {
        UsuarioExternoForm.initAbas();
        PessoaFisicaExterna.initNacionalidade();
        PessoaFisicaExterna.initValidate();
        PessoaFisicaExterna.initMask();

        UsuarioExternoForm.initCep();
        UsuarioExternoForm.initMaks();
        UsuarioExternoForm.initSistemas();

        $(document).ajaxSuccess(function(event, xhr, settings) {
            if (settings.url == "/usuario-externo/check-credencials") {

                $(['#nuPassaporte', '#noUsuarioExterno']).each(function(index, value) {
                    if (eval(xhr.responseText)) {
                        $(value).parents('.error').removeClass("error");
                        $(value).parent().find('p.help-block').remove();
                        $(value).removeData("previousValue");
                    }
                });
            }
        });
    }
};

$(document).ready(function() {
    PessoaFisicaExterna.init();
});