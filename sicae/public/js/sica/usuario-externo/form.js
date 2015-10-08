UsuarioExternoForm = {
    initCep: function() {
        $('#complementar-sqPais').change(function() {
            $('#complementar-sqEstado option').not('#complementar-sqEstado option:first').remove();
            $('#complementar-sqMunicipio option').not('#complementar-sqMunicipio option:first').remove();

            if ($(this).val()) {
                Address.config.estado = $('#complementar-sqEstado');
                Address.populateEstadoFromPais($(this).val());
            }
        });

        $('#complementar-sqEstado').change(function() {
            $('#complementar-sqMunicipio option').not('#complementar-sqMunicipio option:first').remove();

            if ($(this).val()) {
                Address.config.municipio = $('#complementar-sqMunicipio');
                Address.populateMunicipioFromEstado($(this).val());
            }
        });

        $('#btnCep').click(function() {
            Address.config.cep = $('#complementar-nuCep');
            Address.config.pais = $('#complementar-sqPais');
            Address.config.estado = $('#complementar-sqEstado');
            Address.config.municipio = $('#complementar-sqMunicipio');
            Address.config.endereco = $('#complementar-txEndereco');

            Address.populateFromCep($('#complementar-sqEstado').val());
        });

        if ($('#sqUsuarioExterno').val() == 0 || $('#sqUsuarioExterno').val() == '') {
            $('#complementar-sqPais').val('1').change();
        }
    },
    initMaks: function() {
        $('#complementar-nuCep').setMask('cep');
        $('#nuTelefone-nuTelefone, #complementar-nuTelefoneCelular').setMask({
            mask: '(999) 99999-9999?'
        });
    },
    initSistemas: function() {
        $('.aviso').css('color', '#595959');

        $('a[id=asmListItemRemove]').hover(function() {
            $(this).css('cursor', 'pointer');
        });

        $('#sqSistema').change(function() {
            var value = $(this).val();
            var label = $(this).find('option:selected').text();

            var item = '<li class="asmListItem">';
            item = item + '<input type="hidden" name="sistemas[sqPerfil][]" value="' + value + '" />';
            item = item + '<span class="asmListItemLabel">';
            item = item + label;
            item = item + '</span>';
            item = item + '<a class="asmListItemRemove" id="asmListItemRemove" rel="' + value + '">';
            item = item + '<i title="Excluir" class="icon-trash"></i>';
            item = item + '</a>';
            item = item + '</li>';

            if ($(this).val()) {
                $('#asmList').append(item);
                $(this).find('option:selected').remove();
                $(this).val('').change().removeClass('required');
            }

            $('a[id=asmListItemRemove]').hover(function() {
                $(this).css('cursor', 'pointer');
            });
        });

        $('a[id=asmListItemRemove]').live('click', function() {

            var element = $(this);
            var callBack = function() {
                var value = $('a[id=asmListItemRemove]').parents('li').find('input').val();
                var label = $.trim(element.parents('li').find('span.asmListItemLabel').text());

                var item = '<option value="' + value + '">' + label + '</option>';

                $('#sqSistema').append(item);

                element.parents('li').remove();

                if (!$('a[id=asmListItemRemove]').size()) {
                    $('#sqSistema').addClass('required');
                }

                Message.showSuccess('Exclusão realizada com sucesso.');
            };

            Message.showConfirmation({
                'body': 'Confirma exclusão do registro?',
                'yesCallback': callBack
            });
        });

        if ($('a[id=asmListItemRemove]').size()) {
            $('#asmList li.asmListItem').each(function() {
                var value = $(this).find('input').val();

                $('#sqSistema option[value=' + value + ']').remove();
            });

            $('#sqSistema').removeClass('required');
        }
    },
    initResumo: function(tpPessoa) {
        $('#resumo').modal('show');

        $('.resumo-pj').hide();
        $('.resumo-cpf').html('CPF');
        $('.resumo-cpf').parent('p').find('span').html($('#nuCpf').val());

        if ($('#nuPassaporte').val()) {
            $('.resumo-cpf').parent('p').find('span').html($('#nuPassaporte').val());
            $('.resumo-cpf').html('Passaporte');
        }

        if (tpPessoa) {
            $('.resumo-pj').show();
            $('.resumo-pf').hide();
        }

        $('.resumo-nome').parent('p').find('span').html($('#noUsuarioExterno').val());
        $('.resumo-email').parent('p').find('span').html($('#txEmail').val());
        $('.resumo-cnpj').parent('p').find('span').html($('#nuCnpj').val());
        $('.resumo-razao').parent('p').find('span').html($('#noUsuarioExterno').val());
        $('.resumo-fantasia').parent('p').find('span').html($('#noFantasia').val());
        $('.resumo-pais').parent('p').find('span').html($('#complementar-sqPais option:selected').text());
        $('.resumo-cep').parent('p').find('span').html($('#complementar-nuCep').val());
        $('.resumo-endereco').parent('p').find('span').html($('#complementar-txEndereco').val());

        if ($('#complementar-sqEstado').val()) {
            $('.resumo-estado').parent('p').find('span').html($('#complementar-sqEstado option:selected').text());
        } else {
            $('.resumo-estado').parent('p').find('span').html('');
        }

        if ($('#complementar-sqMunicipio').val()) {
            $('.resumo-municpio').parent('p').find('span').html($('#complementar-sqMunicipio option:selected').text());
        } else {
            $('.resumo-municpio').parent('p').find('span').html('');
        }

        var telefone = $('#nuTelefone-nuTelefone').val();
        var celular = $('#complementar-nuTelefoneCelular').val();

        if ($('#complementar-nuDddTelefone').val()) {
            telefone = '(' + $('#complementar-nuDddTelefone').val() + ') ' + $('#nuTelefone-nuTelefone').val();
        }

        if ($('#complementar-nuDddCelular').val()) {
            celular = '(' + $('#complementar-nuDddCelular').val() + ') ' + $('#complementar-nuTelefoneCelular').val();
        }

        $('.resumo-telefone').parent('p').find('span').html(telefone);
        $('.resumo-celular').parent('p').find('span').html(celular);

        $('#resumo-sistemas').html($('#asmList').clone());
        $('#resumo #asmList a.asmListItemRemove').remove();
    },
    initValidate: function() {
        $("#txEmail").rules("add", {
            remote: {
                url: '/usuario-externo/check-credencials',
                type: 'post',
                data: {
                    txEmail: function() {
                        return $("#txEmail").val();
                    },
                    sqUsuarioExterno: function() {
                        return $("#sqUsuarioExterno").val() ? $("#sqUsuarioExterno").val() : '';
                    }
                }
            },
            messages: {
                remote: "E-mail já cadastrado na base de dados."
            }
        });

        $("#txEmailConfirmado").rules("add", {
            equalTo: "#txEmail",
            messages: {
                equalTo: "A informação do campo e-mail não está igual a informação do campo confirmar e-mail."
            }
        });

        if ($('#txSenhaConfirmado').size()) {
            $("#txSenhaConfirmado").rules("add", {
                equalTo: "#txSenha",
                messages: {
                    equalTo: "A confirmação da nova senha não confere."
                }
            });
        }
    },
    initAbas: function() {
        $('.form-actions .btn_next_wizard').click(function() {
            if (!$('#form-usuario-externo').valid()) {
                return false;
            }
            $('ul.breadcrumbs li').each(function() {
                $(this).removeClass('ativo');
            });

            $('.campos-obrigatorios').hide();
            $(document).scrollTop(0);

            $("#form-usuario-externo").validate().resetForm();
            $("#form-usuario-externo").validate().elements().each(function(elment) {
                $(this).parents('.error').removeClass('error');
            });

            $('.breadcrumbs li a[href=' + $(this).attr('href') + '], #tabs-usuario-externo li a[href=' + $(this).attr('href') + ']').tab('show');
            $('ul.breadcrumbs li.active:eq(0)').addClass('ativo');
            return false;
        });

        $('.form-actions .btn_prev_wizard').click(function() {
            $('ul.breadcrumbs li').each(function() {
                $(this).removeClass('ativo');
            });

            $('.campos-obrigatorios').hide();

            $("#form-usuario-externo").validate().resetForm();
            $("#form-usuario-externo").validate().elements().each(function(elment) {
                $(this).parents('.error').removeClass('error');
            });

            $('.breadcrumbs li a[href=' + $(this).attr('href') + '], #tabs-usuario-externo li a[href=' + $(this).attr('href') + ']').tab('show');
            $('ul.breadcrumbs li.active:eq(0)').addClass('ativo');
            return false;
        });

        $('ul.breadcrumbs li').click(function() {
            var hrefActive = $('ul.breadcrumbs li.active:eq(0) a')
                    .attr('href')
                    .replace('#', ''),
                    hrefCurrent = $(this).find('a')
                    .attr('href')
                    .replace('#', '');

            if (parseInt(hrefCurrent) > parseInt(hrefActive)) {
                if (!$('#form-usuario-externo').valid()) {
                    return false;
                }
            }

            $('.campos-obrigatorios').hide();

            $("#form-usuario-externo").validate().resetForm();
            $("#form-usuario-externo").validate().elements().each(function(elment) {
                $(this).parents('.error').removeClass('error');
            });

            $('ul.breadcrumbs li').each(function() {
                $(this).removeClass('ativo');
            });
            $(this).addClass('ativo');
        });

        $('#tabs-usuario-externo li a').click(function() {
            if ($('#form-usuario-externo').valid()) {
                $(this).tab('show');
            }

            return false;
        });

        $('.concluir').click(function() {
            if ($('#form-usuario-externo').valid()) {
                if ($('#sqUsuarioExterno').val() == 0 || $('#sqUsuarioExterno').val() == '') {
                    UsuarioExternoForm.initResumo($('#nuCnpj').val() ? true : false);
                } else {
                    $('.concluir').unbind('click');

                    $.post($('#form-usuario-externo').attr('action'), $('#form-usuario-externo').serialize(),
                            function(data) {
                                Message.show('Sucesso', 'Alteração realizada com sucesso.', function() {
                                    history.back();
                                });

                                $('.bootbox .modal-footer a.btn-primary').html('Ok');

                            }).fail(function(data) {
                        Message.show('Erro', 'Erro na operação.');
                    });

                }
            }
        });

        $('.cancelar').click(function() {
            history.back();
        });

        $('#concluir-resumo').click(function() {
            $('#concluir-resumo').unbind('click');
            $('#form-usuario-externo').submit();
        });
    }
};