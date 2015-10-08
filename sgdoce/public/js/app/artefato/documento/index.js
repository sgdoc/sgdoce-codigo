DocumentValidation = {
    init: function(){
        DocumentValidation.events();
    },

    events:function(){
        $('input[name="tipoDigital"]').on('change', DocumentValidation.handleChangeTipoDocumento);
        $('#btValidar'               ).on('click' , DocumentValidation.handleClickValidar);
        $('#cancelar'                ).on('click' , DocumentValidation.handleClickCancelar);

        $('#nuDigitalValida').focus();

        return DocumentValidation;
    },

    handleChangeTipoDocumento:function(){
        var span = $('.digitalRequired')
            ,nuDigital = $('#nuDigitalValida');

        if ($(this).val() === '2') {
            Message.show('Informação','A digital será gerada ao final do cadastro');
            nuDigital.val('').prop('readonly','readonly')
                                 .removeClass('required');
            span.hide();
        }else{
            nuDigital.removeProp('readonly').addClass('required');
            span.show();
        }
    },

    handleClickCancelar: function(){
        Message.showConfirmation({
            body: UI_MSG.MN011, //'Tem certeza que deseja cancelar o cadastro?'
            yesCallback: function(){
                window.location = '/';
            }
        });
        return false;
    },

    handleClickValidar: function(){
        if( $('form').valid() ){
            $('.campos-obrigatorios').addClass('hidden');

            $.post('/artefato/documento/valida-digital',
                $('#dadosValidacao :input').serialize(),
                function(data){
                    console.log('data: ',data);
                    if (data.error) {
                        Message.showAlert(data.msg);
                        return false;
                    }else{
                        var go = function(){
                            var options = {
                                action: '/artefato/documento/create',
                                method: 'post'
                            };
                            var form = $('<form />', options);
                            $('<input />', {type: 'hidden', name: 'nuDigital', value: $('#nuDigitalValida').val()}).appendTo(form);
                            $('<input />', {type: 'hidden', name: 'tipoDigital', value: $('input[name="tipoDigital"]:checked').val()}).appendTo(form);

                            form.appendTo('body').submit();

                            return true;
                        };

                        if (data.new) {
                            if(data.msg){
                                Message.showConfirmation({body: data.msg, yesCallback: go});
                            }else{
                                go();
                            }
                        }
                        return true;
                    }
                }
            );
        }
    }
};

$(DocumentValidation.init);

