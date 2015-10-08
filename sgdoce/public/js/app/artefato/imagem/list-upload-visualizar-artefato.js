grid = {
		
    initAjustaGrid : function() {
        var i   = 1;
        var iq  = 6;
        var iqb = true;

        while(i < $('.box-img').length) {

            if (iqb) {
                $('.box-img:nth-child('+ i +')').css('margin-left', '0');
            } else {
                $('.box-img:nth-child('+ i +')').removeAttr('style');
            }

            iqb = false;

            if ( i == iq ) {
                iqb = true;
                iq += 6;
            }

            i++;
        }
    },

    initReturnMoveFalse : function() {
        $('#btn-move').click(function(){
        	resizeThumb.execute();
            return false;
        });
    },

    initAlteraFrenteVerso : function(inFrente, val) {
                $.get('artefato/imagem/altera-frente-verso',{
                       sqAnexoArtefato : val,
                       inFrente   : inFrente
                }).done(function(){
                        return true;
                }).fail(function(){
                        return false;
                });
    },

    init : function() {
        grid.initAjustaGrid();
        grid.initReturnMoveFalse();
    }
};

listUpload = {

    reloadDivImagem : function() {
        $('#dadosImagem').html('');
        listUpload.assingContentImage();
    },

    assingContentImage : function() {
        $.get('artefato/imagem/list',{
            id: $('#sqArtefato').val(),
            obrigatoriedade: false,
            naoEditavel:true,
            visualizarArtefato:true
            },
        function(data){
            $('#dadosImagem').html(data);
            resizeThumb.execute();
        });
    }
};

resizeThumb = {
		
	execute : function() {
		var tamanho = 276; 
        $('.thumbnail').each(function(i) {
        	if (tamanho < $(this).height()) {
        		tamanho = $(this).height();
        	}
        });
        
        $('.thumbnail').css('height', tamanho);
	}
};

$(document).ready(function() {
    $('#btnOrdenar').click(function (){
        Imagem.ordenacao('artefato/imagem/list/id', ordem);
        listUpload.reloadDivImagem();
    });

    $('.tab').click(function() {
    	if ($(this).text() == 'Imagem') {
    		resizeThumb.execute();
    	}

        if ($(this).attr('href') == '#dadosImagem') {
            listUpload.reloadDivImagem();
        }
    });
    
    grid.init();
});