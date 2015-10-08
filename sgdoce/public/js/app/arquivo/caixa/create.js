CaixaCreate = {
    formId: 'form_caixa_arquivo',

    init: function(){
        CaixaCreate.events();
    },

    events:function(){
        $('#btn_gerar').on('click', CaixaCreate.handleFormSubmit);

        $('#sqUnidadeOrg').simpleAutoComplete("arquivo/caixa/search-unidade-org/", {
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel'
        });

        $('#sqClassificacao').simpleAutoComplete("arquivo/caixa/search-classificacao-caixa/", {
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel'
        });

        return CaixaCreate;
    },

    handleFormSubmit: function(){
        if ($('form').valid()) {
            $('.campos-obrigatorios').addClass('hidden');

            var currentDate = new Date();

            if (currentDate.getFullYear() < parseInt($('#nuAno').val())) {
                Message.show('Erro','Ano da caixa não pode ser maior que o ano atual');
                return false;
            }

            $('#' + CaixaCreate.formId).submit();
        }
        return false;
    },
};

$(CaixaCreate.init);

