var ComentarioGrid = {
    T_TITLE_UPDATE: 'Alterar Comentário',
    gridId: '#divGrid',
    modalId: '#modal-comentario-artefato',
    sqArtefato: null,
    urlDelete: '/artefato/comentario/delete/id/%d/sqArtefato/%d',
    urlForm: '/artefato/comentario/form/sqArtefato/%d/backUrl/%s',
    urlPrint: '/artefato/comentario/print/sqArtefato/%d',
    urlCreate: '/artefato/comentario/create/sqArtefato/%d',
    urlUpdate: '/artefato/comentario/update/sqComentario/%d/backUrl/%s',
    urlDetail: '/artefato/comentario/detail/sqComentario/%d/backToModal/1',
    urlGridData: '/artefato/comentario/list',
    init: function() {
        try {
            ComentarioGrid.sqArtefato = $('#sqArtefato').val();

            this.hasArtefato();
            this.grid();

            $('.btnAddComentario').click(ComentarioGrid.create);
            $('#btn_imprimir').click(ComentarioGrid.print);

        } catch (e) {
            Message.showError(e.message);
            return;
        }
        
        $('#cancelar').click(function(e){
            e.preventDefault();
            $("#modal_container_xl_size").modal('hide').html('').css('display', 'none');
            $('.modal-backdrop').remove();
            return false;
        });
    },
    hasArtefato: function() {
        if (!ComentarioGrid.sqArtefato) {
            throw {"code": 301, "message": "Nenhum artefato localizado."};
        }
    },
    grid: function() {
        Grid.load($('#comentario-artefato-grid-form'), $('#grid_comentario'));
    },
    print: function() {
        var target = sprintf(ComentarioGrid.urlPrint, ComentarioGrid.sqArtefato);

        window.location = target;
    },
    create: function() {
        var target = sprintf(ComentarioGrid.urlForm, ComentarioGrid.sqArtefato,$('#urlBack').val());
        AreaTrabalho.initModal(target);
        $("#modal_container_xl_size").on('shown', function () {
            $('#txComentario').focus();
        });
    },
    update: function(sqComentario) {
        var target = sprintf(ComentarioGrid.urlUpdate, sqComentario,$('#urlBack').val());
        AreaTrabalho.initModal(target);



//        var target = sprintf(ComentarioGrid.urlUpdate, sqComentario);
//
//        $request = $.ajax({url: target, type: 'get', dataType: 'html'});
//
//        $request.success(function(html) {
//
//            var result = {};
//            result.message = {"subject": ComentarioGrid.T_TITLE_UPDATE, "body": html};
//
//            Message.showMessage(result);
//
//            $('.bootbox:visible').addClass('modal-large');
//            var toolbar = $('.modal-footer:visible');
//            var btnCancel = $('.btn-primary', toolbar).removeClass('btn-primary');
//            var btnSubmit = $('<a class="btn btn-primary" href="#">Salvar</a>');
//            toolbar.append(btnSubmit);
//
//            /*ajuta contador de caracteres do textarea*/
//            Limit.init();
//
//            /*
//             * este objeto deve ser disponibilizado dentro do conteudo do conteudo
//             * recuperado pela requisicao 'html'
//             * */
//            btnSubmit.click(comentarioUpdate.update);
//            btnCancel.click(comentarioUpdate.cancel);
//        });
//
//        $request.fail(function(result) {
//            /* o.0 nesse ponto, rolou um erro 0.o */
//            Message.showError(
//                    "Ocorreu um erro desconhecido e a operação solicitação não pode ser realizada."
//                    );
//        });
    },
    confirmDelete: function(sqComentario,sqArtefato) {
        Message.showConfirmation({
            body: UI_MSG.MN089,
            yesCallback: function() {
                ComentarioGrid.doDelete(sqComentario,sqArtefato);
            }
        });
        return;
    },
    detail: function(sqComentario) {
        var target = sprintf(ComentarioGrid.urlDetail, sqComentario,$('#urlBack').val());
        AreaTrabalho.initModal(target, $("#modal_container_medium"));
    },
    doDelete: function(sqComentario,sqArtefato) {

        var target = sprintf(ComentarioGrid.urlDelete, sqComentario, sqArtefato);

        request = $.ajax({
            url: target,
            type: "post",
            datatype: 'json'
        }).success(function(result) {
            if (result.status) {
                var _url = sprintf(
                        '/artefato/comentario/index/id/%d/back/%s',
                        $('#sqArtefato').val(),
                        AreaTrabalho.getUrlBack()
                        );
                AreaTrabalho.initModal(_url);
            }else{
                /* o.0 nesse ponto, rolou um erro 0.o */
                Message.showError(result.message);
            }

        }).error(function(result) {
            /* o.0 nesse ponto, rolou um erro 0.o */
            Message.showError(
                    "Ocorreu um erro desconhecido e a operação solicitada não pode ser realizada."
                    );
        });
    }
};

$(document).ready(function() {
    ComentarioGrid.init();
});