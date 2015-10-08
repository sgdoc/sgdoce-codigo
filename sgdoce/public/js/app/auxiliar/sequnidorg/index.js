SequencialArtefato = {
    alterar: function(codigo, unidade){
        var gridLength = $('[name="table-grid-sequnidorg_length"] option:selected').val();
        window.location = '/auxiliar/sequnidorg/edit/id/' + codigo + '/unidade/' + unidade + '/gridLength/' + gridLength;
    },

    adicionar: function(sqUnidade, nuAno, tipo){
        window.location = '/auxiliar/sequnidorg/create/unidade/' + sqUnidade + '/ano/' + nuAno + '/tipo/' + tipo;
    },
    
    getSequencial : function () {
        $('select[name=sqTipoDocumento]').change(function () {
            $.ajax({
              data : $('#form-manter-seq-unid-org').serialize(),
              type: "POST",
              dataType : "json",
              url : '/auxiliar/sequnidorg/buscar-sequencial',
              success : function(response){
                  if (response == null) {
                      var sqUnidadeOrg = $('input[name=sqUnidadeOrg]').val();
                      var nuAno = $('input[name=nuAno]').val();
                      var tipo = $('select[name=sqTipoDocumento]').val();
                      window.location = '/auxiliar/sequnidorg/create/unidade/' + sqUnidadeOrg + '/ano/' + nuAno + '/tipo/' + tipo;
                  } else {
                      window.location = '/auxiliar/sequnidorg/edit/id/' + response.sqSequencialArtefato;
                  }
              }
          });
        });
    }
}

$(function(){
   $('#nuAno').each(function(){
       $(this).find("option[label=Selecione]").remove();
   });

    $('#noPessoa').simpleAutoComplete("/auxiliar/sequnidorg/search-unidades-organizacionais/",{
        extraParamFromInput: '#extra',
        attrCallBack: 'id',
        autoCompleteClassName: 'autocomplete',
        selectedClassName: 'sel'
    },function(data){
        $.post('/auxiliar/sequnidorg/search-nup',{unidadeOrg:data[1]},function(result){
            if (result.success) {
                $('#divNup').show();
                $('#nup').val(result.nup);
            } else {
                $('#nup').val('');
                $('#divNup').hide();
            }
        });
    });
    $("#table-grid-sequnidorg_length option:selected").val($('[name="gridLength"]').val());
    Grid.load($('#form-seq-unid-org'), $('#table-grid-sequnidorg'));
    
    var url = window.location.href.split("gridLength/");
    if (url[1] != undefined) {
    	$("#table-grid-sequnidorg_length option:selected").val(url[1]).trigger('change');
    }
    
    $('#form-seq-unid-org').submit(function() {
        $('.alert.alert-success').remove();
    });

    if ($('#nup').val()) {
        $('#divNup').show();
    }
    SequencialArtefato.getSequencial();
});
