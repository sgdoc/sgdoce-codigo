var ManagerUserExternal = {

    changePass : function(){
        if(typeof Validation == 'undefined' || typeof $.validator == 'undefined'){
            jQuery.getScript(Sistemas.base + '/assets/js/library/jquery.validate.js', function(){
                jQuery.getScript(Sistemas.base + '/assets/js/components/validation.js', function(){
                    jQuery.getScript(Sistemas.base + '/js/sica/usuario/usuario.js',  function(){
                        ManagerUser.changePass(true);
                    });
                });
            });
        }else{
            jQuery.getScript(Sistemas.base + '/js/sica/usuario/usuario.js',  function(){
                ManagerUser.changePass(true);
            });
        }
    }

};

$(document).ready(function(){
    ManagerUserExternal.changePass();
});