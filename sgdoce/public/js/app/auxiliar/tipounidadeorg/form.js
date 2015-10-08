$(document).ready(function(){
    $('#sqTipoUnidadeOrg').simpleAutoComplete("auxiliar/tipo-unidade-org/search-tipo-unidade-org/", {
        extraParamFromInput: '#extra',
        attrCallBack: 'rel',
        autoCompleteClassName: 'autocomplete',
        selectedClassName: 'sel'
    });

    $('#sqUnidadeOrg').simpleAutoComplete("auxiliar/tipo-unidade-org/search-unidade-org/", {
        extraParamFromInput: '#sqTipoUnidadeOrg_hidden',
        attrCallBack: 'rel',
        autoCompleteClassName: 'autocomplete',
        selectedClassName: 'sel'
    });
    
    $('#sqPessoaDestInterno').simpleAutoComplete("auxiliar/tipo-unidade-org/search-pessoa/", {
        extraParamFromInput: '#sqUnidadeOrg_hidden',
        attrCallBack: 'rel',
        autoCompleteClassName: 'autocomplete',
        selectedClassName: 'sel'
    });
    
    $('#sqPessoaAssinatura').simpleAutoComplete("auxiliar/tipo-unidade-org/search-pessoa/", {
        extraParamFromInput: '#sqUnidadeOrg_hidden',
        attrCallBack: 'rel',
        autoCompleteClassName: 'autocomplete',
        selectedClassName: 'sel'
    });
    
    
    
});
