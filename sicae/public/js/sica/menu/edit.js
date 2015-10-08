$(document).ready(function(){
    Menu.changeRaiz();
    Menu.changeMenuDependencia();
    Menu.changeSystem();
    Menu.showGrid(true);
    
    if($('[name=grupoRaiz]').is(':checked')){
        $('[name=grupoRaiz]').removeAttr('disabled');
        if ($('[name=grupoRaiz]:checked').val() == 's') {
            $('#selectAbaixoDe').removeClass('hide');
            $("#sqMenuPai option:first").attr('selected','selected');
        } else {
            $('#selectAbaixoDe').removeClass('hide');
            $('#selectMenuDependencia').removeClass('hide');
        }
    }
});