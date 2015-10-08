var Form = {
    init : function(){
        Form.events();
    },
    events : function(){
        $("#nuEtiqueta").setMask();

        $(".btnConcluir").click(function() {
            var nuDigital = $("#nuEtiqueta").val();
            if ($.inArray(nuDigital, RestaurarEtiqueta.vars.listEtiquetas) > -1) {
                Validation.addMessage("Número da Digital já adicionada.");
                return false;
            } else {
                if($("#form-digital").valid()){
                    RestaurarEtiqueta.item.add(nuDigital);
                    RestaurarEtiqueta.vars.typeEtiquetasList = $("#inLoteComNupSiorg").val();
                } else {
                    return false;
                }
            }
        });

        //sempre que mudar de tipo de lote limpa a digital
        $('#inLoteComNupSiorg').on('change',function(){
            $('#nuEtiqueta,#nuEtiqueta_hidden').val('');
        });

        // autocomplete para o tema caverna selecionado
        $('#nuEtiqueta').simpleAutoComplete("/etiqueta/restaurar-etiqueta/lista-numero-etiquetas/", {
            attrCallBack: 'rel',
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel',
            extraParamFromInput: "#inLoteComNupSiorg",
        });

        if( RestaurarEtiqueta.vars.listEtiquetas.length > 0 ) {
            var inLoteComNupSigor = RestaurarEtiqueta.vars.typeEtiquetasList;
            $("#inLoteComNupSiorg").val(inLoteComNupSigor).attr('disabled', 'disabled');
            $("#form-digital").append("<input type='hidden' name='inLoteComNupSiorg' id='inLoteComNupSiorgHidden' value='" + inLoteComNupSigor + "' />");
        } else {
            $("#inLoteComNupSiorg").removeAttr('disabled', 'disabled');
            $("#inLoteComNupSiorgHidden").remove();
        }
    }
}