$(function(){
    $('#sqAssunto').simpleAutoComplete("/auxiliar/assunto/searchAssunto/", {
    extraParamFromInput: '#extra',
    attrCallBack: 'rel',
    autoCompleteClassName: 'autocomplete',
    selectedClassName: 'sel'
    });

    Grid.load($('#form-search-mensagem'), $('#table-grid-mensagem'));

});
