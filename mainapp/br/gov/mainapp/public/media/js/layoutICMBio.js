/**
 * Copyright 2011 ICMBio
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos
 * da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão
 * 2 da Licença.
 *
 * Este programa é distribuído na esperança que possa ser útil, mas SEM NENHUMA GARANTIA; sem
 * uma garantia implícita de ADEQUAÇÃO a qualquer MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a
 * Licença Pública Geral GNU/GPL em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
 * junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço
 * www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF)
 * Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
 *
 *
 * Layout ICMBio
 *  - Esta biblioteca é reponsável por tentar igualar layout utilizado pelo SICAe para
 *   exibir as informações como Imagem do Sistema, Menus Superiores e Imagem do Instituto
 *
 * @author Fábio Lima <fabioolima@gmail.com>
 * @depends jQuery
 * */

var layoutICMBio = {
    /**
     * Monta o Layout do ICMBio
     */
    inPerfilExterno : null,

    mountLayout: function (param) {
        //set atributo para ser usado em systemAndPassword.js
        layoutICMBio.inPerfilExterno = param.inPerfilExterno;

        var divParent = $('.nav');
        var Elemnt = $('.nav-collapse', $('.container-fluid'));
        $('.active', divParent).attr("href", window.location.protocol + '//' + window.location.hostname);
        Elemnt.before(
                '<img class="brand" width="120" height="70" alt="' + param.sysAlias + '" src="' + param.urlSystem + '/sistema/render-logo/id/' + param.sysId + '">'
                );
        $('.nav', $('.nav-collapse')).before(
                '<img class="brandRight" width="120" height="70" alt="ICMBio" src="' + param.cdn + 'common/img/marcaICMBio.png">'
                );

        var htmlCompl = '';
        if (param.multiProfile) {
            htmlCompl = '<li><a id="btn-alterar-perfil" href="javascript:icmbioCommon.doChange(' + param.sysId + ');">Selecionar Perfil</a></li>';
        }

        if (param.uorg == null) {
            param.uorg = '';
        }

        var htmlUnidade = '';
        if (!param.inPerfilExterno){
            htmlUnidade = '<li>' +
                    '<span data-original-title="Unidade Organizacional" id="unidadeOrg-popover" data-trigger="hover" data-placement="left" data-content="' + param.uorg + '">' +
                        'Unidade: ' +
                        ((param.uorg.length > uorgMaxVisibleSize) ? param.uorg.substr(0, uorgMaxVisibleSize) + '&hellip;' : param.uorg) +
                    '</span>' +
                '</li>';
        }

        var uorgMaxVisibleSize = 28;
        Elemnt.append(
                '<ul class="nav pull-right dropdown-perfil">' +
                    '<li class="divider-vertical visible-desktop"></li>' +

                    '<li class="dropdown pull-right">' +
                        '<a class="dropdown-toggle" href="#" data-toggle="dropdown">' +
                        '<img src="' + param.cdn + 'common/img/icon-perfil.png" />' +
                        '</a>' +
                        '<ul class="dropdown-menu">' +
                            '<li>' +
                                '<span>Nome: ' + param.nome + '</span>' +
                            '</li>' +
                            '<li>' +
                                '<span>Perfil: ' + param.perfil + '</span>' +
                            '</li>' +
                            htmlUnidade +
                            '<li class="divider"></li>' +
                            htmlCompl +
                            '<li>' +
                                '<a href="' + param.urlSystem + '/index/home/change/password">Alterar Senha</a>' +
                            '</li>' +
                            '<li>' +
                                '<a target="_blank" href="' + param.help + '">Ajuda</a>' +
                            '</li>' +
                            '<li>' +
                                '<a href="' + param.urlSystem + '/usuario/logout">Sair</a>' +
                            '</li>' +
                        '</ul>' +
                    '</li>' +
                    '<li class="divider-vertical visible-desktop"></li>' +
                '</ul>'
                );
        $('#unidadeOrg-popover').popover();
        icmbioCommon.changeSystem(param.inPerfilExterno);
    }
};