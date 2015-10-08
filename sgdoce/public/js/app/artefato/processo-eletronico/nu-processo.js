//.replace(/\b--*/g, '-').replace(/\b\/\/*/g, '/').replace(/\D\.\.*/g, '.')
var FormProcesso = {
    DivisorDigitorVerificador: 11,
    checkProcessoCadastrado: function(params){
        $.ajax({
            type: 'post',
            url: '/artefato/processo-eletronico/check-processo-cadastrado',
            data: params,
            async: false,
            global: false
        }).done(function(data){
            if (data.status == true) {                
                Message.showAlert(data.msg);
                return;
            } else {
                $('.form-processo-eletronico').submit();
            }
        });
    },

    calcularDigitoVerificador15: function(arrProcesso){
        var incremento = 2;
        var total = arrProcesso.length - 1;
        var soma = null;
        for (i = 0; i < arrProcesso.length; i++) {
            soma += arrProcesso[total--] * incremento++;
        }

        return soma;
    },

    retornaRestoDivisao15:function(soma){
        var resto = soma % FormProcesso.DivisorDigitorVerificador;
        return FormProcesso.DivisorDigitorVerificador - resto;
    },

    DigitoVerificador:function(nuProcesso){
        nuProcesso = nuProcesso.replace(/[^0-9]/g, '');

        var digitoVerificador;
        if( nuProcesso.length == 15 || nuProcesso.length == 13 ) {
            digitoVerificador = FormProcesso.DigitoVerificador15( nuProcesso );
        } else if( nuProcesso.length == 19 ) {
            digitoVerificador = FormProcesso.DigitoVerificador19( nuProcesso );
        } else {
            return false;
        }
        return digitoVerificador;
    },

    DigitoVerificador15 : function( nuProcesso ) {
        var string = String(nuProcesso);
        var arrProcesso = string.split("");

        //Obtem soma 1º digito verificador
        var soma1 = FormProcesso.calcularDigitoVerificador15(arrProcesso);

        //Calcula 1º digito verificador
        var dv1 = FormProcesso.retornaRestoDivisao15(soma1);

        //transforma 1º digito verificador em array para separar casa decimal caso exista
        var strDv1 = String(dv1);
        var arrDv1 = strDv1.split("");
        if (arrDv1.length == 2) {
            dv1 = arrDv1[1];
        }
        
        //acrescento 1º digito verificador ao nuArtefato para obter 2º digito verificador
        arrProcesso.push(dv1);
        
        //Obtem 2º digito verificador
        var soma2 = FormProcesso.calcularDigitoVerificador15(arrProcesso);
        var dv2 = FormProcesso.retornaRestoDivisao15(soma2);
        
        if( dv2 > 9 ) {
            dv2 = dv2.toString().substr(1,1);
        }
        
        //Concatena 1º com 2º digito verificador
        var digitoVerificador = String(dv1.toString() + dv2.toString());

        return digitoVerificador;
    },

    DigitoVerificador19 : function( nuProcesso ) {
        return calculaDVNupSiorg(nuProcesso);
    },

    nuArtefatoMaxMin : function( max, min ) {
        if( $.inArray(max, [NaN, undefined]) > -1
            || $.inArray(min, [NaN, undefined]) > -1 ) {
            return false;
        }

        if( max > 0 ) {
            $("#nuArtefato").attr('maxlength', max);
        } else {
            $("#nuArtefato").removeAttr('maxlength');
        }

        if( min > 0 ) {
            $("#nuArtefato").attr('minlength', min);
        } else {
            $("#nuArtefato").removeAttr('minlength');
        }
    },

    setMask: function(length){
        var objNuArtefato = $("#nuArtefato");
            objNuArtefato.removeAttr('minlength')
                         .removeAttr('maxlength');

        switch( length ){
            case 15:
                
            objNuArtefato.setMask('99999.999999/99-99');
                break;
            case 17:
                objNuArtefato.setMask('99999.999999/9999-99');
                break;
            case 21:            
                objNuArtefato.setMask('9999999.99999999/9999-99');
            default:
                
        }
            
        return FormProcesso;
    }
};

// Pad Right
String.prototype.padRight = function (l, c) {
    return this + Array(l - this.length + 1).join(c || " ");
};

// Pad Left
String.prototype.padLeft = function (l, c) {
    return Array(l - this.length + 1).join(c || " ") + this;
};

$(document).ready(function(){
    $('#coAmbitoProcesso').change(function(){
        var nuArtefatoDigitos = parseInt($(".tpNuArtefato:checked").val());
        var objNuArtefato = $("#nuArtefato");

        $('.Municipio,.divEstado').hide();
        $('#sqEstado,#sqMunicipio,#nuArtefato').val('');

        var condition = $.trim($(this).val());

        objNuArtefato.unsetMask();
        switch(condition){
            case 'F' :
                var cg = objNuArtefato.parent().parent('.control-group');
                    cg.removeClass('error').find('.help-block').remove();

                FormProcesso.setMask(nuArtefatoDigitos);
                $("#nuProcessoLength").show();
                $(".tpNuArtefato").removeAttr('disabled');
                break;
            case 'E' :
            case 'M' :
            case 'J' :
                if (condition !== 'J') { $('.divEstado').show();}
                $("#nuProcessoLength").hide();
                $(".tpNuArtefato").attr('disabled', true);
                FormProcesso.nuArtefatoMaxMin(30, 6);
                break;
        }
    });

    $(".tpNuArtefato").click(function(){
        var valor = parseInt($(this).val()),
            nuArtefato = $("#nuArtefato:not(:hidden)");
        if( $('#coAmbitoProcesso').val() == 'F' ) {
            nuArtefato.unsetMask();
            nuArtefato.val('');
            FormProcesso.setMask(valor);
        } else {
            FormProcesso.nuArtefatoMaxMin(30, 6);
        }
    });

    $('#sqEstado').change(function(){
        var nuArtefatoDigitos = parseInt($(".tpNuArtefato").val());
        if ($('#sqEstado').val()) {
            if( $.inArray($('#coAmbitoProcesso').val(), ['M']) > -1 ) {
                $('.Municipio').show();
                $('#divMunicipio').load('/artefato/processo-eletronico/combo-municipio/sqEstado/' + $('#sqEstado').val());
            }
        } else {
            $('.Municipio').hide();
            FormProcesso.nuArtefatoMaxMin((nuArtefatoDigitos + 3), 15);
        }
     });

    $('#btnValidar').click(function(){
        var params = $('.form-processo-eletronico').serialize();
        if($('.form-processo-eletronico').valid()) {

            //validar digito verificador
            if ($.trim($('#coAmbitoProcesso').val()) === 'F') {
                var nuArtefato = $("#nuArtefato").val().split('-'),
                    DV         = FormProcesso.DigitoVerificador(nuArtefato[0]);                    
                if (DV != nuArtefato[1]) {
                    Message.showError(UI_MSG.MN019);
                    return false;
                }else{
                    FormProcesso.checkProcessoCadastrado(params);
                }
            }else{
                FormProcesso.checkProcessoCadastrado(params);
            }
        }
    });

    $(".tpNuArtefato:checked").trigger('click');
});
