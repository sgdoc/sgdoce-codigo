var comentarioUpdate = {

    T_MESSAGE_REQUIRED_FIELD_EMPTY: "Campos requeridos não preenchidos",

    init:function(){
        $('#btnSubmit').click(comentarioUpdate.update);
        $('#btnCancel').click(function(e){
            e.preventDefault();
            var _url = sprintf(
                    '/artefato/comentario/index/id/%d/back/%s',
                    $('#sqArtefato').val(),
                    AreaTrabalho.getUrlBack()
                    );
            AreaTrabalho.initModal(_url);
            return false;
        });
    },

    update: function (event) {

        event.preventDefault();

        try {
            if(comentarioUpdate.valid()){
                $.ajax({
                    type: "POST",
                    url: "/artefato/comentario/register",
                    data: $('#formComentario').serialize(),
                }).success(function(result) {
                    if (result == '') {
                        var _url = sprintf(
                                '/artefato/comentario/index/id/%d/back/%s',
                                $('#sqArtefato').val(),
                                AreaTrabalho.getUrlBack()
                                );
                        AreaTrabalho.initModal(_url);
                    } else {
                        Message.showError(result.message);
                    }
                    $('#btnSubmit').attr('disabled', false);
                }).error(function(err) {
                    Message.showError("Ocorreu um erro inesperado na execução.");
                    $('#btnSubmit').attr('disabled', false);
                });
            }
//            comentarioUpdate.valid();
//
//            $('#formComentario').submit();
//            $.ajax({
//                type: "POST",
//                 url: "/artefato/comentario/register",
//                data: $('#formComentario').serialize()
//            }).success(function (result) {
//
//                    if (result.status) {
////                        $('.modal').modal('hide');
////                        Message.showSuccess(result.message);
////                        $('#comentario-artefato-grid-form').submit();
//                        window.location.reload();
//                        return;
//                    }else{
//                        /* o.0 nesse ponto, rolou um erro 0.o */
//                        Message.showError(result.message);
//                    }
//              })
//              .error(function (err) {
//                  comentarioUpdate.errorMessage("Ocorreu um erro inesperado na execução");
//              });

        } catch (e) {
            comentarioUpdate.errorMessage(e.message);
        }

        return false;
    },

    valid: function () {

        if (this.isEmpty($('#sqComentarioArtefato'))) {
            throw {"code": 301, "message": "Identificador do comentário não foi encontrado."};
        }

        /* valida o campo comentario */
        if (this.isEmpty($('#txComentario'))) {
            throw {"code": 301, "message": "O Comentário não pode ser vazio."};
        }
        return true;
    },

    isEmpty: function (elm) { return !elm.val().trim(); },

    errorMessage: function (message) {

        var container = $("#formComentario").closest("div");
        var divError  = $(".alert-error");

        if (!divError.length) {
            divError = $("<div>").addClass("alert alert-error campos-obrigatorios hidden");
            container.prepend(divError);
        }

        divError.removeClass("hidden")
                .html('<button class="close" data-dismiss="alert">×</button>' + message);
    }
};

$(comentarioUpdate.init);