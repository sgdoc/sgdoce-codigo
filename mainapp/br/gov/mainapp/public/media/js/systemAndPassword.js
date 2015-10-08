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
 * System and Password
 *  - Esta biblioteca é reponsável por tentar igualar layout utilizado pelo SICAe para
 *   alterar Senha e Mudança de Sistemas
 *  (OBS) - Está biblioteca é totalmente dependente das Actions do SICAe, portanto
 *  se o SICAe for alterado poderá causar impacto nesta, logo esta deverá ser revista
 *  para adequação.
 *
 * @author Fábio Lima <fabioolima@gmail.com>
 * @depends jQuery, SICAe
 * */
var icmbioCommon = {
    /**
     * Obtem a URL do sistema e altera o nome do sistema para o SICAe
     */
    getUrlSystem: function () {
        var sysName = window.location.hostname.split('.').reverse();
        // Substituir para usar o SICAe (Métodos para Alterar Senha e Sistema pertecem ao SICAe)
        sysName[4] = 'sicae';
        sysName = sysName.reverse().join('.');
        return window.location.protocol + '//' + sysName;
    },
    /**
     * Efetua a chamada para alterar o sistema (Preenchendo Sessão e Perfil [Regras SICAe])
     */
    doChange: function (sysId) {
        $.ajax({
            url: icmbioCommon.getUrlSystem() + '/principal/usuario-perfil/user-unit/sqSistema/' + sysId,
            dataType: 'jsonp',
            contentType: "application/json",
            statusCode: {
                404: function () {
                    console.log('Action user-unit do SICA não foi encontrado !!');
                }
            }
        }).fail(function (){
            window.location.reload();
        });
    },
    /**
     * Efetua a chamada para alterar o sistema (Preenchendo Sessão e Perfil [Regras SICAe])
     */
    changeSystem: function (inPerfilExterno) {

        //////////////////////////////////////////////////////////////////////
        var inputsForm = '';
        var modalTitle = 'Unidade Organizacional / Perfil';

        if (!inPerfilExterno) {
            inputsForm  = '<div class="control-group">';
            inputsForm +=    '<label class="control-label"><span class="required">*</span> Unidade</label>';
            inputsForm +=    '<div class="controls">';
            inputsForm +=        '<select name="feijoadaUnit" class="required span3" id="usersList"></select>';
            inputsForm +=    '</div>';
            inputsForm += '</div>';
            inputsForm += '<div class="control-group">';
            inputsForm +=    '<label class="control-label"><span class="required">*</span> Perfil</label>';
            inputsForm +=    '<div class="controls">';
            inputsForm +=        '<select name="feijoadaProfile" id="feijoadaProfile" class="required  span3" disabled="disabled"><option value=""></option></select>';
            inputsForm +=    '</div>';
            inputsForm += '</div>';
        }else{
            modalTitle = 'Perfil';
            inputsForm  =  '<div class="control-group">';
            inputsForm +=    '<label class="control-label"><span class="required">*</span> Perfil</label>';
            inputsForm +=    '<div class="controls">';
            inputsForm +=        '<select id="feijoadaProfile" class="required" name="feijoadaProfile"></select>';
            inputsForm +=    '</div>';
            inputsForm +=  '</div>';
        }

        var xhtml  = '<div class="modal hide" id="modal-usuario-perfil">';
            xhtml +=     '<div class="modal-header">';
            xhtml +=         '<button data-dismiss="modal" class="close" type="button">×</button>';
            xhtml +=         '<h3>' + modalTitle + '</h3>';
            xhtml +=     '</div>';
            xhtml +=     '<form name="form-user-profile" method="post" id="form-user-profile" class="form-horizontal">';
            xhtml +=         '<div class="modal-body">';
            xhtml +=             '<fieldset>';
            xhtml +=                 '<input type="hidden" value="" id="systemId" name="systemId">';
            xhtml +=                 inputsForm;
            xhtml +=             '</fieldset>';
            xhtml +=         '</div>';
            xhtml +=         '<div class="modal-footer">';
            xhtml +=             '<button id="btnChngSys" class="btn btn-primary" type="submit">Concluir</button>';
            xhtml +=             '<a href="#" class="btn" data-dismiss="modal" id="btnCancelar"><i class="icon-remove"></i>Cancelar</a>';
            xhtml +=         '</div>';
            xhtml +=     '</form>';
            xhtml += '</div>';

        $('body').append($(xhtml));
        //////////////////////////////////////////////////////////////////////

        var elmnt = $('.nav > .dropdown > .dropdown-menu').first();
        var lis = $('li', elmnt);
        var count = 0;
        $(lis).each(function () {
            var aElmnt = $('a', this);
            if (count >= 5) {
                $(this).remove();
            } else {
                aElmnt.attr('href', 'javascript:icmbioCommon.doChange(' + aElmnt.attr('href') + ');');
            }
            count++;
        });

        if (count > 5) {
            elmnt.append('<li class="divider"></li><li><a href="' + icmbioCommon.getUrlSystem() + '/index/home">Todos</a></li>');
        }

        $('#btnChngSys').click(function () {
            $('#form-user-profile').submit(function (e) {
                e.preventDefault();
                if (ModalFormValidate.validateForm($(this))) {
                    $.ajax({
                        url: icmbioCommon.getUrlSystem() + '/usuario-perfil/user-profile',
                        dataType: 'jsonp',
                        contentType: "application/json",
                        data: {
                            sqUnidadeOrg: $('#usersList').val(), //quando perfilExterno não existe no form
                            sqPerfil: $('#feijoadaProfile').val(),
                            systemId: $('#systemId').val()
                        },
                        statusCode: {
                            404: function () {
                                console.log('Action user-profile do SICA não foi encontrado !!');
                            }
                        }
                    });
                }
                return false;
            });
        });
    }
};

/**
 * Biblioteca feita para ser utilizada sem precisar efetuar chamada da mesma do SICAe
 */
var Sistemas = {
    leiauteBootstrap: 1,
    populateUsersList: function (data, system) {
        var config = {
            backdrop: 'static',
            keyboard: false
        };

        if (!layoutICMBio.inPerfilExterno) {
            $("#usersList option").remove();
            $("#usersList").append($("<option></option>").text('Selecione uma opção').val(''));

            $.each(data, function (index, item) {
                if (!$('#usersList option[value=' + item.sqPessoa + ']').size()) {
                    $("#usersList").append($("<option></option>").text(item.sgUnidadeOrg + ' - ' + item.noPessoa).val(item.sqPessoa));
                }
            });

            $("#systemId").val(system);
            $('#modal-usuario-perfil').modal(config);

            Sistemas.comboUnidadePerfil();
        }else{
            $("#feijoadaProfile option").remove();
            $("#feijoadaProfile").append($("<option></option>").text('Selecione uma opção').val(''));

            $.each(data, function(index, item) {
                $("#feijoadaProfile").append($("<option></option>").text(item.noPerfil).val(item.sqPerfil));
            });

            $("#systemId").val(system);
            $('#modal-usuario-perfil').modal(config);
        }
    },
    comboUnidadePerfil: function () {
        $("#usersList").unbind('change').change(function () {
            if ($(this).val() !== '') {
                $('#feijoadaProfile option:first').html('Carregando ...');

                $.ajax({
                    url: icmbioCommon.getUrlSystem() + '/usuario-perfil/perfil-unidade',
                    dataType: 'jsonp',
                    contentType: "application/json",
                    data: {
                        sqUnidadeOrgPessoa: $(this).val(),
                        sqSistema: $('#systemId').val()
                    },
                    statusCode: {
                        404: function () {
                            console.log('Action perfil-unidade do SICA não foi encontrado !!');
                        }
                    }
                });
                $("#feijoadaProfile").removeAttr('disabled');
                $("#feijoadaProfile").parent('div').parent('div').show();
            } else {
                $("#feijoadaProfile option").remove();
                $("#feijoadaProfile").attr('disabled', 'disabled');
                $("#feijoadaProfile").parent('div').parent('div').hide();
            }
        });

        $("#feijoadaProfile").parent('div').parent('div').hide();
    },
    redirectSystem: function (id) {
        $.ajax({
            url: icmbioCommon.getUrlSystem() + '/principal/sistema/user-system-menu/sqSistema/' + id,
            dataType: 'jsonp',
            contentType: "application/json",
            statusCode: {
                404: function () {
                    console.log('Action user-system-menu do SICA não foi encontrado !!');
                }
            }
        });
    }
};

var ModalFormValidate = {
    validateForm: function (container) {
        $('.help-block', container).remove();
        var result = true;
        $('input.required, select.required, textarea.required', container).each(function () {
            if ("" == $(this).val() && $(this).is(':visible')) {
                $(this).closest('.control-group').addClass('error');
                $(this).parent().append('<p class="help-block">Campo de preenchimento obrigatório.</p>');
                result = false;

                $(this).off().on('focusout',function(){
                    if ("" != $(this).val()) {
                        $(this).closest('.control-group').removeClass('error');
                        $(this).closest('.control-group').find('p.help-block').remove();
                    }else{
                        return ModalFormValidate.validateForm($(this).parent());
                    }
                });
            } else {
                $(this).closest('.control-group').removeClass('error');
            }
        });
        return result;
    }
};