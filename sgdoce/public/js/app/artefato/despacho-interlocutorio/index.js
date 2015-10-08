DespachoInterlocutorio = {

    T_DESPACHO_DEFAULT_URL : '/artefato/despacho-interlocutorio/index/id/%d',

    init: function(){

        $('#form_print').remove();
        DespachoInterlocutorio.events();
        DespachoInterlocutorio.grid();
        sessionStorage.clear();
    },

    events:function(){
        $('#btn_imprimir').on('click', DespachoInterlocutorio.handleClickPrint);
        $('.btnAddDespacho').on('click', DespachoInterlocutorio.handleClickCreate);
        

        $('#cancelar').click(function(e){
            e.preventDefault();
            $("#modal_container_xl_size").modal('hide').html('').css('display', 'none');
            $('.modal-backdrop').remove();
            return false;
        });

    },

    handleClickCreate: function(){
        var sqArtefato = $('#sqArtefato').val();
        DespachoModal.create(sqArtefato);
    },

    handleClickPrint: function(){
        var sqArtefato = $('#sqArtefato').val();
         Message.showConfirmation({
            body: UI_MSG.MN112,
            yesCallback: function () {
                window.location = sprintf('/artefato/despacho-interlocutorio/print/id/%d', sqArtefato);
            }
        });
    },

    grid:function(){
        Grid.load($('#form-historico-despachos'), $('#table-grid-historico-despacho'));
    }
};

DespachoModal = {
    _urlCreate: '/artefato/despacho-interlocutorio/create/id/%d',
    _urlDetail: '/artefato/despacho-interlocutorio/detail/id/%d/backToModal/1',
    _urlPrint : '/artefato/despacho-interlocutorio/print-despacho/id/%d',
    _urlEdit  : '/artefato/despacho-interlocutorio/edit/id/%d/backUrl/%s',
    _urlDelete: '/artefato/despacho-interlocutorio/delete',

    create:function(sqArtefato){
        DespachoModal.createForm(sqArtefato);
    },

    view:function(sqDespacho){
        var target = sprintf(sprintf(DespachoModal._urlDetail, sqDespacho));
        AreaTrabalho.initModal(target, $("#modal_container_medium"));
    },

    edit:function(sqDespacho){
        Message.showConfirmation({
            body: UI_MSG.MN110,
            yesCallback: function () {
                DespachoModal.editForm(sqDespacho);
            }
        });
    },

    editForm: function (sqDespacho) {
        var target = sprintf(DespachoModal._urlEdit, sqDespacho,$('#urlBack').val());
        AreaTrabalho.initModal(target);


//        var modalContainer = $("#modal_update_despacho");
//        modalContainer.load(sprintf(DespachoModal._urlEdit, sqDespacho),function(responseText, textStatus) {
//            if (textStatus === 'success') {
//                modalContainer.modal();
//                Limit.init();
//            } else {
//                Message.showError(responseText);
//            }
//        });
    },

    print : function (sqDespacho) {
        DespachoModal.printDespacho(sqDespacho);
    },

    confirmDelete: function(sqDespacho){
        Message.showConfirmation({
            body: UI_MSG.MN111,
            yesCallback: function () {
                DespachoModal.deleteDespacho(sqDespacho);
            }
        });
    },

    createForm: function(sqArtefato,url){
        var __url = sprintf(
                DespachoModal._urlCreate,
                sqArtefato
                );
        AreaTrabalho.initModal(__url);
        $("#modal_container_xl_size").on('shown', function () {
            $('#sqUnidadeDestino').focus();
        });
    },

    deleteDespacho: function(sqDespacho){
        try {
            Message.wait();
            $.ajax({
                type: "POST",
                url: DespachoModal._urlDelete,
                data: {id:sqDespacho}
            }).success(function(result) {
                Message.waitClose();
                if (result.status) {
                    var _url = sprintf(
                            '/artefato/despacho-interlocutorio/index/id/%d/back/%s',
                            $('#sqArtefato').val(),
                            AreaTrabalho.getUrlBack()
                            );
                    AreaTrabalho.initModal(_url);
                }else{
                    /* o.0 nesse ponto, rolou um erro 0.o */
                    Message.showError(result.message);
                }
                
            }).error(function(err) {
                Message.waitClose();
                Message.showError("Ocorreu um erro inesperado na execução");
            });
        } catch (e) {
            Message.showError(e.message);
            $('div.modal-footer:visible').find('a.btn-primary:visible').focus();
        }
    },

    printDespacho: function(sqDespacho) {
        var target = sprintf(DespachoModal._urlPrint, sqDespacho);
        window.location = target;
    }
};

$(DespachoInterlocutorio.init);