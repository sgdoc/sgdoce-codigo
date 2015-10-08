var DocumentoMigracao = {
    init : function(){
        $("#chekProcedenciaInterno,#chekDestinoInterno").click(function(){
            var isChecked = $(this).is(':checked');
            if( isChecked ) {
                $(this).parents('.control-group').removeClass('error');
            }
        });
    }
};

$(document).ready(function(){
    DocumentoMigracao.init();
});