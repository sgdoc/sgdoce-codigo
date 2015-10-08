function agrupaCampos() {
    var grupos = {};
    var i = 0;

    $('form').find('.control-group .controls').each(function() {
        i++;
        grupos['group' + i] = '';
        $(this).find('.required, .email, .url, .number, .dateMonthYear, .dateBR').each(function() {
            grupos['group' + i] += this.name + " ";
        });
        if (grupos['group' + i] == '') {
            delete grupos['group' + i];
        }
    });
    return grupos;
}
;

var Validation = {
    errorClass: 'help-block',
    errorElement: 'p',
    initializer: false,
    errorLabelContainer: '.control-group',
    messages: {
        required: 'Campo de preenchimento obrigatório.',
        remote: "",
        email: "Por favor, informe um endereço de e-mail válido.",
        url: "Url inválida.",
        date: "Data inválida.",
        dateISO: "Data inválida.",
        number: "Insira somente números.",
        digits: "Insira somente digitos.",
        creditcard: "Please enter a valid credit card number.",
        equalTo: "Please enter the same value again.",
        accept: "Please enter a value with a valid extension.",
        maxlength: $.validator.format("Máximo de {0} caracteres."),
        minlength: $.validator.format("Mínimo de {0} caracteres."),
        rangelength: $.validator.format("Entre com um valor entre {0} e {1} caracteres."),
        range: $.validator.format("Please enter a value between {0} and {1}."),
        max: $.validator.format("Please enter a value less than or equal to {0}."),
        min: $.validator.format("Please enter a value greater than or equal to {0}.")
    },
    formatInput: function(type, element) {
        return $('label[for=' + element.name + ']').text();
    },
    highlight: function(element) {
        if (!$(element).parent('div').parents('div.control-group').hasClass('error')) {
            $(element).parent('div').parents('div.control-group').addClass('error');
        }
    },
    unhighlight: function(element) {
        var erros = this.errorsFor(element);

        if (typeof erros[0] !== undefined) {
            var campoErro = erros[0];

            if ($(campoErro).is(':hidden')) {
                $(element).parent('div').parents('div.control-group').removeClass('error');
            }
        }
    },
    addValidationDateBr: function() {
        jQuery.validator.addMethod("dateBR", function(value, element) {

            if (value.length > 0) {
                // verificando data
                var data = value;
                var dia = data.substr(0, 2);
                var barra1 = data.substr(2, 1);
                var mes = data.substr(3, 2);
                var barra2 = data.substr(5, 1);
                var ano = data.substr(6, 4);
                if (data.length != 10 || barra1 != "/" || barra2 != "/" || isNaN(dia) || isNaN(mes) || isNaN(ano) || dia > 31 || mes > 12)
                    return false;
                if ((mes == 4 || mes == 6 || mes == 9 || mes == 11) && dia == 31)
                    return false;
                if (mes == 2 && (dia > 29 || (dia == 29 && ano % 4 != 0)))
                    return false;
                if (ano < 1900)
                    return false;
                if (dia == '00')
                    return false;
                if (mes == '00')
                    return false;
                return true;
            } else {
                return true;
            }

        }, "Por favor, informe uma Data válida.");
    },
    addValidationCpf: function() {
        jQuery.validator.addMethod("cpf", function(value, element) {
            value = value.replace('.', '');
            value = value.replace('.', '');
            var cpf = value.replace('-', '');

            if (cpf == "") {
                return true;
            }

            while (cpf.length < 11)
                cpf = "0" + cpf;
            var expReg = /^0+$|^1+$|^2+$|^3+$|^4+$|^5+$|^6+$|^7+$|^8+$|^9+$/;
            var a = [];
            var b = new Number;
            var c = 11;
            for (i = 0; i < 11; i++) {
                a[i] = cpf.charAt(i);
                if (i < 9)
                    b += (a[i] * --c);
            }
            if ((x = b % 11) < 2) {
                a[9] = 0
            } else {
                a[9] = 11 - x
            }
            b = 0;
            c = 11;
            for (y = 0; y < 10; y++)
                b += (a[y] * c--);
            if ((x = b % 11) < 2) {
                a[10] = 0;
            } else {
                a[10] = 11 - x;
            }
            if ((cpf.charAt(9) != a[9]) || (cpf.charAt(10) != a[10]) || cpf.match(expReg))
                return false;
            return true;
        }, "Por favor, informe um CPF válido."); // Mensagem padrão
    },
    addValidationCnpj: function() {
        jQuery.validator.addMethod("cnpj", function(cnpj, element) {
            cnpj = jQuery.trim(cnpj);// retira espaços em branco
            // DEIXA APENAS OS NÚMEROS
            cnpj = cnpj.replace('/', '');
            cnpj = cnpj.replace('.', '');
            cnpj = cnpj.replace('.', '');
            cnpj = cnpj.replace('-', '');

            if (cnpj == "") {
                return true;
            }

            var numeros, digitos, soma, i, resultado, pos, tamanho, digitos_iguais;
            digitos_iguais = 1;

            if (cnpj.length < 14 && cnpj.length < 15) {
                return false;
            }
            for (i = 0; i < cnpj.length - 1; i++) {
                if (cnpj.charAt(i) != cnpj.charAt(i + 1)) {
                    digitos_iguais = 0;
                    break;
                }
            }

            if (!digitos_iguais) {
                tamanho = cnpj.length - 2
                numeros = cnpj.substring(0, tamanho);
                digitos = cnpj.substring(tamanho);
                soma = 0;
                pos = tamanho - 7;

                for (i = tamanho; i >= 1; i--) {
                    soma += numeros.charAt(tamanho - i) * pos--;
                    if (pos < 2) {
                        pos = 9;
                    }
                }
                resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
                if (resultado != digitos.charAt(0)) {
                    return false;
                }
                tamanho = tamanho + 1;
                numeros = cnpj.substring(0, tamanho);
                soma = 0;
                pos = tamanho - 7;
                for (i = tamanho; i >= 1; i--) {
                    soma += numeros.charAt(tamanho - i) * pos--;
                    if (pos < 2) {
                        pos = 9;
                    }
                }
                resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
                if (resultado != digitos.charAt(1)) {
                    return false;
                }
                return true;
            } else {
                return false;
            }
        }, "Por favor, informe um CNPJ válido."); // Mensagem padrão
    },
    addValidationCep: function() {
        jQuery.validator.addMethod("cep", function(value, element) {

            // Caso o CEP não esteja nesse formato ele é inválido!
            var expr = /^[0-9]{2}\.{0,1}[0-9]{3}-[0-9]{3}$/;

            if (value.length > 0) {
                if (expr.test(value) && value != '00000-000' && value != '00000000')
                    return true;
                else
                    return false;
            } else {
                return true;
            }

        }, "Por favor, informe um CEP válido."); // Mensagem padrão
    },
    addValidationValidatePassword: function() {
        jQuery.validator.addMethod("validatePassword", function(value, element) {
            if (value.length < 6 || value.length > 32) {
                return false;
            }

            var valid = true;
            var letters = '';

            for (i = 31; ++i < 125; ) {
                if (i != 39 && i != 94 && i != 96) {
                    letters = letters + String.fromCharCode(i);
                }
            }

            $.each(value, function(key, letter) {
                if (letters.search(letter) == "-1") {
                    valid = false;
                    return;
                }
            });

            return valid;

        }, "A senha informada deve conter no mínimo 6 e no máximo 32 caracteres e sem acentuação.");
    },
    addValidationGeo: function() {
        jQuery.validator.addMethod("geo", function(value, element) {

            // Caso o geo não esteja nesse formato ele é inválido!
            var expr = /^-?[0-1]?[0-9]{1,2}°[0-9]{1,2}'[0-9]{1,2}.\d*"$/;

            if (value.length > 0) {
                if (expr.test(value))
                    return true;
                else
                    return false;
            } else {
                return true;
            }

        }, "O formato das coordenadas está incorreto. "); // Mensagem padrão
    },
    addValidationFoneBr: function() {
        jQuery.validator.addMethod("foneBR", function(value, element) {
            value = value.replace('-', '');
            value = value.replace('(', '');
            value = value.replace(')', '');
            value = value.replace(' ', '');

            if (value == '') {
                return true;
            }

            if (value.length < 11 || value.length > 12) {
                return false;
            }

            return true;

        }, "Por favor, informe um telefone válido.");
    },
    init: function() {
        if (Validation.initializer) {
            return;
        }

        $.validator.defaults.invalidHandler = function() {
        }

        // função para adicionar o erro apenas no final da div
        $.validator.setDefaults({
            errorPlacement: function(error, element) {
                element.parent().append(error);
            }
        });

        $.validator.messages = Validation.messages;
        //        $.validator.methods.maxlength = function(){
        //            return true;
        //        };
        //        $.validator.methods.minlength =  function(){
        //            return true;
        //        };
        $.validator.defaults.errorElement = Validation.errorElement;
        $.validator.defaults.errorClass = Validation.errorClass;
        $.validator.defaults.highlight = Validation.highlight;
        $.validator.defaults.unhighlight = Validation.unhighlight;
        $.validator.defaults.groups = agrupaCampos();
        $.validator.defaults.focusInvalid = false;

        Validation.addValidationDateBr();
        Validation.addValidationCpf();
        Validation.addValidationCnpj();
        Validation.addValidationCep();
        Validation.addValidationGeo();
        Validation.addValidationValidatePassword();
        Validation.addValidationFoneBr();

        /**
         * percorre todos os form e verfica se em algum tem alguma classe
         * de validacao do Jquery Validate
         */
        $('form').each(function() {
            if ($(this).find('.required, .email, .url, .number, .cep')) {

                if ($(this).attr('ignore-input') == 'false') {
                    $.validator.defaults.ignore = false;
                }

                $(this).validate();
            }
        });

        Validation.initializer = true;
    },
    addMessage: function(message)
    {
        if (!$('.campos-obrigatorios').is(':visible')) {
            $('<div class="alert alert-error campos-obrigatorios">' +
                    '<button class="close" data-dismiss="alert">×</button>' + message
                    + '</div>').insertAfter(
                    '.title-main:visible:last');
            $(document).scrollTop(0);
        } else {
            $('.campos-obrigatorios:visible:last').html('<button class="close" data-dismiss="alert">×</button>' + message);
            $('.campos-obrigatorios:visible:last').parents('.modal').scrollTop(0);
        }

        setTimeout(function() {
            $('.campos-obrigatorios:visible:last').find('.close').trigger('click');
        }, 6000);
    }
}

$(document).ready(function() {
    Validation.init();
});
