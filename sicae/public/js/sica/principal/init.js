var ASSETS_PATH = 'assets',
    BASE        = $('base').attr('href').replace(/\s*$/g, '');

if (BASE === '/') {
    BASE = '';
}

$('.sistema-load-menu').live('change', function() {
    var $this   =  $(this),
    $target =  $($this.attr('target')),
    value   =  $this.val();

    if (!value) {
        return;
    }

    $target.load(BASE + '/sistema/find-menu/id/' + value);
});

$(document).on('click', 'a.active-inactive', function(event){
    var link               = $(this),
    btnActiveInactive  = link.find('span'),
    active             = link.attr('active'),
    msgActive          = link.attr('msgActive'),
    msgInactive        = link.attr('msgInactive'),
    titleActive        = link.attr('titleActive'),
    titleInactive      = link.attr('titleInactive'),
    url                = link.attr('href');

    msg = btnActiveInactive.hasClass('icon-inativado')
    ? msgActive
    : msgInactive;

    UI_MSG     =  typeof UI_MSG !== 'undefined' ? UI_MSG : {};
    Message.showConfirmation({
        'body': msg in UI_MSG ? UI_MSG[msg] : msg,
        'yesCallback': function() {
            $.getJSON(url, {
                'status': btnActiveInactive.hasClass('icon-inativado') ? '1' : '0'
            }, function(response) {
                console.log(response);
                //caso ocorrer erro de permissão {success:false,code:403,message:'xxxx'}
                if (!response.success && response.hasOwnProperty('code')) {
                    Message.showError(response.message);
                    return false;
                }else{
                    Message.showMessage(response);
                    var tr = link.closest('tr'),
                    td = tr.find('td:eq(' + (tr.find('td').size() - 2) + ')');

                    if (btnActiveInactive.hasClass('icon-inativado')) {
                        btnActiveInactive.removeClass('icon-inativado')
                        .addClass('icon-ativado');
                        link.attr('title', titleActive);
                        td.text('Inativo');
                    } else {
                        btnActiveInactive.removeClass('icon-ativado')
                        .addClass('icon-inativado');
                        link.attr('title', titleInactive);
                        td.text('Ativo');
                    }
                }
            });
        }
    });

    return false;
});

$(document).ajaxError(function(response, xhr){
    if (xhr.status === 420) { // response code user logout
        window.location = BASE + '/usuario/login';
    }
});

$(document).ready(function(){
    $('.gerar-pdf').live('click', function(){
        var $this = $(this);
        window.location = BASE + $this.attr('href') + '?' + $('#pesquisa-pdf').val();
        return false;
    });

    // FIX Bug Twitter Bootstrap
    // https://github.com/twitter/bootstrap/issues/4803
    $('body').on('hidden', '#modal-view', function () {
        $(this).removeData('modal');
    });

    var buttonDelete;
    $('body').on('click', '.delete', function() {
        buttonDelete = $(this);
        Message.showConfirmation({
            'body': buttonDelete.attr('message'),
            'yesCallback': function() {
                window.location = buttonDelete.attr('href');
            }
        });
        return false;
    });

    // atalho padrao para filtro
    $('.filtro').click(function(){
        $(document).scrollTop(0);
        return false;
    });
});
