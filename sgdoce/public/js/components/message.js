var Message = (function ()
{
    var TYPE_ERROR = 1;
    var TYPE_ALERT = 2;
    var TYPE_SUCCESS = 3;

    var caption = "Caption padrão";
    var body = "Body padrão";
    var icon = "";

    function generateDialogDiv(dialogId, caption, body, icon) {
        return '<div class="modal" id="' + dialogId + '">' +
                '<div class="modal-header">' +
                '<button class="close" data-dismiss="modal">×</button>' +
                '<h3>' + caption + '</h3>' +
                '</div>' +
                '<div class="modal-body">' +
                '<p>' + body + '</p>' +
                '</div>' +
                '<div class="modal-footer">' +
                '<a href="javascript:;" class="btn btn-primary" data-dismiss="modal">Fechar</a>' +
                '</div>' +
                '</div>';
        /*
         return "<div id='" + dialogId + "' title='" + caption + "' style='display:none;'>" +
         "<div class='messageIcon'><img src='" + icon + "'></div>"+
         "<div class='messageBody'>" + body + "</div>" +
         "</div>";
         */
    }

    function getClass(type) {
        switch (type)
        {
            case TYPE_ERROR:
                var classType = 'alert alert-error';
                break;
            case TYPE_ALERT:
                var classType = 'alert';
                break;
            case TYPE_SUCCESS:
                var classType = 'alert alert-success';
                break;
            case 'confirm':
                var classType = 'alert alert-info';
                break;
        }

        return classType;
    }

    function getIcon(type) {
        switch (type)
        {
            case TYPE_ERROR:
                var icon = 'img/icon-error.gif';
                break;
            case TYPE_ALERT:
                var icon = 'img/icon-warning.gif';
                break;
            case TYPE_SUCCESS:
                var icon = 'img/icon-info.gif';
                break;
            case 'confirm':
                var icon = 'img/icon-question.gif';
                break;
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

        $("#" + dialogId).modal().on('hide', function () {
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

        $("#" + dialogId).modal().on('hide', function () {
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

        $("#" + dialogId).modal().on('hide', function () {
            if (typeof callback === 'function') {
                callback();
            }
        });
    }

    function showMessage(response) {
        var message = response.message;

        var caption = message.subject || "Erro";
        var body = message.body || "Body padrão";
        var icon = getIcon(message.type);
        var classType = getClass(message.type);
        // class='"+classType+"'
        var str = "<div >" + body + "</div>";

        return bootbox.dialog(str, {"label": 'Fechar'}, {"header": caption});

    }

    function show(title, response, callback) {
        bootbox.dialog(response, {
            'label': 'Fechar',
            'class': 'btn-primary', // or primary, or danger, or nothing at all
            'callback': callback
        }, {header: title});
    }

    function showConfirmation(options) {
        var settings = $.extend({
            'type': 'confirm',
            'subject': 'Confirmação',
            'body': '',
            'yesCallback': null,
            'noCallback': null
        }, options);

        icon = getIcon(settings.type);

        bootbox.confirm(settings.body, 'Não', 'Sim', function (result) {
            if (result) {
                if (settings.yesCallback)
                    settings.yesCallback.call();
            } else {
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

    function wait(message) {
        if($('#jquery-msg-overlay').length){
            $('#jquery-msg-overlay').remove();
        }
        
        message = message || '<div class="bubbling"><span></span><span></span><span></span></div>';
        $.msg({
            content: message,
            z: 2000,
            bgPath: 'img/',
            autoUnblock: false,
            clickUnblock: false});
    }

    function waitClose() {
        $.msg('unblock');
    }

    /**
     *
     * @param {string} message
     * @param {boolean} fadeout
     * @param {jQuery} parent
     * @returns {void}
     */
    function showSuccessNotification(message, fadeout, parent) {
        showNotification(message, TYPE_SUCCESS, fadeout, parent);
    }

    /**
     *
     * @param {string} message
     * @param {boolean} fadeout
     * @param {jQuery} parent
     * @returns {void}
     */
    function showAlertNotification(message, fadeout, parent) {
        showNotification(message, TYPE_ALERT, fadeout, parent);
    }

    /**
     *
     * @param {string} message
     * @param {boolean} fadeout
     * @param {jQuery} parent
     * @returns {void}
     */
    function showErrorNotification(message, fadeout, parent) {
        showNotification(message, TYPE_ERROR, fadeout, parent);
    }


    /**
     *
     * @param {string} message
     * @param {integer} type
     * @param {boolean} fadeout
     * @param {jQuery} parent
     * @returns {void}
     */
    function showNotification(message, type, fadeout, parent) {

        parent = parent || null;
        fadeout = fadeout || true;
        var scroll = false;
        if (!parent) {
            if (!$('.notificationContainer').length) {
                $('<div />', {'class': 'notificationContainer hide'}).insertAfter('h1:visible:last');
            }
            parent = $('.notificationContainer');
            scroll = true
        }

        var divId = "notification_" + new Date().getTime();

        var divM = $('<div id="' + divId + '" class="' + getClass(type) + '">' +
                '<button class="close" data-dismiss="alert">×</button>' + message + '</div>');

        if (fadeout){
            setTimeout(function () {
                $('#' + divId).fadeOut(2000, function () {
                    $(this).remove()
                });
            }, 5000);
        }

        parent.prepend(divM).show();
        if (scroll) {
            $(document).scrollTop(0);
        }
    }


    return {
        show: show,
        showConfirmation: showConfirmation,
        showMessage: showMessage,
        SUCCESS: 3,
        ALERT: 2,
        ERROR: 1,
        isSuccess: function (data) {
            return isType(data, TYPE_SUCCESS);
        },
        isAlert: function (data) {
            return isType(data, TYPE_ALERT);
        },
        isError: function (data) {
            return isType(data, TYPE_ERROR);
        },
        hasMessage: function (response) {
            response = response || {};
            return 'message' in response;
        },
        showAlert: showAlert,
        showSuccess: showSuccess,
        showError: showError,
        wait: wait,
        waitClose: waitClose,
        showSuccessNotification: showSuccessNotification,
        showAlertNotification: showAlertNotification,
        showErrorNotification: showErrorNotification
    };

})();