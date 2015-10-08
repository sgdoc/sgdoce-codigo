$(document).ready(function(){
    $('#form-create-usuario-externo').submit(function(){
        if($(this).valid()){
            if($('#sqTipoPessoa').val() == 'pessoa-fisica'){
                window.location = '/principal/usuario-externo-pessoa-fisica/create';
            }else{
                window.location = '/principal/usuario-externo-pessoa-juridica/create';
            }
        }

        return false;
    });
});

