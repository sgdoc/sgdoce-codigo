$(document).ready(function(){
    var params = {
        sqTipoDocumento : $('#sqTipoDocumento').val() , 
        sqAssunto : $('#sqAssunto').val()
    };
    var arrCampoModeloDocumento = FormCadastrarArtefato.getCampoModeloDocumento(params);
    var j = $.parseJSON(arrCampoModeloDocumento);    
    if(j.length > 0){
        $.each(j, function(i) {
            $('#dv'+j[i].sqGrupoCampo+'-'+j[i].noColunaTabela).show();
        });
    }
    
    $('#sqPessoa').simpleAutoComplete("/artefato/pessoa/search-pessoa", {
        extraParamFromInput: '#extra',
        attrCallBack: 'rel',
        autoCompleteClassName: 'autocomplete',
        selectedClassName: 'sel'
    });

    $('#sqMunicipio').simpleAutoComplete("/artefato/pessoa/search-pessoa", {
        extraParamFromInput: '#extra',
        attrCallBack: 'rel',
        autoCompleteClassName: 'autocomplete',
        selectedClassName: 'sel'
    });
    
    $("#cep").live('keyup blur', function(){
        var tamanho = $("#cep").val().length;
        if(tamanho == 9){
            FormCadastrarArtefato.getCep($(this).val())
        }
    })
})

FormCadastrarArtefato = {
    getCampoModeloDocumento: function(params) {
        return $.ajax({
            type: 'post',
            url: '/artefato/minuta-eletronica/get-campo-modelo-documento',
            data: params,
            async: false,
            global: false
        }).responseText;       
    },
    getCep : function(cep){
        $.ajax({
            url  : '/artefato/minuta-eletronica/search-endereco-cep',
            data : {
                cep : cep
            },
            type : 'post',
            dataType : 'json',
            success : function(data){
                $("#logradouro").val(data.logradouro);
            }
        })
    }
}