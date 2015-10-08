var resizeSistema = {
    init: function () {
        var tamanho = 0,
            elementos = $('.welcome-system p'),
            total = elementos.length;

        elementos.each(function (index) {
            if (tamanho < $(this).height()) {
                tamanho = $(this).height();
            }
            if (index + 1 === total) {
                elementos.css('min-height', tamanho);
            }
        });
    }
};

$(document).ready(function () {
    $(window).resize(function () {
        resizeSistema.init();
    });

    resizeSistema.init();

    var findCallback = function () {
        var query = $.trim($('#finder-txt').val()),
            boxes = $('.welcome-system'),
            finderMessage = $('#finder-message'),
            finderContainer = $('#finder-container'),
            total = boxes.length,
            fx = 'fast',
            show = function (box, withFx) {
                withFx = withFx || true;
                box.removeClass('welcome-system-hidden').addClass('welcome-system-shown');
                if (withFx) {
                    box.fadeIn(fx);
                } else {
                    box.show();
                }
            },
            hide = function (box, withFx) {
                withFx = withFx || true;
                box.removeClass('welcome-system-shown').addClass('welcome-system-hidden');
                if (withFx) {
                    box.fadeOut(fx);
                } else {
                    box.hide();
                }
            },
            organize = function (boxes) {
                var visibleBoxes = $('.welcome-system.welcome-system-shown');
                boxes.removeClass('welcome-system-first');
                for (var i = 0; i < visibleBoxes.length; i += 4) {
                    visibleBoxes.slice(i, i + 4).eq(0).addClass('welcome-system-first');
                }
                if (! visibleBoxes.length) {
                    show(finderMessage);
                    finderContainer.addClass('error');
                }
            };

        hide(finderMessage);
        finderContainer.removeClass('error');

        hide(boxes, false);
        if (query !== '') {

            var accentsStrip = function(text){
                var normalized = text.toLowerCase();
                var nonASCIIs = {
                    'a': '[àáâãäå]',
                    'c': 'ç',
                    'e': '[èéêë]',
                    'i': '[ìíîï]',
                    'n': 'ñ',
                    'o': '[òóôõö]',
                    'u': '[ùúûűü]',
                    'y': '[ýÿ]',
                    'ae': 'æ',
                    'oe': 'œ',
                    '...': '…'
                };
                for (var i in nonASCIIs) {
                    normalized = normalized.replace(new RegExp(nonASCIIs[i], 'img'), i);
                }
                normalized = normalized.replace(/\s+/g,' ');
                return normalized;
            };

            boxes.each(function (index) {
                var regexp = new RegExp(accentsStrip(query), 'img'),
                    box = $(this);
                if (regexp.test(accentsStrip(box.data('text-find-me')))) {
                    show(box);
                } else {
                    hide(box);
                }
                if (index + 1 === total) {
                    organize(boxes);
                }
            });
        } else {
            show(boxes);
            organize(boxes);
        }
    };

    $('#finder-txt').keyup(function (event) {
        var length = $.trim($(this).val()).length;
        if (length <= 0 || event.keyCode === 13) { //ENTER
            findCallback();
        }
    });
    $('#finder-btn').click(findCallback).trigger('click');
    $('[rel=popover]').popover({delay:{show:350,hide:0}});
    $('.welcome-system').each(function () {
        $(this).hover(
            function () {
                $(this).addClass('active').find('.btn').trigger('focus');
            }, 
            function () {
                $(this).removeClass('active').find('.btn').trigger('blur');
            }
        )
    });
    $('.welcome-system .btn').focus(function (){
        if (! $('.modal-backdrop.in').is(':visible')){
            $(this).addClass('active').parents('[rel=popover]:not(.active)').popover('show');
        }
    });
    $('.welcome-system .btn').blur(function (){
        $(this).removeClass('active').parents('[rel=popover]:not(.active)').popover('hide');
    });
    $('.welcome-system .btn').click(function (){
        var btn = $(this);
        btn.button('loading');
        Sistemas.verifica(btn.data('sistema'), function (){
            btn.trigger('blur');
            btn.button('reset');
        });
    });
});