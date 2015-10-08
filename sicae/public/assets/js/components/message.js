var Message = (function()
{
    var TYPE_ERROR   = 1;
    var TYPE_ALERT   = 2;
    var TYPE_SUCCESS = 3;

    var caption = "Caption padrão";
    var body    = "Body padrão";
    var icon = "";

    function generateDialogDiv(dialogId, caption, body, icon)
    {
        return '<div class="modal message-show" id="' + dialogId +'">' +
               '<div class="modal-header">'+
               '<button class="close" data-dismiss="modal">×</button>'+
               '<h3>' + caption + '</h3>'+
               '</div>'+
               '<div class="modal-body">'+
               '<p>' + body + '</p>'+
               '</div>'+
               '<div class="modal-footer">'+
               '<a href="javascript:;" class="btn btn-primary" data-dismiss="modal">Fechar</a>'+
               '</div>'+
               '</div>';

        var id = '#' + dialogId;
    }

    function getClass(type)
    {
        var classType = undefined;

        switch(type)
        {
            case TYPE_ERROR:   classType = 'alert alert-error'; break;
            case TYPE_ALERT:   classType = 'alert'; break;
            case TYPE_SUCCESS: classType = 'alert alert-success'; break;
            case 'confirm':    classType = 'alert alert-info';break;
        }

        return classType;
    }

    function getIcon(type)
    {
        var icon = undefined;

        switch(type)
        {
            case TYPE_ERROR:   icon = 'img/icon-error.gif'; break;
            case TYPE_ALERT:   icon = 'img/icon-warning.gif'; break;
            case TYPE_SUCCESS: icon = 'img/icon-info.gif'; break;
            case 'confirm':    icon = 'img/icon-question.gif'; break;
        }

        return icon;
    }

    function showAlert(message, callback) {
        var caption = "Alerta";
        var body = message;
        var icon = getIcon(TYPE_ALERT);
        var dialogId = "message_" + new Date().getTime();
        var dialogDiv = generateDialogDiv(dialogId, caption, body, icon);
        $(dialogDiv).appendTo("body");

        $("#" + dialogId).modal().on('hide', function(){
            if (typeof callback === 'function') {
                callback();
            }
        });
    }

    function showError(message, callback) {
        var caption = "Erro";
        var body = message;
        var icon = getIcon(TYPE_ERROR);
        var dialogId = "message_" + new Date().getTime();
        var dialogDiv = generateDialogDiv(dialogId, caption, body, icon);
        $(dialogDiv).appendTo("body");

        $("#" + dialogId).modal().on('hide', function(){
            if (typeof callback === 'function') {
                callback();
            }
        });
    }

    function showSuccess(message, callback) {
        var caption = "Sucesso";
        var body = message;
        var icon = getIcon(TYPE_SUCCESS);
        var dialogId = "message_" + new Date().getTime();
        var dialogDiv = generateDialogDiv(dialogId, caption, body, icon);
        $(dialogDiv).appendTo("body");

        $("#" + dialogId).modal().on('hide', function(){
            if (typeof callback === 'function') {
                callback();
            }
        });
    }

    function showMessage(response)
    {
        var message   = response.message;
        var caption   = message.subject   || "Erro";
        var body      = message.body      || "Body padrão";
        var icon      = getIcon(message.type);
        var classType = getClass(message.type);
        // class='"+classType+"'
        var str       = "<div >" + body + "</div>";

        return bootbox.dialog(str, {"label": 'Fechar'}, {"header": caption});

    }

        function show(title, response, callback)
    {
            bootbox.dialog(response, {
                'label' : 'Fechar',
                'class' : 'btn-primary', // or primary, or danger, or nothing at all
                'callback': callback
            }, {header: title});
    }

    function showDialog(options)
    {
        var settings = $.extend( {
            type: 'success',
            subject: '',
            body: '',
            closeLabel: 'Fechar'
        }, options);

        icon = getIcon(settings.type);

        var dialog = bootbox.dialog(
            settings.body, 
            [{ label: settings.closeLabel }],
            { header: settings.subject }
        ).on("hidden", function() {
            if (typeof settings.callback === 'function') {
                settings.callback();
            }
        });

        if (settings.large && settings.large === true) {
            dialog.addClass('modal-large');
        }
    }

    function showConfirmation(options)
    {
        var settings = $.extend( {
                      'type' : 'confirm',
                   'subject' : 'Confirmação',
                      'body' : '',
               'yesCallback' : null,
                'noCallback' : null,
            'cancelCallback' : null,
                  'yesLabel' : 'Sim',
                   'noLabel' : 'Não'
        }, options);

        icon = getIcon(settings.type);

        /* executado quando clicar fora da modal ou no "×" */
        if (settings.cancelCallback) {
            $('.modal-backdrop,.close')
            .die('click').live('click', function (event){
                event.preventDefault();
                settings.cancelCallback();
            });
        }

        bootbox.confirm(settings.body, settings.noLabel, settings.yesLabel,
            function(result) {
            if (result) {
                if (settings.yesCallback) settings.yesCallback.call();
            }
            else
            {
                if (settings.noCallback)
                    settings.noCallback.call();
            }

            }, settings.subject);

    }

    function isType(data, type) {
        data = data || {};
        if (typeof data === 'string') {
            try {
                data = $.parseJSON(data);
            } catch (error) {
                data = {};
            }
        }

        if (!('message' in data)) {
            return false;
        }

        if (!('type' in data.message)) {
            return false;
        }

        return type === data.message.type;
    }

    return {
        show: show,
        showConfirmation: showConfirmation,
        showMessage: showMessage,
        SUCCESS: 3,
        ALERT:   2,
        ERROR:   1,
        isSuccess: function(data) {
            return isType(data, TYPE_SUCCESS);
        },
        isAlert:   function(data) {
            return isType(data, TYPE_ALERT);
        },
        isError:   function(data) {
            return isType(data, TYPE_ERROR);
        },
        hasMessage: function(response) {
            response = response || {};
            return 'message' in response;
        },
        showAlert: showAlert,
        showSuccess: showSuccess,
        showError: showError,
        showDialog: showDialog
    }

})();