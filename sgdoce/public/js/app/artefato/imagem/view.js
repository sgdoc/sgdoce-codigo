var ImageView = {
    vars: {
        sqArtefato: null
    },
    init: function () {
        'use strict';

        var sideTreeviewToggle = $('#sideTreeviewToggle'),
            tree = $("#tree"),
            firstItem = tree.find('.treeviewItem:first'),
            activeItem = tree.find('.treeviewItem.active');

        sideTreeviewToggle.bind('click', function () {
            var treeviewContainer = $('#treeviewContainer'),
                viewerContainer = $('.viewerContainer');
            treeviewContainer.toggleClass('expanded');
            treeviewContainer.toggleClass('span3');
            treeviewContainer.toggleClass('span1');
            viewerContainer.toggleClass('span11');
            viewerContainer.toggleClass('span9');
        });

        tree.treeview({
            collapsed: false,
            animated: "fast",
            control: "#sidetreecontrol",
            persist: "location"
        });

        if (activeItem.length) {
            activeItem.get(0).click();
        } else {
            firstItem.get(0).click();
        }

        setTimeout(function () {
            sideTreeviewToggle.trigger('click');
        }, 1000);

        $('#btnDownload').click(ImageView.download);
    },

    load: function (id) {
        'use strict';

        var viewerContainer = $('.viewerContainer'),
            viewerItems = viewerContainer.find('.viewerItem'),
            viewerItem = viewerContainer.find('#viewerItem-' + id),
            treeviewItemClicked = $('.treeviewItem-' + id),
            treeviewItemSelected = $('.treeviewItem.selected');

        treeviewItemSelected.removeClass('selected');
        treeviewItemClicked.addClass('selected');

        treeviewItemClicked.append('<span class="threeLittleDots">.</span><span class="threeLittleDots">.</span><span class="threeLittleDots">.</span>');
        viewerItems.hide();

        ImageView.vars.sqArtefato = id;
        
        if (viewerItem.length) {
            treeviewItemClicked.find('.threeLittleDots').remove();
            viewerItem.show();
            // Carrega ID para alteração
//            ImageView.vars.sqArtefato = id;
        } else {
            if (treeviewItemClicked.is('.Processo')) {

                treeviewItemClicked.find('.threeLittleDots').remove();
                treeviewItemClicked.parents('.parent').find('ul .treeviewItem').get(0).click();

            } else {

                viewerItem = $('<div />').attr('id', 'viewerItem-' + id).addClass('viewerItem');
                viewerContainer.append(viewerItem);

                viewerItem.load('/artefato/imagem/viewer/id/' + id, function (responseText, textStatus) {
                    treeviewItemClicked.find('.threeLittleDots').remove();
                    if (textStatus === 'success') {
                        var pdfViewerContainer = viewerItem.find('.pdfViewerContainer');
                        if (pdfViewerContainer.length) {
                            var pdfViewer = PDFViewer({
                                pdfViewerContainer: pdfViewerContainer
                            });
                            pdfViewer.error(function () {
                                // console.debug(arguments);
                                Message.showError('Erro ao visualizar a imagem do artefato', function () {
                                    viewerItem.remove();
                                });
                            });
                            pdfViewer.load('/artefato/imagem/pdf/id/' + id);
                            // Carrega ID para alteração
//                            ImageView.vars.sqArtefato = id;
                        }
                    } else {
                        Message.showError(responseText);
                    }
                });
            }
        }
    },
    download:function(e){
        e.preventDefault();

        Message.wait();

        var request = $.ajax({
            url      : 'artefato/imagem/download',
            data     : {id : $('#sqArtefato').val()},
            dataType : 'json'
        });

        request.success(function(r){
            Message.waitClose();
            if(r.success){
                if(r.link){
                    window.open(r.link,'_blank');
                    return false;
//                    Message.showConfirmation({
//                            body: 'O arquivo gerado contém ' + r.filesize + '. Deseja realmente baixar o arquivo?',
//                            yesCallback: function(){
//                                window.open(r.link,'_blank');
//                            }
//                        });
                }else{
                    Message.showSuccess(r.msg);
                    return false;
                }
            }else{
                Message.showAlert(r.msg);
                return false;
            }
        });

        request.error(function(e){
            Message.waitClose();
            Message.showError('Ocorreu um erro: ' +e.responseText);
        });
    },
    alterar : function( url ){
        if( ImageView.vars.sqArtefato != null ) {
            Message.showConfirmation({
                'body': "Tem certeza que deseja alterar a imagem da digital \"" + $("a.treeviewItem-" + ImageView.vars.sqArtefato).html() + "\"?",
                'yesCallback': function () {
                    window.opener.location.href = url.replace(/%id/g, ImageView.vars.sqArtefato);
                    window.close();
                }
            });
        } else {
            Message.showError('Não foi possível localizar a imagem para alteração.');
        }
        return;
    }
};

ImageView.init();