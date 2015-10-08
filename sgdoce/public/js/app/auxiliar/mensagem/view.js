cMensagem = {
    deletar: function(codigo){
        var callBack = function(){
            window.location = 'auxiliar/mensagem/delete/id/' + codigo;
        }
                
        Message.showConfirmation({
            'body': 'Tem certeza que deseja realizar a exclusão?',
            'yesCallback': callBack
        });
    },
    
    desativar: function(codigo){
        var callBack = function(){
        window.location = 'auxiliar/mensagem/desativar/id/' + codigo;
        }
        Message.showConfirmation({
            'body': 'Tem certeza que deseja desativar a mensagem?',
            'yesCallback': callBack
        });
    },

    editar: function(codigo){
        window.location = 'auxiliar/mensagem/edit/id/' + codigo;
    },

    switchStatus: function(codigo, assunto, tipodoc, status){
        var callBack = function()
        {
            $.get('auxiliar/mensagem/switch-status/id/' + codigo + '/stMensagemAtiva/' + status,function(data){
                var msg = '';
                if (status == 1){
                    msg = UI_MSG['MN070'];
                }
                else {
                    msg = UI_MSG['MN069'];
                }
            $('#form-search-mensagem').submit();
            $('#grid-search').submit();

               Message.showSuccess(msg);

            });
        }

        if (status == 1) {
            params = $.param({id:codigo,assunto:assunto,tipodoc:tipodoc,status:status})
            $.post("/auxiliar/mensagem/find-mensagem-ativa", params , function(data) {
                if ($.isPlainObject(data[0][0]))
                {
                    Message.showConfirmation({
                        'body': StringUtils.parseTxt(UI_MSG['MN072'], {'<descrição da mensagem>': '"'+ data[0][0].txMensagem +'"'}),
                        'yesCallback': callBack
                    });
                }
                else
                {
                    callBack.call()
                }
            })
        }
        else if (status == 0)
        {
            Message.showConfirmation({
                'body': UI_MSG['MN068'],
                'yesCallback': callBack
            });
        }
    }

}