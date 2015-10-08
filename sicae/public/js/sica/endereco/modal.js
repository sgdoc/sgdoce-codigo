EnderecoModal = {
    initCep: function() {
        $('#form-endereco-modal #sqCep').setMask('cep');
        $('#form-endereco-modal #nuEndereco').setMask({
            mask: '9',
            type: 'repeat'
        });

        $('#form-endereco-modal #sqEstadoEndereco').change(function() {
            Address.config.municipio = $('#form-endereco-modal #sqMunicipioEndereco');
            Address.populateMunicipioFromEstado($(this).val());
        });

        $('#btnCep').click(function() {
            if ($('#sqCep').valid()) {
                Address.config.cep = $('#form-endereco-modal #sqCep');
                Address.config.pais = $('#form-endereco-modal #sqPai');
                Address.config.estado = $('#form-endereco-modal #sqEstadoEndereco');
                Address.config.municipio = $('#form-endereco-modal #sqMunicipioEndereco');
                Address.config.bairro = $('#form-endereco-modal #noBairro');
                Address.config.endereco = $('#form-endereco-modal #txEndereco');
                Address.config.numero = $('#form-endereco-modal #nuEndereco');
                Address.config.complemento = $('#form-endereco-modal #txComplemento');

                Address.populateFromCep($('#form-endereco-modal #sqCep').val());
            }
        });
    },
    concluir: function() {
        $('.btnAdicionarEndereco').click(function() {
            if ($('#form-endereco-modal').valid()) {

                if (!$('#txComplemento').val()) {
                    $('#txComplemento').val('NULL');
                }

                if (!$('#nuEndereco').val()) {
                    $('#nuEndereco').unsetMask().val('NULL');
                }

                if ($('#form-endereco-modal #sqEndereco').val()) {
                    PessoaForm.saveFormWebService(
                            'app:Endereco',
                            'libCorpUpdateEndereco',
                            $('#form-endereco-modal'),
                            $('#form-endereco'),
                            {
                                sqCep: 'Digits'
                            }
                    );
                } else {
                    PessoaForm.saveFormWebService(
                            'app:Endereco',
                            'libCorpSaveEndereco',
                            $('#form-endereco-modal'),
                            $('#form-endereco'),
                            {
                                sqCep: 'Digits'
                            }
                    );
                }
            } else {
                return false;
            }
        });

        PessoaForm.validateType('#modal-endereco', '#sqTipoEndereco', 'endereço');
    },
    init: function() {
        EnderecoModal.initCep();
        EnderecoModal.concluir();
    }
}

$(document).ready(function() {
    EnderecoModal.init();
});