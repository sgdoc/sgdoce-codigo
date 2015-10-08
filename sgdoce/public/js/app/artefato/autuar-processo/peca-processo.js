var PecaProcesso = {

    VincularDocumentoAutoComplete : function() {
        $('#nuArtefatoVinculacaoPeca').simpleAutoComplete("/artefato/processo-eletronico/find-tipo-artefato", {
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel',
            attrCallBack: 'id'
        },function(data){
            PecaProcesso.BuscaDigitaisTipoArtefato(data);
        });
    },

    BuscaDigitaisTipoArtefato: function(data){
        $.post('/artefato/processo-eletronico/find-artefato-peca-processo',{identificador: data[1]},function(result){
            var html = '<option value="">Selecione</option>';
            $.each(result,function(key,value){
                var numero = value.nuDigital ? value.nuDigital : value.nuArtefato;
                html += '<option inOriginal="'+value.inOriginal+'" sqPessoa="'+value.sqPessoa+'" value="'+value.sqArtefato+'">'+numero+'</option>';
            })
            $('#nuDigital').html(html);
        });
    },
    ListGridImagens: function(){
        $.get('artefato/imagem/list',{
                id: $('#sqArtefato').val(),
                obrigatoriedade: false
            },
            function(data){
                $('#dadosImagem').html(data);
                $('.thumbnail').css('height', 276);
            });
    }
}

$(document).ready(function(){
    $('#nuDigital').html('<option value="">Selecione</option>');
    $('#nuDigital').change(function(){
        var sqPessoa = $('#nuDigital').find(':selected').attr('sqPessoa');
        var inOriginal = $('#nuDigital').find(':selected').attr('inOriginal');
        var html = '<option value="">Selecione</option>';
        var result = ['Original','Cópia'];
        var iterator = 1;
        if (inOriginal != 'null' || sqPessoa != $('#sqPessoaLogada').val()) {
            result.shift();
            iterator = 2;
        }
        $.each(result,function(key,value){
            chave = key + iterator;
            html += '<option value="'+chave+'">'+value+'</option>';
        });
        $('#inOriginal').html(html);
    });
	PecaProcesso.VincularDocumentoAutoComplete();
	//inicia tema
    $(".btnConcluirPeca").click(function(){
    	if($('#form-peca-processo-modal').valid()) {

            Message.showConfirmation({
                'body': 'Deseja incluir a digital '+$('#nuDigital').val()+' ao processo?',
                'yesCallback':  function() {
		            	var inOriginal = false;
		            	if($('#inOriginal').val() == '1'){
		            		inOriginal = true;
		            	};            	
		                $.post('/artefato/processo-eletronico/add-peca-processo', {
		                    sqArtefatoPai: $('#sqArtefato').val(),
		                    sqArtefatoFilho: $('#nuDigital').val(),
		                    nuDigital: $('#nuDigital').val(),
		                    sqTipoVinculoArtefato: '3',
		                    inOriginal: inOriginal
		                },
	                    function(data){
	                    	if(data.sucess == 'true'){
	                            Message.showAlert('Item já incluído na lista.');
	                            $(".bootbox .btn").click(function(){
	                            	$('#modal-peca-processo').modal();
	                            });
	                            return false;
	                    	}else if(data.sucess == 'false'){
	    		                Message.showSuccess(UI_MSG['MN013']);
	    		                ListaPeca.reloadGridPeca();
                                PecaProcesso.ListGridImagens();
	                    	}
                    });
                },
	        	'noCallback' : function()  {
	        		$('#modal-peca-processo').modal();	        		
	        	}
			});          
            $(".bootbox .close").click(function(){
            	$('#modal-peca-processo').modal();
            });
            
        }else{
            $('.campos-obrigatorios').remove();
            return false;
        }
   });
});