TelefoneModal = {
    concluir: function() {
        $('#nuTelefone').setMask('foneBR');

        $('.btnAdicionarTelefone').click(function() {
            if ($('#form-telefone-modal').valid()) {
                var value = $('#form-telefone-modal #nuTelefone').val().split(' ');

                $('#form-telefone-modal #nuDdd').val(value[0]);
                $('#form-telefone-modal #nuTelefone').val(value[1]);

                if ($('#form-telefone-modal #sqTelefone').val()) {
                    PessoaForm.saveFormWebService(
                            'app:Telefone',
                            'libCorpUpdateTelefone',
                            $('#form-telefone-modal'),
                            $('#form-telefone'),
                            {
                                nuDdd: 'Digits',
                                nuTelefone: 'Digits'
                            });
                } else {
                    PessoaForm.saveFormWebService(
                            'app:Telefone',
                            'libCorpSaveTelefone',
                            $('#form-telefone-modal'),
                            $('#form-telefone'),
                            {
                                nuDdd: 'Digits',
                                nuTelefone: 'Digits'
                            });
                }
            } else {
                return false;
            }
        });

        PessoaForm.validateType('#modal-telefone', '#sqTipoTelefone', 'telefone');
    },
    init: function() {
        TelefoneModal.concluir();
    }

}

$(document).ready(function() {
    TelefoneModal.init();
});