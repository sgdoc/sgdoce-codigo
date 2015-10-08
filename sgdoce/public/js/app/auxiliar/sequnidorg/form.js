    function Left(str, n){
            if (n <= 0)
                return "";
            else if (n > String(str).length)
                return str;
            else
                return String(str).substring(0,n);
    }
    function Right(str, n){
        if (n <= 0)
        return "";
        else if (n > String(str).length)
        return str;
        else {
        var iLen = String(str).length;
        return String(str).substring(iLen, iLen - n);
        }
    }
   
   function digitoVerificador(entrada){
      
   /*remove ".", "-" e "/" utilizando expressão regular, assim
    * permite validar valor com ou sem pontos, barra e traço.*/
    if(!entrada){
        return false;
    }
    
    entrada = entrada.replace(/[.\-\/]/g,"");
    
    n = entrada.length;    
    
    /*calcular 1º dígito verificador*/
    var c  = 2;
    var soma = 0;
    
    for (i = n - 1;i >= 0; i--){
         soma += parseFloat(entrada[i]) * c++;
    }
    
    /*calcular dígito verificador*/
    var dv1 = soma%11;
    dv1 = 11-dv1;
    dv1=Right(dv1,1);
    
    /*calcular 2º dígito verificador*/
    c = 3;
    soma = dv1 * 2;
    
    for (i = n - 1;i >= 0; i--){
         soma += parseFloat(entrada[i]) * c++;
    }
    
    /*calcular dígito verificador*/
    var dv2 = soma%11;
    dv2 = 11-dv2;
    dv2 = Right(dv2,1); 
    
    var dv = dv1 +''+dv2;
    return dv;
    
   };
    


$(document).ready(function() {

    $(function(){
        Grid.load($('#form-seq-unid-org'), $('#table-gridp-sequnidorg'));

        $('#noPessoa').simpleAutoComplete("auxiliar/sequnidorg/search-unidades-organizacionais/", {
        extraParamFromInput: '#extra',
        attrCallBack: 'rel',
        autoCompleteClassName: 'autocomplete',
        selectedClassName: 'sel'
        });    
    });
    //Calcula o digito verificador ao carregar a pagina e atribui ao campo
    window.onload = function (){
       var entrada = $('#nuNup').val() + '.' + $('#nuSequencial').val() + '/' + $('#nuAno').val();
       $('#nuVerificador').val(digitoVerificador(entrada));
    }
    $("#nuSequencial").css("text-align", "right");
    //Calcula o digito verificador sob qualquer alteração do campo
    $('#nuSequencial').keyup(function(){
       
       var entrada = $('#nuNup').val() + '.' + $('#nuSequencial').val() + '/' + $('#nuAno').val();
       $('#nuVerificador').val(digitoVerificador(entrada));
       
       var carc = /[^0-9]/gi;
       var obj = $('#nuSequencial').val();
       obj = obj.replace(carc, "");
       $('#nuSequencial').val(obj);
       
    });
    
});

