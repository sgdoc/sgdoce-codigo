/**
 *
 * A simple wrap to Typeahead's Bootstrap consuming
 * the quick, elastic and cached searched items to autocomplete
 *
 * @requires jQuery ($)
 * @requires Bootstrap v2.1.1 (CSS, Typeahead)
 *
 * @example
 * // instanciando o componente
 * var autocomplete = new QuickCompleter(
 *     // usando o DSN para configuração
 *     QuickCompleter.DSN({
 *         input: '#testeAutocomplete', // seletor ou objeto jQuery
 *         type: 'municipio', // tipo disponível
 *         env: '<?php echo APPLICATION_ENV; ?>' // configuração de ambiente
 *     })
 * );
 * // registrando o callback de erro
 * autocomplete.onError( function (error) {
 *     console.error( 'deu erro no autocomplete', error );
 * });
 * // registrando o callback de selecionado
 * autocomplete.onSelect( function (item) {
 *     console.info( 'Selecionou o item:', item );// {text:'xxx', value:999, sq_estado:999}
 * });
 * // iniciando o componente
 * autocomplete.init();
 *
 */

/*jslint unparam: false*/
(function (global, $, undefined) {
    'use strict';
    /*jslint unparam: true*/

    /**
     *
     *
     * @param {QuickCompleter.DSN}
     *
     * @return {Object}
     */
    var QuickCompleterClosure = function (DSN) {

        /**
         * @type {Object}
         */
        var _options = DSN.parse().getOptions();

        /**
         * @type {Object}
         */
        var _callbacks = {
            onError: null,
            onSelect: null
        };

        /**
         * @type {Function}
         */
        var _debugNoise,
            _fail,
            _get,
            _validation,
            _decoration;

        /**
         * @return {undefined}
         */
        _debugNoise = function () {
            if (_get('inputElement').data('quick-completer-debug-noise')) {
                var args = [].slice.apply(arguments);
                args.unshift('[quick-completer debug noise]');
                args.unshift((new Date()).toString());
                console.debug.apply(console, args);
            }
        };

        /**
         * @param {string} error
         * @return {undefined}
         */
        _fail = function (error) {
            _debugNoise('): Ops!', arguments);
            if (typeof _callbacks.onError === 'function') {
                var args = [].slice.apply(arguments);
                args.unshift(error);
                _callbacks.onError.apply(null, args);
            }
            throw new Error(error);
        };

        /**
         * @param  {string} what
         * @return {mixed}
         */
        _get = function (what) {
            if (_options.hasOwnProperty(what)) {
                return _options[what];
            }
            _fail('"' + what + '" not found in "_options"');
        };

        /**
         * @return {undefined}
         */
        _validation = function () {
            if (typeof _get('inputElement') !== 'object' || !_get('inputElement').jquery) {
                _fail('Opção "inputSelector" inválida!');
            }

            if (typeof _get('hiddenElement') !== 'object' || !_get('hiddenElement').jquery) {
                _fail('Opção "hiddenSelector" inválida!');
            }

            if (typeof _get('inputSelector') !== 'string') {
                _fail('Opção "inputSelector" inválida!');
            }

            if (typeof _get('hiddenSelector') !== 'string') {
                _fail('Opção "hiddenElement" inválida!');
            }

            if (_get('inputElement').attr('type') !== 'text') {
                _fail('Informe o elemento "<input type="text" />"');
            }

            if (_get('hiddenElement').attr('type') !== 'hidden') {
                _fail('Informe o elemento escondido "<input type="hidden" />"');
            }

            var i,
                extraValueField;
            for (i in _get('extraValuesFields')) {
                if (_get('extraValuesFields').hasOwnProperty(i)) {
                    extraValueField = _get('extraValuesFields')[i];
                    if (typeof extraValueField !== 'object' || !extraValueField.jquery) {
                        _fail('Opção "extraValuesFields" inválida!');
                    }
                }
            }
        };

        /**
         * @param {string} error
         * @return {undefined}
         */
        _decoration = function () {
            var decorator = {},
                url = function (query) {
                    var uri = _get('url').protocol + '://' + _get('url').host + ':' + _get('url').port + '?',
                        params = {
                            _stoken: _get('aclToken'),
                            _method: "get",
                            _command: "_search",
                            _qparam: {
                                q: query,
                                l: _get('itemsToDisplay'),
                                i: _get('schemaToSearch'),
                                dt: _get('tableToSearch'),
                                of: _get('resultFields')
                            }
                        };
                    _debugNoise('Vai requisitar', uri, params);
                    return uri + JSON.stringify(params);
                };

            //@todo: implementar outros estilos...
            //       decorator['style'] = function () {/*...*/};
            decorator['bootstrap-v2.x'] = function () {
                var __items = {},

                    __abortText = 'quick-completer-controled-abortation',

                    __timeout,

                    __ajax,

                    __getHelpInline = function () {
                        var helpInline = _get('inputElement').siblings('.help-inline:first');
                        if (!helpInline.length) {
                            helpInline = $('<span />').addClass('help-inline');
                            _get('inputElement').parent().append(helpInline);
                        }
                        return helpInline;
                    },

                    __clearValues = function () {
                        var i,
                            extraValueField;
                        for (i in _get('extraValuesFields')) {
                            if (_get('extraValuesFields').hasOwnProperty(i)) {
                                extraValueField = _get('extraValuesFields')[i];
                                extraValueField.val('');
                            }
                        }
                        _get('hiddenElement').val('');
                        __getHelpInline().empty();
                        _get('inputElement').data('quick-completer-selected-value', '');
                        _get('inputElement').data('quick-completer-selected-text', '');
                        _get('inputElement').parents('.control-group').removeClass('error warning');
                    },

                    __selected = function (key) {
                        var text = __items[key][_get('textKey')];
                        var value = __items[key][_get('valueKey')];

                        _debugNoise('Selecionado', text, value);

                        _get('inputElement').data('quick-completer-selected-text', text);
                        _get('inputElement').data('quick-completer-selected-value', value);

                        _get('inputElement').val(text);
                        _get('hiddenElement').val(value);

                        var i,
                            extraValueField;
                        for (i in _get('extraValuesFields')) {
                            if (_get('extraValuesFields').hasOwnProperty(i)) {
                                extraValueField = _get('extraValuesFields')[i];
                                extraValueField.val(__items[key][i]);
                            }
                        }

                        if (typeof _callbacks.onSelect === 'function') {
                            _callbacks.onSelect.apply(null, [__items[key]]);
                        }
                    },

                    __beforeProcessResult = function () {
                        _debugNoise('Antes de processar o resultado', _get('inputElement'), _get('hiddenElement'));

                        __clearValues();
                        // __getHelpInline().html('Carregando<span class="threeLittleDots">.</span><span class="threeLittleDots">.</span><span class="threeLittleDots">.</span>');
                        __getHelpInline().html('<div class="bubbling mini"><span></span><span></span><span></span></div>');
                    },

                    __afterProcessResult = function (total) {
                        _debugNoise('Depois de processar o resultado', _get('inputElement'), _get('hiddenElement'));

                        __clearValues();
                        if (!total && $.trim(_get('inputElement').val()) !== '') {
                            __getHelpInline().text('Nenhum registro encontrado');
                            _get('inputElement').parents('.control-group').addClass('warning');
                        }
                    },

                    __processFailure = function (message) {
                        __clearValues();
                        __getHelpInline().text(message);
                        _get('inputElement').parents('.control-group').addClass('error');
                        _fail(message);
                    },

                    __processResult = function (result) {
                        _debugNoise('Processando o resultado', result);
                        var visibleItems = [];
                        __items = {};

                        if (typeof result !== 'object' || !result) {
                            __processFailure('Erro ao receber a informação do autocomplete');
                        } else if (result.error) {
                            __processFailure(result.error);
                        } else if (!result.content) {
                            __processFailure('Erro na formatação do retorno da informação do autocomplete');
                        } else {
                            var data = result.content,
                                text,
                                index,
                                datum;

                            for (index in data) {
                                if (data.hasOwnProperty(index)) {
                                    datum = data[index];

                                    text = datum[_get('textKey')];

                                    __items[text] = datum;
                                    visibleItems.push(text);
                                }
                            }
                        }

                        _debugNoise('Itens do autocomplete', __items, visibleItems);

                        return {
                            visibleItens: visibleItems,
                            total: result.total || 0
                        };
                    },

                    __events = function () {
                        _debugNoise('Delegação de eventos', _get('inputElement'), _get('hiddenElement'));

                        var cleaner = function (eventMessage) {
                            var selectedText = $.trim(_get('inputElement').data('quick-completer-selected-text')),
                                selectedValue = $.trim(_get('inputElement').data('quick-completer-selected-value')),
                                inputText = $.trim(_get('inputElement').val()),
                                hiddenValue = $.trim(_get('hiddenElement').val());

                            _debugNoise(eventMessage, "\n\"selectedText\"", selectedText, '"selectedValue"', selectedValue, '"inputText"', inputText, '"hiddenValue"', hiddenValue);

                            if (hiddenValue === '' || selectedValue === '' || hiddenValue !== selectedValue || inputText !== selectedText) {
                                inputText = '';
                                _get('inputElement').val(inputText);
                                _debugNoise('Zera o valor do campo');
                            }

                            if (inputText === '') {
                                hiddenValue = '';
                                __clearValues();
                                _debugNoise('Zera o valor do campo oculto (e os extras, se tiver)');
                            }
                        };

                        _get('inputElement').bind({
                            blur: function () {
                                cleaner('Perdeu o foco');
                            },
                            focus: function () {
                                cleaner('Ganhou foco');
                            },
                            // change: function () {
                            //     cleaner('Mudou o valor');
                            // },
                            // paste: function () {
                            //     cleaner('Colou');
                            // },
                            // copy: function () {
                            //     cleaner('Copiou');
                            // },
                            // drop: function () {
                            //     cleaner('Jogou algo');
                            // },
                            // drag: function () {
                            //     cleaner('Arrastou algo');
                            // },
                        });
                    };

                //Typeahead config
                _get('inputElement').typeahead({
                    minLength: _get('minLengthToSearch'),
                    items: _get('itemsToDisplay'),
                    updater: function (item) {
                        __selected(item);
                        return item;
                    },
                    matcher: function () {
                        return true;
                    },
                    sorter: function (items) {
                        return items;
                    },
                    highlighter: function (item) {

                        var normalizer = function (text) {
                            var i,
                                normalized = text.toLowerCase(),
                                map = {
                                    'a': '[àáâãäå]',
                                    'e': '[èéêë]',
                                    'i': '[ìíîï]',
                                    'o': '[òóôõö]',
                                    'u': '[ùúûűü]',
                                    'y': '[ýÿ]',
                                    'c': 'ç',
                                    'n': 'ñ',
                                    '-': '–'
                                };

                            for (i in map) {
                                if (map.hasOwnProperty(i)) {
                                    normalized = normalized.replace(new RegExp(map[i], 'img'), i);
                                }
                            }

                            normalized = normalized.replace(/\s+/g, ' ');
                            return normalized;
                        };

                        try {
                            var itemNormalized = normalizer(item),
                                queryNormalized = normalizer(this.query),
                                start = itemNormalized.indexOf(queryNormalized);

                            if (start === -1) {
                                return '<span title="Resultado aproximado">' + item + '</span>';
                            }

                            var length = queryNormalized.length,
                                pattern = '(' + item.substr(start, length) + ')',
                                regexp = new RegExp(pattern, 'img');

                            return item.replace(regexp, '<strong>$1</strong>');

                        } catch (e) {
                            _debugNoise("(Destacador) ops: Exception!'", e, item);

                            return item;
                        }
                    },
                    source: function (query, process) {
                        query = $.trim(query);
                        if (query) {
                            __beforeProcessResult();

                            if (__timeout) {
                                _debugNoise('Reinicia o aguardo para procurar');
                                clearTimeout(__timeout);
                            }
                            if (__ajax) {
                                _debugNoise('Aborta a requisição anterior');
                                __ajax.abort(__abortText);
                            }
                            _debugNoise('Registrando o aguardo para procurar', query);
                            __timeout = setTimeout(function () {
                                _debugNoise('Procurando…', query);

                                __ajax = $.getJSON(url(query));

                                __ajax.success(function (result) {
                                    var processed = __processResult(result);
                                    __afterProcessResult(processed.total);
                                    process(processed.visibleItens);
                                });
                                __ajax.fail(function (xhr) {
                                    if (xhr.statusText !== __abortText) {
                                        __processFailure('Não foi possível recuperar as informações do servidor do autocomplete');
                                    } else {
                                        _debugNoise('Aborto controlado');
                                    }
                                });
                            }, _get('delayInMillisecondsToSearch'));
                        }
                        return [];
                    }
                });

                //Events
                __events();
            };

            decorator[_get('style')]();
        };

        /**
         * @return {undefined}
         */
        this.init = function () {
            var splash = function () {
                return "             _      _                      "
                    + "             _      _            \n"
                    + "  __ _ _   _(_) ___| | __      ___ ___  _ _"
                    + "_ ___  _ __ | | ___| |_ ___ _ __ \n"
                    + " \/ _` | | | | |\/ __| |\/ \/____ \/ __\/ _"
                    + " \\| '_ ` _ \\| '_ \\| |\/ _ \\ __\/ _ \\ '__|\n"
                    + "| (_| | |_| | | (__|   <_____| (_| (_) | | "
                    + "| | | | |_) | |  __\/ ||  __\/ |   \n"
                    + " \\__, |\\__,_|_|\\___|_|\\_\\     \\___\\_"
                    + "__\/|_| |_| |_| .__\/|_|\\___|\\__\\___|_|   \n"
                    + "    |_|                                    "
                    + "      |_|                        \n";
            };

            _validation();
            _decoration();

            _debugNoise("\n\n" + splash(), _options);
        };

        this.onSelect = function (callback) {
            _debugNoise('Registrando o callback de selecionado');
            _callbacks.onSelect = callback;
        };

        this.onError = function (callback) {
            _debugNoise('Registrando o callback de erro');
            _callbacks.onError = callback;
        };
    };

    /**
     * The DSN Object from Quick Completer
     *
     * @param {Object} options Object with
     *     Autocomplete input element
     *         {jQuery Object|string} "input" // 'selector' or jQuery('selector')
     *          or
     *         {jQuery Object}        "inputElement"
     *          or
     *         {string}               "inputSelector"
     *
     *     Autocomplete hidden element (if aready exists a input[type=hidden] and the ID is the same ID + "_hidden", is not necessary pass)
     *         {jQuery Object} "hiddenElement"
     *          or
     *         {string}        "hiddenSelector"
     *
     *     Type of search, consult quick-completer-config for mor information (available in data attribute)
     *         {string} "type" // available on <input data-quick-completer-type="..." />
     *
     *     Default configs (available in data attribute)
     *         {string}  "env"                         (default 'production')  // available on <input data-quick-completer-env="[development|tcti|hmg|homologacao|production]" />
     *         {integer} "minLengthToSearch"           (default: 3)            // available on <input data-quick-completer-min-length-to-search="..." />
     *         {integer} "delayInMillisecondsToSearch" (default: 500)          // available on <input data-quick-completer-delay-seconds-to-search="..." />
     *         {integer} "itemsToDisplay"              (default: 10)           // available on <input data-quick-completer-items-to-display="..." />
     *         {boolean} "debugNoise"                  (default: false)        // available on <input data-quick-completer-debug-noise="..." />
     *
     *     Default config
     *         {string} "style" (default: 'bootstrap-v2.x')
     *
     * @return {QuickCompleter.DNS}
     */
    QuickCompleterClosure.DSN = function (settings) {
        function DNSInstance(settings) {

            var _getConfig = function (config, index) {
                var cfg = QuickCompleterClosure.config[config][index];
                if (!cfg) {
                    throw new Error('Configuração "' + config + '=' + index + '" não encontrada no arquivo "component/quick-completer/quick-completer-config.js"');
                }
                return cfg;
            };

            var _defaults = {
                debugNoise: false,
                style: 'bootstrap-v2.x',
                input: '',
                inputElement: $(),
                inputSelector: '',
                hiddenElement: $(),
                hiddenSelector: '',
                schemaToSearch: '-',
                tableToSearch: '-',
                minLengthToSearch: 3,
                itemsToDisplay: 10,
                delayInMillisecondsToSearch: 500,
                textKey: 'text',
                valueKey: 'value',
                extraValues: [],
                extraValuesFields: {},
                resultFields: [],
                aclToken: '',
                env: 'production',
                url: {
                    protocol: '',
                    host: '',
                    port: 0
                }
            };

            var _options = {};

            var _parse = function () {
                _options = $.extend(_defaults, settings);

                _options.aclToken = _getConfig('acl', 'token');

                if (_options.env) {
                    _options.url.protocol = _getConfig('env', _options.env).protocol;
                    _options.url.host = _getConfig('env', _options.env).host;
                    _options.url.port = _getConfig('env', _options.env).port;
                    delete _options.env;
                }

                if (_options.input) {
                    var opt = {
                        'string' : function () {
                            _options.inputSelector = _options.input;
                        },
                        'object' : function () {
                            _options.inputElement = _options.input;
                        }
                    };
                    var type = typeof _options.input;
                    if (opt.hasOwnProperty(type)) {
                        opt[type]();
                    }
                    delete _options.input;
                }

                if (_options.type) {
                    var i,
                        extraValues = _getConfig('type', _options.type).extraValues;

                    for (i in extraValues) {
                        if (extraValues.hasOwnProperty(i)) {
                            _options.extraValues.push(extraValues[i]);
                        }
                    }

                    _options.schemaToSearch = _getConfig('type', _options.type).index;
                    _options.tableToSearch = _getConfig('type', _options.type).documentType;
                    _options.textKey = _getConfig('type', _options.type).textKey;
                    _options.valueKey = _getConfig('type', _options.type).valueKey;
                    delete _options.type;
                }


                if (_options.inputSelector.length && typeof _options.inputSelector === 'string') {
                    _options.inputElement = $(_options.inputSelector);
                }

                if (_options.hiddenSelector.length && typeof _options.hiddenSelector === 'string') {
                    _options.hiddenElement = $(_options.hiddenSelector);
                }

                if (_options.inputElement.length && typeof _options.inputElement === 'object') {
                    _options.inputSelector = _options.inputElement.selector;

                    var inputElement_data_minLengthToSearch = _options.inputElement.data('quick-completer-min-lenght-to-search');
                    var inputElement_data_delayInMillisecondsToSearch = _options.inputElement.data('quick-completer-delay-in-milliseconds-to-search');
                    var inputElement_data_itemsToDisplay = _options.inputElement.data('quick-completer-items-to-display');
                    var inputElement_data_schemaToSearch = _options.inputElement.data('quick-completer-schema-to-search');
                    var inputElement_data_tableToSearch = _options.inputElement.data('quick-completer-table-to-search');

                    if (inputElement_data_minLengthToSearch) {
                        _options.minLengthToSearch = inputElement_data_minLengthToSearch;
                    }
                    if (inputElement_data_delayInMillisecondsToSearch) {
                        _options.delayInMillisecondsToSearch = inputElement_data_delayInMillisecondsToSearch;
                    }
                    if (inputElement_data_itemsToDisplay) {
                        _options.itemsToDisplay = inputElement_data_itemsToDisplay;
                    }
                    if (inputElement_data_schemaToSearch) {
                        _options.schemaToSearch = inputElement_data_schemaToSearch;
                    }
                    if (inputElement_data_tableToSearch) {
                        _options.tableToSearch = inputElement_data_tableToSearch;
                    }
                }

                if (_options.hiddenElement.length && typeof _options.hiddenElement === 'object') {
                    _options.hiddenSelector = _options.hiddenElement.selector;
                } else {
                    var inputElement_id = _options.inputElement.attr('id');
                    _options.hiddenElement = $('#' + inputElement_id + '_hidden');
                }

                var inputElement_data_debugNoise = _options.inputElement.data('quick-completer-debug-noise');
                if (inputElement_data_debugNoise === undefined) {
                    _options.inputElement.data('quick-completer-debug-noise', _options.debugNoise);
                } else {
                    _options.debugNoise = inputElement_data_debugNoise;
                }

                _options.resultFields = _options.extraValues.slice(0);
                _options.resultFields.unshift(_options.valueKey);
                _options.resultFields.unshift(_options.textKey);

                if (_options.extraValues.length) {
                    var o,
                        extraValueField,
                        extraValuesFields = {};
                    for (o in _options.extraValuesFields) {
                        if (_options.extraValuesFields.hasOwnProperty(o)) {
                            extraValueField = _options.extraValuesFields[o];
                            if (typeof extraValueField === 'string') {
                                extraValueField = $(extraValueField);
                            }
                            extraValuesFields[o] = extraValueField;
                        }
                    }
                    _options.extraValuesFields = extraValuesFields;
                }
            };

            this.parse = function () {
                _parse();
                return this;
            };

            this.getOptions = function () {
                return _options;
            };
        }
        return new DNSInstance(settings);
    };

    global.QuickCompleter = QuickCompleterClosure;

}(window, jQuery));