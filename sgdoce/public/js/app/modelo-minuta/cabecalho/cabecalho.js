Cabecalho = {
	initGrid: function(){
	    Grid.load('/modelo-minuta/cabecalho/list-cabecalho/sqModeloDocumento/'+ $('#idModeloDocumento').val() , $('#table-lista-cabecalho'));		
    },
    
    visualizar: function(id){
        window.location = '/modelo-minuta/cabecalho/view-cabecalho/sqCabecalho/' + id;
    },
    
    initModal: function(){
		  setTimeout(function() {
			$('#modalCabecalho').on('hidden', function () {
		        $(this).find('.modal-body').html('')
		        $("body").on('click','a[data-toggle=modal]',function (e) {
		            lv_target = $(this).attr('data-target')
		            lv_url = $(this).attr('href')
		            $(lv_target).find('.modal-body').load(lv_url)
		          })	
		    });
		    if ($('#idModeloDocumento').val() == 0)
			{
		    	$('input[name="sqCabecalho"][value="1"]').attr('checked','checked');
		    }
		    
			if($("input[name='sqCabecalho']:checked").val() == '1'){

			 	  jQuery.each($('.tipoCabecalho'), function() {
			 		  $(this).attr('disabled','disabled');
			 	  });
			}

		  $("input[name='sqCabecalho']").click(function(){     
			    if($("input[name='sqCabecalho']:checked").val() == '1')
			    {
			    	jQuery.each($('.tipoCabecalho'), function() {
				       $(this).removeAttr('checked');
				       $(this).attr('disabled','disabled');
			    	});
			    }else{
		 		   jQuery.each($('.tipoCabecalho'), function() {
					   $(this).removeAttr('disabled');
				    });
			    }
			});
		  }, 1000);
    },
    
	init: function(){
		Cabecalho.initGrid();
		Cabecalho.initModal();
    }
};

$(document).ready(function(){
	Cabecalho.init();
});
