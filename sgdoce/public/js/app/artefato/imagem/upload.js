var ordem;

$(document).ready(function() {
    $('#btnOrdenar').click(function (){
        Imagem.ordenacao('artefato/imagem/list/id', ordem);
        listUpload.reloadDivImagem();
    });

    $("#concluirUpload").click(function() {
        Imagem.upload('artefato/imagem/list/id');
        return false;
    });

   $("#cancelarModalUpload").click(function() {
        $('input[type="radio"]').removeAttr('checked');
        $('#imageFile').attr({ value: '' });
        $("#inFrente").val($("#inFrente option:last").val());
        $('.close').click();
        return false;
    });

    $("#removeImage").click(function() {
        if($('#qtdAll').val() == 0) return false;
        var sqArtefato = $('#sq-artefato').val()
        var arrSqArtefato = $('ul.thumbnails').children();
        var podeExcluir = 0;
        $.each(arrSqArtefato,function(key, value){
            if ($(value).attr('artefatoValue') == sqArtefato) {
                podeExcluir++;
            }
        });
        if (!podeExcluir) {
            return false;
        }
        Imagem.remove('artefato/imagem/list/id', 3);
        $('#checkado').val('');
        return false;
    });

    $('#confirmDelete').live('click', function() {

        if ($('#checkado').val() != 1) {
            Imagem.removeAllAjax('artefato/imagem/list/id');
            $('#checkado').val(1);
        }
        return false;
    });

    $('#confirmDeleteSingle').live('click', function() {
        var arCheck = new Array();
        arCheck.push($("#sqAnexoHidden").val());
        Imagem.removeAjax('artefato/imagem/list/id', arCheck);
        return false;
    });

    $(".remove-image").click(function() {
        Imagem.removeSingle($(this).attr("modalValue"));
        return false;
    });

    $("#btnAdicionarImagem").click(function() {
        $('#adicionarImagem').show();
        return false;
    });

    $('#bntParaTudo').click(function (){
        Imagem.reload('artefato/imagem/list/id/' + $('#sq-artefato').val());
    });

//    $('#btnOrdenar').click(function (){
//        Imagem.ordenacao('artefato/imagem/list/id', ordem);
//        listUpload.reloadDivImagem();
//    });

    $("#lista ul").sortable({
        opacity: 0.5,
        cursor: 'move',
        revert: true,
        update: function(){
            ordem = $(this).sortable('serialize');
        },
        stop: function( e, ui ) {
		grid.initAjustaGrid();
	}
    });
});