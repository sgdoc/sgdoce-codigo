/**
 * Copyright 2011 ICMBio
 *
 * Manipulador do componente Smart Filter
 * @license http://dev.static.cdn.icmbio.gov.br/license.txt
 * @author J. Augusto <augustowebd@gmail.com>
 * @depends jQuery
 * * */
(function ($) {
    "use strict";

    $.fn.smartFilter = function (settings) {

        var config = {
            requestType: "POST",
               useCache: false ,
               dataType: "json"
        };

        /* recupera seed do ID */
        settings.seed = settings.gridID
                                .toString()
                                .split('-')[1]
                                .split('_')[0];

        if (settings){
            $.extend(config, settings);
        }


        return this.each(function () {
            var btnAction     = $('.btnAction', this);
            var optionList    = $('ul > li', this);
            var selectLength  = $(':input[name="' + settings.seed + '_length"]');
            var selectedIndex = $(':input[name="selectedIndex"]');
            var SFInput       = $(':input[name="smartFilterInput"]');

            /* se o combo for composto apenas por uma opcao */
            if (1 === optionList.length) {
               btnAction.text($.SFGetOptionLabel(optionList[0]));
            }

            $(optionList).each(function () {
                $(this).click(function () {
                    /* muda o label do botao de acao */
                    $.SFSetActionButtonLabel(btnAction, $.SFGetOptionLabel(this));
                    selectedIndex.val($.SFGetOptionValue(this));
                });
            });

            /* ao clicar busca as informacoes na url informada */
            $('.form-actions :button[class="btn"]', this).click(function () {
                var $grid = $(config.gridID);
                var dataParam = {};
                    dataParam[selectedIndex.val()] = SFInput.val();
                    dataParam['len'] = selectLength.val();

                /* @todo criar barra de carregando... */
                $.ajax({
                     url: config.url,
                    type: config.requestType,
                   cache: config.useCache,
                dataType: config.dataType,
                    data: dataParam,
              beforeSend: function () { $('tbody', $grid).empty();} })
                .done(function (response) {
                    if(response.total) {
                        $.SFGridAddColumn($grid, response.result);
                        $grid.show();
                    } else {
                        alert.showMessage(response.response,'warning','gridDivAlert');
                    }
                });
            });

            return this;
        });
    };

    /**
     * adiciona resultado a grid
     * @param object[]
     * */
    $.SFGridAddColumn = function ($grid, data) {
        var body      = $('tbody', $grid);
        var cols      = $.parseJSON($(':input[name="columns"]').val().replace(/'/g, '"'));
        var SFEvents  = $.parseJSON($(':input[name="event"]'  ).val().replace(/'/g, '"'));
        var SFRowKey  = $(':input[name="rowKey"]').val();
        var SFBtnIcon = {
                         detail: "icon-eye-open",
                         edit  : "icon-pencil",
                         remove: "icon-trash"
                        };

        for (var d in data) {

            var row = $('<tr>');
            body.append(row);

            /* adicionao numero da linha */
            row.append($.SFGridCreateColumn(1 + parseInt(d, 10)));

            for (var c in cols) {
              if (undefined !== cols[c]) {
                var dindex = cols[c].dindex;
                row.append($.SFGridCreateColumn(data[d][dindex], dindex === SFRowKey));
              }
            }


            /*---[ Refatorar ]-------------------------------------------------------------- */
            var $colPerm = $("<td>");

            /* para cada evento, um novo botao */
            for (var e in SFEvents) {

                var btn = $('<a href="#" class="btn ' + SFEvents[e].on + '"><i class="' + SFBtnIcon[SFEvents[e].on] + '"></a>')
                    .bind('click', function () {
                        var action    = $(this);
                        var $modal    = $('.SFModal');
                        var btnXClose = $('.close');
                        var btnClose  = $('#modalBtnClose');
                        var btnSave   = $('#modalBtnSaveChange');

                        btnXClose.click(function () { $modal.modal('hide');});
                        btnClose.click(function () { $modal.modal('hide');});

                        var mBody   = $('.modal-body', $modal);
                        var mFooter = $('.modal-footer', $modal);
                        var rowKey  = $('.rowKey', $(this).closest('tr')).text();

                        var action  = $(this).attr('class').split(' ')[1];
                        var info    = {on: null, title: null, url: null, requestType: null};

                        for (var o in SFEvents) {
                          if (SFEvents[o].on == action) {
                              info = SFEvents[o]; break;
                          }
                        }

                        /* define o titulo da modal */
                        $('h3', $modal).text(info.title);

                        /* configura requisicao */
                        var reqConf = {
                                 url: info.url,
                                type: info.requestType.toUpperCase(),
                            dataType: info.dataType ? info.dataType : 'html',
                          beforeSend: function () { mBody.empty(); },
                          statusCode: {
                            404: function () { mBody.html('<h4>Página não encontrada!</h4>')},
                            403: function () { mBody.html('<h4>Sem permissão de acesso!</h4>')},
                          }
                        };

                        /* anexa os params */
                        var type = $('#selectedIndex').val();
                        if('post' === info.requestType.toLowerCase()) {
                            reqConf.data = {param: rowKey, type: type};
                        } else {
                            reqConf.url += '?param=' + rowKey +  '&type=' + type;
                        }


                        /* conexao para recupera os dados */
                        $.ajax(reqConf)
                         .done(function (response) {
                            mBody.html(response);

                            if ('detail' === action) {
                              btnSave.hide();
                            } else {
                              btnSave.show();
                            }
                          });

                        /* ordena exibicao da grid */
                        $modal.modal({keyboard: true, backdrop: true}).show();
                    });

                   $colPerm.append(btn);
            /*---[ Refatorar ]-------------------------------------------------------------- */
            }

            row.append($colPerm);
        }
    };

    /**
     * cria columa com contendo
     * @param string val
     * @param boolean isRowkey
     * @return HTMLTableData
     * */
    $.SFGridCreateColumn = function (val, isRowkey) {
        var col = $('<td>').text(val);
        if (isRowkey) {
            col.addClass('rowKey');
        }
        return col;
    };

    /**
     * @param HTMLlist
     * @return string
     * */
    $.SFGetOptionLabel = function (option) {
        return $('a', option).text();
    };

    /**
     * recupera o valor da opcao selecionada
     * @param HTMLlist
     * @return string
     * */
    $.SFGetOptionValue = function (option) {
        return $('a', option).attr('href').split('#')[1];
    };

    /**
     * @param HTMLButton
     * */
    $.SFSetActionButtonLabel = function (button, label) {
        $(button).text(label);
    };

    /**
     * @param Object
     * */
    var cl = function() { return console.log.apply(console, arguments); };

})(jQuery);