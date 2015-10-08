$(document).ready(function(){
    var wasSend = false;

    /* dispara formulario da grid */
    $('#combo-input').comboInput({
                        id: "filter-artefato",
               placeholder: "Informe o número Artefato",
        displayDefaultText: "Tipo de Artefato",

            /* local onde sera recuperado os dados do combo */
            comboWebServer: {
                     url: "/auxiliar/tipo-artefato/list-items-vinculo-artefato/sqTipoArtefatoParent/"+$("#sqTipoArtefatoParent").val(),
                dataType: "json",
                    type: "get"
            },

        onTextKeyUp: function (data, refer) {

            var comp = refer.getData();

            if ((!comp.comboValue || data.value.length < 3) && !wasSend) {
                return;
            }

            wasSend = true;

            $('#nuArtefato').val(comp.textValue );
            $('#sqArtefatoTipo').val(comp.comboValue);

            $('#grid-artefato-vinculo').dataTable().fnDraw(false);
        }
    });

});