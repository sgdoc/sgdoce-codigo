
(function ($) {
    var cl = function () { return console.log.apply(console, arguments); }

    if (!String.prototype.format) {
      String.prototype.format = function() {
        var args = arguments;
        return this.replace(/{(\d+)}/g, function(match, number) {
          return typeof args[number] != 'undefined'
            ? args[number]
            : match
          ;
        });
      };
    }

    var defaultOtions = {

        name: "combo-input",

        placeholder: "Informe o filtro",

        parser: "html",

        /*
         * dados iniciais do combo
         *
         * @var object[]
         * */
        comboInitData: [],

        /*
         * inicializar o conteudo da URL caracteriza o uso
         * de ajax para buscar os dados iniciais do combo
         */
        comboWebServer: {
                   url: null,
           requestType: "get",
              dataType: "json",
             parameter: null,
               onError: null,
                onDone: null
        },

        isRequired: false,

        message: {
            requestError: "Houve um erro ao processar a requisão",
            requeried: "O conte",
            loading: "Carregando...",
        },

        /* left, right, top, down */
        errorMessagePlace: "right",

        beforeRender: function () {},

        afterRender: function () {},

        /* disparado sempre que o combo mudar seu valor */
        onComboChange: function () {},

        /* disparado sempre que o campo textto sobre um keypress */
        onTextKeyUp: function () {}
    };

    var methods = {

        ON_BEFORE_RENDER: 'combo-input-before-render',

        ON_AFTER_RENDER: 'combo-input-after-render',

        ON_BEFORE_CREATE: 'combo-componente-elements-before-create',

        ON_AFTER_CREATE: 'combo-componente-elements-after-create',

                 id: 'combo-input-' + Math.random(0, 999).toString().split(".")[1],

        listElement: null,

        inputElement: null,

        buttonCombo: null,

        display: null,

        displayDefaultText: 'Selecione...',

        settings: {},

        init: function (options) {

            methods.settings = $.extend(defaultOtions, options);

            return this.each(function () {

                /* cria os elementos */
                methods.create();

                /* popula a lista */
                methods.loadData();

                /* renderiza o componete */
                methods.render(this);
            });
        },

        create: function () {

            var that = this;

            $(document).trigger( this.ON_BEFORE_CREATE );

            this.listElement  = $('<ul class="dropdown-menu list">');

            this.inputElement = $('<input type="text" placeholder="' + this.settings.placeholder + '">');

            this.buttonCombo  = $('<button class="btn dropdown-toggle" data-toggle="dropdown">').append('<span class="caret"></span>');
            this.display      = $('<button type="button" class="btn" tabindex="-1">' + this.settings.displayDefaultText + '</button>');

            this.inputElement.keyup(function (event){

                that.settings.onTextKeyUp({
                    char: event.key,
                    code: event.charCode,
                   value: that.inputElement.val()
                }, that);
            });

            $(document).trigger( this.ON_AFTER_CREATE );
        },

        loadData: function () {
            var t  = this;
            var s  = this.settings;

            if (! s.comboWebServer.url) {
                t.loadCombo(s.comboInitData);
                return;
            }

            /* objeto de requisicao */
            var reqCfg = {
                    url: s.comboWebServer.url,
               dataType: s.comboWebServer.dataType,
                   type: s.comboWebServer.requestType,
                  cache: s.comboWebServer.cache ? s.comboWebServer.cache : false
            };

            if (s.comboWebServer.parameter) {
                reqCfg.data = s.comboWebServer.parameter;
            }

            var jqxhr = $.ajax(reqCfg);

            jqxhr.error(s.comboWebServer.onError);

            if (s.comboWebServer.done) {
                jqxhr.done = function (result) {
                    s.comboWebServer.onError.apply(this, result);
                }
            }

            jqxhr.done(function (result) {
                t.loadCombo(result);
            });
        },

        /*
         * o formato esperado é um array de objetos ['value', 'text']
         * @param object[]
         * */
        loadCombo: function (data) {
            var t  = this;
            // var s  = this.settings;
            // var sc = s.comboWebServer;

            var cmb = t.listElement;

            for (var o in data) {

                var option = $('<li>').click(function () { t.change(this); });

                var strAnchor = '<a href="javascript:;" data-value="{0}">{1}</a>'.format(data[o].value, data[o].text);

                var anchor = $(strAnchor);

                cmb.append(option.append(anchor));
            };
        },

        change: function (elm) {

            var elm    = $(elm);
            var anchor = elm.find('a');

            anchor.closest('ul').find('li').each(function (idx, elm) {
                $(elm).removeData("selected");
            });

            elm.data("selected", true);

            this.inputElement.focus();

            this.display.html(anchor.html());
        },

        getType: function () {

            var $selected = $('li', this.listElement).filter(function() {
              return $(this).data("selected")
            }).find('a');

            return {
                comboValue: $selected.data('value'),
                comboText: $selected.html()
            };
        },

        getData: function () {
            var data = this.getType();
                data.textValue = this.inputElement.val();

            return data;
        },

        /*
         * @param HTMLElement container
         * */
        render: function (cnt) {
            var group = $('<div class="btn-group" id="{0}">'.format(this.settings.id))
                        .append(this.display, this.buttonCombo, this.listElement)
                        ;

            $(cnt).addClass('input-prepend form-inline')
                  .append(group, this.inputElement)
                  ;
        },

        isFunction: function (fnc) {
            return "function" === typeof fnc;
        }
    };

    $.fn.comboInput = function(method) {
        if (methods[ method ]) {
            return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof method === 'object' || ! method ) {
            return methods.init.apply( this, arguments );
        } else {
            $.error('Method ' + method + 'doest not exist on jQuery.comboInput');
        }
    };

})(jQuery);