(function ($, window, undefined) {

    "use strict";

    window.SAFUploaderWithThumbs = function (options) {

        var _debugNoiseLabel,
            _options,
            _filesCount,
            _callbacks,
            _actions,
            _fail,
            _getOptions,
            _initializeUpload,
            _initializeThumbs;

        _debugNoiseLabel = '[SAFUploaderWithThumbs Debug noise] ';

        /**
         * @type {Object}
         */
        _options = $.extend({
            fileElement: $(),
            thumbnailsContainer: $(),
            uploadUrl: '',
            thumbsUrl: '',
            deleteUrl: '',
            uploadedThumbKey: '',
            style: 'bootstrap-v2.x',
            maxSize: 2097152, //2MB
            maxFiles: 0,
            fileTypes: {
                png: 'image/png',
                jpe: 'image/jpeg',
                jpg: 'image/jpeg',
                jpeg: 'image/jpeg',
                gif: 'image/gif',
                svg: 'image/svg+xml'
            },
            debugNoise: false
        }, options);

        /**
         * @type {integer}
         */
        _filesCount = 0;

        /**
         * @type {Object}
         */
        _callbacks = {
            error: function (errorData) {
                _getOptions('debugNoise') && console.error(_debugNoiseLabel + '): _callbacks.error', errorData);
            },

            addFileError: function (message, file) {
                _getOptions('debugNoise') && console.error(_debugNoiseLabel + '): _callbacks.addFileError', message, file);
            },

            fileUploaded: function (index, thumbUrl, data) {
                _getOptions('debugNoise') && console.info(_debugNoiseLabel + '): _callbacks.fileUploaded', index, thumbUrl, data);
            },

            fileDeleted: function (index, thumbUrl, result) {
                _getOptions('debugNoise') && console.info(_debugNoiseLabel + '): _callbacks.fileDeleted', index, thumbUrl, result);
            }
        };

        /**
         * @type {Object}
         */
        _actions = {
            appendThumb: function (resultData) {
                _getOptions('debugNoise') && console.log(_debugNoiseLabel + '): _actions.appendThumb', resultData);

                var thumbUrl = _getOptions('thumbsUrl'),
                    deleteUrl = _getOptions('deleteUrl'),
                    index = _getOptions('uploadedThumbKey'),
                    decorator = {};
                //@todo: implementar outros estilos...
                //       decorator['style'] = function (index, thumbUrl, deleteUrl, thumbsContainer, fileElement) {/*...*/};
                decorator['bootstrap-v2.x'] = function (index, thumbUrl, deleteUrl, thumbsContainer, fileElement) {
                    var thumbnails = thumbsContainer.find('.thumbnails'),
                        thumb = $('<li />').addClass('span2').css('position', 'relative'),
                        link = $('<a />').addClass('thumbnail').attr({
                            href: thumbUrl,
                            target: 'blank'
                        }).css('height', '190px'),
                        item = $('<img />').attr({
                            src: thumbUrl,
                            alt: 'Imagem não encontrada'
                        }),
                        deleterTextDefault = 'Excluir…',
                        deteterText = $('<span/>').text(''),
                        deleter = $('<a href="javascript:void(0);" />').addClass('deleter hide').css({
                            'position': 'absolute',
                            'bottom': '0',
                            'text-decoration': 'none'
                        });

                    if (thumbnails.length === 0) {
                        thumbnails = $('<ul />').addClass('thumbnails');
                        thumbnails.append(thumb.clone().hide());
                        thumbsContainer.append(thumbnails);
                    }

                    link.append(item);
                    thumb.append(link);
                    thumbnails.append(thumb);

                    if (fileElement.is(':not(:disabled)')) {
                    
                        var linkHoverIn = function () {
                            deleter.fadeIn();
                        };

                        var linkHoverOut = function () {
                            deleter.fadeOut();
                            deleter.html($('<i />').addClass('icon icon-trash'));
                            deleter.append(deteterText);
                            deteterText.text(deleterTextDefault);
                        };

                        link.hover(linkHoverIn, linkHoverOut);

                        linkHoverOut();

                        deleter.click(function (event) {
                            var yes = $('<a href="javascript:void(0);" />').css('text-decoration','none').html('<span class="label label-success">Sim</span>'),
                                no = $('<a href="javascript:void(0);" />').css('text-decoration','none').html('<span class="label">Cancelar</span>');

                            deteterText.text('Confirmar?');
                            deteterText.append('<br />', yes, ' ou ', no);

                            yes.focus();
                            yes.click(function(event) {
                                $.get(deleteUrl, function (result) {
                                    _callbacks.fileDeleted(index, thumbUrl, result);
                                    thumb.fadeOut();
                                    _filesCount--;
                                    no.trigger('click');
                                });
                                event.stopPropagation();
                            });

                            no.click(function(event) {
                                deteterText.text(deleterTextDefault);
                                yes.remove();
                                no.remove();
                                event.stopPropagation();
                            });

                            event.stopPropagation();
                        });

                        link.append(deleter);
                    }
                };

                thumbUrl += resultData[index];
                deleteUrl += index;
                deleteUrl += '/';
                deleteUrl += resultData[index];
                decorator[_getOptions('style')](
                    resultData[index],
                    thumbUrl,
                    deleteUrl,
                    _getOptions('thumbnailsContainer'),
                    _getOptions('fileElement')
                );
            },

            validateAddedFile: function (file) {
                _getOptions('debugNoise') && console.log(_debugNoiseLabel + 'validateAddedFile', file);

                var allowedTypes = [],
                    allowedExtensions = [],
                    extension = file.name.substr(file.name.lastIndexOf('.')).replace('.', '');

                $.each(_getOptions('fileTypes'), function (ext, type) {
                    if ($.inArray(ext, allowedExtensions) < 0) {
                        allowedExtensions.push(ext);
                    }
                    if ($.inArray(type, allowedTypes) < 0) {
                        allowedTypes.push(type);
                    }
                });

                if ($.inArray(file.type, allowedTypes) < 0) {
                    _callbacks.addFileError('Tipo "' + file.type + '" do arquivo "' + file.name + '" inválido.', file);
                    return false;
                }

                if ($.inArray(extension, allowedExtensions) < 0) {
                    _callbacks.addFileError('Extensão "' + extension + '" do arquivo "' + file.name + '" inválida.', file);
                    return false;
                }

                if (file.size >= _getOptions('maxSize')) {
                    _callbacks.addFileError('Tamanho do arquivo "' + file.name + '" inválido.', file);
                    return false;
                }

                if (_getOptions('maxFiles') > 0 && _filesCount >= _getOptions('maxFiles')) {
                    _callbacks.addFileError('Quantidade de arquivos excedida.', file);
                    return false;
                }
                _filesCount++;
                return true;
            },

            filesUploaded: function (result) {
                _getOptions('debugNoise') && console.log(_debugNoiseLabel + 'filesUploaded', result);

                var resultData = {},
                    thumbUrl = _getOptions('thumbsUrl'),
                    index = _getOptions('uploadedThumbKey');

                if (typeof result === 'object' && result.hasOwnProperty('data')) {
                    resultData = result.data;
                }

                if (!resultData.hasOwnProperty(index)) {
                    _fail('Informe o "uploadedThumbKey" correto.', resultData);
                }

                thumbUrl += resultData[index];
                _callbacks.fileUploaded(resultData[index], thumbUrl, resultData);
                _actions.appendThumb(resultData);
            }
        };

        /**
         * @return {undefined}
         */
        _fail = function () {
            var args = [].slice.apply(arguments),
                error = args.shift();
            _getOptions('debugNoise') && console.error(_debugNoiseLabel + '): Fail!', error, args);
            if (typeof _callbacks.error === 'function') {
                _callbacks.error.apply(null, [{
                    error: error,
                    args: args
                }]);
            } else {
                throw new Error('[SAFUploaderWithThumbs Fail] ): ' + error);
            }
        };

        /**
         * @param  {string} what
         * @return {mixed}
         */
        _getOptions = function (what) {
            if (_options.hasOwnProperty(what)) {
                return _options[what];
            }
            _fail('"' + what + '" not found in "_options"');
        };

        /**
         * @return {undefined}
         */
        _initializeUpload = function () {
            _getOptions('debugNoise') && console.log(_debugNoiseLabel + '… _initializeUpload', _getOptions('fileElement'));
            var fileElement = _getOptions('fileElement'),
                fileTypes = _getOptions('fileTypes'),
                fileTypeList = [],
                acceptTypes = '',
                key;

            for (key in fileTypes) {
                if (fileTypes.hasOwnProperty(key)) {
                    fileTypeList.push(fileTypes[key]);
                }
            }
            acceptTypes = fileTypeList.join(',');

            _getOptions('debugNoise') && console.log(_debugNoiseLabel + 'acceptTypes', acceptTypes);

            fileElement.attr('accept', acceptTypes);

            fileElement.fileupload({
                url: _getOptions('uploadUrl'),
                dataType: 'json',
                add: function (event, uploadData) {
                    _getOptions('debugNoise') && console.log(_debugNoiseLabel + 'fileupload add event', event);
                    if (uploadData.hasOwnProperty('files')) {
                        uploadData.files.forEach(function (file) {
                            if (file instanceof File) {
                                if (_actions.validateAddedFile(file)) {
                                    uploadData.process().done(function () {
                                        uploadData.submit();
                                    });
                                }
                            } else {
                                _fail('"File" é inválido', file);
                            }
                        });
                    } else {
                        _fail('"Files" não encontrado no upload');
                    }
                },
                always: function (event, uploadData) {
                    _getOptions('debugNoise') && console.log(_debugNoiseLabel + 'fileupload always event', event);
                    if (uploadData.hasOwnProperty('result')) {
                        _actions.filesUploaded(uploadData.result);
                    } else {
                        _fail('"Result" não encontrado no upload');
                    }
                }
            });
        };

        /**
         * @return {undefined}
         */
        _initializeThumbs = function () {
            _getOptions('debugNoise') && console.log(_debugNoiseLabel + '… _initializeThumbs', _getOptions('thumbnailsContainer'));
            _getOptions('thumbnailsContainer').empty();
            _filesCount = 0;
        };

        _getOptions('debugNoise') && console.log(_debugNoiseLabel + '😀 Iniciando…', _options);

        return (function initClosure() {
            if (!_getOptions('fileElement').length) {
                _fail('Informe a opção "fileElement".');
            }

            return {
                init: function () {
                    _initializeUpload();
                    _initializeThumbs();
                },
                registerEvent: function (event, callback) {
                    _getOptions('debugNoise') && console.log(_debugNoiseLabel + 'Registrando o callback de "' + event + '"');
                    _callbacks[event] = callback;
                },
                appendThumb: _actions.appendThumb
            };
        }());
    };

}(jQuery, window));