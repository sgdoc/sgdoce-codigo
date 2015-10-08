var DadoBancarioModal = {
    initMask: function() {
        $('#coDigitoAgencia, #nuConta, #nuContaDv, #coOperacao').setMask({
            mask: '9',
            type: 'repeat'
        });

        $('#sqBanco').change(function() {
            var nomeBanco = $.trim($('#sqBanco option:selected').text());

            if ($(this).val()) {
                $('#coAgencia').removeAttr('readonly');
                $('#sqAgencia, #sqAgencia_hidden').val('');
            } else {
                $('#coAgencia').attr('readonly', true);
            }

            if (nomeBanco.toUpperCase().match('CAIXA ECO')) {
                $("#coOperacao").addClass("required");
                $("#operacao-required").show();
            } else {
                $("#coOperacao").removeClass("required");
                $("#operacao-required").hide();
                $("#coOperacao").removeClass("required");
                $("#coOperacao").parent().parent().removeClass("error");
                $("#coOperacao").parent().find("p").remove();
            }
        });

        if ($('#sqBanco').val()) {
            $('#coAgencia').removeAttr('readonly');
        } else {
            $('#coAgencia').attr('readonly', true);
        }
    },
    initAutoComplete: function() {
        $('#sqAgencia').simpleAutoComplete("/principal/dado-bancario/search-agencia", {
            attrCallBack: 'id',
            autoCompleteClassName: 'autocomplete',
            extraParamFromInput: '#sqBanco',
            minLength: 1

        }, function(element) {
            $.post("/principal/dado-bancario/search-digito-agencia",
                    {
                        sqBanco: $('#sqBanco').val(),
                        sqAgencia: element[1]

                    }, function(data) {
                $('#coDigitoAgencia').val(data.coDigitoAgencia);
            });
        });
    },
    initBanco: function() {
        $('#coBanco').keyup(function() {
            var coBanco = $(this).val();

            $('#sqBanco option').each(function() {
                var texto = $(this).html();
                var arTex = texto.split('-');
                var codigo = $.trim(arTex[0]);

                if (coBanco == codigo) {
                    $(this).attr('selected', true);
                }
            });

            var texto = $('#sqBanco option:selected').html();
            var arTex = texto.split('-');
            var codigo = $.trim(arTex[0]);

            if (codigo != coBanco) {
                $('#sqBanco option').eq(0).attr('selected', true);
            }
        });

        $('#sqBanco').change(function() {
            if ($(this).val() == '') {
                $('#coBanco').val('');
            } else {
                var texto = $(this).find('option:selected').html();
                var arTex = texto.split('-');

                $('#coBanco').val($.trim(arTex[0]));
            }
        });
    },
    concluir: function() {
        $('.btnAdicionarDadoBancario').click(function() {
            if ($('#form-dado-bancario-modal').valid()) {

                if (!$('#coOperacao').val()) {
                    $('#coOperacao').val('NULL');
                }
                
                if (!$('#nuContaDv').val()) {
                    $('#nuContaDv').val('NULL');
                }

                if ($('#form-dado-bancario-modal #sqDadoBancario').val()) {
                    PessoaForm.saveFormWebService(
                            'app:DadoBancario',
                            'libCorpUpdateDadoBancario',
                            $('#form-dado-bancario-modal'),
                            $('#form-dado-bancario')
                            );
                } else {
                    PessoaForm.saveFormWebService(
                            'app:DadoBancario',
                            'libCorpSaveDadoBancario',
                            $('#form-dado-bancario-modal'),
                            $('#form-dado-bancario')
                            );
                }
            } else {
                return false;
            }
        });

        PessoaForm.validateType('#modal-dado-bancario', '#sqTipoDadoBancario', 'conta');
    },
    init: function() {
        DadoBancarioModal.initBanco();
        DadoBancarioModal.initMask();
        DadoBancarioModal.initAutoComplete();
        DadoBancarioModal.concluir();
    }
}

$(document).ready(function() {
    $.validator.defaults.groups = agrupaCampos();
    DadoBancarioModal.init();
});