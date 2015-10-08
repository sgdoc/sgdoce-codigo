$(document).ready(function(){
    Perfil.grid();
    Perfil.gerarPdf();
    Perfil.index();

    if ($('#sqSistema').val()){
		$('.pesquisa-perfil').trigger('click');
    }
});