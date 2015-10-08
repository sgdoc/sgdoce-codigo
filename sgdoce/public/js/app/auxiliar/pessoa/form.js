PessoaForm = {

    saveFormWebService: function(repository, method, form, formGrid, filters){

        var params;

        if($(form).serializeArray().length){
            params = form.serializeArray();
        }else{
            params = form;
        }
        
        var config = {
            url: '/auxiliar/pessoa/save-form-web-service',
            type: 'post',
            data: {
                params: params,
                repository: repository,
                method: method,
                filters: filters
            },
            dataType: 'json',
            success: function(data){
                Message.showMessage(data);

//                if(Message.isSuccess(data)){
                    formGrid.submit();
//                }
            }
        };

        $.ajax(config);
    },

    searchCpfCnpj: function(element, type){
        element.blur(function(){
            var config = {
                url: '/auxiliar/pessoa/search-cpf-cnpj',
                type: 'post',
                data: type ? {
                    'nuCpf': $(this).val()
                }: {
                    'nuCpf': $(this).val()
                },
                dataType: 'json',
                success: function(result){
                    if(result.total){
                        PessoaForm.redirectForEdit(element, result.type, result.sqPessoa);
                    }
                }
            };

            $.ajax(config);
        });
    },

    initEndereco: function(){
        $('#form-pessoa #sqEstado').change(function(){
            Address.config.municipio = $('#form-pessoa #sqMunicipio');
            Address.populateMunicipioFromEstado($(this).val());
        });
    },

    initDatePicker: function(){
        loadJs('/js/library/bootstrap-datepicker.js', function(){
            var options = {
                format: 'dd/mm/yyyy',
                language: 'br'
            };

            $('.datepicker').datepicker(options);

            $('.datepicker-icon').click(function(){
                $('#dtNascimento').focus();
                return false;
            });
        });
    },

    initAutoComplete: function(){
        loadJs('/js/library/jquery.simpleautocomplete.js', function(){
            $('#noPessoa').simpleAutoComplete("/auxiliar/pessoa/search-pessoa", {
                attrCallBack: 'id',
                autoCompleteClassName: 'autocomplete',
                extraParamFromInput: '#sqTipoPessoa',
                clearInput: true

            }, function(object){
                PessoaForm.redirectForEdit($('#noPessoa'), 'Nome', object[1]);
            });
        });
    },

    redirectForEdit: function(element, type, codigo){
        var yesCallback = function(){
            element.val('');
            window.location = '/auxiliar/pessoa-fisica/edit/id/' + codigo;
        }
        var noCallback = function(){
            element.val('');
        }

        Message.showConfirmation({
            'body': 'O ' + type +' informado já existe. Deseja alterar as informações?',
            'yesCallback': yesCallback,
            'noCallback'    : noCallback
        });
    },

    clearForm: function(form){
        form.each(function(){
            this.reset();
        });
    },

    init: function(){
//        PessoaForm.initDatePicker();
        PessoaForm.initEndereco();
//        PessoaForm.initAutoComplete();
    }
}

$(document).ready(function(){
    PessoaForm.init();
});