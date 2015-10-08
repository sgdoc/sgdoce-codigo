$(document).ready(function(){
    Perfil.controlProfile();
    Perfil.init();
    
    if ($('#sqMenu').val() != '') {
        $('#sqMenu').trigger('change');
    }
    
});