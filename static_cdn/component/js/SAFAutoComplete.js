/**
 * Copyright 2011 ICMBio
 *
 * Manipulador do componente AutoComplete
 *
 * @license http://dev.static.cdn.icmbio.gov.br/license.txt
 * @author J. Augusto <augustowebd@gmail.com>
 * @depends jQuery
 * * */
 (function ($) {
     "use strict";

$.fn.SAFAutoComplete = function (settings) {
    "use strict";

    var config = {
        requestType: "POST",
           useCache: true ,
           dataType: "json",
            minChar: 3,
              limit: 10,
                 id: this.attr('id')
    };

    var $input = $('#' + config.id + '-input');
    $input.addClass('autocomplete');

    var info = $.parseJSON( $(':input[name="' + config.id + '-info"]').val().replace(/'/g, '"') );
    if (info){ $.extend(config, info); }

    if (undefined !== config.hidden) {
        var pattern = ':input[name="' + config.hidden + '"]';
        var $hidden = $(pattern);

        if (0 == $hidden.length) {
            $hidden = $('<input type="hidden" name="' + config.hidden + '">');
            $input.parent().append($hidden);
        }

        $hidden.val( config.defaultValue );
    }

    var curtainID = config.id + '-curtain';
    var $curtain  = $('#' + curtainID).css({'width': $input.width()});

    $("body").click(function(event) {
        var current = $(event.target).parent().parent().attr('id');
        if(current != curtainID) { $curtain.empty().css({'border': '0px'}); }
    });

    if (config.displayValue.toString()){
       $input.val(config.displayValue);
    }


    var dataParam = {};
        dataParam.limit = config.limit;

    return this.each(function () {

        $input.keyup(function (event) {


            if (this.value.length >= config.minChar) {
                /* cuida para que o nome do input seja usado como param */
                dataParam[$input.attr('name')] = this.value;
                $input.addClass('loading');

                var extraFilter = config.extraFilter.split(',');
                for (var i in extraFilter) {
                    var element = $(extraFilter[i]);
                    dataParam[element.attr('name')] = element.val();
                }

                $.ajax({
                       url: config.httpRequestConf.url
                ,     type: config.httpRequestConf.requestType
                ,    cache: config.httpRequestConf.useCache
                , dataType: config.httpRequestConf.dataType
                ,     data: dataParam
                }).done(function (response) {
                    $input.removeClass('loading');
                    var $content = $.SAFAutoCompleteIsResultFormat($input, $curtain, response.result, config.dindex, config);
                    $curtain.empty().html($content).css({'border': '1px solid #000'});
                });
            } else {
                var pattern = ':input[name="' + config.hidden + '"]';
                $(pattern).val('');
            }
        });

        return this;
    });
}

/**
 * @param object[]
 * @return jQueryUL
 * */
 $.SAFAutoCompleteIsResultFormat = function ($input, $curtain, result, dindex, config)
 {
    var $ul = $('<ul>');

    if (!result.length) {
        $ul.append('<li>' + config.message + '</li>');
    }

    for (var r in result) {
        var $li = $('<li>');

        $li.mouseover(function (event) { this.className = 'sel'; })
           .mouseout(function (event)  { this.className = '';    })
           .click(function () {

                $input.val($(this).html());
                $curtain.empty();


                if (undefined != config.hidden) {
                    var pattern = ':input[name="' + config.hidden + '"]';
                    var $hidden = $(pattern);

                    if (0 == $hidden.length) {
                        $hidden = $('<input type="hidden" name="' + config.hidden + '">');
                        $input.parent().append($hidden);
                    }

                    $hidden.val($(this).attr('id'));
                }

                // notifica evento autocompleteClick
                $('#' + config.id).trigger('autocompleteClick');
            });

       if (config.hidden) {
           $li.attr('id', result[r][config.hidden]);
       }

       $ul.append($li.html(result[r][dindex]));
    }

    return $ul;
 }
})(jQuery);