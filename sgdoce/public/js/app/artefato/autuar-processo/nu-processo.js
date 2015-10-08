$(document).ready(function(){
//    ProcessoEletronico.validaNuProcesso();   
    $('#coAmbitoProcesso').change(function(){
        $('.Municipio').hide();
        $('.divEstado').hide();   
        $('#sqEstado').val('');
        $('#sqMunicipio').val('');
        $('#nuArtefato').val('');
        switch($(this).val()){
            case 'F' :
                $("#nuArtefato").val('');
                $("#nuArtefato").attr('maxlength','15');
                break;
            case 'E' :
            case 'M' :
                $('.divEstado').show();  
                $("#nuArtefato").val('').unsetMask().attr('maxlength','15');
                break;
            case 'J' :
                $("#nuArtefato").val('').unsetMask().attr('maxlength','15');
                break;
                
        }
    });

    $('#sqEstado').change(
            function() {
                if ($('#sqEstado').val()) {
                    switch($('#coAmbitoProcesso').val())
            		{
                    case 'F' :                    	
                    	break;
                    case 'E' :
                    	$('.Municipio').hide();
                    	break;
                    case 'M' :
                    	$('.Municipio').show();
                    	break;
                    case 'J' :
                        $('.divEstado').hide();  
                    	$('.Municipio').hide();
                    	break;
            		}
                    $('#divMunicipio').load(
                            '/artefato/processo-eletronico/combo-municipio/sqEstado/'
                                    + $('#sqEstado').val());
                }else{
                	$('.Municipio').hide();
                }
     });

    $('#nuArtefato').blur(function(){
        if ($('#coAmbitoProcesso').val() == 'F' && String($('#nuArtefato').val()).length == 15) {
            $("#nuArtefato").attr('maxlength','20');
            var digitoVerificador = FormProcesso.DigitoVerificador($('#nuArtefato').val());
            $('#nuArtefato').val($('#nuArtefato').val() + digitoVerificador);
            $("#nuArtefato").setMask('99999.999999/9999-99');
        } else if ($('#coAmbitoProcesso').val() == 'F') {
            $("#nuArtefato").val('');
            $('#nuArtefato').unsetMask();
            $("#nuArtefato").attr('maxlength','15');
        }
    });
    
     $('.form-processo-eletronico').submit(function() {
    	var msg = 'Número do processo inválido.';
    	var value = $('#nuArtefato').val()
    	if($('#coAmbitoProcesso').val() == 'F') {
//    		if(value.length < '15'){
//    			Message.showAlert(msg);
//    	    	return false;
//    		}
    	}
    	if($('#coAmbitoProcesso').val() == 'F') {
    		if(value.length < '17'){
    			Message.showAlert(msg);
    	    	return false;
    		}
    	}
        var params = $('.form-processo-eletronico').serialize();

//        if (FormProcesso.checkProcessoCadastrado(params) == 'true') {
//            Message.showAlert('Número do processo já cadastrado.');
//            return false;
//        } else {
//            return true;
//        }
	 });
     

    
});
FormProcesso = {
	checkProcessoCadastrado: function(params){
	    var result = $.ajax({
	            type: 'post',
	            url: '/artefato/processo-eletronico/check-processo-cadastrado',
	            data: params,
	            async: false,
	            global: false
	        }).responseText;
	    return (result);
	},

    calcularDigitoVerificador: function(arrProcesso){
        var incremento = 2;
        var total = arrProcesso.length - 1;
        var soma = null;
        for (i = 0; i < arrProcesso.length; i++) {
            soma += arrProcesso[total--] * incremento++;
        }

        return soma;
    },

    retornaRestoDivisao:function(soma){
        var resto = soma % FormProcesso.DivisorDigitorVerificador;
        return FormProcesso.DivisorDigitorVerificador - resto;
    },

    DigitoVerificador:function(nuProcesso){
        var string = String(nuProcesso);
        var arrProcesso = string.split("");

        //Obtem soma 1º digito verificador
        var soma1 = FormProcesso.calcularDigitoVerificador(arrProcesso);

        //Calcula 1º digito verificador
        var dv1 = FormProcesso.retornaRestoDivisao(soma1);

        //transforma 1º digito verificador em array para separar casa decimal caso exista
        var strDv1 = String(dv1);
        var arrDv1 = strDv1.split("");
        if (arrDv1.length == 2) {
            dv1 = arrDv1[1];
        }
        //acrescento 1º digito verificador ao nuArtefato para obter 2º digito verificador
        arrProcesso.push(dv1);

        //Obtem 2º digito verificador
        var soma2 = FormProcesso.calcularDigitoVerificador(arrProcesso);
        var dv2 = FormProcesso.retornaRestoDivisao(soma2);

        //Concatena 1º com 2º digito verificador
        var digitoVerificador = String(dv1.toString() + dv2.toString());

        return digitoVerificador;
    }

}