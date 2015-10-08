
var Imagem = {
    canAlterImage : false,
    urlRedirect : ""
};


$(function () {

    'use strict';

    var _pickImageElement = $('#selectImage');
    if (!_pickImageElement.length) {
        return;
    }

    var _reset = function () {
        window.location.reload();
        // $('.showAfterUpload').fadeOut();
        // var __letter = $('#jokerLetter');
        // __letter.text(__letter.text().toUpperCase());
        // $('#jokerText').text('um');
        // $('#titleText').html('Artefato sem imagem.');
    };

    var __pdfViewer = PDFViewer({
        pdfViewerContainer: $('#pdfViewerContainer')
    });
    __pdfViewer.error(function () {
        // console.debug(arguments);
        Message.showError('Erro ao visualizar o arquivo', function (){
            _reset();
        });
    });

    var _setUploadProgress = function (percent) {
        var progress = $('#uploadProgress'),
            bar = progress.find('> .bar');

        progress.removeClass('hide');

        bar.width(percent + '%');
        if (0 === percent || 100 === percent) {
            progress.fadeOut(1000);
        } else if (progress.is(':not(:visible)')) {
            progress.fadeIn();
        }
    };



    var _previewChanges = function () {
        $('.showAfterUpload').fadeIn(2000);
        var __letter = $('#jokerLetter');
        __letter.text(__letter.text().toLowerCase());
        $('#jokerText').text('outro');
        $('#titleText').text('Esta é a imagem que você escolheu?');
        $('.defaultText').hide();
        $('.otherText').show();
    };

    var _previewAction = function (file) {
        __pdfViewer.load( '/artefato/imagem/pdf/tmp/' + file.target_name );
    };

    var _bindSaveAction = function (file) {
        $('#btnSave').unbind('click').bind('click', function () {
            Message.showConfirmation({
                body: 'Deseja salvar o arquivo <b>' + file.name + '</b> para este artefato?',
                yesCallback: function () {
                    var __failback = function (msg) {
                        msg = msg || 'Ocorreu um erro ao salvar';
                        Message.showError(msg);
                    };
                    var dataPost = {
                        id: $('#sqArtefato').val(),
                        filenameTemporary: file.target_name,
                        bytes: file.size,
                        pages: __pdfViewer.getPages(),
                        reason: null
                    };
                    $.post(
                        '/artefato/imagem/save-image',
                        dataPost,
                        function (result) {
                            if (typeof result === 'object' && 'success' in result) {
                                if (result.success) {
                                    Message.showSuccess(result.message, function(){
                                        if( Imagem.canAlterImage ) {
                                            window.location.href = Imagem.urlRedirect;
                                        } else {
                                            if (Imagem.urlRedirect) {
                                                window.location.href = Imagem.urlRedirect;
                                            }else{
                                                window.location.reload();
                                            }
                                        }
                                    });
                                } else {
                                    __failback(result.message);
                                }
                            } else {
                                __failback();
                            }
                        }
                    ).fail(function () {
                        __failback();
                    });
                }
            });
        });
    };

    var _events = {
        Error: function (uploader, error) {
            var message = error.message || 'Ocorreu um erro';
            Message.showError(message, _reset);
        },
        FilesAdded: function (uploader) {
            uploader.start();
        },
        UploadProgress: function (uploader, file) {
            _setUploadProgress(file.percent);
        },
        BeforeUpload: function () {
            $('#titleText').html(
                'Carregando a imagem' +
                '<span class="threeLittleDots">.</span>' +
                '<span class="threeLittleDots">.</span>' +
                '<span class="threeLittleDots">.</span>'
            );
        },
        FileUploaded: function (uploader, file) {
            _previewChanges();
            _previewAction(file)
            _bindSaveAction(file);
        }
    };

    var _uploader = new Uploader({
        selector: _pickImageElement.selector,
        mode: 'standAlone',
        url: '/auxiliar/upload/tmp'
        // ,debugNoise: true
    });
    _uploader.addEventListener(_events);
    _uploader.init();
});