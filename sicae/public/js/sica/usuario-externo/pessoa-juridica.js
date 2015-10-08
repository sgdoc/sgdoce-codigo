var PessoaJuridicaExterna = {
    initMask: function() {
        $('#nuCnpj').setMask('cnpj');
    },
    initValidate: function() {
        Validation.init();

        $("#nuCnpj").rules("add", {
            remote: {
                url: '/usuario-externo/check-credencials',
                type: 'post',
                data: {
                    nuCnpj: function() {
                        return $("#nuCnpj").val();
                    },
                    sqUsuarioExterno: function() {
                        return $("#sqUsuarioExterno").val() ? $("#sqUsuarioExterno").val() : ''
                    }
                }
            },
            messages: {
                remote: 'Usuário já cadastrado na base de dados.'
            }
        });

        UsuarioExternoForm.initValidate();
    },
    init: function() {
        UsuarioExternoForm.initAbas();
        PessoaJuridicaExterna.initValidate();
        PessoaJuridicaExterna.initMask();

        UsuarioExternoForm.initCep();
        UsuarioExternoForm.initMaks();
        UsuarioExternoForm.initSistemas();
    }
}

$(document).ready(function() {
    PessoaJuridicaExterna.init();
});