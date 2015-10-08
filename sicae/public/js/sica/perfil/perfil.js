var Perfil  = {
    grid : function()
    {
        Grid.load($('#form-perfil'), $('#table-perfil'));
    },

    init : function (edit)
    {
        $("#sqMenu").live('change', function() {
            $('.checkboxAll').attr("checked", false);
            if ($(this).val() != "") {
                $('#perfilLista').show();
                $.ajax({
                    async : false,
                    data : {
                        sqMenu    : $(this).val() ,
                        sqSistema : $("#sqSistema").val(),
                        sqPerfil  : $("#sqPerfil").val()
                    },
                    type: "POST",
                    url: "/menu/menu-funcionality",
                    success : function(response) {
                        $("#perfilLista tbody").html(response);

                        if (!(edit && $("#funcionalidades").val() == "")) {
                            var funcionalidades = $("#funcionalidades").val();
                            var arFun           = funcionalidades.split(",");
                            $('input.checkbox').each(function(i) {
                                $(this).attr('checked', false);
                                for (var x in arFun) {
                                    if ($(this).val() == arFun[x]) {
                                        $(this).trigger('click');
                                        break;
                                    }
                                }
                            });
                        }
                        Perfil.clickInputCheck();
                    }
                });
                return true;
            }
            $('#perfilLista').hide();
        });

        $("#sqSistema").change(function() {
            $('#perfilLista').hide();

            if ($(this).val() != "") {
                $("#sqMenu").parent().load(
                    '/sistema/find-menu',
                    {
                        id : $('#sqSistema').val()
                    },
                    function() {
                        $("#sqMenu").removeAttr('disabled');
                        $("#sqMenu option:first").after(
                            $("<option></option>")
                            .text('Todos')
                            .val('0'));
                    }
                    );
            } else {
                $("#sqMenu").attr('disabled','disabled');
            }
        }).trigger('change');

        $(".checkboxAll").click(function() {
            $('input:checkbox').attr("checked", $(this).is(':checked'));
            Perfil.clickInputCheck();
        });

        $('input.checkbox').live("click", function() {
            Perfil.clickInputCheck();
        });

        $('input[name=inPerfilExterno][value=1]').click(function() {
            $('input[name=perfilPadraoExterno][value=0]').attr('checked', true);
        });

        var radioInPerfilInterno = $('input[name=inPerfilExterno][value=0]');
        if( radioInPerfilInterno.is(':checked') ) {
            radioInPerfilInterno.trigger('click');
        }
    },

    clickInputCheck : function ()
    {
        $('.checkboxAll').attr("checked", $('input.checkbox:checked').length == $('input.checkbox').length);

        var val = "";
        $("#funcionalidades").val(val);
        $('input.checkbox:checked').each(function(i) {
            if (i > 0) {
                val += ",";
            }

            val += $(this).val();

            $("#funcionalidades").val(val);
        });
    },

    loadProfile : function()
    {
        var inPerfilExterno = ($('[name=inPerfilExterno]:checked').val() != undefined)
        ? $('[name=inPerfilExterno]:checked').val()
        : '';
        $("#sqPerfil").parent('div').load('/perfil/combo-profile',
        {
            sqSistema             : $('#sqSistema').val(),
            inPerfilExterno : inPerfilExterno
        },
        function() {
            $("#sqPerfil").removeAttr('disabled').addClass('span9');
        });
    },

    controlProfile : function()
    {
        $('[name=inPerfilExterno]').click(function(){
            if($(this).val() == '0'){
                $('#perfilPadrao').hide();
                // $('[name=sqTipoPerfil]').val('');
                $('[name=perfilPadraoExterno]').removeAttr('checked');
                $('#tipoPerfil').show();
                return true;
            }

            $('#tipoPerfil').hide();
            $('[name=perfilPadraoExterno]').removeAttr('checked');
            $('[name=sqTipoPerfil]').val('');
            $('#perfilPadrao').show();
            $('[name=perfilPadraoExteno]:last-child').attr('checked', 'checked');

        });
    },

    index : function()
    {
        $("#sqSistema").change(function() {
            if ($(this).val()) {
                Perfil.loadProfile();
            } else {
                $('#sqPerfil').attr('disabled', 'disabled');
            }
        }).trigger('change');

        $("[name=inPerfilExterno]").click(function() {
            Perfil.loadProfile();
        });
    },

    view : function(sqPerfil, sqMenu, sqFuncionalidade)
    {
        $.post(
            BASE + 'perfil/view',
            {
                sqPerfil : sqPerfil,
                sqMenu : sqMenu,
                sqFuncionalidade : sqFuncionalidade
            },
            function(response) {
                $('.modal-perfil .modal-body').html(response);
                $('.modal-perfil').modal({
                    keyboard: false,
                    backdrop: 'static'
                });
            }
            );
    },

    edit : function()
    {
        //$("#sqMenu").die("change");
        $("#sqMenu").change(function(e) {
            $('.checkboxAll').attr("checked", false);
            if ($(this).val() != "") {
                $('#perfilLista').show();
                $.ajax({
                    async : false,
                    data : {
                        sqMenu    : $(this).val() ,
                        sqSistema : $("#sqSistema").val(),
                        sqPerfil  : $("#sqPerfil").val()
                    },
                    type: "POST",
                    url: "/menu/menu-funcionality",
                    success : function(response) {
                        $("#perfilLista tbody").html(response);
                        $('.checkboxAll').attr("checked", $('input.checkbox:checked').length == $('input.checkbox').length);
                    }
                });
                return true;
            }
            $('#perfilLista').hide();
        });

        $("#sqMenu").trigger("change");
    },

    gerarPdf : function()
    {
        $('#form-perfil').unbind("submit").submit(function() {

            if(!$('#form-perfil').valid()){
                return false;
            }

            var $this = $(this);
            $('#pesquisa-pdf').val($this.find(':not(#pesquisa-pdf)').serialize());

            $('#table-perfil').dataTable().fnDraw(false);
            setTimeout(function(){
                $('#table-perfil').closest('div.hidden').removeClass('hidden');
            },1000);

            return false;
        });
    },

    checkUserExists : function(obj)
    {
        var btnActiveInactive = obj.find('span'),
        titleActive           = obj.attr('titleActive'),
        titleInactive         = obj.attr('titleInactive'),
        url                   = obj.attr('href');

        $.ajax({
            async : false,
            data : {
                sqPerfil : obj.attr('id'),
                stRegistroAtivo : btnActiveInactive.hasClass('icon-inativado') ? '1' : '0'
            },
            type: "POST",
            dataType :'json',
            url: "/perfil/check-user-exists-profile/",
            success : function(response) {
                Message.showConfirmation({
                    'body': response.message,
                    'yesCallback': function() {
                        $.getJSON(url, {
                            'status': btnActiveInactive.hasClass('icon-inativado') ? '1' : '0'
                        }, function(response) {
                            //caso ocorrer erro de permissão {success:false,code:403,message:'xxxx'}
                            if (!response.success && response.hasOwnProperty('code')) {
                                Message.showError(response.message);
                                return false;
                            }else{
                                Message.showMessage(response);
                                var tr = obj.closest('tr'),
                                td = tr.find('td:eq(' + (tr.find('td').size() - 2) + ')');

                                if (btnActiveInactive.hasClass('icon-inativado')) {
                                    btnActiveInactive.removeClass('icon-inativado')
                                    .addClass('icon-ativado');
                                    obj.attr('title', titleActive);
                                    td.text('Inativo');
                                } else {
                                    btnActiveInactive.removeClass('icon-ativado')
                                    .addClass('icon-inativado');
                                    obj.attr('title', titleInactive);
                                    td.text('Ativo');
                                }
                            }
                        });
                    }
                });
            }
        });
    },

    remove : function(id)
    {
        Message.showConfirmation({
            body: MessageUI.translate('MN186'),
            yesCallback: function(){
                var failback = function( msg ) {
                    msg = msg || MessageUI.translate('MN187');
                    Message.showError(msg);
                };
                $.get(
                    BASE + "/perfil/delete/id/" + id,
                    function(result) {
                        if (typeof result === 'object' && 'success' in result) {
                            if (result.success) {
                                Message.showSuccess( MessageUI.translate( result.message ) );
                                $('#form-perfil').trigger('submit');
                            } else {
                                failback( MessageUI.translate( result.message ) );
                            }
                        } else {
                            failback();
                        }
                    }
                ).fail(function(){
                    failback();
                });
            }
        });
    }
};

$(document).on('click', 'a.active-inactive-profile', function(event){
    var obj = $(this);
    Perfil.checkUserExists(obj);

    return false;
});