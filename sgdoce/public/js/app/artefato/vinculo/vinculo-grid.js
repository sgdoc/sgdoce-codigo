if (!String.prototype.format) {
  String.prototype.format = function() {
    var args = arguments;
    return this.replace(/{(\d+)}/g, function(match, number) { return typeof args[number] !== 'undefined' ? args[number] : match; });
  };
};

cl = function() {
    return console.log.apply(console, arguments);
};

/**
 * Vinculo Grid = vGrid
 *
 * para cada novo metodo de acao será necesario uma entrada no MSG_CODE de
 * mesmo nome, que será usada como prompt de confirmação
 * */
var vGrid = {

    /* pseudo static*/
     MSG_CONFIRM_CODE: {
       "anexar"             : "MN114",
       "anexarProcesso"     : "MN114",
       "apensar"            : "MN115",
       "apensarProcesso"    : "MN115",
       "apensarMultiProc"   : "MN115",
       "inserirPeca"        : "MN116",
       "desanexar"          : "MN117",
       "desanexarProcesso"  : "MN117",
       "desapensar"         : "MN118",
       "desapensarProcesso" : "MN118",
       "desapensarMultiProc": "MN118",
       "removerPeca"        : "MN119"
    },

    T_DICT_ACTION: {
       "anexar"             : "juntada",
       "anexarProcesso"     : "juntada",
       "apensar"            : "juntada",
       "apensarProcesso"    : "juntada",
       "inserirPeca"        : "juntada",
       "apensarMultiProc"   : "juntada",
       "desanexar"          : "remocao",
       "desanexarProcesso"  : "remocao",
       "desapensar"         : "remocao",
       "desapensarProcesso" : "remocao",
       "desapensarMultiProc": "remocao",
       "removerPeca"        : "remocao"
    },

    T_TERM_NAME: {
       "anexar"             : "anexar",
       "anexarProcesso"     : "anexar",
       "apensar"            : "apensar",
       "apensarProcesso"    : "apensar",
       "apensarMultiProc"   : "apensar",
       "inserirPeca"        : "inserirPeca",
       "desanexar"          : "desanexar",
       "desanexarProcesso"  : "desanexar",
       "desapensar"         : "desapensar",
       "desapensarProcesso" : "desapensar",
       "desapensarMultiProc": "desapensar",
       "removerPeca"        : "removerPeca"
    },

    T_MODAL_FORM_TERMO_JUNTADA_ID: "#modal-form-termo-juntada-por-apensacao-anexacao",

    /* pseudo static */
    T_URL_GERAR_TERMO_JUNTADA: 'gerar-termo-juntada-{0}',

    T_URL_DESMEMBRAR_DESENTRANHAR: 'artefato/desmembrar-desentranhar/%s/id/%d',

    init: function () {
        vGrid.eventBtn().initGrid();

        var wasSend = false;
        
        var acMult = $(".acaoMultDesApenProc");

        var tpArt = $('#tpArt');
        var nrArt = $('#nrArt');
        //carrega as opções de tipo de artefato disponivel para vinculo de acordo com o artefato paizão
        $.ajax({
            url     : "/auxiliar/tipo-artefato/list-items-vinculo-artefato/sqTipoArtefatoParent/"+$("#sqTipoArtefatoParent").val(),
            dataType: "json",
            type    : "get",
            success : function(data){
                for(var i in data){
                    tpArt.append('<option value="'+data[i].value+'">' + data[i].text + '</option>');
                }
            }
        });

        tpArt.change(function(){
            wasSend = false;
            nrArt.val('');
            if ($(this).val()){
                nrArt.removeProp('readonly');
            }else{
                nrArt.prop('readonly','readonly');
            }
        });

        nrArt.on('keyup',function(e){
            if ((!tpArt.val() || $(this).val().length < 3) && !wasSend) {
                return;
            }
            wasSend = true;
            $('#nuArtefato').val($(this).val());
            $('#sqArtefatoTipo').val(tpArt.val());
            $('#grid-artefato-vinculo').dataTable().fnDraw(false);
        });
        
        acMult.on('click', function(){
            var vinculos = $("input[name='vinculo[]']:checked"),
                acao = $(this).attr('id'), stop = false;
            var options  = { parent : null, child : {}, number: "" }, action = null, msg = "";
            
            $.each(vinculos, function(index, checkbox){
                var source = $(checkbox),
                    isVinculo = false;
                options.parent = source.data('sqartefato-parent');
                options.child[index] = source.data('sqartefato-child');
                options.number += source.data('nuartefato') + ", ";
                options.action =  source.data('action');
                
                isVinculo = source.data('isvinculo');
                
                if( action == null ){
                    action = options.action;
                }
                
                if( acao == 'acaoMultApenProc' && isVinculo == 1 ) {
                    stop = true;
                    msg += sprintf(UI_MSG['MN203'], source.data('nuartefato')) + "<br />";
                }
                
                if( acao == 'acaoMultDesApenProc' && isVinculo == 0 ) {
                    stop = true;
                    msg += sprintf(UI_MSG['MN204'], source.data('nuartefato')) + "<br />";
                }
            });    
            
            if( stop ){
                Message.showError(msg);
                return;
            }
            
            options.termo   = vGrid.T_DICT_ACTION[options.action];            
            
            var msgCode = vGrid.MSG_CONFIRM_CODE[options.action];
            
            Message.showConfirmation({
                       body: sprintf(UI_MSG[msgCode], options.number, options.parent),
                yesCallback: function () { vGrid[options.action](options); }
            });
            
            return;
        });
        
    },

    initGrid: function () {
        /* inicializa a grid para mostrar o docs anexados ao artefato selecionaod */
        Grid.load($('#form-artefato-vinculo'), $('#grid-artefato-vinculo'));
        return vGrid;
    },

//    reloadGrid: function() {
//        $('#form-artefato-vinculo').submit();
//        return vGrid;
//    },

    refresh: function () {
        $('#grid-artefato-vinculo').dataTable().fnDraw(false);
        return vGrid;
    },

    eventBtn: function () {
        var that = this;
        $('#grid-artefato-vinculo').on('click', '.elmAction', function () {

            var source = $(this);

            var options = {
                source: source,
                action: source.data('action'),
                 child: source.data('sqartefato-child'),
                parent: source.data('sqartefato-parent'),
                number: source.attr('data-nuartefato')
//                number: source.data('nuartefato') //não usa o data pois para nr de processo o .data faz parseInt e da zica
                                                    //exemplo 021600000250201591 fica 21600000250201590
            };

            options.hasTerm = options.source.data('has-term');
            options.termo   = vGrid.T_DICT_ACTION[options.action];

            var action = that[options.action];

            if (typeof action !== "function") {
                Message.showError('Operação indisponível');
                return;
            }

            var msgCode = vGrid.MSG_CONFIRM_CODE[options.action];

            Message.showConfirmation({
                       body: sprintf(UI_MSG[msgCode], options.number, options.parent),
                yesCallback: function () { action(options); }
            });
        });
        return vGrid;
    },

    desmembrar: function (sqArtefato) {
        $("#modal-form-desmembrar-desentranhar").load(
            sprintf(vGrid.T_URL_DESMEMBRAR_DESENTRANHAR,
                    'desmembrar',
                    sqArtefato
        )).modal();
    },

    desentranhar: function (sqArtefato) {
        $("#modal-form-desmembrar-desentranhar").load(
            sprintf(vGrid.T_URL_DESMEMBRAR_DESENTRANHAR,
                    'desentranhar',
                    sqArtefato
        )).modal();
    },

    anexar: function (options, _url) {

        if( _url == undefined ){
            _url = "/artefato/vinculo/anexar/";
        }

        var hasTerm = options.source.data('has-term');

        var doAnexar = function (options) /* throws Error */ {
            options.config = {
                type: "post",
                dataType: "json",
                url: _url,
                data: {"parent": options.parent, "child": options.child}
            };

            vGrid.doRequest(options);
        };

        /* se houver termo, agenda vinculacao */
        if (hasTerm) {

            options.callback = doAnexar;

            vGrid.gerar_termo(options);
        }

        /* se nao houver termo, apenas anexa */
        if (! hasTerm) {
            doAnexar(options);
        }
    },

    anexarProcesso: function(options){
        var _url = "/artefato/vinculo/anexar-processo/";
        vGrid.anexar(options, _url);
    },

    desanexar: function (options, _url) {

        if( _url == undefined ){
            _url = "/artefato/vinculo/desanexar/";
        }

        options.config = {
            type: "post",
            dataType: "json",
            url: _url,
            data: {"parent": options.parent, "child": options.child}
        };

        vGrid.doRequest(options);
    },

    desanexarProcesso: function(options){
        var _url = "/artefato/vinculo/desanexar-processo/";
        vGrid.desanexar(options, _url);
    },

    apensar: function (options, _url) {

        if( _url == undefined ){
            _url = "/artefato/vinculo/apensar/";
        }

        var hasTerm = options.source.data('has-term');
        
        var doApensar = function (options) {
            options.config = {
                type: "post",
                dataType: "json",
                url: _url,
                data: {"parent": options.parent, "child": options.child}
            };

            vGrid.doRequest(options);
        };

        /* se houver termo, agenda vinculacao */
        if (hasTerm) {

            options.callback = doApensar;

            vGrid.gerar_termo(options);
        }

        /* se nao houver termo, apenas anexa */
        if (! hasTerm) {
            doApensar(options);
        }
    },

    apensarProcesso: function(options){
        var _url = "/artefato/vinculo/apensar-processo/";
        vGrid.apensar(options, _url);
    },
    
    apensarMultiProc: function(options){      
        var _url = "/artefato/vinculo/apensar-multi-proc/";

        var hasTerm = 1;
        
        var doApensar = function (options) {
            
            options.config = {
                type: "post",
                dataType: "json",
                url: _url,
                data: {"parent": options.parent, "child": options.child}
            };
            
            vGrid.doRequest(options);
        };

        /* se houver termo, agenda vinculacao */
        if (hasTerm) {
            options.callback = doApensar;
            vGrid.gerar_termo(options);
        }

        /* se nao houver termo, apenas anexa */
        if (!hasTerm) {
            doApensar(options);
        }  
    },
    
    desapensarMultiProc: function(options){      
        var _url = "/artefato/vinculo/desapensar-multi-proc/";

        var hasTerm = 1;
        
        var doDesapensar = function (options) {
            
            options.config = {
                type: "post",
                dataType: "json",
                url: _url,
                data: {"parent": options.parent, "child": options.child}
            };
            
            vGrid.doRequest(options);
        };

        /* se houver termo, agenda vinculacao */
        if (hasTerm) {
            options.callback = doDesapensar;
            vGrid.gerar_termo(options);
        }

        /* se nao houver termo, apenas anexa */
        if (!hasTerm) {
            doDesapensar(options);
        }  
    },
    
    desapensar: function (options, _url) {

        if( _url == undefined ){
            _url = "/artefato/vinculo/desapensar/";
        }

        var hasTerm = options.source.data('has-term');

        var doDesapensar = function doDesapensar(options) {
            options.config = {
                type: "post",
                dataType: "json",
                url: _url,
                data: {"parent": options.parent, "child": options.child}
            };

            vGrid.doRequest(options);
        };

        /* se houver termo, agenda vinculacao */
        if (hasTerm) {

            options.callback = doDesapensar;
            vGrid.gerar_termo(options);
        }

        /* se nao houver termo, apenas anexa */
        if (! hasTerm) {
            doDesapensar(options);
        }
    },

    desapensarProcesso: function (options) {
        var _url = "/artefato/vinculo/desapensar-processo";
        vGrid.desapensar(options, _url);
    },

    inserirPeca: function (options) {

        var hasTerm = options.source.data('has-term');

        var doInserirPeca  = function (options) {
            options.config = {
                type: "post",
                dataType: "json",
                url: "/artefato/vinculo/inserir-peca/",
                data: {"parent": options.parent, "child": options.child}
            };

            vGrid.doRequest(options);
        };

        /* se houver termo, agenda vinculacao */
        if (hasTerm) {

            options.callback = doInserirPeca;

            vGrid.gerar_termo(options);
        }

        /* se nao houver termo, apenas anexa */
        if (! hasTerm) {
            doInserirPeca(options);
        }
    },

    removerPeca: function (options) {

        var hasTerm = options.source.data('has-term');

        var doRemoverPeca  = function (options) {
            options.config = {
                type: "post",
                dataType: "json",
                url: "/artefato/vinculo/remover-peca/",
                data: {"parent": options.parent, "child": options.child}
            };

            vGrid.doRequest(options);
        };

        /* se houver termo, agenda vinculacao */
        if (hasTerm) {

            options.callback = doRemoverPeca;

            vGrid.gerar_termo(options);
        }

        /* se nao houver termo, apenas anexa */
        if (! hasTerm) {
            doRemoverPeca(options);
        }
    },

    doRequest: function (options) {
        Message.wait();
        $.ajax(options.config)
         .done(function (result) {
            vGrid.refresh();

            $(vGrid.T_MODAL_FORM_TERMO_JUNTADA_ID).modal('hide').empty();

            Message.waitClose();
            if (result.status) {
                if( typeof UI_MSG[result.message] != "undefined" ) {                
                    Message.showSuccessNotification(UI_MSG[result.message]);
                } else {
                    Message.showSuccessNotification(result.message);
                }
                if (typeof options.callback === "function") {
                    var callback = options.callback;
                    delete options.callback;
                    callback(options);
                }
            }else{
                Message.showError(UI_MSG[result.message] + '<br />' + result.errorCompl);
            }
         });
    },

    gerar_termo: function (options) {
        var term = vGrid.T_TERM_NAME[options.action],
            child = "",
            urlFormTermo = "";
    
        if( !$.isNumeric(options.child) ) {
            $.each(options.child, function(index, item){
                child += "/child/" + item;
            });
            urlFormTermo = "/artefato/vinculo/form-termo-{0}-{1}/parent/{2}{3}/tOper/{1}"
                         .format(
                            options.termo,
                            term,
                            options.parent,
                            child
                         );
        } else {
            child = options.child;
            urlFormTermo = "/artefato/vinculo/form-termo-{0}-{1}/parent/{2}/child/{3}/tOper/{1}"
                         .format(
                            options.termo,
                            term,
                            options.parent,
                            child
                         );
        }

        var MContainer = $(vGrid.T_MODAL_FORM_TERMO_JUNTADA_ID);
            MContainer.load(urlFormTermo, function (responseText, textStatus) {
                if (textStatus === 'success') {
                    MContainer.modal();
                } else {
                    Message.showError(responseText);
                }
            });//.modal();
            MContainer.off('click','#btn-gerar-termo-juntada')
                      .on('click', '#btn-gerar-termo-juntada', function (event) {
                try{
                    var form = $('#form-termo-juntada');

                    if (! form.valid()) { return false; }

                    /* invoca o callback antes de forçar o download */
                    if (typeof options.callback === "function") {

                        var callback = options.callback;

                        /* complementa as informações de paramentro */
                        options.tOper     = term;
                        options.cargo     = $('#sqCargo').val();
                        options.funcao    = $('#sqFuncao').val();
                        options.assinante = $('#sqAssinante_hidden').val();
                        options.despacho  = $('#sqDespachoInterlocutorio').val();

                        /* define a nova tarefa que devera ser executada */
                        options.callback = function () {
                            var downloader = 'do_download_termo_' + options.termo;
                            vGrid[downloader](options);
                        };

                        /* executa o callback agendado até outrora */
                        callback(options);
                    }

                } catch (e) {
                    console.log('Não foi possível anexar o Artefato');
                }

            });
    },

    do_download_termo_juntada: function (options) {
        vGrid.do_download_termo("/artefato/vinculo/termo-juntada-"+options.tOper+"?", options);
    },

    do_download_termo_remocao: function (options) {
        vGrid.do_download_termo("/artefato/vinculo/termo-remocao-"+options.tOper+"/?", options);
    },

    do_download_termo: function (s_url, options) {
        var params  = '';
        var options = options;
        delete options.source;
        delete options.callback;

        for (var p in options) {             
            if( typeof options[p] == 'object' ) {
                for( y in options[p] ){                    
                    params += "&{0}[]={1}".format(p, options[p][y]); 
                }  
            } else {
                params += "&{0}={1}".format(p, options[p]); 
            }
        }
        
        s_url += params.substr(1);
        
        $('<iframe/>', {
                src: s_url,
                style: 'display:none',
                load:function(){
                    var error_code = $('body', $(this).contents() ).find('.alert-error').text().replace('×', '');
                    if (error_code) { Message.showError(UI_MSG[error_code]); }
                }
            }).appendTo('body');
    },
    
    up_row: function(id){
        
        var _url = "/artefato/vinculo/ordenar/";
        
        var options = {};
        
        options.config = {
            type: "post",
            dataType: "json",
            url: _url,
            data: {"id": id, "op": 'up'}
        };

        vGrid.doRequest(options);
    },
    
    down_row: function(id){
        
        var _url = "/artefato/vinculo/ordenar/";
        
        var options = {};
        
        options.config = {
            type: "post",
            dataType: "json",
            url: _url,
            data: {"id": id, "op": 'down'}
        };

        vGrid.doRequest(options);
    }

};

$(document).ready(function(){ vGrid.init(); });