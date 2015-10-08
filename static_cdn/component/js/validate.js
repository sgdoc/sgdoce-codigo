var Validation = {
    validateForm: function (container) {

        $('.help-block', container).remove();

        var result = true;


        $(':input[class*="required"]', container).each(function () {
            var elm = $(this);
            var isNotDisabled = !elm.attr('disabled');

            if ("" == elm.val() && elm.is(':visible') && isNotDisabled) {
                elm.closest('.control-group').addClass('error');
                elm.parent().append('<p class="help-block">Campo de preenchimento obrigatório.</p>');
                result = false;
            } else {
                elm.closest('.control-group').removeClass('error');
            }
        });

        return result;
    },
    
    validateFormAll: function (container) {

        $('.help-block', container).remove();

        var result = true;


        $(':input[class*="required"],textarea[class*="required"],select[class*="required"]', container).each(function () {
            var elm = $(this);
            var isNotDisabled = !elm.attr('disabled'),
                isHidden = elm.hasClass('hide');
                
            if( isHidden == false ) {
                isHidden = elm.parents('div, fieldset').hasClass('hide');
            }

            if ("" == elm.val() && isHidden == false && isNotDisabled) {                
                elm.closest('.control-group').addClass('error');
                elm.parent().append('<p class="help-block">Campo de preenchimento obrigatório.</p>');
                result = false;
                elm.on('blur', function(){
                    if( $(this).val() != "" ){
                        elm.closest('.control-group').removeClass('error');
                        elm.parent().find('.help-block').remove();
                    }
                });
            } else {
                elm.closest('.control-group').removeClass('error');
            }
        });

        return result;
    },

    validateMail: function(element)
    {
        var result = true;
        var value   = element.val();
        var pattern = /^[\w\d\-\.]+@([\w\d\-]+\.)*([\w\d\-]+)$/;

        result  = pattern.test(value);

        $('.help-block', element.closest('.control-group')).remove();

        if (value != "" && result == false) {
            element.closest('.control-group').addClass('error');
            element.parent().append('<p class="help-block">Por favor, informe um Email válido.</p>');
        }

        return result;
    },

    validateMailInstitucional: function(element)
    {
        var result  = false;

        if (Validation.validateMail(element)) {
            $('.help-block', element.closest('.control-group')).remove();
            if (/^.*\.gov\.br$/.test(element.val())) {
                result = true;
                element.parents('.control-group').removeClass('error');
            } else {
                result = false;
                element.parents('.control-group').addClass('error');
                element.parent().append('<p class="help-block">Por favor, informe um Email Governamental válido.</p>');
            }
        }

        return result;
    }
};