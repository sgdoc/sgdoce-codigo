var Imagem = {
    reload : function(url, status) {
    	listUpload.reloadDivImagem();
//        $.post(url, {}, function(data) {
//            $('#page-list-image-view').html(data);
//            Imagem.showModal(status);
//        });
    },

    hideModal : function() {
        $('#imageFile').val('');
        $('#adicionarImagem').modal('hide');
    },

    versoBranco : function (element, sqAnexoArtefato) {
        $.ajax({
            url      : 'artefato/imagem/verso-branco',
            data     : {
                sqAnexoArtefato : sqAnexoArtefato,
                sqArtefato      : $('#sq-artefato').val(),
                inValueBranco   : element.checked
            },
            dataType : 'json',
            success  : function(data) {
                if (data.success) {
                    Imagem.reload('artefato/imagem/list/id/' + $('#sq-artefato').val());
                } else {
                    var erros = "";

                    for (var erro in data.errors) {
                        erros += data.errors[erro] + '\n';
                    }

                    $('#modalImagem').modal('hide');
                    $('#messageImage').html('<div class="alert alert-error campos-obrigatorios">'+
                        '<button class="close" data-dismiss="alert">×</button>'+erros+'</div>');
                }
            }
        });
    },

    checkInputObrigatorio : function() {
        $('input[id=imageFile]:first').change(function(){
            $('.help-block').hide();
            $('.obrigatorio').css('color', '#333333');
        });
    },

    upload : function(url) {
        if ($('#imageFile').val() == "") {
            $('.help-block').show();
            $('.obrigatorio').css('color', '#B94A48');
            $('#error-image').html('<div class="alert alert-error campos-obrigatorios">'+
                '<button class="close" data-dismiss="alert">×</button>Arquivo não selecionado.</div>');
            return false;
        }else{
        	$('#adicionarImagem').modal('hide');
        	$('#modalUploadion').modal('show');
        }

        $('#adicionarImagem').css('cursor', 'wait');

        $('#formUpload').ajaxForm({
            success: function(data) {
                if (data.success) {
                    Imagem.hideModal();
                    Imagem.reload(url + '/' + $('#sq-artefato').val(), 1);
                	$('#modalUploadion').modal('hide');
                    $('#adicionarImagem').css('cursor', 'default');
                    $('.modal-backdrop').remove();
                    Message.showAlert('Upload concluído.');
                } else {
                    var erros = "";

                    for (var erro in data.errors) {
                        erros += data.errors[erro] + '\n';
                    }


                    Message.showError(erros);
                    $('#modalUploadion').modal('hide');

                    $('#adicionarImagem').css('cursor', 'default');

                    $('#error-image').html('<div class="alert alert-error campos-obrigatorios">'+
                        '<button class="close" data-dismiss="alert">×</button>'+erros+'</div>');
                }
            },
            error : function(){
                $('#error-image').html('<div class="alert alert-error campos-obrigatorios">'+
                    '<button class="close" data-dismiss="alert">×</button>O tamanho do arquivo é superior ao permitido. O tamanho máximo permitido é 100Mb.</div>');
            },
            dataType: 'json',
            resetForm: true
        }).submit();
    },

    remove : function(url, status) {
        Imagem.showModal(status);
    },

    removeSingle : function(val) {
        Imagem.showModal(4, val);
    },

    removeAjax : function(url, check) {
        var checkboxes = $(".checkbox-remove-image:checked");
        var arCheck    = check ? check : new Array();

        checkboxes.each(function() {
            arCheck.push($(this).val());
        });

        $.ajax({
            url      : 'artefato/imagem/delete',
            data     : {
                sqAnexoArtefato : arCheck
            },
            dataType : 'json',
            success  : function(data) {
                if (data.success) {
                    Imagem.reload(url + '/' + $('#sq-artefato').val(), 2);
                    Message.showAlert('Exclusão realizada com sucesso.');
                } else {
                    var erros = "";

                    for (var erro in data.errors) {
                        erros += data.errors[erro] + '\n';
                    }

                    $('#modalImagem').modal('hide');
                    $('#messageImage').html('<div class="alert alert-error campos-obrigatorios">'+
                        '<button class="close" data-dismiss="alert">×</button>'+erros+'</div>');
                }
            }
        });
    },

    removeAllAjax : function(url) {
        $.ajax({
            url      : 'artefato/imagem/delete-all',
            data     : {
                sqArtefato : $('#sq-artefato').val()
            },
            dataType : 'json',
            success  : function(data) {
                if (data.success) {
                    Imagem.reload(url + '/' + $('#sq-artefato').val(), 2);
                    Message.showAlert('Exclusão realizada com sucesso.');
                } else {
                    var erros = "";

                    for (var erro in data.errors) {
                        erros += data.errors[erro] + '\n';
                    }

                    $('#modalImagem').modal('hide');
                    $('#messageImage').html('<div class="alert alert-error campos-obrigatorios">'+
                        '<button class="close" data-dismiss="alert">×</button>'+erros+'</div>');
                }
            }
        });
    },

    alertaImagem : function (){
        $('#modalImagem .row-fluid').html('Não é possível remover este carimbo, pois o mesmo está vinculado a imagem anterior.');
        $('#modalImagem .modal-footer').html('<a class="btn" data-dismiss="modal" href="#"><i class="icon-remove"></i> Fechar</a>');
        $('#modalImagem').modal('show');
    },

    showModal : function(status, val) {
        switch (status) {
            case 1:
                $('#modalImagem .row-fluid').html('Operação realizada com sucesso.');
                $('#modalImagem .modal-footer').html('<a class="btn" data-dismiss="modal" href="#"><i class="icon-remove"></i> Fechar</a>');
                $('#modalImagem').modal('show');
                break;
            case 2:
                $('#modalImagem .row-fluid').html('Exclusão realizada com Sucesso.');
                $('#modalImagem .modal-footer').html('<a class="btn" data-dismiss="modal" href="#"><i class="icon-remove"></i> Fechar</a>');
                $('#modalImagem').modal('show');
                break;
            case 3:
                $('#modalImagem .row-fluid').html('Tem certeza que deseja realizar a exclusão?');
                $('#modalImagem .modal-footer').html('<a class="btn btn-primary" id="confirmDelete" data-dismiss="modal" href="#"> Sim</a>' +
                    '<a class="btn" data-dismiss="modal" href="#"><i class="icon-remove"></i> Não</a>');
                $('#modalImagem').modal('show');
                break;
            case 4:
                $('#modalImagem .row-fluid').html('Tem certeza que deseja realizar a exclusão?');
                $('#modalImagem .modal-footer').html('<input type="hidden" id="sqAnexoHidden" value="' + val + '" />' +
                    '<a class="btn btn-primary" id="confirmDeleteSingle" data-dismiss="modal" href="#"> Sim</a>' +
                    '<a class="btn" data-dismiss="modal" href="#"><i class="icon-remove"></i> Não</a>');
                $('#modalImagem').modal('show');
                break;
        }
    },

    ordenacao : function (url, ordem){
        $.post('/artefato/imagem/atualiza-ordem/artefato/'+$('#sq-artefato').val(), ordem, function(data){
//            Imagem.reload(url + '/' + $('#sq-artefato').val());
        	listUpload.reloadDivImagem();
        });
    },

    imagemBranco : function() {
        if($('#inFrenteImagem').val() == 'F'){
            Imagem.inBranco(false);
        }
    },

    inBranco : function(element, val) {
        if(element.value == 'F'){
            $('#inBranco-'+val).attr('disabled', 'disabled');
            inFrente = 'TRUE';
        } else {
            $('#inBranco-'+val).removeAttr('disabled');
            inFrente = 'FALSE';
        }

        // chama método que altera status inFrente no banco
        grid.initAlteraFrenteVerso(inFrente, val);
    },

    init : function() {
        Imagem.checkInputObrigatorio();
    }
};

$(document).ready(function() {
    $("a.view-image").colorbox({
        photo: true
    });

    Imagem.init();

});