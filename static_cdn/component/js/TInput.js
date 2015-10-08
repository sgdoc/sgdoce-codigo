/**
 * componente responsavel por carregar o conteudo do input informado
 * para uma tabela grid, esta com a opcao de remover o elementos nela
 * registrado.
 *
 * @param {Object} configs
 * @param {JQueryHtmlTable} config.table
 * @param {JQueryHtmlInput} config.input
 *  - {table, input, storage, allowDuplicate}
 * */
; var TInput = function (configs)
{
    var configs = configs;
        configs.icon           = {'remove': 'icon-trash'};
        configs.allowDuplicate = !!configs.allowDuplicate;
        configs.table          = $(configs.table).find('tbody');
        configs.input          = $(configs.input);
        configs.storage        = $(configs.storage);
        configs.separator      = configs.separator ? configs.separator : ';';

    var focus = function () {
        configs.input.focus();
    };

    /* retorna true se o o campo estiver vazio */
    var isEmpty = function () {
        return !configs.input.val();
    };

    var empty = function () {
        configs.input.val(null);
    };

    var save = function () {

        var vStorage = configs.storage.val().trim();
            vStorage = vStorage ? vStorage.split(configs.separator) : [];
            vStorage[vStorage.length] = configs.input.val();

        configs.storage.val(vStorage.join(configs.separator));
    };

    var remove = function () {
        var targer = $(this ).closest('tr');
        var svalue = configs.storage.val().trim().split(configs.separator);
            svalue.splice(svalue.indexOf(targer.data('rowValue')), 1);
            configs.storage.val(svalue.join(configs.separator));

        render();
    };

    var duplicated = function () {
        return -1 < configs.storage
                            .val()
                            .split(configs.separator)
                            .indexOf(configs.input.val())
    };

    var render = function () {
        configs.table.empty();

        var innerCreateLine = function (val) {
            var iconRemove = $('<i class="{0}" />'.format(configs.icon.remove));

            var btnRemove  = $('<a href="javascript:;">')
                              .addClass('btn btn-mini')
                              .append(iconRemove)
                              .on('click', remove);

            var celData    = $('<td>').text(val);
            var celCont    = $('<td style="text-align: right">').append(btnRemove);
            var row        = $('<tr>').data('row-value', val)
                                      .append(celData, celCont);

            return row;
        };

        var values = configs.storage.val().split(configs.separator);

        for (var prop in values) {
            if(! values.hasOwnProperty(prop)) {
                continue;
            }

            if (values[prop].trim()) {
                configs.table.append( innerCreateLine(values[prop]));
            }
        }
    };

    var add  = function () {

        if (! isEmpty()) {

            if (!configs.allowDuplicate && duplicated()) {
                focus();
                return;
            }

            save();
            empty();
            render();
        }

        focus();
    };

    /* interface publica */
    this.add = add;
};