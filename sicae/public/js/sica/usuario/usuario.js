var ManagerUser = {

    changePass : function(urlExterno){
        $("#txSenha").val('');

        $("#txSenhaNovaConfirm").rules("add", {
            equalTo: "#txSenhaNova",
            messages: {
                equalTo: "A confirmação da nova senha não confere."
            }
        });

        var url = Sistemas.base + '/usuario';

        if(urlExterno){
            url = Sistemas.base + '/usuario-externo';
        }

        $('#btnAlteraSenha').click(function(){
            $('#error-pass').html('');
            if($('#form-altera-senha').valid()){
                $.post(url + '/change-pass',
                {
                    txSenha             : $('#txSenha').val(),
                    txSenhaNova         : $('#txSenhaNova').val(),
                    txSenhaNovaConfirm  : $('#txSenhaNovaConfirm').val()
                },
                function(response){
                    if(response.error){
                        $('.error-pass').html('<div class="alert alert-error campos-obrigatorios">'+
                            '<button class="close" data-dismiss="alert">×</button>'+
                            response.error+'</div>');
                    }else{
                        window.top.location = url + '/logout';
                    }

                }, 'json');
            }
            return false;
        });

        $('#btnCancelar, .modal-header > .close').click(function(){
            $(".campos-obrigatorios > .close").click();
            $("#form-altera-senha").validate().resetForm();
            $("#form-altera-senha").validate().elements().each(function(elment){
                $(this).parents('.error').removeClass('error');
                $(this).val('');
            });
        });
    },

    init: function(){
        if(typeof Validation == 'undefined' || typeof $.validator == 'undefined'){
            jQuery.getScript(Sistemas.base + '/assets/js/library/jquery.validate.js', function(){
                jQuery.getScript(Sistemas.base + '/assets/js/components/validation.js', function(){
                    ManagerUser.changePass();
                });
            });
        }else{
            ManagerUser.changePass();
        }
    }

};

$(document).ready(function(){
    ManagerUser.init();
});