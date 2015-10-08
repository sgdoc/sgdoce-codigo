$(document).ready(function(){
    $('#form-create-pessoa').submit(function(){
        if($('#form-create-pessoa').valid()){
            if($('#sqTipoPessoa').val() == 1){
                window.location = '/principal/pessoa-fisica/create';
            }else{
                window.location = '/principal/pessoa-juridica/create';
            }
        }

        return false;
    });
});

