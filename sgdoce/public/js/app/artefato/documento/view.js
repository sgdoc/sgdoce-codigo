ViewImagem = {   		
	initImagem: function(){
		// ATIVA E DESATIVA O BOTÃO CONCLUIR NA TELA DE VALIDAR IMAGENS
			$('#validaImagens').on('click', function(){				
				var $this = $('.btn-concluir2');				
				$this.attr('disabled') ? $this.removeAttr('disabled').addClass('btn-primary') 
						               : $this.attr('disabled', true).removeClass('btn-primary');
			});
    },
	initButton: function(){
	    $('#btConcluir2').click(function(){
	      	window.location = '/artefato/documento/edit/id/'+ $('#sqArtefato').val() +'/nuDigital/'+ $('#nuDigital').val();
	    });  
    },
    init: function(){
    	ViewImagem.initImagem();
    	ViewImagem.initButton();
    }
}

$(document).ready(function(){
	ViewImagem.init();
});