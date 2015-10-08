ModeloMinuta = {
    validarPesquisa: function(){

        if($('#sqPadraoModeloDocumento').val() == '' && $('#sqTipoDocumento').val() == '' && $('#sqAssunto').val() == ''){
            var div = '<button class="close" data-dismiss="alert">×</button>'+
            'Informe pelo menos um campo para realizar a pesquisa.';
            
            $('.alert-success').html(div).removeClass('hidden').hide();
            $('.campos-obrigatorios').html(div).removeClass('hidden').show();
            $('#divGrid').hide();
            return false;
        }else{
            $('#divGrid').show();
        }
        
        return true;
    },
	visualizar: function(id){
        $('#modal').find('.modal-body').load('/modelo-minuta/modelo-minuta/view/codigo/' + id);
        $('#modal-title').text('documento');
        $('#modal').modal();
    },
    deletar: function(codigo){
        Message.showConfirmation({
            'body'          : 'Tem certeza que deseja realizar a exclusão ?',
            'subject'       : 'Atenção',
            'yesCallback'   : function(){
                $.post(
                    '/modelo-minuta/modelo-minuta/delete/',
                    {
                        id : codigo
                    },
                    function(response){
                        Message.showSuccess('Exclusão realizada com sucesso!');
                        $('#table-grid-modelo-minuta').dataTable().fnDraw(false);
                        return false;
                    }, 'json'
                    );
                return false;
            }
        });
    },
    alterar: function(codigo,padrao){
        sessionStorage.setItem('editModeloMinuta',true);
        window.location = ('modelo-minuta/modelo-minuta/edit/id/' + codigo + '/sqPadraoModeloDocumento/' + padrao);
    }
};

$(function(){
    if (sessionStorage.getItem('editModeloMinuta')){
        $('#form-pesquisa-modelo-minuta').append('<input type="hidden" id="sqTipoDocumento_hidden" name="sqTipoDocumento"/>');
        $('#form-pesquisa-modelo-minuta').append('<input type="hidden" id="sqAssunto_hidden" name="sqAssunto"/>');
        $('#sqPadraoModeloDocumento').val(sessionStorage.getItem('modeloMinuta'));
        $('#sqTipoDocumento').val(sessionStorage.getItem('tipoDocumento'));
        $('#sqAssunto').val(sessionStorage.getItem('assunto'));
        $('#sqTipoDocumento_hidden').val(sessionStorage.getItem('tipoDocumento_hidden'));
        $('#sqAssunto_hidden').val(sessionStorage.getItem('assunto_hidden'));
        Grid.load($('#form-pesquisa-modelo-minuta'), $('#table-grid-modelo-minuta'));
        $('#table-grid-modelo-minuta').parents('.hidden').removeClass('hidden');
        sessionStorage.clear();
        return;
    }
    Grid.load($('#form-pesquisa-modelo-minuta'), $('#table-grid-modelo-minuta'));
    $('#sqAssunto').simpleAutoComplete("/auxiliar/assunto/searchAssunto/", {
        extraParamFromInput: '#extra',
        attrCallBack: 'class',
        autoCompleteClassName: 'autocomplete',
        selectedClassName: 'sel'
    });
    $('#sqTipoDocumento').simpleAutoComplete("/auxiliar/tipodoc/search-tipo-documento/", {
        extraParamFromInput: '#extra',
        attrCallBack: 'class',
        autoCompleteClassName: 'autocomplete',
        selectedClassName: 'sel'
    });
    $('#btLimpar').click(function(){
    	$('#sqPadraoModeloDocumento').val('');
    	$('#sqTipoDocumento_hidden').val('');
    	$('#sqAssunto_hidden').val('');
    	$('#sqTipoDocumento').val('');
    	$('#sqAssunto').val('');
    	
    });

	$('#btnPesquisar').live('click', function(){
        sessionStorage.setItem('modeloMinuta',$('#sqPadraoModeloDocumento').val());
        sessionStorage.setItem('tipoDocumento_hidden',$('#sqTipoDocumento_hidden').val());
        sessionStorage.setItem('assunto_hidden',$('#sqAssunto_hidden').val());
        sessionStorage.setItem('tipoDocumento',$('#sqTipoDocumento').val());
        sessionStorage.setItem('assunto',$('#sqAssunto').val());
        if(!ModeloMinuta.validarPesquisa()){
       	 	$('html,body').animate({scrollTop:0},500);
            $('#table-grid-modelo-minuta').parent('div').addClass('hidden');
            return false;
        }else{
        	$('html,body').animate({scrollTop:300},500);
        }
    });
    
    $('#btnFiltro').click(function(){
    	 $('html,body').animate({scrollTop:0},500);
    });
    
});

