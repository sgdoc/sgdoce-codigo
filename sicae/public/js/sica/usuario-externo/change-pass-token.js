var UsuarioExterno = {

    changePassToken : function(){
        Validation.init();

        $('.aviso').css('color', '#595959');

        $("#txSenhaNovaConfirm").rules("add", {
            equalTo: "#txSenhaNova",
            messages: {
                equalTo: "A confirmação da nova senha não confere."
            }
        });
    }

};

$(document).ready(function(){
    UsuarioExterno.changePassToken();
});