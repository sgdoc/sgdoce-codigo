MenuProcesso = {
		validaTexto: function(){
    		if($('#txTextoArtefato').val()){
    			$('#txTextoArtefatoHidden').attr('checked',true);
    	    	$('#txTextoArtefatoHidden').removeClass('required');
    	    	$('.dv2-txTextoArtefato').removeClass('error');
    		}else{
    			$('#txTextoArtefatoHidden').attr('checked',false);
    	    	$('#txTextoArtefatoHidden').addClass('required');
    		}
		},

    initPassos: function(){

        var valid = false;
        $('.tab').click(function(event){
            var indexAtivo = $('li').index($('li.active'));
            var indexClick = $('li').index($(this).parent('li'));
            MenuProcesso.validaTexto();
            if($('ul.tabsForm li').length > 2){
                if(indexClick > indexAtivo) {
                    if($('form').valid()){

                        if($(this).attr('href') != $('.tab:first').attr('href') && $(this).attr('href') != $('.tab:last').attr('href')){
                            $('#btnProximo').addClass('btn-primary');
                            $('.btn-concluir').removeClass('btn-primary');
                            $('#btnSalvar').addClass('hidden');
                        }

                        $('.campos-obrigatorios').addClass('hidden');
                        $(this).tab('show');
                    }
                } else {
                    $(this).tab('show');
                }
            }

            return false;
        });

        $('#btnProximo').click(function(){
            MenuProcesso.validaTexto();
            $('li.active').next('li').children().click();
        });

        $('#btnAnterior').click(function(){
            MenuProcesso.validaTexto();
            $('li.active').prev('li').children().click();
        });

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            if($(this).attr('href') == $('.tab:last').attr('href')){
                $('#btnProximo').attr('disabled', true);
            }else{
                $('#btnProximo').attr('disabled', false);
            }
            if ($(this).attr('href') == $('.tab:first').attr('href')){
                $('#btnAnterior').attr('disabled', true);
            }else{
                $('#btnAnterior').attr('disabled', false);
            }

            if($('ul.tabsForm li:last').hasClass('active')){
                $('.btn-concluir').addClass('btn-primary');
                $('#btnProximo').removeClass('btn-primary');
                $('#btnSalvar').removeClass('hidden');
            }else{
                $('#btnProximo').addClass('btn-primary');
                $('.btn-concluir').removeClass('btn-primary');
            }
        });
    },
    init: function(){
    	MenuProcesso.initPassos();
    	
    	$('#controle-button-image').off('click').on('click', function() {
    		$('#deImagemRodape').click();
    		
    		return false;
    	});
    	
    	$('#deImagemRodape').off('change').on('change', function(e) {
    		$('#conteudo-nome-imagem').html($('#deImagemRodape').val());
    		
    		$('#controle-button-image').html('Alterar Imagem');
    	});
    }
}

$(document).ready(function(){
	MenuProcesso.init();
});