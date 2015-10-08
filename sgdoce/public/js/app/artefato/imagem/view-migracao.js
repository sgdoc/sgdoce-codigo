var ImageViewMigracao = {
    urlConfirmar : "/migracao/vinculo/confirmar-imagem",
    confirmar : function( sqArtefato ){                
        try {                       
            Message.showConfirmation({
                'body': "Tem certeza que deseja confirmar a imagem?",
                'yesCallback': function(){ 
                        $.ajax({
                            type: "POST",
                            url: ImageViewMigracao.urlConfirmar,
                            data: {id: sqArtefato}
                        }).success(function(result) {
                            if (result.status) {                    
                                var message = "Imagem confirmada com sucesso!";
                                window.opener.Validation.addMessage(message, 'success');
                                window.opener.location.reload();
                                window.close();
                            }else{
                                /* o.0 nesse ponto, rolou um erro 0.o */
                                Message.showError(result.message);
                            }
                        }).error(function(err) {
                            Message.showError("Ocorreu um erro inesperado na execução");                
                        });
                }
            });
        } catch (e) {
            Message.showError(e.message);
            $('div.modal-footer:visible').find('a.btn-primary:visible').focus();
        }
        return;        
    },
    alterar : function( url, id ){
        Message.showConfirmation({
            'body': "Tem certeza que deseja alterar a imagem da digital \"" + $("a.treeviewItem-" + id).html() + "\"?",
            'yesCallback': function () {
                window.opener.location.href = url;
                window.close();       
        }});
        return;        
    }
};