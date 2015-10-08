TipoPrioridade = {
    deletar: function(codigo){
        var callBack = function(){
            window.location = ('/auxiliar/prioridade/delete/id/' + codigo);
        }
                
        Message.showConfirmation({
            'body': 'Tem certeza que deseja excluir o tipo de prioridade?',
            'yesCallback': callBack
        });
    },
    
    alterar: function(codigo){
        window.location = ('/auxiliar/prioridade/edit/id/' + codigo);
    }    
}

$(function(){
    //$('#gridPesquisa').addClass('hide');

    $('#pesquisar').click(function(){
        $('#gridPesquisa').removeClass('hide');
    });
    Grid.load($('#form-tipo-prioridade'), $('#table-grid-prioridade'));
});