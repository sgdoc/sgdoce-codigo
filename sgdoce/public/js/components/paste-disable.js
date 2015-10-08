var PasteDisable = {
    init:function(){
        //DESABILITAR A SELEÇÃO DE TEXTOS BODY PARA COPIAR
        PasteDisable.disableSelection(document.body);
        PasteDisable.disablePasteEvent();

        $(document).on('shown.bs.modal', '.modal', function (e) {
            PasteDisable.disablePasteEvent($(this).find(':input'));
        });
    },
    disableSelection: function (target){
        if (typeof target.onselectstart!="undefined") {//IE route
            target.onselectstart=function(){return false;};
        } else if (typeof target.style.MozUserSelect!="undefined") {//Firefox route
            target.style.MozUserSelect="none";
        } else {//All other route (ie: Opera)
            target.onmousedown=function(){return false;};
            target.style.cursor = "default";
        }
    },
    disablePasteEvent:function(selector){
        //BLOQUEANDO O EVENDO DE COLAR NOS INPUTS
        selector = selector || $('input,textarea');
        var inputSelector = selector.not('.canPaste,input[type="hidden"]');
        inputSelector.each(function() {
            var elem = $(this);
            if (!elem.data('paste-disable')) {
                elem.data('paste-disable',true);
                this.addEventListener('paste',function (event) {
                    Message.showAlert('Não é permitido colar neste campo.');
                    event.preventDefault();
                });
            }
        });
    }
};

$(PasteDisable.init);