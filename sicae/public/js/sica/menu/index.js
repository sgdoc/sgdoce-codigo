$(document).ready(function(){
    Menu.gridIndex(false);
    Menu.registerEvents();

    $("#gerar-pdf").click(function(){
        $(this).attr('href','/menu/gerar-pdf/sqSistema/' + $('#sistema-sel').val()) ;
    });

    if ($('#sqSistema').val()) {
    	$('#form-busca-menu').trigger('submit');
    }
});