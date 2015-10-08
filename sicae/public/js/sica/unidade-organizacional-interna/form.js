var UnidadeOrganizacaoInterna = {


    urlSearchType: {
        du: '/unidade-organizacional-interna/form-search-dados-unidade',
        cc: '/unidade-organizacional-interna/form-search-correcao-codigo',
        bl: '/unidade-organizacional-interna/form-search-base-legal',
        en: '/unidade-organizacional-interna/form-search-endereco'
    },

    watchSearchType: function () {

        var that = this;

        var $container = $('#containerSearch');

        $('#tipoPesquisa').on('change', function () {

            var opt = $(this).val();

            var urlTarget = that.urlSearchType[opt]

            if (opt) {

                $.get(urlTarget, {}, function(data){

                    $container.html(data).show();

                    that.enableSearch(true);

                }).fail(function () {

                    $container.html('<h2>Tipo de pesquisa inválido/indisponível.</h2>').show();

                    that.enableSearch(false);

                });

            } else {

                $container.empty();

                that.enableSearch(false);
            }
        });
    },

    enableSearch: function (status) {
        $('button#pesquisar').prop( "disabled", !status );
    },

    watchLocalizacao: function () {
        $('#inUoExterna_ed').live('click', function (e) { $('.cg-noTipoUnidadeOrg').removeClass('hidden'); });
        $('#inUoExterna_ec').live('click', function (e) { $('.cg-noTipoUnidadeOrg').addClass('hidden');    });
    },

    initEvent: function () {

        this.enableSearch(false);

        this.watchSearchType();

        this.watchLocalizacao();
    },

    init: function () {
        this.initEvent();
    }
};

$(document).ready(function(){
    UnidadeOrganizacaoInterna.init();
});