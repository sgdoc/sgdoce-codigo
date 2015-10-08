OrigemDestino = {
    initAjustaCpfCnpj: function() {
        if ($('#procedenciaOrigem').val() == 1) {
            $('#origemCpfCnpj').parent().hide();
        } else {
            if ($('#sqTipoPessoaOrigem').val() == 1) {
                if ($('#nacionalidadeOrigem').val() == 1) {
                    $('#origemCpfCnpj').parent().show();
                    $('#origemCpfCnpj').text('CPF');
                } else {
                    $('#origemCpfCnpj').parent().show();
                    $('#origemCpfCnpj').text('Passaporte');
                }
            }
            else if ($('#sqTipoPessoaOrigem').val() == 2) {
                $('#origemCpfCnpj').parent().show();
                $('#origemCpfCnpj').text('CNPJ');
            }
            else
                $('#origemCpfCnpj').parent().hide();
        }

        if ($('#procedenciaDestino').val() == 1) {
            $('#destinoCpfCnpj').parent().hide();
        } else {
            if ($('#sqTipoPessoaDestino').val() == 1) {
                if ($('#nacionalidadeDestino').val() == 1) {
                    $('#destinoCpfCnpj').parent().show();
                    $('#destinoCpfCnpj').text('CPF');
                } else {
                    $('#destinoCpfCnpj').parent().show();
                    $('#destinoCpfCnpj').text('Passaporte');
                }
            }
            else if ($('#sqTipoPessoaDestino').val() == 2) {
                $('#destinoCpfCnpj').parent().show();
                $('#destinoCpfCnpj').text('CNPJ');
            }
            else
                $('#destinoCpfCnpj').parent().hide();
        }
    }
}
$(document).ready(function() {
    OrigemDestino.initAjustaCpfCnpj();
});