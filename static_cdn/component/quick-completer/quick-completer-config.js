/**
 *
 * The supreme mapper config of Quick Completer
 *
 */

/*jslint unparam: false*/
(function (global, undefined) {
    'use strict';
    /*jslint unparam: true*/

    if (!global.QuickCompleter) {
        throw new Error("Componente 'Quick Completer' não encontrado!\nO arquivo 'component/quick-completer/quick-completer.js' foi carregado primeiro?");
    }

    var _port = 1704,
        _protocol = 'https',
        _textKey = 'text',
        _valueKey = 'value';

    /**
     * @type {Object}
     */
    global.QuickCompleter.config = {
        acl: {
            token: "d4e5f6",
        },
        env: {
            development: {
                protocol: 'http',
                host: 'dev.appdemo.localhost',
                port: _port,
            },
            tcti: {
                protocol: _protocol,
                host: 'tcti.appdemo.icmbio.gov.br',
                port: _port,
            },
            hmg: {
                protocol: _protocol,
                host: 'hmg.appdemo.icmbio.gov.br',
                port: _port,
            },
            homologacao: {
                protocol: _protocol,
                host: 'hmg.appdemo.icmbio.gov.br',
                port: _port,
            },
            production: {
                protocol: _protocol,
                host: 'appdemo.icmbio.gov.br',
                port: _port,
            }
        },
        type: {
            unidadeExterna: {
                index: 'corporativo',
                documentType: 'unidade_org_externa',
                textKey: _textKey,
                valueKey: _valueKey,
                extraValues: [],
            },
            municipio: {
                index: 'corporativo',
                documentType: 'municipio',
                textKey: _textKey,
                valueKey: _valueKey,
                extraValues: [
                    'sq_estado'
                ],
            },
            //...
        }
    };

}(window));