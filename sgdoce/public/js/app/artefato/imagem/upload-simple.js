$(document).ready(function() {
    $("#concluirUpload").click(function() {
        Imagem.upload('artefato/imagem/list-simple/id');
        return false;
    });

    $("#removeImage").click(function() {
        Imagem.remove('artefato/imagem/list-simple/id', 3);
        return false;
    });

    $('#confirmDelete').live('click', function() {
        Imagem.removeAjax('artefato/imagem/list-simple/id');
        return false;
    });

    $('#confirmDeleteSingle').live('click', function() {
        var arCheck = new Array();
        arCheck.push($("#sqAnexoHidden").val());
        Imagem.removeAjax('artefato/imagem/list-simple/id', arCheck);
        return false;
    });

    $(".remove-image").click(function() {
        Imagem.removeSingle($(this).attr("modalValue"));
        return false;
    });
});