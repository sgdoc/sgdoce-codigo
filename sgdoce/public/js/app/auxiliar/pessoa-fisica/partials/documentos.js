var Documentos = {
	serializeObject : function(form) {
		var elementsList = $('select:not(:hidden), input:not(:hidden), textarea:not(:hidden), input[type=hidden]', form),
			strObj       = '{';
		
	    $.each(elementsList, function(index, element) {
	        var el  = $(element);
	        
	        strObj += (index == 0 ? '' : ',') + '"' + el.attr('name') + '":"' + el.val() + '"';
	    });
	    
	    strObj += '}';
		
		return JSON.parse(strObj);
	},
	
	salvar : function() {
        $('.btn-adicionar', '#modalDocumento').on('click', function() {
			var inputs = $('.obrigatorio:not(:hidden)', '#modalDocumento'),
				error  = false;
			
			if(inputs.length) {
				inputs.each(function(index, element) {
					var input = $(element);
					
					if(!input.val()) {
						input.closest('.control-group').addClass('error');
						input.closest('.control-group').find('.help-block').removeClass('hide').addClass('show');
						
						error = true;
					} else {
						input.closest('.control-group').removeClass('error');
						input.closest('.control-group').find('.help-block').removeClass('show').addClass('hide');	
					}
				});
			}
			
			if(!error) {
				var object = Documentos.serializeObject($('#form-modal-documento'));
				
				$.ajax({
					url  : '/auxiliar/documento/save',
					type : 'POST',
					data : object,
					success : function(response) {
						
					}
				});
			}
            
            return false;
        });
	},
	
	init : function() {
		this.salvar();
	}
};

(function() {
	$('[class*=sqTipoDocumento]').addClass('hide');
	$('#sqTipoDocumento').on('change', function(){
		var value = $(this).val();
		var all   = $('[class*=sqTipoDocumento]');
		
		all.addClass('hide');
		
		if(value) {
			$('.sqTipoDocumento-' + value).removeClass('hide').addClass('show');
		}
	});
	
	Documentos.init();
})();