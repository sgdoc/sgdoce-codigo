/**
 * Copyright 2011 ICMBio
 *
 * Manipulador do componente Grid
 *
 * @license http://dev.static.cdn.icmbio.gov.br/license.txt
 * @author J. Augusto <augustowebd@gmail.com>
 * @depends jQuery
 * * */
(function ($, window, undefined) {
    "use strict";

    $.fn.SAFGrid = function (settings, extraParam, cdn) {

        var config = {
            requestType: "POST",
               useCache: false ,
               dataType: "json",
                 maxPag: 5
        };

        /* recupera seed do ID */
        settings.seed = settings.gridID.toString().split('-')[1].split('_')[0];

        if (settings){$.extend(config, settings); }

        $.extend(config, $.SAFGridParam(settings));

        /* recupera todos os elementos necessarios para manipular a grid */
        var $grid        = $(settings.gridID);
        var $tPage       = $(':input[name="' + settings.seed + '_length"]');
        var $loadOnReady = $(':input[name="' + settings.gridID + 'LoadDataOnReady"]');

        /*
         * agenda evento para quando houver mudança da quantidade de elementos por pagina
         * */
        $tPage.change(function (){
            /* Reseta a paginação */
            config.curPage = 1;

            /* dispara consulta de dados */
            $.SAFGridDataLoad(config, extraParam);
        });

        /**
         * verifica se existe evento agendado para carga inicial
         * */
        if(true == $loadOnReady.val()) {
            $.SAFGridDataLoad(config, extraParam);
        }

        $.SAFGridSorter(config, extraParam, cdn);
    };

    $.SAFGridSorter = function (config, extraParam, cdn)
    {
        var sorter = {
            resetColumns : function(obj) {
                obj.css({"background-color":"white", "text-shadow" : "0"});
                obj.each(function() {
                    $(this).removeClass('sortSelected');
                    $(this).removeClass('headerSortUp');
                    sorter.setMouseOut($(this));
                });
            },

            setActiveColumn : function(tSorter, eSorter, config, field) {
                sorter.resetColumns(tSorter);

                var order = eSorter.attr('order');

                config['orderBy'] = field;
                config['order']   = order;
                tSorter.attr('order', 'asc');

                eSorter.addClass('sortSelected');
                eSorter.attr('order', 'asc' == order ? 'desc' : 'asc');

                var imgClass = 'asc' == order ? 'headerSortDown' : 'headerSortUp';

                eSorter.css({"background-color":"rgba(141, 192, 219, 0.25)", "text-shadow" : "0 1px 1px rgba(255, 255, 255, 0.75)" });
                eSorter.addClass(imgClass);
                sorter.setMouseOver(eSorter);
            },

            setMouseOver : function(obj) {
                var imgOrder = 'asc' == obj.attr('order') ? 'asc' : 'desc';

                obj.css('cursor','pointer');
                obj.addClass('headerSortDown');
            },

            setMouseOut : function(obj) {
                var isPrimary = obj.hasClass('sortSelected');

                if (isPrimary === false) {
                    obj.removeClass('headerSortDown');
                }
            }
        };

        var tSorter = $('.grid_sorter');
        var counter = 0;

        tSorter.each(function(){
            var elmnt = $(this).attr('class').split(" ");
            var field = elmnt[elmnt.length-1];
            elmnt = elmnt.join(".");
            var eSorter = $('.' + elmnt);
            var imgOrder = 'asc' == eSorter.attr('order') ? 'asc' : 'desc';

            eSorter.css('cursor','pointer');

            if(counter === 0) {
                sorter.setActiveColumn(tSorter, eSorter, config, field);
            }

            eSorter.click(function(){
                sorter.setActiveColumn(tSorter, eSorter, config, field);
                $.SAFGridDataLoad(config, extraParam);
            });

            eSorter.mouseout(function() {
                sorter.setMouseOut($(this));
            });

            eSorter.mouseover(function(){
                sorter.setMouseOver($(this));
            });

            counter++;
        });
    };

    /**
     * @param object config{tPage, curPage, httpRequestConf{url, requestType, useCache, dataType}
     * */
    $.SAFGridDataLoad = function (config, extraParam)
    {
        var $grid  = $('#' + config.gridID);
        var $body  = $('tbody', $grid);
        var $tPage = $('[name^="' + config.seed + '"][name$="length"]');

        config.tPage = $tPage.val();

        var dataParam = {};
            dataParam.length  = config.tPage;
            dataParam.curPage = config.curPage;
            dataParam.orderBy = config.orderBy;
            dataParam.order   = config.order;

        for (var o in extraParam) {
            dataParam[o] = extraParam[o];
        }

        var $loadingText = $grid.find('.dataTables_processing>label');
        $loadingText.fadeIn();
        $.ajax({
                   url: config.httpRequestConf.url
            ,     type: config.httpRequestConf.requestType
            ,    cache: config.httpRequestConf.useCache
            , dataType: config.httpRequestConf.dataType
            ,     data: dataParam
            // , beforeSend: function () {$body.html("");}
            , error : function (par1, par2) {console.log(par2);}
        }).done(function (result) {
            $loadingText.fadeOut();
            if(result.total) {
                config.curPage = dataParam.curPage;
                $.SAFGridBodyLoader(result, $body, $grid, config);
                $.SAFGridPagination(config, result, $grid, extraParam);
            } else {
                $grid.trigger('noResultFound');
            }
        });
    }

    /**
     * @param object[total, result] dataSource
     * @param jQuery $grid
     * */
    $.SAFGridBodyLoader = function (dataSource, $tbody, $grid, config)
    {
        $tbody.empty();
        var colsInfo     = $.SAFGridColumnInfo($grid);

        var rowPerm  = $.parseJSON($(':input[name="rowPerm"]', $grid).val().replace(/'/g, '"'));

        /* verifica se a tabela ja possui uma coluna hasCounLine */
        var hasCountLineExists = '#' === $('table thead tr th label', $grid)[0].innerHTML;

        /* verifica se a coluna contadora sera necessaria */
        if (false === colsInfo.hasCountLine && hasCountLineExists) {
            /* remove a coluna hasCountLine quando a mesma nao for necessario */
            $($('table thead tr th')[0]).remove();
        }


        /* current */

        var currRegister = ((config.curPage - 1) * config.tPage + 1);

        for (var r in dataSource.result) {
            if (!dataSource.result.hasOwnProperty(r)) {
                continue;
            }

            var $row = $('<tr>');
            var row = dataSource.result[r];

            /* insere o contador de linhas */
            if (hasCountLineExists) {
                $row.append($('<td>').text(currRegister++));
            }

            for (var c in colsInfo.columns) {
                var col = colsInfo.columns[c].dindex;

                if (colsInfo.columns[c].hide) {
                    if (col == 'recordcount') {
                        $tbody.attr('data-' + col, row[col]);
                    } else {
                        $row.attr('data-' + col, row[col]);
                    }
                }

                if (undefined !== row[col] && !colsInfo.columns[c].hide) {

                    var colVal = row[col];

                    /* se existir,aplica callback */
                    var callback = colsInfo.columns[c].callback;
                    if (callback) {
                        try { colVal = window[callback](colVal); } catch (e) { throw "Erro ao executar o callback:  " + callback };
                    }

                    $row.append($('<td>').text($.trim(colVal)));
                }
            }

            /* aqui entra a coluna de botoes actions, se houver */
            var rowKeyVal = row[rowPerm.rowKey];

            if (undefined !== rowPerm.allow) {
                var $controll = $('<td>').addClass('grid-column-action');
                $row.append($controll.html($.SAFGridAllow(rowKeyVal, rowPerm)));
            }

            $tbody.append($row);
        }
        $grid.trigger('bodyLoaded');
    }

    $.SAFGridAllow = function (id, permission)
    {
        var dictionary = {};
            dictionary.edit   = '<a class="edit btn btn-mini" href="javascript:;" title="alterar"><span class="icon-pencil"></span></a>';
            dictionary.detail = '<a class="detail btn btn-mini" href="javascript:;" title="detalhar"><span class="icon-eye-open"></span></a>';
            dictionary.status = '<a class="status btn btn-mini" href="javascript:;" title="Status"><span class="icon-inativado"></span></a>';
            dictionary.delete = '<a class="delete btn btn-mini" href="javascript:;" title="remover"><span class="icon-trash"></span></a>';
            dictionary.print  = '<a class="print btn btn-mini" href="javascript:;" title="imprimir"><span class="icon-print"></span></a>';

            var perm = '';
            for (var a in permission.allow) {
                var allow = permission.allow[a];
                perm += dictionary[allow];
            };

            var btnGroup  = '<div class="btn-group" id="{0}">{1}</div>'.format(id, perm) ;

        return btnGroup;
    }

    $.SAFGridColumnInfo = function ($grid)
    {
        return $.parseJSON($(':input[name="grid-column-info"]', $grid).val().replace(/'/g, '"'));
    }

    $.SAFGridPagination = function (config, result, $grid, extraParam)
    {
        var overflow  = false;
        var isCurrent = false;
        var param     = config;
        var total     = parseInt($grid.children('table').children('tbody').attr('data-recordcount'));

        config.labelFirst = "&laquo;";
        config.labelLast  = "&raquo;";
        config.labelPrev  = "&lsaquo;";
        config.labelNext  = "&rsaquo;";
        config.total      = total;

        var curPage   = parseInt(config.curPage);
        var maxPag    = parseInt(config.maxPag);
        var pageLen   = parseInt(config.tPage);
        var tpages    = Math.ceil(total / pageLen);
        var isFirst   = 1 == curPage;
        var isLast    = (curPage * pageLen >= total);
        var $ul       = $('<ul>');
        var maxPag    = (total < pageLen) ? 1: Math.ceil(total / pageLen);
        var $first    = $('<li>').append($('<a href="javascript:;" class="first">').html(config.labelFirst) );
        var $prev     = $('<li>').append($('<a href="javascript:;" class="previous">').html(config.labelPrev) );
        var $next     = $('<li>').append($('<a href="javascript:;" class="next">').html(config.labelNext));
        var $last     = $('<li>').append($('<a href="javascript:;" class="last">').html(config.labelLast));
        var page      = curPage;
        var pageLimit = 5;
        var info;

        var pagination = {
            render : function() {
                $('.dataTables_info').html(info).parent().parent().show();
                $('.dataTables_paginate', $grid).empty().append($ul);
            },
            noConflict : function() {
                total  = parseInt(result.total);
                maxPag = 5;
                if (total >= pageLen) {
                    if (4 <= curPage) {
                        i = (curPage > 1) ? (curPage - 2) : curPage;
                        maxPag = curPage + 2;
                   }
               }
            },
            resetPage : function() {
                page = 1;
                maxPag = pageLimit;
            },
            setNavigation: function() {
                var availablePages = maxPag - pageLimit + 1;

                // Última página ou recordCount é menor que pageLimit (5)
                if ((curPage === maxPag) || (curPage > availablePages)) {
                    if (maxPag >= pageLimit) {
                        page = (maxPag - pageLimit) + 1;
                    } else {
                        page = 1;
                    }
                } else {
                    maxPag = (curPage > 1) ? ((page + pageLimit) -1) : (page + pageLimit);

                    /* permite navegar de 2 em 2 a partir da 3ª página */
                    if (curPage <= 3) {
                        pagination.resetPage();
                    } else {
                        page = page - 2;
                        maxPag = maxPag - 2;
                    }
                }
            },
            setPages : function() {
                pagination.setNavigation();

                for (; page <= maxPag; page++) {

                    var $current = $('<li>').append($('<a href="javascript:;">').text(page));

                    if (page == curPage) {
                        $current.attr({'class': 'selected'});
                    } else {
                        $current.click(function () { $.SAFGridOptionLoader(this, config, extraParam, $grid); });
                    }

                    $ul.append($current);
                }
            },
            setNavBarBefore : function() {
                $ul.append($first);
                $ul.append($prev);

                if (!isFirst) {
                    $first.click(function () { $.SAFGridOptionLoader(this, config, extraParam, $grid); });
                    $prev.click(function () { $.SAFGridOptionLoader(this, config, extraParam, $grid); });
                } else {
                    $first.addClass('disabled');
                    $prev.addClass('disabled');
                }
            },
            setNavBarAfter : function() {
                $ul.append($next);
                $ul.append($last);

                if (!isLast) {
                    $next.click(function () { $.SAFGridOptionLoader(this, config, extraParam, $grid); });
                    $last.click(function () { $.SAFGridOptionLoader(this, config, extraParam, $grid); });
                } else {
                    $next.addClass('disabled');
                    $last.addClass('disabled');
                }
            },
            setFooter : function() {
                var label          = 'Mostrando _x a _y de _z registros.';
                var currentPageLen = ((curPage - 1) * pageLen + pageLen);
                var lastPageLen    = (currentPageLen > total) ? total : ((curPage - 1) * pageLen + pageLen);
                info               = label.replace('_x', (((curPage - 1)  * pageLen) + 1))
                                          .replace('_y', (lastPageLen))
                                          .replace('_z', total);

            }
        };

        /* Para manter a compatibilidade com as aplicações que já utilizavam a
         * grid sem informar o recordcount
         */
        if (isNaN(total)) {
            pagination.noConflict();
        }

        /* Barra de navegação 'Primeiro' e 'Anterior' */
        pagination.setNavBarBefore();

        /* Páginas dispoíveis para navegação */
        pagination.setPages();

        /* Barra de navegação 'Próximo' e 'Último' */
        pagination.setNavBarAfter();

        /* Rodapé */
        pagination.setFooter();

        /* Renderiza a paginação */
        pagination.render();
    }

    /**
     * @param HTMLLi $el
     * @param object config
     * */
    $.SAFGridOptionLoader = function (el, config, extraParam, $grid)
    {
        var val = $('a', $(el)).text();

               if (val === config.labelFirst){ val = 1;
        } else if (val === config.labelPrev) { val = parseInt(config.curPage) - 1;
        } else if (val === config.labelNext) { val = parseInt(config.curPage) + 1;
        } else if (val === config.labelLast) { val = Math.ceil(parseInt(config.total) / parseInt(config.tPage));
        } else {                               val = parseInt(val);
        }

        /* atualiza o numero da pagina que sera recuperada */
        config.curPage = val;
        $(':input[name="currentpage"]', $grid).val(config.curPage);
        $.SAFGridDataLoad(config, extraParam);
    }

    /**
     * @param string seed
     * @return object
     * */
    $.SAFGridParam = function (config)
    {
        var $grid = $('#' + config.gridID);
        var $gridInfo = $(':input[name="grid-http-info"]', $grid);
        if (!$gridInfo.length) return null;

        return {
                      tPage: $(':input[name="' + config.seed + '_length"]', $grid).val(),
                    curPage: $(':input[name="currentpage"]', $grid).val(),
            httpRequestConf: $.parseJSON($gridInfo.val().replace(/'/g, '"').replace(/\\/g, ''))
        };
    }

    /**
     * @param Object
     * */
    var cl = function() { return console.log.apply(console, arguments); };
    var debug = function (label, val) { cl("[" + label + ": " + val + "]"); }

    if (!String.prototype.format) {
      String.prototype.format = function() {
        var args = arguments;
        return this.replace(/{(\d+)}/g, function(match, number) {
          return typeof args[number] != 'undefined' ? args[number] : match;
        });
      };
    }

})(jQuery, window);
