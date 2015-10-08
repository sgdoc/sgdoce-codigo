(function (angular, core_configuration) {
    'use strict';

    /**
     * Module initalization
     * @type {object}
     */
    var workArea = angular.module('workArea', ['ngRoute', 'ngAnimate']);

    /**
     * Module settings
     */
    workArea.constant('workAreaSettings', {
        debugNoise: true,
        url: {
            base: '/area-trabalho',
            myItemsChecker: '/area-trabalho/qtd-itens-minha-caixa-json',
            grid: '/area-trabalho/grid-json',
        },
        timmers: {
            myItemsChecker: 10000,    // 10.00s
            artefactSearchDelay: 750, //  0.75s
            gridLoaderDelay: 500,     //  0.50s
            gridMessageDelay: 500,    //  0.50s 
        },
        artefacts: {
            active: {
                id: core_configuration.sgdoceTipoArtefatoDocumento,
                className: 'doc',
                title: 'Documento',
                classIcon: 'file',
                gridData: {
                    idKey: 'documentDigital',
                    idLabel: 'Digital',
                },
            },
            next:   {
                id: core_configuration.sgdoceTipoArtefatoProcesso,
                className: 'proc',
                title: 'Processo',
                classIcon: 'book',
                gridData: {
                    idKey: 'processNumber',
                    idLabel: 'Número',
                },
            },
        },
        boxes: {
            'minha':            {id: 'minha',            link: 'Minha Caixa',   title: 'Minha Caixa'              },
            'unidade':          {id: 'unidade',          link: 'Unidade',       title: 'Caixa da Unidade'         },
            'arquivo-setorial': {id: 'arquivo-setorial', link: 'Arq. Setorial', title: 'Caixa do Arquivo Setorial'},
            'externa':          {id: 'externa',          link: 'Externa',       title: 'Caixa Externa'            },
            'arquivo':          {id: 'arquivo',          link: 'Arquivo',       title: 'Caixa dos Arquivos'       },
        }
    });

    /**
     * Module logger
     */
    workArea.factory('workAreaLogger', [
        '$window',
        '$log',
        'workAreaSettings',
        function ($window, $log, workAreaSettings) {
            $window.workAreaDebugNoise = workAreaSettings.debugNoise;

            var _proxy = function () {
                var type = this,
                    args = [].slice.apply(arguments);

                args.push(' <<< Work Area [debug noise] em ' + (new Date()).toString());

                return $window.workAreaDebugNoise ? $log[type].apply(null, args) : undefined;
            };

            return {
                debug : _proxy.bind('debug'),
                log: _proxy.bind('log'),
                info: _proxy.bind('info'),
                warn: _proxy.bind('warn'),
                error: $log.error
            };
        }
    ]);

    /**
     * Module view elements
     */
    workArea.factory('workAreaViewServices', [
        '$window',
        function ($window) {
            return {
                artefactFinder: (function () {
                    var _input,
                        _getInput = function () {
                            if (!_input) {
                                _input = angular.element('#artefactFinder');
                            }
                            return _input;
                        };
                    return {
                        setMask: function (artefactActiveId) {
                            var setter = {};
                            // Máscara de documento
                            setter[core_configuration.sgdoceTipoArtefatoDocumento] = function () {
                                _getInput().setMask({mask: '9', type: 'repeat'});
                            };
                            // Sem máscara para processo...
                            setter[core_configuration.sgdoceTipoArtefatoProcesso] = function () {
                                _getInput().unsetMask();
                            };

                            setter[artefactActiveId]();
                        },
                        setFocus: function () {
                            _getInput().focus();
                        }
                    };
                }()),
                getFirstBoxHref: function () {
                    return angular.element('.work-area-box-option:first').attr('href');
                },
                reload: function () {
                    $window.location.reload();
                },
            };
        }
    ]);

    /**
     * Module HTTP tools
     */
    workArea.factory('workAreaHTTPServices', [
        '$interval',
        '$timeout',
        '$http',
        'workAreaLogger',
        'workAreaSettings',
        'workAreaViewServices',
        function ($interval, $timeout, $http, workAreaLogger, workAreaSettings, workAreaViewServices) {
            var _takeCare = function (options) {
                options.beforeCallback  = options.beforeCallback  || angular.noop;
                options.successCallback = options.successCallback || angular.noop;
                options.failureCallback = options.failureCallback || angular.noop;
                return options;
            };

            var _ajax = function (url, params, options) {
                options.beforeCallback();
                var _error = function (error) {
                        error = error || 'Erro ao recuperar as informações do servidor';
                        options.failureCallback(error);
                    },
                    _ajaxPromise = $http.get(url, {params: params}),
                    _ajaxFailure = function (response) {
                        workAreaLogger.error(response.data, response.status);
                        if (!response.data || response.status === -1) {
                            workAreaViewServices.reload();
                        }
                        _error();
                    },
                    _ajaxSuccess = function (response) {
                        if (typeof response.data === 'object') {
                            if (response.data.error) {
                                if (typeof response.data.message === 'string') {
                                    response.data.error = response.data.message;
                                }
                                _error(response.data.error);
                            } else {
                                options.successCallback(response.data.data);
                            }
                        } else {
                            _error();
                        }
                    };
                _ajaxPromise.then(_ajaxSuccess, _ajaxFailure);
            };

            var _myItemsCheckerClosure = function () {
                var _intervalPromise,
                    _myItemsCheckerActions = {
                        abort: function () {
                            $interval.cancel(_intervalPromise);
                        },
                        start: function (options) {
                            options = _takeCare(options);

                            var ajax = function () {
                                _ajax(workAreaSettings.url.myItemsChecker, {
                                    artefactType: options.artefactActive.id,
                                }, options);
                            };

                            _intervalPromise = $interval(ajax, workAreaSettings.timmers.myItemsChecker);

                            _myItemsCheckerActions.abort();

                            ajax();
                        }
                    };

                return _myItemsCheckerActions;
            };

            var _artefactSearchClosure = function () {
                var _timeoutPromise = null;
                return function (options) {
                    options = _takeCare(options);

                    //@todo colocar a aqui a lógica de fazer o AJAX para pesquisar por documento ou processo.
                    var ajax = function () {
                        options.beforeCallback();
                        if (options.query === '123') {
                            options.successCallback();
                        } else {
                            options.failureCallback('Registro não encontrado');
                        }
                    };

                    if (_timeoutPromise) {
                        $interval.cancel(_timeoutPromise);
                    }
                    
                    _timeoutPromise = $timeout(ajax, workAreaSettings.timmers.artefactSearchDelay);
                };
            };

            var _gridClosure = function () {
                var _timeoutPromise = null;
                return function (options) {
                    options = _takeCare(options);

                    var ajax = function () {
                        _ajax(workAreaSettings.url.grid, {
                            limit: options.limit,
                            offset: options.offset,
                        }, options);
                    };

                    if (_timeoutPromise) {
                        $interval.cancel(_timeoutPromise);
                    }

                    _timeoutPromise = $timeout(ajax, workAreaSettings.timmers.gridLoaderDelay);
                };
            };

            return {
                myItemsChecker : _myItemsCheckerClosure(),
                artefactSearch : _artefactSearchClosure(),
                grid           : _gridClosure(),
            };
        }
    ]);

    /**
     * Configure the module route
     */
    workArea.config([
        '$routeProvider',
        '$locationProvider',
        '$httpProvider',
        'workAreaSettings',
        function ($routeProvider, $locationProvider, $httpProvider, workAreaSettings) {

            $httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';

            $routeProvider.when(workAreaSettings.url.base + '/caixa/:boxId', {
                templateUrl: 'boxTemplate',
                controller: 'boxCtrl',
                resolve: {
                    boxId: [
                        '$q',
                        '$route',
                        '$location',
                        'workAreaLogger',
                        function ($q, $route, $location, workAreaLogger) {
                            var _deferred = $q.defer(),
                                _boxIdSelected = $route.current.params.boxId,
                                _found = false;

                            angular.forEach(workAreaSettings.boxes, function (box) {
                                if (!_found) {
                                    _found = (box.id === _boxIdSelected);
                                }
                            });

                            if (_found) {
                                workAreaLogger.debug('Caixa "%s" valida, pode abrir.', _boxIdSelected);
                                _deferred.resolve(_boxIdSelected);
                            } else {
                                workAreaLogger.error('): Ops! Caixa "%s" selecionada inválida.', _boxIdSelected);
                                $location.path(workAreaSettings.url.base);
                            }

                            return _deferred.promise;
                        }
                    ]
                }
            });
            $routeProvider.when(workAreaSettings.url.base, {
                templateUrl: 'initTemplate',
                controller: 'initCtrl'
            });
            $routeProvider.otherwise({
                redirectTo: workAreaSettings.url.base
            });
            $locationProvider.html5Mode(true);
        }
    ]);

    /**
     * Main controller
     */
    workArea.controller('mainCtrl', [
        '$rootScope',
        'workAreaLogger',
        'workAreaSettings',
        'workAreaViewServices',
        'workAreaHTTPServices',
        function ($rootScope, workAreaLogger, workAreaSettings, workAreaViewServices, workAreaHTTPServices) {
            workAreaLogger.info('Oi controladora principal.');

            $rootScope.boxes = workAreaSettings.boxes;

            $rootScope.viewServices = workAreaViewServices;

            $rootScope.artefact = workAreaSettings.artefacts;

            $rootScope.artefactiActiveChanged = false;

            $rootScope.artefactSearch = {
                query:   '',
                error:   null,
                loading: false
            };

            $rootScope.changeToNextArtefact = function () {
                var prev = $rootScope.artefact.active,
                    next = $rootScope.artefact.next;
                workAreaLogger.debug('Alterando de "%s" para: "%s"', prev.title, next.title);
                $rootScope.artefact.active = next;
                $rootScope.artefact.next = prev;

                $rootScope.artefactiActiveChanged = true;
                $rootScope.artefactSearch = {
                    query:   '',
                    error:   null,
                    loading: false
                };

                $rootScope.$broadcast('workArea.changeToNextArtefact');
            };

            $rootScope.findArtefact = function (artefactSearch) {
                if (artefactSearch.query === '') {
                    artefactSearch.error = 'Campo obrigatório para pesquisa.';
                } else {
                    workAreaHTTPServices.artefactSearch({
                        query: artefactSearch.query,
                        beforeCallback: function () {
                            workAreaLogger.debug('Pesquisa por: "%s"', artefactSearch.query);
                            artefactSearch.error = null;
                            artefactSearch.loading = true;
                        },
                        successCallback: function (data) {
                            workAreaLogger.debug('Finalizada a pesquisa', data);
                            artefactSearch.loading = false;
                        },
                        failureCallback: function (error) {
                            workAreaLogger.error('Deu erro na pesquisa', error);
                            artefactSearch.error = error;
                            artefactSearch.loading = false;
                        }
                    });
                }
            };


            var _splash = function () {
                return "\n"
                    + "     __                        _        _______        _           _ _           \n"
                    + "    /_/                       | |      |__   __|      | |         | | |          \n"
                    + "    / \\   _ __ ___  __ _    __| | ___     | |_ __ __ _| |__   __ _| | |__   ___  \n"
                    + "   / _ \\ | '__/ _ \\/ _` |  / _` |/ _ \\    | | '__/ _` | '_ \\ / _` | | '_ \\ / _ \\ \n"
                    + "  / ___ \\| | |  __/ (_| | | (_| |  __/    | | | | (_| | |_) | (_| | | | | | (_) |\n"
                    + " /_/   \\_\\_|  \\___|\\__,_|  \\__,_|\\___|    |_|_|  \\__,_|_.__/ \\__,_|_|_| |_|\\___/ \n"
                    + "      _          _____  _____ _____   ____   _____                               \n"
                    + "     | |        / ____|/ ____|  __ \\ / __ \\ / ____|                              \n"
                    + "   __| | ___   | (___ | |  __| |  | | |  | | |     ___                           \n"
                    + "  / _` |/ _ \\   \\___ \\| | |_ | |  | | |  | | |    / _ \\                          \n"
                    + " | (_| | (_) |  ____) | |__| | |__| | |__| | |___|  __/                          \n"
                    + "  \\__,_|\\___/  |_____/ \\_____|_____/ \\____/ \\_____\\___|                          \n"
                    + "                                                                                 \n"
                    + "                                                                                 \n"
                    + "\n";
            };
            workAreaLogger.debug(_splash());
            $rootScope.started = true;
        }
    ]);

    /**
     * Init controller
     */
    workArea.controller('initCtrl', [
        '$rootScope',
        '$scope',
        'workAreaLogger',
        'workAreaHTTPServices',
        function ($rootScope, $scope, workAreaLogger, workAreaHTTPServices) {
            workAreaLogger.warn('Init controller');

            var _startMyItensChecker = function () {
                workAreaHTTPServices.myItemsChecker.start({
                    artefactActive: $rootScope.artefact.active,
                    beforeCallback: function () {
                        workAreaLogger.debug('Vai pesquisar a quantidade de itens na minha caixa...');
                        $scope.myItems = -1;
                        $scope.myItensError = null;
                    },
                    successCallback: function (myItems) {
                        $scope.myItems = myItems;
                        $scope.myItensError = null;
                    },
                    failureCallback: function (error) {
                        workAreaLogger.error(error);
                        $scope.myItems = -1;
                        $scope.myItensError = error;
                        workAreaHTTPServices.myItemsChecker.abort();
                    }
                });
            };

            $rootScope.boxIdSelected = null;

            _startMyItensChecker();
            $scope.$on('workArea.changeToNextArtefact', _startMyItensChecker);
        }
    ]);

    /**
     * Box controller
     */
    workArea.controller('boxCtrl', [
        '$routeParams',
        '$rootScope',
        '$scope',
        '$timeout',
        'workAreaLogger',
        'workAreaHTTPServices',
        'workAreaSettings',
        function ($routeParams, $rootScope, $scope, $timeout, workAreaLogger, workAreaHTTPServices, workAreaSettings) {
            workAreaLogger.warn('Box controller', '$routeParams', $routeParams);

            workAreaHTTPServices.myItemsChecker.abort();

            var _boxId = $routeParams.boxId;

            $rootScope.boxIdSelected = _boxId;

            $scope.box = workAreaSettings.boxes[_boxId];
            $scope.gridData = [];
            $scope.gridTotal = 0;
            $scope.gridLoading = false;
            $scope.gridError = '';

            $scope.grid = function (action) {
                var limit = 5, offset = 0;

                action = action || ''; // 'prev', 'next' ou ''

                if (action !== '') {
                    $scope.gridData = [];
                    $scope.gridTotal = 0;
                    $scope.gridLoading = false;
                    return;
                }

                workAreaHTTPServices.grid({
                    limit: limit,
                    offset: offset,
                    beforeCallback: function () {
                        workAreaLogger.debug('Vai carregar a grid... Mostra %d registros apartir da linha %d', limit, offset);
                        $scope.gridData = [];
                        $scope.gridTotal = 0;
                        $scope.gridLoading = true;
                        $scope.gridError = '';
                    },
                    successCallback: function (data) {
                        $scope.gridData = data.dataGrid;
                        $scope.gridTotal = data.total;
                        $scope.gridLoading = false;
                        $scope.gridError = '';
                    },
                    failureCallback: function (error) {
                        workAreaLogger.error(error);
                        $scope.gridData = [];
                        $scope.gridTotal = 0;
                        $scope.gridLoading = false;
                        $scope.gridError = error;
                    }
                });
            };

            $scope.showGridDetail = function (selectedIndex) {
                var selectedItem = null;
                angular.forEach($scope.gridData, function (item, index) {
                    item.selected = selectedIndex === index;
                    if (item.selected) {
                        selectedItem = item;
                    }
                });
                workAreaLogger.debug('Mostra os detalhes de', selectedItem);
            };

            $scope.previousPage = function () {
                return $scope.grid('prev');
            };

            $scope.nextPage = function () {
                return $scope.grid('next');
            };

            $scope.gridMessageDelay = function () {
                workAreaLogger.debug('Mostra daqui a pouco a mensagem da grid.');
                $scope.gridMessage = false;
                $timeout(function (){
                    workAreaLogger.debug('Mostra agora a mensagem da grid.');
                    $scope.gridMessage = true;
                }, workAreaSettings.timmers.gridMessageDelay);
            }

            $scope.$on('workArea.changeToNextArtefact', function () {
                $scope.grid();
            });
        }
    ]);


}(window.angular, window.core_configuration));