var Menu = {

    nivel1 : '1',

    init : function(){
        $('.filtro').click(function(){
            $(document).scrollTop(0);
        });

        $('#sqMenu, #abaixoDe, #id_menu').addClass('span8');
    },

    populateTree : function()
    {
        $('#sqSistema').trigger('change');

        $('#sqSistema').ajaxComplete(function() {
            var father = $("[name=menuPai]").val();
            $("#sqMenuPai option[value='"+$('[name=sqMenu]').val()+"']").remove();
            if(father == ""){
                $("#sqMenuPai").val('0');
            } else {
                $("#sqMenuPai").val(father);
            }
        });
    },

    registerEvents : function()
    {
        $('.status').live('click', function(){
            Menu.switchStatus($(this));
        });
        $('.deleteMenu').live('click', function(){
            Menu.deleteMenu($(this));
        });
    },

    switchStatus : function(obj) {
        var hidden = obj.parent().find(':hidden').val();
        var msg    = 'Atenção! Existe pelo menos um submenu, vinculado a este menu. Ao prosseguir';
        msg       += ' com esta ação os submenus não serão apresentados no menu do sistema. Deseja continuar?';

        if (obj.attr('status') == '1') {
            msg  = hidden > 0 ? msg : 'Deseja realmente inativar este Menu?';
        } else if (obj.attr('status') == '0') {
            msg = 'Deseja realmente reativar este Menu?';
        }

        Message.showConfirmation({
            'body'          : msg,
            'subject'       : 'Atenção',
            'yesCallback'   : function(){
                $.post(
                    '/menu/switch-status/',
                    {sqMenu : obj.attr('id')},
                    function(response){
                        //erro de acl
                        if (!response.success && response.hasOwnProperty('code')) {
                            Message.showError(response.message);
                            return false;
                        }else{

                            if(!response.success){
                                Message.showAlert(response.message);
                                return false;
                            }

                            $('.pesquisar').click();
                            $('#sqSistema').change();

                            if(obj.attr('status') == '1'){
                                obj.find('i').attr('class','iconAtivarDesativar icon-inativado');
                                obj.attr('status','0');
                                obj.attr('title','Inativado');
                            } else {
                                obj.find('i').attr('class','iconAtivarDesativar icon-ativado');
                                obj.attr('status','1');
                                obj.attr('title','Ativado');
                            }
                            Message.showSuccess(response.message);
                            return false;
                        }
                    }, 'json'
                    );
                return false;
            }
        });
    },

    deleteMenu: function(btn) {
        Message.showConfirmation({
            body: MessageUI.translate('MN180'),
            yesCallback: function(){
                var failback = function( msg ) {
                    msg = msg || MessageUI.translate('MN181');
                    Message.showError(msg);
                };
                $.get(
                    btn.data('url'),
                    function(result) {
                        if (typeof result === 'object' && 'success' in result) {
                            if (result.success) {
                                Message.showSuccess( MessageUI.translate( result.message ) );
                                Menu.showGrid( $('#form-busca-menu').length === 0 );
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
    },

    registerOrdenationEvents : function(){
        $('.btnUp').live('click', function() {
            $.post(
                '/menu/order',
                {
                    sqMenu : $(this).attr('menu'),
                    direcao : 'up',
                    sqSistema : $('#sqSistema').val()
                },
                function(response){
                    if(response.error){
                        return false;
                    }
                    $('#sqSistema').trigger('change');
                }
            );
        });

        $('.btnDown').live('click', function() {
            $.post(
                '/menu/order',
                {
                    sqMenu : $(this).attr('menu'),
                    direcao : 'down',
                    sqSistema : $('#sqSistema').val()
                },
                function(response){
                    if(response.error){
                        return false;
                    }
                    $('#sqSistema').trigger('change');
                }
            );
        });
    },

    changeRaiz : function(){
        $('.menuRaiz').css('margin-top', '0').parent('div').css('padding-top', '3px');

        $('.menuRaiz').click(function(){
            if ($(this).val() == 's') {
                $('#selectAbaixoDe').removeClass('hide');
                $('#selectMenuDependencia').addClass('hide');
                $("#sqMenuPai option:first").attr('selected','selected');
                $('#sqMenu').attr('disabled', true);

                Menu.comboOrdenarAbaixo(false, Menu.nivel1);
            } else {
                $('#selectAbaixoDe').removeClass('hide');
                $("#selectAbaixoDe option[value!='']").remove();
                $('#selectMenuDependencia').removeClass('hide');
                $('#sqMenu').removeAttr('disabled').attr('name', 'sqMenuPai');
            }
        });
    },

    changeMenuDependencia : function (){
        $("#sqMenuPai, #sqMenu").live('change', function(){
            if ($(this).val() != '') {
                Menu.comboOrdenarAbaixo($(this).val(), false);
            }
        });
    },

    changeSystem : function (){
        $('#sqSistema').change(function(){
            $('[name=grupoRaiz]').removeAttr('checked');
            $('#sqMenuPai option').remove();
            $("#abaixoDe option[value!='']").remove();
            $('#table-menu-sistema tbody').html('');
            $('#table-menu-sistema').addClass('hide');

            $('#selectMenuDependencia').addClass('hide');
            $('#selectAbaixoDe').addClass('hide');

            if ($(this).val() !== '') {
                $('#menu-sistema').load(BASE + '/sistema/find-menu/id/' + $(this).val(), null, Menu.showGrid(true));
                $('[name=grupoRaiz]').removeAttr('disabled');
            } else {
                $('[name=grupoRaiz]').attr('disabled','disabled');
            }
        });

        $(document).ajaxStop(function(){
            $('#sqMenu, #abaixoDe, #id_menu').addClass('span8');
        });
    },

    showGrid : function (ordenar){
        $.post(
            'menu/list',
            {
                sqSistema : $('#sqSistema').val(),
                ordenar : ordenar
            },
            function(response){
                $('#table-menu-sistema tbody').html(response);
                $('#grid-menu').removeClass('hide');
                $('#table-menu-sistema').removeClass('hide');
            }
            );
    },

    gridIndex : function(ordenar) {
        $('#form-busca-menu').submit(function(){

            if($('#form-busca-menu').valid()){
                $('#sistema-sel').val($('#sqSistema').val());
                $('#table-menu-sistema tbody').html('');
                Menu.showGrid(ordenar);
            }

            return false;
        });
    },

    comboOrdenarAbaixo : function(sqMenuPai, nuNivel){
        $.post(
            '/menu/find-menu/',
            {
                sqMenuPai : (sqMenuPai)?sqMenuPai : '',
                nuNivel   : (nuNivel)?nuNivel : '',
                sqSistema : $('#sqSistema').val()
            },
            function(response){
                $("#selectAbaixoDeDiv").html(response);
                $('select[id=id_menu]').attr('name', 'abaixoDe');
            });

        $(document).ajaxStop(function(){
            $('#sqMenu, #abaixoDe, #id_menu').addClass('span8');
        });
    }

};