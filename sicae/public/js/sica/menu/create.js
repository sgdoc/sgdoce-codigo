$(document).ready(function(){
    Menu.changeRaiz();
    Menu.changeMenuDependencia();
    Menu.changeSystem();

    if($('#sqSistema').val() !== ''){
        $('#sqSistema').trigger('change');
    }
});