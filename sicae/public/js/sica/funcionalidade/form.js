var changeFuncionalidade = function(obj) {
    var $this   =  obj,
        $target =  $($this.attr('target')),
        value   =  $this.val();

    if (!value) {
        return;
    }

    $target.load(BASE + '/principal/funcionalidade/find-menu/id/' + value);
};

$(document).ajaxComplete(function(){
    $('#sqMenu').val($('#menuAcesso').val());
});

$(document).ready(function() {
    $('#form-funcionalidade').submit(function() {
        $('#sqMenu').addClass('required');

    });

    $('.sistema-load-menu-funcionalidade').live('change', function() {
        return changeFuncionalidade($(this));
    });

    $('.sistema-load-menu').trigger('change');

    $('.add').click(function(){
        var $tableCondesed = $('#form-funcionalidade tr .table-condensed'),
        key, regex = /\[(\d+)\]/;

        if ($tableCondesed.size()) {
            $tableCondesed.parent().remove();
        }

        key = regex.exec($('#form-funcionalidade table :text:last').attr('name')) || [];

        if (typeof key[1] === "undefined") {
            key[1] = -1;
        }

        key = parseInt(key[1]);

        if ($('#form-funcionalidade table :text:visible').size()) {
            $('#form-funcionalidade table :text:visible:last').focus();
            return;
        }

        ++key;
        
        var tr = $('<tr>').append(
            '<td><input type="radio" name="rota[' + (key) + '][inRotaPrincipal]" value="1" class="hide edit radio" /></td>'
            + '<td><div class="control-group clear-none">'
            + '<div class="controls span12">'
            + '<input type="text" name="rota[' + (key) + '][txRota]" maxlength="200" class="input-xxlarge create required">'
            + '</div></div></td>'
            + '<td>'
            + '<div class="btn-group">'
            + '<span class="edit hide">'
            + '<button title="Alterar" href="#" class="btn btn-mini btnEdit" type="button"><i class="icon-pencil"></i></button>'
            + '</span>'
            + '<span class="edit hide">'
            + '<button title="Excluir" href="#" class="btn btn-mini btnDelete" type="button"><i class="icon-trash"></i></button>'
            + '</span>'
            + '<span class="create">'
            + '<button title="OK" class="btn btn-mini btnOk" type="button"><i class="icon-ok"></i></button>'
            + '</span>'
            + '</div></td>'
            + '</td>'
            );

        $('#form-funcionalidade table')
        .append(tr)
        .find(':text:last').focus();
    });

    $('#form-funcionalidade .btnOk').live('click', function(){
        var $this = $(this),
        input = $('#form-funcionalidade table :text:visible:eq(0)'),
        $tr    = input.closest('tr');

        if (input.hasClass('required')) {
            var value = $.trim(input.val());
            if (!value) {
                $('#form-funcionalidade').submit();
                return;
            }
        }

        $tr.find('span.text').remove();
        $('<span class="edit text">' + input.val() + '</span>').insertBefore(input);

        $tr.find('.edit').removeClass('hide');
        $tr.find('.create').addClass('hide');
    });

    $('#form-funcionalidade .btnEdit').live('click', function(){
        var $this = $(this),
        $tr   = $this.closest('tr');

        $tr.parent().find('.create').addClass('hide');
        $tr.parent().find('.edit').removeClass('hide');
        $tr.find('.create').removeClass('hide');
        $tr.find('.edit').addClass('hide');
    });

    $('#form-funcionalidade .btnDelete').live('click', function(){
        var $this = $(this),
        $tr   = $this.closest('tr');

        $tr.remove();
    });

    $('.radio').live('click', function() {
        $('.radio').attr('checked', false);
        $(this).attr('checked', true);
    });

    changeFuncionalidade($('.sistema-load-menu-funcionalidade'));
});