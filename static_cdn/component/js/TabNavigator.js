/**
* Manipulador de navegacao em abas do SIMAC
*
* o elemento informado precisa ter
* */
;(function( window, $, undefined ) {
// Escopo da função anonima.
TabNavigator = function (strClass)
{
    /**
    * referencia do objeto principal
    * */
    var that = this;

    /**
    * armazena os eventos do objeto
    *
    * @var object
    * */
    var events = {};

    /**
    * opcoes da tabnav
    * */
    var options;

    /**
    * divs form
    * */
    var contents;

    /**
     * aponta para aba corrente
     * */
    var current;

    /**
    * ativa o elemento informado
    * */
    var active = function ($elm) {
        var content = '.' + $elm.attr('id');
        desactive();
        $(content).addClass('active');
        $elm.addClass('active');
    };

    /**
    * remove o status de ativo de todas as abas
    * */
    var desactive = function () {
        $(contents).each(function () {
            $(this).removeClass('active');
        });
        $(options).each(function () {
            $(this).removeClass('active');
        });
    };

    /**
    * agenda ativacao da guia clicada
    * */
    var bind = function () {
        $(options).each(function () {
            $(this).click(function(event){
                event.preventDefault();
                var target = $(this);

                /* garante disparo do evento apenas se as abas
                 * envolvidas forem diferentes
                 * */
                if (current.attr('id') == target.attr('id')) {
                    return;
                }

                current = target;

                $(that).trigger('evt_before_change');
                $(that).trigger('evt_change');
                active(target);
                $(that).trigger('evt_after_change');
            });
        });
    };

    /**
    * ativa aba anterior
    * */
    var backward = function () {
        for(var pos = 0; pos < options.length; pos++) {
            if ($(options[pos]).hasClass('active') && pos > 0 ) {
                active($(options[pos - 1]));
                return true;
            }
        }
        return false;
    };

    /**
    * ativa aba posterior
    * */
    var forward = function () {
        for (var pos = 0; options.length > pos; pos++) {
            if ($(options[pos]).hasClass('active') && (pos + 1) < options.length) {
                active($(options[pos + 1]));
                return true;
            }
        }
        return false;
    };

    /**
     * retorna referencia da aba selecionada
     * */
    this.selected = function () {
        return options.parent().find('.active');
    };

    /* @todo implementar forma mais otimizada para fazer isto */
    this.isFirst = function () {

        for(var pos = 0; pos < options.length; pos++) {
            if ($(options[pos]).hasClass('active') && pos === 0) {
                return true;
            }
        }

        return false;
    };

    /* @todo implementar forma mais otimizada para fazer isto */
    this.isLast = function () {
        var last = options.length - 1;

        for(var pos = 0; pos < options.length; pos++) {
            if ($(options[pos]).hasClass('active') && pos == last) {
                return true;
            }
        }

        return false;
    };

    /**
    * mostra aba anterior
    * */
    this.prev = function () {
        /* fire event: before change */
        $(that).trigger('evt_before_change');
        /* fire event: before prev */
        $(that).trigger('evt_before_prev');
        /* fire event: change */
        $(that).trigger('evt_change');
        /* fire event: prev */
        $(that).trigger('evt_prev');
        /* alterna para aba anterior */
        if (!backward()) { return; }
        /* fire event: after change */
        $(that).trigger('evt_after_change');
        /* fire event: after prev */
        $(that).trigger('evt_after_prev');
        /* retorna referencia do objeto */
        return that;
    };

    /**
    * mostra proxima aba
    * */
    this.next = function () {
        /* fire event: before change */
        $(that).trigger('evt_before_change');
        /* fire event: before prev */
        $(that).trigger('evt_before_next');
        /* fire event: change */
        $(that).trigger('evt_change');
        /* fire event: next */
        $(that).trigger('evt_next');
        /* altera para proxima aba */
        if (!forward()) {
            return;
        }
        /* fire event: after change */
        $(that).trigger('evt_after_change');
        /* fire event: after next */
        $(that).trigger('evt_after_next');
        return that;
    };

    /**
    * pseudo construtor
    * */
    var __construct = function (strClass) {
        var pattern = '.' + strClass + ' li';
        options = $(pattern);
        contents = $('.tab-pane');
        current = that.selected();
        bind();
        $(that).trigger('evt_read');
    }; __construct(strClass);
};
})( window, jQuery);