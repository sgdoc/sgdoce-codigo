var alert = {
    
    showMessage: function (message, type, id)
    {
        if (!type) {
            type = 'success';
        }

        if (!id) {
            id = 'container-alert';
        }

        var alert = $('<div>').addClass('alert alert-' + type).html(message).append(
            $('<button>').addClass('close')
                         .attr('type', 'button')
                         .attr('data-dismiss', 'alert')
                         .html('&times;')
        );

        $('#'+id).html(alert);
        
        $(document).scrollTop(0);
    },
            
    confirm: function (message, callbackConfirm, callbackCancel)
    {
        $('<div>').attr('id', 'modal-confirm').addClass('modal hide fade').append(
            $('<div>').addClass('modal-header').append(
                $('<button>').attr('type', 'button')
                             .attr('data-dismiss', 'modal')
                             .attr('aria-hidden', 'true')
                             .addClass('close')
                             .html('&times;')
            ).append(
                $('<h3>').html('Confirmação')
            )
                
        ).append(
            $('<div>').addClass('modal-body').append(
                $('<p>').html(message)
            )
                
        ).append(
            $('<div>').addClass('modal-footer').append(
                $('<a>').attr('href', 'javascript:;')
                        .attr('id', 'modal-confirm-button-cancel')
                        .addClass('btn')
                        .html('Cancelar')
            ).append(
                $('<a>').attr('href', 'javascript:;')
                        .attr('id', 'modal-confirm-button-confirm')
                        .addClass('btn btn-primary')
                        .html('Confirmar')
            )
                
        ).appendTo('body');
            
        $('#modal-confirm-button-cancel, #modal-confirm-button-confirm').unbind().click(function () {
            $('#modal-confirm').modal('hide');
        });
        
        if (callbackConfirm) {
            $('#modal-confirm-button-confirm').click(callbackConfirm);
        }
        
        if (callbackCancel) {
            $('#modal-confirm-button-cancel').click(callbackCancel);
        }
        
        $('#modal-confirm').modal('show');
        
        $('#modal-confirm').on('hidden', function () {
            $('#modal-confirm').remove();
        });
    }
};