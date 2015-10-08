MenuProcesso = {
		initPassos: function(){

	        var valid = false;
	        $('.tab').click(function(){
	        	
	        	if($(this).attr('href') == $('.tab:first').attr('href')){
                    $('#btnAnterior').attr('disabled', true);
                }
            
                if($(this).attr('href') != $('.tab:first').attr('href') && $(this).attr('href') != $('.tab:last').attr('href')){
                    $('#btnAnterior, #btnProximo').removeAttr('disabled');
                    $('.btn-concluir').removeClass('btn-primary');
                    $('#btnSalvar').addClass('hidden');
                }
            
                $(this).tab('show');
                
                if($('ul.tabsForm li:last').hasClass('active')){
                    $('#btnAnterior').removeAttr('disabled');
                    $('.btn-concluir').addClass('btn-primary');
                    $('#btnProximo').removeClass('btn-primary');
                    $('#btnProximo i').removeClass('icon-white');
                    $('#btnProximo').attr('disabled', true);
                    $('#btnSalvar').removeClass('hidden');
                }else{
                    $('.btn-concluir').removeClass('btn-primary');
                    $('#btnProximo').removeAttr('disabled');
                    $('#btnProximo').addClass('btn-primary');
                    $('#btnProximo i').addClass('icon-white');
                }
                
	            return false;
	        });

	        $('#btnProximo').click(function(){	        	
	        	var isValid = MenuProcesso.next();
	        	if( isValid ) {
	        		$('li.active').next('li').children().click();
	        	}	            
	        });
	        
            $('#btnAnterior').attr('disabled', true);
	        
	        $('#btnAnterior').click(function(){
	            $('li.active').prev('li').children().click();
	            $('#btnProximo').addClass('btn-primary');
	            $('#btnProximo i').addClass('icon-white');
	        });
	    },
	    
	    initButton: function (){
	    	
	    },
	    
	    next: function(){
	    	return $('form').valid();
	    },

	    init: function(){
	    	MenuProcesso.initPassos();
	    	MenuProcesso.initButton();
	    }
}

$(document).ready(function(){
	MenuProcesso.init();
});