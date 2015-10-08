Email = {
    initGrid: function(){
    	if($('#sqPessoa', $('#form-email')).val()) {
    		Grid.loadNoPagination($('#form-email'), $('#table-email'));
    	}
    },

    adicionar: function(){
        $('#btn-add-email').off('click').on('click', function() {
            $.get('/auxiliar/email/create', {
                sqPessoa: $('#sqPessoa').val()
            }, function(data){
                $('#modal-email').html(data).modal();
            });
        });
    },
    
    alterar: function(id, sqPessoa){
        $.get('/auxiliar/email/edit', {
            id: id, 
            sqPessoa: sqPessoa
        }, function(data){
            $('#modal-email').html(data).modal();
        });
    },

    deletar: function(codigo){
        var callBack = function(){
            PessoaForm.saveFormWebService('app:VwEmail', 'libCorpDeleteEmail',
                [{
                    name: 'sqEmail',
                    value: codigo
                }],
                $('#form-email'));
        }
                
        Message.showConfirmation({
            'body': 'Tem certeza que deseja realizar a exclusão?',
            'yesCallback': callBack
        });
    },

    init: function(){
        Email.initGrid();
        Email.adicionar();
    }

}

$(document).on('ready', function(){
    Email.init();
});