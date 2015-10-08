/**
 * Validador de formulário dinâmico
 *
 * @author J. Augusto <augustowebd@gmail.com
 * @date 2015-04-27
 * @version 0.0.1
 * */
;DynamicFormValidator = function () {
    /* mapeamento de teclas */
    this.KEY_HOME      = 36;
    this.KEY_END       = 35;
    this.KEY_SPACE     = 32;
    this.KEY_DELETE    = 46;
    this.KEY_ESC       = 27;
    this.KEY_ENTER     = 13;
    this.KEY_BACKSPACE = 8;
    this.KEY_TAB       = 9;
    this.KEY_LEFT      = 37;
    this.KEY_UP        = 38;
    this.KEY_RIGHT     = 39;
    this.KEY_DOWN      = 40;

    /* expressao de validacao dos tipos */
    this.er_validator = {
        'alfanu'   : /^[\w]+$/,
        'date'     : /^((0[1-9]|[12]\d)\/(0[1-9]|1[0-2])|30\/(0[13-9]|1[0-2])|31\/(0[13578]|1[02]))\/\d{4}$/,
        'email'    : /^[\w-]+(\.[\w-]+)*@(([\w-]{2,63}\.)+[A-Za-z]{2,6}|\[\d{1,3}(\.\d{1,3}){3}\])$/,
        'integer'  : /^\d+$/,
        'money'    : /^[\-\+]?\d{1,3}(\.\d{3})*\,\d{2}$/,
        'time12'   : /^(0[1-9]|1[0-2]):[0-5]\d$/,
        'time24'   : /^([0-1]\d|2[0-3]):[0-5]\d$/
    };

    this.has_error = false;

    /* define como padrão a necessidade de sinalizar o campo com erro */
    this.highlight_on_error = true;

    /* marcará todos os elementos que contenham a classe abaixo */
    this.highlight_elm_class = '.control-group';

    /* classe de error */
    this.highlight_elm_error_class = 'error';
};

/* cria apledio para classe  */
var _dfv = DynamicFormValidator;

/**
 * converte o evento no elemento que o disparaou
 *
 * @param event
 * @return HTMLElement
 * */
_dfv.prototype.target = function (event)
{
    return $(event.target || event.srcElement);
};

/* sinaliza se há algum campo em desconformidade */
_dfv.prototype.hasError = function ()
{
    return this.has_error;
};

/* efetua o destaque do campo com erro */
_dfv.prototype.highlight = function (elm)
{
    if (! this.highlight_on_error) {
        return;
    }

    $(elm).closest(this.highlight_elm_class)
          .addClass(this.highlight_elm_error_class);
};

_dfv.prototype.unhighlight = function (elm) {
    var elm = $(elm);

    elm.closest(this.highlight_elm_class)
       .removeClass(this.highlight_elm_error_class);

    elm.closest('.controls')
        .find('.help-block')
        .remove();
};

/**
 * limita a quantidade de caractes do campo informado
 *
 * @param event
 * @param  integer
 * */
_dfv.prototype.maxlength = function (event, max) {
    var ignoreKeys = [
        this.KEY_HOME,
        this.KEY_END,
        this.KEY_SPACE,
        this.KEY_DELETE,
        this.KEY_TAB,
        this.KEY_BACKSPACE,
        this.KEY_LEFT,
        this.KEY_UP,
        this.KEY_RIGHT,
        this.KEY_DOWN
    ];

    var elm   = this.target(event);
    var kCode = event.which || event.keyCode;
    var val   = elm.val();

    if (val.length < max) {
        elm.val(val);
    } else if(-1 === ignoreKeys.indexOf(kCode)){
        event.preventDefault();
    }
};

/*
 * valida o campo informado como preenchido
 *
 * @param event
 * */
_dfv.prototype.required = function (event)
{
    var elm = this.target(event);

    if (! elm.val().trim()) {
        this.highlight(elm);
        return;
    }

    this.unhighlight(elm);
};

_dfv.prototype.allowOnly = function (event, type)
{
    var elm = this.target(event);
    cl( ': implementar [allowOnly] ' );
};

/**
 * valida os tipos dados
 *
 * @param event
 * @param string
 * */
_dfv.prototype.validType = function (event, type)
{
    if (! this.er_validator[type]) {
        throw "Não existe plugin registrado para validar: {0}".format(type);
    }

    var elm = $(event.target || event.srcElement);
    var val = elm.val();
    var isEmpty = !val.trim();
    var isRequired = elm.hasClass('required');

    if (!isRequired && isEmpty) {
        this.unhighlight(elm);
        return;
    }

    if (isEmpty || !this.er_validator[type].test(val)) {
        this.highlight(elm);
        this.has_error = true;
        return false;
    }

    this.has_error = false;
    this.unhighlight(elm);

    return true;
};