/*
    Simple AutoComplete plugin for jQuery
    Author: Wellington Ribeiro
    Version: 1.0.0 (14/03/2010 12:02)
    Version: 1.1.0 (04/05/2010 13:05) - resolve problemas do ie6 sem necessidade de hacks, fecha o autocomplete ao clicar fora, insere automaticamente o atributo para nÃ£o permitir o autocomplete do navegador.
    Copyright (c) 2008-2010 IdealMind ( www.idealmind.com.br )
    Licensed under the GPL license (http://blog.idealmind.com.br/projetos/simple-autocomplete-jquery-plugin/#license)

 *
 * $('selector').simpleAutoComplete("ajax_query.php", {
 *  identifier: 'estado',
 *  extraParamFromInput: '#extra',
 *  attrCallBack: 'rel',
 *  autoCompleteClassName: 'autocomplete',
 *  selectedClassName: 'sel'
 * },calbackFunction);
 *
 */

/* removedor de acentos - ainda a ser testado!*/
function stripAccents(str)
{
    var rExps=[
    {
        re:/\xC7/g,
        ch:'C'
    },

    {
        re:/\xE7/g,
        ch:'c'
    },

    {
        re:/[\xC0-\xC6]/g,
        ch:'A'
    },

    {
        re:/[\xE0-\xE6]/g,
        ch:'a'
    },

    {
        re:/[\xC8-\xCB]/g,
        ch:'E'
    },

    {
        re:/[\xE8-\xEB]/g,
        ch:'e'
    },

    {
        re:/[\xCC-\xCF]/g,
        ch:'I'
    },

    {
        re:/[\xEC-\xEF]/g,
        ch:'i'
    },

    {
        re:/[\xD2-\xD6]/g,
        ch:'O'
    },

    {
        re:/[\xF2-\xF6]/g,
        ch:'o'
    },

    {
        re:/[\xD9-\xDC]/g,
        ch:'U'
    },

    {
        re:/[\xF9-\xFC]/g,
        ch:'u'
    },

    {
        re:/[\xD1]/g,
        ch:'N'
    },

    {
        re:/[\xF1]/g,
        ch:'n'
    } ];

    for(var i=0, len=rExps.length; i<len; i++)
        str=str.replace(rExps[i].re, rExps[i].ch);

    return str;
}

(function($){
    $.fn.extend(
    {
        simpleAutoComplete: function( page, options, callback )
        {
            options = options || {};
            if(typeof(page) == "undefined" || !page)
            {
                alert("simpleAutoComplete: Você deve especificar a página que processará a consulta.");
            }

            var classAC = 'autocomplete';
            var selClass = 'sel';
            var attrCB = 'rel';
            var thisElement = $(this);

            $(":not(div." + classAC + ")").click(function(){
                $("div." + classAC).remove();
            });

            thisElement.attr("autocomplete","off");
            thisElement.addClass('autocomplete');
            var originalName=thisElement.attr('name');
            var newName = originalName+'_hidden';
            thisElement.attr("name",originalName+'_autocomplete');

            if(!$('#' + newName ).length) {
                $('<input>').attr('type','hidden')
                .attr('id',originalName+'_hidden')
                .insertBefore(thisElement);
            }

            $('#' + newName ).attr('name', options.hiddenName || originalName);

            thisElement.blur(function(){

                if(typeof(options.clearInput) == 'undefined'){
                    if (!$('#' + newName ).val()) {
                        thisElement.val('');
                    }

                    if (!$('#' + originalName ).val()) {
                        $('#' + newName ).val('');
                    }
                }else{
                    $('#' + newName ).val(thisElement.val());
                }

            })

            thisElement.keydown(function ( ev )
            {
                kc = ( ( typeof( ev.charCode ) == 'undefined' || ev.charCode === 0 ) ? ev.keyCode : ev.charCode );

                if (kc == 13)
                {
                    $('div.' + classAC + ' li.' + selClass).trigger('click');
                    return false;
                }
            });

            thisElement.keyup(function( ev )
            {
                var getOptions = {
                    query: thisElement.val()
                };

                classAC = typeof( options.autoCompleteClassName ) != "undefined" ? options.autoCompleteClassName : classAC;
                selClass = typeof( options.selectedClassName ) != "undefined" ? options.selectedClassName : selClass;

                attrCB = typeof( options.attrCallBack ) != "undefined" ? options.attrCallBack : attrCB;
                if( typeof( options.identifier ) == "string" )
                    getOptions.identifier = options.identifier;

                if( typeof( options.minLength ) == "undefined" ){
                    options.minLength = 3;
                }

                if ( typeof(options.extraParamFromInput ) != "undefined") {
                    getOptions.extraParam = $( options.extraParamFromInput ).val();
                }

                if (typeof( options.delay ) != "undefined") {
                    getOptions.delay = options.delay;
                } else {
                    getOptions.delay = 500;
                }

                kc = ( ( typeof( ev.charCode ) == 'undefined' || ev.charCode === 0 ) ? ev.keyCode : ev.charCode );
                
                if(kc == 96){
                	kc = 48; 
                }
                
                key = String.fromCharCode(kc);

                if (kc == 27)
                {
                    $('div.' + classAC).remove();
                }
                if (kc == 13)
                {
                    $('div.' + classAC + ' li.' + selClass).trigger('click');
                    return false;
                }

                comp_str_dig = stripAccents(thisElement.val()).toUpperCase();
                comp_str_list = stripAccents($('div.' + classAC + ' li.' + selClass).text()).toUpperCase();
                
                if ((key.match(/[a-zA-Z0-9_\- ]/) || kc == 8 || kc == 46) && getOptions.query.length >= options.minLength)
                {
                    thisElement.addClass('loading');
                    clearTimeout( self.searching );
                    self.searching = setTimeout(function() {
                        $.get(page, getOptions, function(r) {
                            thisElement.removeClass('loading');
                            $('div.' + classAC).remove();
                            var indexes = [];
                            autoCompleteList = $('<div>').addClass(classAC)//.html(r);
                            lista = $('<ul>');
                            if (r != '')
                            {
                                var js_counter = 0;
                                $.each(r, function(index, value) {
                                    curr_li = $('<li>' + value + '</li>').attr('id', 'autocomplete_'+index).data('id', index).appendTo(lista);
                                    if (js_counter ==0)
                                    {
                                        curr_li.addClass(selClass);
                                        if (comp_str_dig == stripAccents(value).toUpperCase())
                                            $('#'+newName).val(index);
                                    }
                                    js_counter++;
                                    indexes.push(index);
                                });
                                lista.appendTo(autoCompleteList)
                                autoCompleteList.insertAfter(thisElement);

                                var position = thisElement.position();
                                var height = thisElement.height();
                                var width = thisElement.width();

                                $('div.' + classAC).css({
                                    'top': ( height + position.top + 6 ) + 'px',
                                    'left': ( position.left )+'px',
                                    'margin': '0px'
                                });

                                $('div.' + classAC + ' ul').css({
                                    'margin-left': '0px'
                                });

                                $('div.' + classAC + ' li').each(function( n, el )
                                {
                                    el = $(el);
                                    el.mouseenter(function(){
                                        $('div.' + classAC + ' li.' + selClass).removeClass(selClass);
                                        $(this).addClass(selClass);
                                    });
                                    el.click(function(ev)
                                    {
                                        element = $(ev.target);
                                        thisElement.prev().val(element.data('id'));
                                        thisElement.attr('value', element.text());

                                        if( typeof( callback ) == "function" ) {
                                            callback(element.attr(attrCB).split('_'), element, thisElement.prev());
                                        }

                                        $('div.' + classAC).remove();
                                        thisElement.focus();
                                    });

                                    $.each(indexes, function(key, index) {
                                        var matches = /^__NO_([a-z]+)__$/ig.exec(index) || [],
                                        event   =  typeof matches[1] !== 'undefined' ? matches[1].toLowerCase() : null;
                                        if (event) {
                                            el.unbind(event);
                                        }
                                    });
                                });
                            }
                        });
                    }, getOptions.delay );


                }
                if (kc == 38 || kc == 40){
                    if ($('div.' + classAC + ' li.' + selClass).length == 0)
                    {
                        if (kc == 38)
                        {
                            $($('div.' + classAC + ' li')[$('div.' + classAC + ' li').length - 1]).addClass(selClass);
                        } else {
                            $($('div.' + classAC + ' li')[0]).addClass(selClass);
                        }
                    }
                    else
                    {
                        sel = false;
                        $('div.' + classAC + ' li').each(function(n, el)
                        {
                            el = $(el);
                            if ( !sel && el.hasClass(selClass) )
                            {
                                el.removeClass(selClass);
                                $($('div.' + classAC + ' li')[(kc == 38 ? (n - 1) : (n + 1))]).addClass(selClass);
                                sel = true;
                            }
                        });
                    }
                }
                if (thisElement.val() == '')
                {
                    $('div.' + classAC).remove();
                }

                if (kc!=37 && kc!=38 && kc!=39 && kc!=40 && kc!=13 && (!ev.shiftKey && comp_str_dig != comp_str_list))
                {
                    $('#'+newName).val('');
                }
            });
        }
    });
})(jQuery);