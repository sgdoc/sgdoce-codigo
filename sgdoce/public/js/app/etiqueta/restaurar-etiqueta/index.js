RestaurarEtiqueta = {
    init: function() {
        RestaurarEtiqueta.events();
    },
    events: function() {
        $("#btn-add").click(function(){
            RestaurarEtiqueta.initModal(RestaurarEtiqueta.vars._urlForm);
        });

        $(document).on("click", "#btn_excluir", function() {
            var nuEtiqueta = $(this).prev("input").val();
            RestaurarEtiqueta.item.remove(nuEtiqueta);
        });

        $("#btn_gerar").click(function() {
            if ($("#table-digitais .item-etiqueta").length > 0) {
                Message.showConfirmation({
                    'body': "Tem certeza que deseja restaurar as etiquetas?",
                    'yesCallback': function() {
                        $("#form_restaurar_etiqueta").submit();
                    }
                });
            } else {
                Validation.addMessage("Nenhuma Digital adicionada para ser restaurada.");
                return false;
            }
        });

        $("#btn_cancelar").click(function(){
            window.open(RestaurarEtiqueta.vars._urlAreaTrabalho, '_self');
        });
    },
    item: {
        add: function(nuEtiqueta) {
            if ($.trim(nuEtiqueta) == "") {
                Message.show('Alerta', "Número da Digital inválida.");
                return false;
            }

            if ($.inArray(nuEtiqueta, RestaurarEtiqueta.vars.listEtiquetas) == -1) {
                var trId = "n" + RestaurarEtiqueta.item.clearString(nuEtiqueta),
                    tr   = "<tr id=\"" + trId + "\" class=\"item-etiqueta\"><td>" + nuEtiqueta + "</td>";
                    tr += "<td><a href=\"javascript:RestaurarEtiqueta.item.remove('"  + nuEtiqueta +  "')\" title='Excluir' class='btn btn-mini'><span class='icon-trash'></span></a></td></tr>",
                    input = "<input type=\"hidden\" name=\"nuEtiquetas[]\" class=\"span5 " + trId + "\" value=\"" + nuEtiqueta + "\" readonly=\"readonly\" />";

                $("#table-digitais .digitalNone").addClass("hide");
                $("#table-digitais tbody").append(tr);
                $("#form_restaurar_etiqueta").append(input);

                RestaurarEtiqueta.vars.listEtiquetas.push(nuEtiqueta);
            } else {
                Validation.addMessage("Número da Digital já adicionada!");
                return false;
            }

        },
        remove: function(nuEtiqueta) {
            Message.showConfirmation({
                'body': "Tem certeza que deseja excluir a Digital \"" + nuEtiqueta + "\"?",
                'yesCallback': function() {
                    RestaurarEtiqueta.vars.listEtiquetas.splice(RestaurarEtiqueta.vars.listEtiquetas.indexOf(nuEtiqueta), 1);
                    $("#n" + RestaurarEtiqueta.item.clearString(nuEtiqueta)).remove();
                    $("#form_restaurar_etiqueta").find(".n" + RestaurarEtiqueta.item.clearString(nuEtiqueta)).remove();

                    if ($("#table-digitais .item-etiqueta").length <= 0) {
                        $("#table-digitais .digitalNone").removeClass("hide");
                        RestaurarEtiqueta.vars.typeEtiquetasList = '';
                    }
                }
            });
        },
        clearString: function(str) {
            return str.replace(/[\(\)\-\.\/\:\_\s]/g, "");
        }
    },
    vars: {
        typeEtiquetasList: '',
        listEtiquetas    : [],
        _urlForm         : "etiqueta/restaurar-etiqueta/form",
        _urlAreaTrabalho : "artefato/area-trabalho/index/tipoArtefato/1"
    },
    initModal: function(_url, container) {
        Message.wait();
        var modalContainer = container || $("#modal_container");
        modalContainer.empty();
        modalContainer.load(_url, function(responseText, textStatus) {
            Message.waitClose();
            if (textStatus === 'success') {
                modalContainer.modal();
            } else {
                Message.showError(responseText);
            }
        });
    }
};

$(RestaurarEtiqueta.init);