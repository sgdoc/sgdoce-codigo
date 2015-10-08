IndicacaoPrazo = {
    deletar: function(codigo){
        var callBack = function(){
            window.location = '/auxiliar/vincularprazo/delete/id/' + codigo;
        }

        Message.showConfirmation({
            'body': 'Tem certeza que deseja realizar a exclusão?',
            'yesCallback': callBack
        });
    },

    alterar: function(codigo){
        window.location = '/auxiliar/vincularprazo/edit/id/' + codigo;
    }
}

$(function(){
    Grid.load($('#form-pesq-vincular-prazo'), $('#table-grid-vincular-prazo'));
});