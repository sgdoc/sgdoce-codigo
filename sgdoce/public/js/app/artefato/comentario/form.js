var ComentarioForm = {

    /* atua como construtor do "objeto" */
    init: function () {
        ComentarioForm.event();
    },

    /* carrega os elementos do formulario que sera validados */
    event: function () {
        //        $("textarea[name='txComentario']")
        $('#btnSubmit').click(ComentarioForm.submit);
        $('#cancelarForm').click(function(e){
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

    submit: function () {
        try {
            if( ComentarioForm.valid() ){
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

        } catch (e) {
            ComentarioForm.errorMessage(e.message);
        }
    },

    valid: function () {

        /* valida o campo comentario */
        if (ComentarioForm.isEmpty($('.txComentario'))) {
            throw {"code": 301, message: "O Comentário não pode ser vazio."};
        }
        return true;
    },

    isEmpty: function (elm) {
        return '' == elm.val().trim();
    },

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

$(ComentarioForm.init);