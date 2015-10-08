PessoaForm = {
    urlPJ: false,
    withoutValue: '---',
    elements: new Object(),
    saveFormWebService: function(repository, method, form, formGrid, filters) {

        var params;

        if ($(form).serializeArray().length) {
            params = form.serializeArray();
        } else {
            params = form;
        }

        var config = {
            url: '/principal/pessoa/save-form-web-service',
            type: 'post',
            data: {
                params: params,
                repository: repository,
                method: method,
                filters: filters
            },
            dataType: 'json',
            success: function(data) {
                if (!data.success && data.hasOwnProperty('code')) {
                    Message.showError(data.message);
                }else{
                    Message.showMessage(data);

                    if (Message.isSuccess(data)) {
                        formGrid.submit();
                    }
                }
            }
        };

        $.ajax(config);
    },
    searchCpfCnpj: function(element, type) {
//        element.keyup(function(event) {
        element.focusout(function(event) {
            if (event.ctrlKey) {
                element.val('');
            } else {

                if ( ($('#nuCpf').val() != undefined && $(this).val().length == 14 && $('#nuCpf').valid() )
                        || ( $('#nuCnpj').val() != undefined && $(this).val().length == 18 && $('#nuCnpj').valid()) ) {

                    var elementValue = $(this).val();
                    var elementType = type;

                    var config = {
                        url: '/principal/pessoa/search-cpf-cnpj',
                        type: 'post',
                        data: type ? {
                            'nuCnpj': elementValue
                        } : {
                            'nuCpf': elementValue
                        },
                        dataType: 'json',
                        success: function(result) {
                            if (result.total) {
                                PessoaForm.redirectForEdit(element, result.type, result.sqPessoa);
                            } else {
                                //
                                if( elementType != undefined && elementType == true /*is nuCnpj*/ ) {
                                    PessoaJuridica.integrationInfoconv();
                                } else {
                                    PessoaFisica.integrationInfoconv();
                                }
                            }
                        }
                    };

                    $.ajax(config);
                }
            }
        });

        $(document).bind('contextmenu', function() {
            return false;
        });
    },
    getInformationInfoconv: function( elementValue, elementType )
    {
        var objElementType = {'nuCnpj' : elementValue};
        if( elementType == 'nuCpf' ) {
            objElementType = {'nuCpf' : elementValue};
        };
        var config = {
            url:'/principal/infoconv/service-infoconv',
            type:'post',
            data:objElementType,
            dataType:'json',
            async:false,
            success: function( result ) {
                if(elementType == 'nuCnpj') {
                    PessoaForm.elements.nuCnpj = elementValue;
                    PessoaJuridica.setElementIntegrationInfoconv( result );
                } else {
                    PessoaForm.elements.nuCpf = elementValue;
                    PessoaFisica.setElementIntegrationInfoconv( result );
                }

                PessoaForm.elements.response = result.response; //mensagem em caso de exception
                PessoaForm.elements.success  = result.success;
                PessoaForm.elements.code     = result.code;
                PessoaForm.elements.personId = result.personId;
            },
            error: function() {
                PessoaForm.elements.success  = false;
                PessoaForm.elements.personId = null;
            }
        };
        $.ajax(config);
    },
    errorInfoconv: function( msg ) {
        var yesCallback = function() {
            $('#groupTxJustificativaInfoconv').removeClass('hide');
            $('#noPessoa').attr('readonly', false);
            //
            $('#sqPessoaAutoraInfoconv').attr('value', PessoaForm.elements.personId);
            $('#dtIntegracaoInfoconv').attr('value', '').attr('readonly', false);
            $('#txJustificativaInfoconv').attr('readonly', false);
        };
        var noCallback = function() {
            $('#groupTxJustificativaInfoconv').addClass('hide');
            $('#txJustificativaInfoconv').attr('readonly', true);
            $('#dtIntegracaoInfoconv').attr('readonly', true);
            //
            if( $('#sqPessoa').val() == undefined || !$('#sqPessoa').val()) {
                $( '#' + PessoaForm.elements.type ).val('');
            }
        };
        Message.showConfirmation({
            'body': msg,
            'yesCallback': yesCallback,
            'noCallback': noCallback
        });
    },
    initEndereco: function() {
        $('#form-pessoa #sqEstado').change(function() {
            Address.config.municipio = $('#form-pessoa #sqMunicipio');
            Address.populateMunicipioFromEstado($(this).val());
        });
    },
    initDatePicker: function() {
        loadJs('/assets/js/library/bootstrap-datepicker.js', function() {
            loadJs('/assets/js/components/datepicker.js', function() {
                $('.datepicker-icon').click(function() {
                    $('#dtNascimento').focus();
                    return false;
                });
            });
        });
    },
    initAutoComplete: function() {
        loadJs('/js/library/jquery.simpleautocomplete.js', function() {
            if ($('#noPessoa').val() == '') {
                $('#noPessoa').simpleAutoComplete("/principal/pessoa/search-pessoa", {
                    attrCallBack: 'id',
                    autoCompleteClassName: 'autocomplete',
                    extraParamFromInput: '#sqTipoPessoa',
                    clearInput: true

                }, function(object) {
                    PessoaForm.redirectForEdit($('#noPessoa'), 'Nome', object[1]);
                });
            }
        });

        $('a.close[href=#], .modal-header > a.close[href=#]').live('click', function() {
            $('#nuCpf, #noPessoa, #noPessoa_hidden').val('');
        });
    },
    redirectForEdit: function(element, type, codigo) {
        var yesCallback = function() {
            element.val('');
            var url = '/principal/pessoa-fisica/edit/id/';

            if (PessoaForm.urlPJ) {
                url = '/principal/pessoa-juridica/edit/id/';
            }

            window.location = url + codigo;
        }
        var noCallback = function() {
            element.val('');
        }

        Message.showConfirmation({
            'body': 'O ' + type + ' informado já existe. Deseja alterar as informações?',
            'yesCallback': yesCallback,
            'noCallback': noCallback
        });
    },
    clearForm: function(form) {
        form.each(function() {
            this.reset();
        });
    },
    validateType: function(idModal, idElement, tipo) {
        if ($(idModal + ' ' + idElement + ' option').size() === 1) {
            $(idModal + ' .modal-body').html('Todos os tipos de ' + tipo + ' já foram adicionados. É permitido somente alterar ou excluir os registros inseridos.');
            $(idModal + ' .modal-footer a:first').html('Fechar').unbind('click').addClass('btn-primary');
            $(idModal + ' .modal-footer a:last').remove();
        }
    },

    createItemDeParaInfoconv:function(title, oldValue, newValue){
        var xhtml = '<fieldset>';
            xhtml += '   <legend>' + title + '</legend>';
            xhtml += '   <div>';
            xhtml += '       <span class="span1">&rsaquo; Anterior:</span>';
            xhtml += '       <span class="span4">' + oldValue + '</span>';
            xhtml += '   </div>';
            xhtml += '   <div>';
            xhtml += '       <span class="span1">&rsaquo; Atual:</span>';
            xhtml += '       <span class="infoconvNewValue span4">' + newValue + '</span>';
            xhtml += '   </div>';
            xhtml += '</fieldset>';
            return xhtml;
    },

    init: function() {
        PessoaForm.initDatePicker();
        PessoaForm.initEndereco();
        PessoaForm.initAutoComplete();
    }
};

$(document).ready(function() {
    PessoaForm.init();
});