/**
 * Manipulacao de dados no lado do cliente
 *
 * @author J. Augusto <augustowebd@gmail.com>
 * */
;(function( window, $, undefined ) {

/**
 * @param string <nome da "base" criada para o armazenamento>
 * */
SIALLocalPersist = function (scope)
{
    /* constantes de identificacao dos eventos utilizados  neste componete */
    SIALLocalPersist.evt_save        = 'SIALLocalPersist_evt_save';
    SIALLocalPersist.evt_before_save = 'SIALLocalPersist_evt_before_save';
    SIALLocalPersist.evt_after_save  = 'SIALLocalPersist_evt_after_save';

    SIALLocalPersist.evt_select        = 'SIALLocalPersist_evt_select';
    SIALLocalPersist.evt_before_select = 'SIALLocalPersist_evt_before_select';
    SIALLocalPersist.evt_after_select  = 'SIALLocalPersist_evt_after_select';

    SIALLocalPersist.evt_delete        = 'SIALLocalPersist_evt_delete';
    SIALLocalPersist.evt_before_delete = 'SIALLocalPersist_evt_before_delete';
    SIALLocalPersist.evt_after_delete  = 'SIALLocalPersist_evt_after_delete';

    SIALLocalPersist.evt_truncate        = 'SIALLocalPersist_evt_truncate';
    SIALLocalPersist.evt_before_truncate = 'SIALLocalPersist_evt_before_truncate';
    SIALLocalPersist.evt_after_truncate  = 'SIALLocalPersist_evt_after_truncate';

    /**
     * auto referência
     * */
    var that = this;

    /**
     * separador usado para delimitar o prefixo do nome da entrada
     *
     * @type string
     */
    var separator = "]§[";

    var prefix;

    /**
     * registra uma entrada
     *
     * @param string name
     * @param primiteType value
     * @event evt_before_save, evt_save, evt_after_save
     * */
    this.save = function (name, value) {
        var name = _normalize(name);

        /* fire event: before save */
        $(that).trigger(SIALLocalPersist.evt_before_save);

        /* fire event: save */
        sessionStorage.setItem(name, value);
        $(that).trigger(SIALLocalPersist.evt_save);

        /* fire event: after save */
        $(that).trigger(SIALLocalPersist.evt_after_save);
    };

    /**
     * recupera, previsamente, entrada
     *
     * @param string name
     * @event evt_before_delete, evt_delete, evt_after_delete
     * */
    this.select = function (name) {
        var name = _normalize(name);
        var lvalue;

        /* fire event: before select */
        $(that).trigger(SIALLocalPersist.evt_before_select);

        /* fire event: select */
        lvalue = sessionStorage.getItem(name);
        $(that).trigger(SIALLocalPersist.evt_select);

        /* fire event: after select */
        $(that).trigger(SIALLocalPersist.evt_after_select);

        return lvalue;
    };

    /**
     * exclui entrada
     *
     * @param string name
     * @event evt_before_delete, evt_delete, evt_after_delete
     * */
    this.delete = function (name) {
        var name = _normalize(name);

        /* fire event: before delete */
        $(that).trigger(SIALLocalPersist.evt_before_delete);

        /* fire event: delete */
        lvalue = sessionStorage.getItem(name);
        $(that).trigger(SIALLocalPersist.evt_delete);

        /* fire event: after delete */
        $(that).trigger(SIALLocalPersist.evt_after_delete);
    };

    /**
     * exclui entrada
     *
     * @param string name
     * @event evt_before_truncate, evt_truncate, evt_after_truncate
     * */
    this.truncate = function () {

        /* fire event: before truncate */
        $(that).trigger(SIALLocalPersist.evt_before_truncate);

        for (var i = 0; i < sessionStorage.length; i++) {
          var name = sessionStorage.key(i);
          if (name.split(separator)[0] == prefix) {

                $(that).trigger(SIALLocalPersist.evt_truncate);
                localStorage.removeItem(name);
          }
        }
        $(that).trigger(SIALLocalPersist.evt_after_truncate_truncate);
    };

    /**
     * @return Object
     * */
    this.toObject = function () {
        var tmp = {};

        for (var i = 0; i < sessionStorage.length; i++) {

            var name = sessionStorage.key(i);

            if (name.split(separator)[0] != prefix) {
                continue;
            }

            var lvalue = sessionStorage.getItem(name);
            tmp[_unnormalize(name)] = lvalue;
        }

        return tmp;
    }

    var _normalize = function (name) {
        return prefix + separator + name;
    }

    var _unnormalize = function (name) {
        return name.split(separator).slice(1);
    }

   /**
    * @param string <nome da "base" criada para o armazenamento>
    * */
    var __construct = function (scope) {

        if (undefined === scope) {
            throw {"name": "UserException", "message": "invalid parameter"};
        }

        prefix = scope;
    }; __construct(scope);
};
})( window, jQuery);