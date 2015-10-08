$(document).ready(function() {
    loadJs('/js/library/jquery.simpleautocomplete.js', function(){
        $('#noPessoa').simpleAutoComplete("/principal/pessoa/search-pessoa/validate/1", {
            attrCallBack: 'class',
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel',
            extraParamFromInput: '#sqTipoPessoa',
            hiddenName: 'sqPessoa'
        }, function(input, li, hidden){
            var label = li.text(),
            label = label.substr(label.indexOf(' - ') + 3);
            $('#noPessoa').val(label);
            $.getJSON(BASE + '/principal/pessoa-fisica/get-data-institucional/id/' + hidden.val(), function(response) {
                $.each(response.content, function(index, value) {
                    $('#' + index).val(value);
                });
            });
        });
    });

    $('#search-cpf').click(function() {
        if($('#nuCpf').val()){
            $.getJSON(BASE + '/pessoa/search-cpf-cnpj/nuCpf/' + $('#nuCpf').val(), function(response) {
                response = response || {};

                if (!response.sqPessoa) {
                    Message.showMessage({
                        'message': {
                            'body': MessageUI.get('MN138'),
                            'subject': 'Erro',
                            'type': 1
                        }
                    });
                    $('#nuCpf, #nuDdd, #nuTelefone, #txEmail, #noPessoa, #noPessoa_hidden').val('');
                    return false;
                }

                $.getJSON(BASE + '/principal/pessoa-fisica/get-data-institucional/id/' + response.sqPessoa, function(response) {
                    $.each(response.content, function(index, value) {
                        if (('sqPessoa' == index)) {
                            $('#noPessoa_hidden').val(value);
                        }

                        if('nuTelefone' == index && value){
                            if(value.length == 8){
                                value = value.substr(0, 4) + '-' + value.substr(4, 4);
                            }
                        }

                        $('#' + index).val(value);
                    });

                    $("form[name=form-usuario-interno]").validate().resetForm();
                    $("form[name=form-usuario-interno]").validate().elements().each(function(elment){
                        $(this).parents('.error').removeClass('error');
                    });
                });
            });
        }else{
            var error = '<p for="group3" generated="true" class="help-block">Campo de preenchimento obrigatório.</p>';

            $(this).parents('div.control-group').addClass('error');

            if(!$(this).parent('.input-append').find('p.help-block[generated=true]').size()){
                $(this).parent('.input-append').append(error);
            }

        }
    });

});