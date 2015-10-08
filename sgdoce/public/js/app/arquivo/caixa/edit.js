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

        $('#sqClassificacao').prop('disabled','disabled');

        return CaixaCreate;
    },

    handleFormSubmit: function(){
        if ($('form').valid()) {
            $('.campos-obrigatorios').addClass('hidden');
            $('#' + CaixaCreate.formId).submit();
        }
        return false;
    },
};

$(CaixaCreate.init);

