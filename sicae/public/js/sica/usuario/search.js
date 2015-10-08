$(document).ready(function() {
    loadJs('js/library/jquery.simpleautocomplete.js', function() {
        $('#noUnidade').simpleAutoComplete(BASE + '/unidade-organizacional/ativas', {
            attrCallBack: 'class',
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel',
            hiddenName: 'sqUnidadeOrg'
        });
    });

    $('button[type=reset]').click(function() {
        $('#menu-sistema select option:not(:first)').remove();

        $('.campos-obrigatorios').hide();

        $("#form-pesquisa-usuario").validate().resetForm();
        $("#form-pesquisa-usuario").validate().elements().each(function(elment){
            $(this).parents('.error').removeClass('error');
        });

        $('[id=sqPerfil]:last').attr('disabled', true).val('').change();
        $('#form-pesquisa-usuario  input[type=text]').each(function(a, b){
            $(b).attr('value', '');
        });
    });

    if($('#id').val()){
        var callBackPerfil = function(){
            UsuarioInterno.bind($('#id').val());
        }

        Message.showConfirmation({
            'body': 'Deseja atribuir perfil ao usuário cadastrado?',
            'yesCallback': callBackPerfil
        });
    }

    Grid.load($('#form-pesquisa-usuario'), $('#table-usuario'));

    $('#form-pesquisa-usuario').unbind('submit').submit(function() {
        var $this = $(this);
        $('#pesquisa-pdf').val($this.find(':not(#pesquisa-pdf)').serialize());
        $('#search').val('1');

        if ($(':input[value=""]:not(:hidden,button)', this).size() === $(':input:not(:hidden,button)', this).size()) {
            Validation.addMessage('Informar no mínimo um dos parâmetros de consulta.');
            $(document).scrollTop(40);
            return false;
        }

        $('.campos-obrigatorios').addClass('hide');
        $('#table-usuario').dataTable().fnDraw(false);

        $('#table-usuario').closest('div.hidden').removeClass('hidden');
        return false
    });

    $('#form-pesquisa-sistema').unbind('submit').submit(function() {
        var $this = $(this);
        $('#search').val('1');
        $('#pesquisa-pdf').val($this.find(':not(#pesquisa-pdf)').serialize());

        if ($(':input[value=""]:not(:hidden,button)', this).size() === $(':input:not(:hidden,button)', this).size()) {
            Validation.addMessage('Informar no mínimo um dos parâmetros de consulta.');
            $(document).scrollTop(40);
            return false;
        }

        $('.campos-obrigatorios').addClass('hide');
        $('#table-sistema').dataTable().fnDraw(false);

        return false;
    });

    $('[id=sqPerfil]:last').attr('disabled', true);
    $('#sqSistema').change(function() {
        var value = $(this).val();

        if (value) {
            $('[id=sqPerfil]:last').parent('div').load('/perfil/combo-profile', {
                sqSistema             : $('#sqSistema').val(),
                inPerfilExterno : 0
            },function(){
                $('[id=sqPerfil]:last').addClass('span9');
            });

            $('[id=sqPerfil]:last').removeAttr('disabled');
        }else{
            $('[id=sqPerfil]:last').attr('disabled', true).val('').change();
        }
    });
});

var UsuarioInterno = {};
UsuarioInterno.bind = function(id) {
    window.location = BASE + '/usuario-interno/bind/id/' + id;
}
