var Uploader = function (options) {

    'use strict';

    var _settings,
        _mySettings,
        _myFilters,
        _eventsCallback,
        _events,
        _modes,
        _element,
        _max_file_size,
        _triggerEventCallback = function (event, args) {
            if (typeof _eventsCallback[event] === 'function') {
                _eventsCallback[event].apply(args[0], args);
            }
        },
        _fail = function (message) {
            var error = '[Uploader] :( Xi, acho que você cometeu um engano... ';
            error += message;
            throw new Error(error);
        },
        _debugNoise = function () {
            if (_settings.debugNoise) {
                if (!console.warn) {
                    _fail('Habilite o console.warn para o debugNoise funcionar');
                }
                var args = [].slice.apply(arguments);
                args.unshift('<UPDATER debug noise>');
                console.warn.apply(console, args);
            }
        };

    /* default value */
    _max_file_size = '10mb';

    _settings = {
        selector: '',
        mode: null,
        url: '',
        filters: [],
        max_file_size: null,
        debugNoise: false
    };

    _mySettings = {
        runtimes: 'html5,flash,silverlight,html4',
        flash_swf_url: "/js/library/plupload/plupload.flash.swf",
        silverlight_xap_url: "/js/library/plupload/plupload.silverlight.xap",
        chunk_size: '1mb',
        unique_names: true
    };

    _myFilters = {
        list: [{
            title: "Arquivos de imagem",
            extensions: "jpg,jpeg,png,gif"
        },{
            title: "Arquivo PDF",
            extensions: "pdf"
        }],
        standAlone: [{
            title: "Arquivo PDF",
            extensions: "pdf"
        }]
    };

    _eventsCallback = {};

    _events = {
        BeforeUpload: function (uploader, file) {
            _debugNoise('event: BeforeUpload (uploader, file)', uploader, file);
            _triggerEventCallback('BeforeUpload', arguments);
        },

        UploadProgress: function (uploader, file) {
            _debugNoise('event: UploadProgress (uploader, file)', uploader, file);
            _triggerEventCallback('UploadProgress', arguments);
        },

        FilesAdded: function (uploader, files) {
            _debugNoise('event: FilesAdded (uploader, files)', uploader, files);
            _triggerEventCallback('FilesAdded', arguments);
        },

        FilesRemoved: function (uploader, files) {
            _debugNoise('event: FilesRemoved (uploader, files)', uploader, files);
            _triggerEventCallback('FilesRemoved', arguments);
        },

        FileUploaded: function (uploader, file, info) {
            _debugNoise('event: FileUploaded(uploader, file, info)', uploader, file, info);
            if (typeof info === 'object') {
                if (info.hasOwnProperty('response')) {
                    try {
                        var response = $.parseJSON(info.response);
                        if (response.error) {
                            _events.Error(
                                uploader,
                                $.extend(response.error, {file: file})
                            );
                        }
                    } catch (error) {
                        _events.Error(
                            uploader,
                            $.extend(error, {file: file, message: info.response})
                        );
                    }
                }
            }
            _triggerEventCallback('FileUploaded', arguments);
        },

        UploadComplete: function (uploader, files) {
            _debugNoise('event: UploadComplete (uploader, files)', uploader, files);
            _triggerEventCallback('UploadComplete', arguments);
        },

        Error: function (uploader, error) {
            _debugNoise('event: Error (uploader, error)', uploader, error);
            _triggerEventCallback('Error', arguments);
        }
    };

    _modes = {
        list: function (container) {
            if (!container.length) {
                var error = 'Seletor do container é inválido ou o elemento não existe no DOM. ';
                _fail(error);
            }
            container.append($('<p />').text(
                'Ops, alguma coisa errada aconteceu ou o seu navegador não tem suporte para HTML5, Flash ou Silverlight. Que pena :('
            ));

            var __options = $.extend(_mySettings, {
                url: _settings.url,
                max_file_size: _settings.max_file_size ? _settings.max_file_size: _max_file_size,
                dragdrop: true,
                init: _events,
                filters: _settings.filters.length ? _settings.filters : _myFilters.list
            });

            _debugNoise('Options LIST mode', __options);

            container.pluploadQueue(__options);

            $('.plupload_header', container).remove();
            $('.plupload_container', container).removeAttr('title');

            $('.plupload_container').on('click', '.plupload_file_action a', function () {
                return false;
            });

            return this;
        },

        standAlone: function (field) {
            if (!field.length) {
                var error = 'Seletor do elemento é inválido ou o mesmo não existe no DOM. ';
                _fail(error);
            }

            var __options = $.extend(_mySettings, {
                browse_button: field.attr('id'),
                url: _settings.url,
                multi_selection: false,
                init: _events,
                filters: _settings.filters.length ? _settings.filters : _myFilters.standAlone
            });

            _debugNoise('Options LIST mode', __options);

            var __uploader = new plupload.Uploader(__options);
            __uploader.init();

            return this;
        }
    };

    function UploaderInstance(options) {
        var option;
        for (option in _settings) {
            if (_settings.hasOwnProperty(option)) {
                if (options.hasOwnProperty(option)) {
                    _settings[option] = options[option];
                }
            }
        }
        this._events = Object.keys(_events);
    }

    UploaderInstance.prototype = {

        get events() {
            return this._events;
        },

        set events(value) {
            this._events = value;
        },

        addEventListener: function (event, callback) {
            if (typeof event === 'object') {
                var evt;
                for (evt in event) {
                    if (event.hasOwnProperty(evt)) {
                        this.addEventListener(evt, event[evt]);
                    }
                }
            } else {
                if (_events.hasOwnProperty(event)) {
                    _debugNoise('Ajustando o evento "' + event + '".');
                    _eventsCallback[event] = callback;
                } else {
                    var error = 'Evento "' + event + '" inválido. ';
                    error += 'Eventos disponíveis: ' + this._events.join(', ');
                    _fail(error);
                }
            }
        },

        init: function () {
            if (!_modes.hasOwnProperty(_settings.mode)) {
                var error = 'Opção {"mode" : "' + _settings.mode + '"} inválida. ';
                error += 'Tente talvez as opções válidas: ';
                error += '"' + Object.keys(_modes).join('", "') + '"';
                _fail(error);
            }
            _element = $(_settings.selector);
            return _modes[_settings.mode].bind(this)(_element);
        }
    };

    return new UploaderInstance(options);
};