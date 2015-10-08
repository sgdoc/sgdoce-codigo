$.extend($.fn.dataTableExt.oStdClasses, {
    "sWrapper": "dataTables_wrapper form-inline"
});

/* API method to get paging information */
$.fn.dataTableExt.oApi.fnPagingInfo = function (oSettings)
{
    return {
        "iStart": oSettings._iDisplayStart,
        "iEnd": oSettings.fnDisplayEnd(),
        "iLength": 5,
        "iTotal": oSettings.fnRecordsTotal(),
        "iFilteredTotal": oSettings.fnRecordsDisplay(),
        "iPage": Math.ceil(oSettings._iDisplayStart / oSettings._iDisplayLength),
        "iTotalPages": Math.ceil(oSettings._iRecordsTotal / oSettings._iDisplayLength)
    };
}

/* Bootstrap style pagination control */
$.extend($.fn.dataTableExt.oPagination, {
    "bootstrap": {
        "fnInit": function (oSettings, nPaging, fnDraw) {
            var oLang = oSettings.oLanguage.oPaginate;
            var fnClickHandler = function (e) {
                e.preventDefault();
                if (oSettings.oApi._fnPageChange(oSettings, e.data.action)) {
                    fnDraw(oSettings);
                }
            };
            $(nPaging).addClass('pagination').append(
                    '<ul>' +
                    '<li class="paginate_button first disabled" title="' + oLang.sFirst + '"><a href="#">&laquo;</a></li>' +
                    '<li class="paginate_button prev disabled" title="' + oLang.sPrevious + '"><a href="#">&lsaquo;</a></li>' +
                    '<li class="paginate_button next disabled" title="' + oLang.sNext + '"><a href="#">&rsaquo;</a></li>' +
                    '<li class="paginate_button last disabled" title="' + oLang.sLast + '"><a href="#">&raquo;</a></li>' +
                    '</ul>'
                    );

            var els = $('a', nPaging);

            $(els[0]).bind('click.DT', {action: "first"}, fnClickHandler);
            $(els[1]).bind('click.DT', {action: "previous"}, fnClickHandler);
            $(els[2]).bind('click.DT', {action: "next"}, fnClickHandler);
            $(els[3]).bind('click.DT', {action: "last"}, fnClickHandler);
        },
        "fnUpdate": function (oSettings, fnDraw) {
            var oPaging = oSettings.oInstance.fnPagingInfo();
            var iListLength = oPaging.iLength;
            var an = oSettings.aanFeatures.p;
            var i, j, sClass, iStart, iEnd, iHalf = Math.floor(iListLength / 2);

            if (oPaging.iTotalPages < iListLength) {
                iStart = 1;
                iEnd = oPaging.iTotalPages;
            }
            else if (oPaging.iPage <= iHalf) {
                iStart = 1;
                iEnd = iListLength;
            } else if (oPaging.iPage >= (oPaging.iTotalPages - iHalf)) {
                iStart = oPaging.iTotalPages - iListLength + 1;
                iEnd = oPaging.iTotalPages;
            } else {
                iStart = oPaging.iPage - iHalf + 1;
                iEnd = iStart + iListLength - 1;
            }

            for (var i = 0, iLen = an.length; i < iLen; i++) {
                // Remove the middle elements
                $('li:gt(0)', an[i]).filter(':not(.paginate_button)').remove();

                // Add the new list items and their event handlers
                for (var j = iStart; j <= iEnd; j++) {
                    sClass = (j == oPaging.iPage + 1) ? 'class="active"' : '';
                    $('<li ' + sClass + '><a href="#">' + j + '</a></li>')
                            .insertBefore($('li.next', an[i])[0])
                            .bind('click', function (e) {
                                e.preventDefault();
                                oSettings._iDisplayStart = (parseInt($('a', this).text(), 10) - 1) * oSettings._iDisplayLength;
                                fnDraw(oSettings);
                            });
                }

                // Add / remove disabled classes from the static elements
                if (oPaging.iPage === 0) {
                    $('li:first', an[i]).addClass('disabled');
                    $('li.prev', an[i]).addClass('disabled');
                } else {
                    $('li:first', an[i]).removeClass('disabled');
                    $('li.prev', an[i]).removeClass('disabled');
                }

                if (oPaging.iPage === oPaging.iTotalPages - 1 || oPaging.iTotalPages === 0) {
                    $('li.next', an[i]).addClass('disabled');
                    $('li:last', an[i]).addClass('disabled');
                } else {
                    $('li.next', an[i]).removeClass('disabled');
                    $('li:last', an[i]).removeClass('disabled');
                }
            }
        }
    }
});

$.extend($.fn.dataTableExt.oStdClasses,{
    "sSortAsc": "header headerSortDown",
    "sSortDesc": "header headerSortUp",
    "sSortable": "header"
});

var Grid = {
    options: function (target, table) {

        if (typeof target === 'string') {
            var url = target;
            var serverParams = function () {
            };
        } else {
            var url = target.attr('action');
            var serverParams = function (aoData) {
                tableId = $(table).attr('id');
                var formSubmited = sessionStorage.getItem('SGDOC_Form_Submited');
                if (sessionStorage.getItem('SGDOC_' + tableId) && formSubmited == 'false') {
                    table.closest('div.control-group').removeClass('hidden');
                    dados = sessionStorage.getItem('SGDOC_' + tableId);
                    dados = $.parseJSON(dados);

                    $.each(dados, function (i, element) {
                        /**
                         * Validação de Radio e Checkbox para serem postados também
                         */
                        var input = $('select[name=' + element.name + '],input[name=' + element.name + ']');
                        if ( input.attr('type') == 'radio' || input.attr('type') == 'checkbox' ) {
                            var inputValue = $('input[name=' + element.name + ']:checked').val();
                            if (inputValue != '' && element.value != inputValue) {
                              element.value = inputValue;
                            }
                            $('input[name=' + element.name + ']:checked').val(element.value);
                          } else {
                            var inputValue = $('select[name=' + element.name + '],input[name=' + element.name + ']').val();
                            if (inputValue != '' && element.value != inputValue) {
                              element.value = inputValue;
                            }
                            $('select[name=' + element.name + '],input[name=' + element.name + ']').val(element.value);
                          }
                    });

                } else {
                    sessionStorage.setItem('SGDOC_Form_Submited', false);
                    var dados = new Array();
                    /**
                     * ADICIONANDO :checkbox:checked e :radio:checked para serem postados
                     */
                    $('select, input:not(:radio):not(:checkbox), :checkbox:checked, :radio:checked', target).each(function (i, element) {
                        dados.push({name: element.name, value: $(element).val()});
                    });
                }
                $.each(dados, function (i, element) {
                    aoData.push({'name': element.name, 'value': element.value});
                });
                sessionStorage.setItem('SGDOC_' + tableId, JSON.stringify(dados));
            };
        }
        return {
            'bAutoWidth': false,
            'bProcessing': true,
            'bJQueryUI': false,
            'bServerSide': true,
            'bFilter': false,
            'sPaginationType': 'bootstrap',
            'iDisplayLength': 10,
            'bRetrieve': true,
            'sAjaxSource': url,
            'fnServerParams': serverParams,
            "bStateSave": false,
            "sDom": "<'row-fluid'<'span6'fr><'span6'l>>t<'row-fluid'<'span6'i><'span6'p>>",
            "oLanguage": {
                "sProcessing": '<label>Carregando registros<span class="threeLittleDots">.</span><span class="threeLittleDots">.</span><span class="threeLittleDots">.</span></label>',
                "sLengthMenu": "Registros por página _MENU_",
                "sZeroRecords": "Nenhum registro encontrado.",
                "sInfo": "Mostrando _START_ a _PAGINADO_ de _MAX_ registro(s).",
                "sInfoEmpty": "Mostrando de 0 até 0 de 0 registros",
                "sInfoFiltered": "Filtrado de _MAX_ registros no total.",
                "sInfoPostFix": "",
                "sSearch": "Buscar:",
                "sInfoThousands": "",
                "sUrl": "",
                "oPaginate": {
                    "sFirst": "Primeira",
                    "sPrevious": "Anterior",
                    "sNext": "Próxima",
                    "sLast": "Última"
                }
            }
        }
    },
    loadNoPagination: function (target, element) {
        this.load(target, element);
        element.siblings('.row').hide();

        var headers = element.find('.header');

        $(document).ajaxStop(function () {
            headers.removeClass('headerSortDown');
        });
        headers.unbind('click');
    },
    load: function (target, element, messageRecordsNotFound) {

        //caso necessário uma mensagem diferenciada
        messageRecordsNotFound = messageRecordsNotFound || null;

        var gridOptions = Grid.options(target, element);

        if (messageRecordsNotFound){
            gridOptions.oLanguage.sZeroRecords = messageRecordsNotFound;
        }

        element.addClass('table')
                .addClass('table-striped')
                .addClass('table-bordered')
                .dataTable(gridOptions);

        if (typeof target === 'object') {
            target.unbind('submit').bind('submit', function () {
                if (target.valid()) {
                    sessionStorage.setItem('SGDOC_Form_Submited', true);
                    $(document).ajaxStop(function () {
                        element.parents('.hidden').removeClass('hidden');
                        $('.alert').css('display', 'none');
                    });

                    element.fnDraw(false);

                    return false;
                }
                return false;
            });
        } else {
            element.off('submit').on('submit', function () {
                element.fnDraw(false);
                return false;
            });
        }
        $('.sorting_disabled').off('click');
    },
    actionDefault: function (codigo, tipo, callBack) {
        switch (tipo) {
            case 'edit':
                Navegacao.dispatch('edit/codigo/' + codigo);
                break;

            case 'delete':
                if (!callBack) {
                    callBack = function () {
                        Navegacao.dispatch('delete/codigo/' + codigo);
                    }
                }
                Message.showConfirmation({
                    'body': 'Deseja realmente excluir?',
                    'yesCallback': callBack
                });
                break;
        }
    }
}
