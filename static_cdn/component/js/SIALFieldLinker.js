/**
 * encadead o conteudo de um campo baseado no preenchimento de outro
 *
 * @example
 *     fildLinker({
 *           on: 'change',                                           // evento que ira disparar a busca de dados
 *       source: {elm: $('#source_id_field'), type: 'select'},       // referencia do campo de origem
 *       target: {elm: $('#target_id_field'), type: 'select'},       // referencia do campo onde sera acomodado os dados recuperados
 *  columnModel: {
 *       dValue: 'result_column_name_used_as_VALUE_in_source_field', // nome da propriedade usada como valor
 *        dText: 'result_column_name_used_as_TEXT_in_source_field',  // nome da propriedade usada para exibicao, esta prop sera usada para campos do tipo select
 *   },
 *          xhr: {
 *              url: '/url_from_data', // onde sera recuperado os dados de preenchimento
 *             type: 'post',           // [get | post | head | ...]
 *         dataType: 'json',           // [json | html | text]
 *            cache: false             // boolean,
 *       beforeSend: function (config) { ... } // ao executar o callback, o objeto de configuracao ser injetado no proprio callback
 *        afterSend: function (config) { ... } // ao executar o callback,
 *          }
 *   });
 *
 * @author j. augusto
 * @version 0.0.2
 * */
var SIALFieldLinker = function (config) {

    /* agenda evento que disparará a busca dos dados */
    config.source.elm[config.on](function () {
        /*
         *  note que foi delegado ao jQuery a recuperacao do valor
         *  do campo, mas que isso pode ser trocado por codigo nativo
         *  tendo em vista que config.source.type informa o tipo do
         *  campo em questao
         * */
        config.xhr.data = {};
        config.xhr.data[this.name] = $(this).val();
        $.extend(config.xhr.data, config.xhr.extraParam);
        config.loading = 'data:image/gif;base64,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';

        // <img width="16" height="16" title="" alt="" src="" />

        /* define o comportamento a ser executado quando
         * recuperar os dados
         * * * * * * * * * * * * * * */
        config.xhr.success = undefined != config.xhr.success
                                        ? config.xhr.success
                                        : function (result) {
                                            var target = config.target.elm.empty();

                                            /* criar plugin para preenchimento de outros tipos de campo diferentes de select(combo) */
                                            for (var m in result) {
                                                target.append(
                                                    $('<option>').val(result[m][config.columnModel.dValue])
                                                                 .text(result[m][config.columnModel.dText])
                                                );
                                            }

                                            $('#icon_loading', config.target.elm.parent()).remove();
                                            var evName = config.target.elm.attr('name') + '_loaded';
                                            $(document).trigger(evName);
                                        }

        /*
         * define o comportamento a ser executado quando
         * recuperacao dos dados gerar um error
         * * * * * * * * * * * * * * */
        config.xhr.error = undefined != config.xhr.error
                                      ? config.xhr.error
                                      : function (result) { console.log(result); }

        /* cira requisicao dos dados */
        var xhr = {
            /* define o tipo de conexao utilizada para recuperar os dados. get eh o default */
            type: config.xhr.type,
        dataType: config.xhr.dataType,
           cache: config.xhr.cache,
            data: config.xhr.data,
             url: config.xhr.url,
         success: config.xhr.success,
           error: config.xhr.error
        };

        /* @todo possibilitar a personalizacao do metodo: before_request */
        /* before_request() */
        config.target.elm.attr("disabled", "disabled");

        $load = $('<img>').attr('id', 'icon_loading')
                          .attr('width', 16)
                          .attr('height', 16)
                          .attr('src', config.loading)
                          .attr('alt', 'carregando...');

        config.target.elm.after($load);

       /* se existir, executa callback antes enviar os dados */
       if (undefined != config.xhr.beforeSend) { config.xhr.beforeSend(config); }

       /* efetua requisicao dos dados */
       $.ajax(xhr);

       /* se existir, executa callback apos enviar os dados */
       if (undefined != config.xhr.afterSend) { config.xhr.afterSend(config); }

        /* habilita o campo alvo novamente */
        config.target.elm.removeAttr("disabled");
    });
}