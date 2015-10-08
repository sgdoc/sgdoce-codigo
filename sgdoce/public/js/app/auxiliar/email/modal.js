EmailModal = {

    validationEmail: function(){
        $('#sqTipoEmail').change(function(){
            if($('#sqTipoEmail').val() == 4){
                $('#txEmail').removeClass('email');
            }else{
                $('#txEmail').addClass('email');
            }
        });
        
        if($('#sqTipoEmail').val() == 4){
            $('#txEmail').removeClass('email');
        }else{
            $('#txEmail').addClass('email');
        }
    },
    
    concluir: function(){
    	$('.btnAdicionarEmail').off('click');
        $('.btnAdicionarEmail').on('click', function(){
            if($('#form-email-modal').valid()){
                if($('#form-email-modal #sqEmail').val()){
                    PessoaForm.saveFormWebService(
                        'app:VwEmail',
                        'libCorpUpdateEmail',
                        $('#form-email-modal'),
                        $('#form-email')
                        );
                }else{
                    PessoaForm.saveFormWebService(
                        'app:VwEmail',
                        'libCorpSaveEmail',
                        $('#form-email-modal'),
                        $('#form-email')
                        );
                }
            } else {
                $('.alert-error').addClass('hide');
                $('.alert-error', '#modal-email').removeClass('hide');
                
                return false;
            }
        });
    },
    
    init: function(){
        EmailModal.validationEmail();
        EmailModal.concluir();
    }

}

$(document).ready(function(){
    EmailModal.init();
});