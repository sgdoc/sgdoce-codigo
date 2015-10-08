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
};

var Validation = {
    errorClass: 'help-block',

    errorElement: 'p',

    errorLabelContainer: '.control-group',

    messages: {
        required: 'Campo de preenchimento obrigatório.',
        remote: "",
        email: "E-mail inválido.",
        url: "Url inválida.",
        date: "Data inválida.",
        dateISO: "Data inválida.",
        number: "Insira somente números.",
        digits: "Insira somente digitos.",
        creditcard: "Please enter a valid credit card number.",
        startDate: "Data é inferior ao limite definido",
        endDate: "Data é maior que o limite definido",
        equalTo: "Please enter the same value again.",
        accept: "Extensão inválida.",
        maxlength: $.validator.format("Máximo de {0} caracteres."),
        minlength: $.validator.format("Mínimo de {0} caracteres."),
        rangelength: $.validator.format("Entre com um valor entre {0} e {1} caracteres."),
        range: $.validator.format("Informe um número entre {0} e {1}."),
        max: $.validator.format("Informe um valor menor ou igual a {0}."),
        min: $.validator.format("Informe um valor maior ou igual a {0}."),
    },

    formatInput: function(type, element){
        return $('label[for=' + element.name + ']').text();
    },

    highlight: function(element) {
        if(!$(element).parent('div').parents('div.control-group').hasClass('error')) {
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

//    highlight: function(element) {
//
//        var controlGroup = $(element).parent('div').parents('div.control-group');
//        if (element.type == 'radio') {
//            controlGroup = $(element).parent('label').parent('div').parents('div.control-group');
//        }
//
//        if(!controlGroup.hasClass('error')) {
//            controlGroup.addClass('error');
//        }
//    },
//
//    unhighlight: function(element) {
//        var erros = this.errorsFor(element);
//        if (typeof erros[0] !== undefined) {
//
//            var controlGroup = $(element).parent('div').parents('div.control-group');
//            if (element.type == 'radio') {
//                controlGroup = $(element).parent('label').parent('div').parents('div.control-group');
//            }
//            var campoErro = erros[0];
//
//            if ($(campoErro).is(':hidden')) {
//                controlGroup.removeClass('error');
//            }
//        }
//    },

    addValidationDateBr: function(){
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

        }, UI_MSG.MN023);
    },

    addValidationEndDate: function(){
        jQuery.validator.addMethod("endDate", function(value, element) {
            var parent = $(element).parent('div.date');

            if (value.length > 0 && parent.data('dateEnddate')) {
                var endDate = parent.data('dateEnddate');
                //-1 se a < b
                // 0 se a = b
                // 1 se a > b
                if (Dates.compare(Dates.convertBrToUs(value),Dates.convertBrToUs(endDate)) > 0){
                    return false;
                }
                return true;
            }else{
                return true;
            }
        });
    },

    addValidationStartDate: function(){
        jQuery.validator.addMethod("startDate", function(value, element) {
            var parent = $(element).parent('div.date');
            if (value.length > 0 && parent.data('dateStartdate')) {
                var startDate = parent.data('dateStartdate');
                //-1 se a < b
                // 0 se a = b
                // 1 se a > b
                if (Dates.compare(Dates.convertBrToUs(value),Dates.convertBrToUs(startDate)) < 0){
                    return false;
                }
                return true;
            }else{
                return true;
            }
            return true;
        });
    },

    addValidationCpf: function(){
        jQuery.validator.addMethod("cpf", function(value, element) {
            return isCPFValid(value);
        }, UI_MSG.MN021); // Mensagem padrão
    },

    addValidationCnpj: function(){
        jQuery.validator.addMethod("cnpj", function(value, element) {
            return isCNPJValid(value);
        }, UI_MSG.MN022); // Mensagem padrão
    },

    addValidationCep: function(){
        jQuery.validator.addMethod("cep", function(value, element) {
            return isCEPValid(value);
        }, UI_MSG.MN127); // Mensagem padrão
    },

    addValidationGeo: function(){
        jQuery.validator.addMethod("geo", function(value, element) {

            // Caso o geo não esteja nesse formato ele é inválido!
            var expr = /^-?[0-1]?[0-9]{1,2}°[0-9]{1,2}'[0-9]{1,2}.\d*"$/;

            if(value.length > 0){
                if(expr.test(value))
                    return true;
                else
                    return false;
            }else{
                return true;
            }

        }, "O formato das coordenadas está incorreto. "); // Mensagem padrão
    },

    addValidationNupSiorg: function(){
        jQuery.validator.addMethod("nupSiorg", function(value, element) {
            var nup = value.replace(/\D+/g, '');
            if (nup == "") {return true;}
            if (parseInt(bi_mod(nup,'97')) !== 1) {return false;}
            return true;
        }, UI_MSG.MN126); // Mensagem padrão
    },

    init: function(){
        $.validator.defaults.invalidHandler = function(){
            Validation.addMessage('Campos de preenchimento obrigatório não foram preenchidos.');
        };

        // função para adicionar o erro apenas no final da div
        $.validator.setDefaults({
            errorPlacement: function(error, element) {
            	if(element.parents('.error-placement').length) {
            		$(element.parents('.error-placement')[0]).append(error);

            		return false;
            	}

                element.parent().append(error);
            }
        });

        $.validator.messages = Validation.messages;
        $.validator.methods.minlength = function(value, element, min){
            if( String(value).length < min ){
                return false;
            }
            return true;
        };
        $.validator.methods.maxlength = function(value, element, max){
            if( String(value).length > max ){
                return false;
            }
            return true;
        };
        $.validator.defaults.errorElement = Validation.errorElement;
        $.validator.defaults.errorClass = Validation.errorClass;
        $.validator.defaults.highlight = Validation.highlight;
        $.validator.defaults.unhighlight = Validation.unhighlight;
        $.validator.defaults.groups = agrupaCampos();
        $.validator.defaults.focusInvalid = false;

        Validation.addValidationDateBr();
        Validation.addValidationEndDate();
        Validation.addValidationStartDate();
        Validation.addValidationCpf();
        Validation.addValidationCnpj();
        Validation.addValidationCep();
        Validation.addValidationGeo();
        Validation.addValidationNupSiorg();

        /**
         * percorre todos os form e verfica se em algum tem alguma classe
         * de validacao do Jquery Validate
         */
        $('form').each(function(){
            if($(this).find('.required, .email, .url, .number, .cep')){
                if($(this).attr('ignore-input') == 'false'){
                    $.validator.defaults.ignore = false;
                }
                $(this).validate();
            }
        });
    },

    addMessage: function(message, alertClass){
        if( alertClass == undefined ){
            alertClass = 'error';
        }
        $('.campos-obrigatorios').remove();
        if (!$('.campos-obrigatorios').is(':visible')) {
            $('<div class="alert alert-' + alertClass + ' campos-obrigatorios">' +
                    '<button class="close" data-dismiss="alert">×</button>' + message
                    + '</div>').insertAfter(
                    'h1:visible:last');
            $(document).scrollTop(0);
        } else {
            $('.campos-obrigatorios:visible:last').html('<button class="close" data-dismiss="alert">×</button>' + message);
            $('.campos-obrigatorios:visible:last').parents('.modal').scrollTop(0);
        }
    }
}

$(document).ready(function() {
    Validation.init();
});
