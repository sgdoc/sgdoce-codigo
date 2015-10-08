var DespachoEdit = {
    T_DESPACHO_DEFAULT_URL : '/artefato/despacho-interlocutorio/index/id/%d/backUrl/%s',

    init:function(){
        $('#btnSubmit').click(DespachoEdit.save);
        $('#btnCancel').click(function(e){
            e.preventDefault();
            var _url = sprintf(
                    '/artefato/despacho-interlocutorio/index/id/%d/back/%s',
                    $('#sqArtefato').val(),
                    AreaTrabalho.getUrlBack()
                    );
            AreaTrabalho.initModal(_url);
            return false;
        });
    },

    save: function(e) {
        e.preventDefault();
        if ($('#form_novo_despacho').valid()) {
            $('#btnSubmit').attr('disabled', true);
            $.ajax({
                type: "POST",
                url: "/artefato/despacho-interlocutorio/save",
                data: $('#form_novo_despacho').serialize()
            }).success(function(result) {
                $('#btnSubmit').attr('disabled', false);
                if (result == '') {
                    var _url = sprintf(
                            '/artefato/despacho-interlocutorio/index/id/%d/back/%s',
                            $('#sqArtefato').val(),
                            AreaTrabalho.getUrlBack()
                            );
                    AreaTrabalho.initModal(_url);
                    
                }else{
                    Message.showError(result.message);
                }
            }).error(function(err) {
                $('#btnSubmit').attr('disabled', false);
                DespachoEdit.errorMessage("Ocorreu um erro inesperado na execução");
            });
        }
    },
    errorMessage: function(message) {

        var container = $("#form_novo_despacho").closest("div");
        var divError = $(".alert-error");

        if (!divError.length) {
            divError = $("<div>").addClass("alert alert-error campos-obrigatorios hidden");
            container.prepend(divError);
        }

        divError.removeClass("hidden")
                .html('<button class="close" data-dismiss="alert">×</button>' + message);
    }
};

$(DespachoEdit.init);