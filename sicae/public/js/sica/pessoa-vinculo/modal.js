PessoaVinculoModal = {
    initDatePicker: function() {
        loadJs('/assets/js/library/bootstrap-datepicker.js', function() {
            loadJs('/assets/js/components/datepicker.js', function() {
                $('.datepicker-icon').click(function() {
                    $(this).parents('.input-append').find('input').focus();
                    return false;
                });
            });
        });
    },
    initMask: function() {
        $('#form-pessoa-vinculo-modal #nuCpf').setMask('cpf');
        $('.dateBR').setMask('date');
    },
    searchNomeCpf: function() {
        $('#form-pessoa-vinculo-modal #nuCpf').simpleAutoComplete("/principal/pessoa/search-cpf", {
            attrCallBack: 'id',
            autoCompleteClassName: 'autocomplete',
            extraParamFromInput: '<input value="1" />',
            clearInput: true

        }, function(data, li, object) {
            var nuCpf = "";
            var noPessoa = $(li).text();

            if ($(li).text().length > 14) {
                nuCpf = $(li).text().substring(0, 14);
                noPessoa = $(li).text().substring(17);
            }

            $('#sqPessoaRelacionamento').val($(li).attr('id').substring(13));
            $('#form-pessoa-vinculo-modal #noPessoaVinculo').val(noPessoa);
            $('#form-pessoa-vinculo-modal #noPessoaVinculo_hidden').val($(li).attr('id').substring(13));
            $('#form-pessoa-vinculo-modal #nuCpf').val(nuCpf);

            $('#nuCpf').blur();
        });

        $('#form-pessoa-vinculo-modal #noPessoaVinculo').simpleAutoComplete("/principal/pessoa/search-pessoa", {
            attrCallBack: 'id',
            autoCompleteClassName: 'autocomplete',
            extraParamFromInput: '<input value="1" />'

        }, function(data, li) {
            var nuCpf = "";
            var noPessoa = $(li).text();

            if ($(li).text().length > 14) {
                nuCpf = $(li).text().substring(0, 14);
                noPessoa = $(li).text().substring(17);
            }

            $('#sqPessoaRelacionamento').val($(li).attr('id').substring(13));
            $('#form-pessoa-vinculo-modal #noPessoaVinculo').val(noPessoa);
            $('#form-pessoa-vinculo-modal #nuCpf').val(nuCpf);
        });
    },
    initValidateData: function() {
        Validation.init();
        jQuery.validator.addMethod("dateEarlier", function(value, element) {
            var dataInicio = $('#dtInicioVinculo').val().split('/');
            var dataFim    = value.split('/'); 

            dataInicio = new Date(dataInicio[2], dataInicio[1] - 1, dataInicio[0]);
            dataFim = new Date(dataFim[2], dataFim[1] - 1, dataFim[0]);

            if (dataInicio.valueOf() > dataFim.valueOf()) {
                return false;
            } else {
                return true;
            }

        }, MessageUI.get('MN092'));
    },
    concluir: function() {
        $('.btnAdicionarPessoaVinculo').click(function() {
            if ($('#form-pessoa-vinculo-modal').valid()) {

                if (!$('#dtFimVinculo').val()) {
                    $('#dtFimVinculo').val('NULL');
                }

                if ($('#form-pessoa-vinculo-modal #sqPessoaVinculo').val()) {
                    PessoaForm.saveFormWebService(
                            'app:PessoaVinculo',
                            'libCorpUpdatePessoaVinculo',
                            $('#form-pessoa-vinculo-modal'),
                            $('#form-pessoa-vinculo')
                            );
                } else {
                    PessoaForm.saveFormWebService(
                            'app:PessoaVinculo',
                            'libCorpSavePessoaVinculo',
                            $('#form-pessoa-vinculo-modal'),
                            $('#form-pessoa-vinculo')
                            );
                }
            } else {
                return false;
            }
        });

        $('#form-pessoa-vinculo-modal').validate();
    },
    init: function() {
        PessoaVinculoModal.initDatePicker();
        PessoaVinculoModal.initValidateData();
        PessoaVinculoModal.initMask();
        PessoaVinculoModal.searchNomeCpf();
        PessoaVinculoModal.concluir();
    }

}

$(document).ready(function() {
    PessoaVinculoModal.init();
});