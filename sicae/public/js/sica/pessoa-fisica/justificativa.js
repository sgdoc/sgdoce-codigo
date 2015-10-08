PessoaFisicaJustificativa = {

    concluir: function(){
        $('.btnConcluirJustificativa').click(function(){
            if(!$('#txJustificativa').val().length){
                $('#txJustificativa').parent().parent().addClass('error');
                $('#txJustificativa').parent().find('p.help-block').remove();
                $('#txJustificativa').parent().append('<p for="group9" generated="true" class="help-block">Campo de preenchimento obrigatório.</p>');

                return false;

            }

            if($.trim($('#txJustificativa').val()).split(' ').length <= 4){
                $('#txJustificativa').parent().parent().addClass('error');
                $('#txJustificativa').parent().find('p.help-block').remove();
                $('#txJustificativa').parent().append('<p for="group9" generated="true" class="help-block">A justificativa deve conter no mínimo 05 palavras.</p>');

                return false;
            }


            $('#form-pessoa').submit();
        });

        $('#txJustificativa').keyup(function(){
            if(!$('#txJustificativa').val().length){
                $(this).parent().parent().addClass('error');
                $(this).parent().find('p.help-block').remove();
                $(this).parent().append('<p for="group9" generated="true" class="help-block">Campo de preenchimento obrigatório.</p>');
            }else{
                $(this).parent().parent().removeClass('error');
                $(this).parent().find('p.help-block').remove();
            }
        });

        $('.viewJustificativa').click(function(){
            $('#txJustificativa').attr('disabled', true);
            $('#modal-justificativa').modal({backdrop: 'static', keyboard: false});
        });
    },

    initJustificativa: function(){
        $('#btnProximo, #btnSalvar, #btnConcluir').click(function(){
            $('.alert').hide();

            if($(this).attr('id') == 'btnProximo'){
                $('#aba').val('2');
            }

            if($(this).attr('id') == 'btnSalvar'){
                $('#aba').val('1');
            }

            if($(this).attr('id') == 'btnConcluir'){
                $('#aba').val('');
            }

            if($('#form-pessoa').valid()){
                if(!$('#nuCpf').val() && !$('#sqPessoa').val()){
                    $('#modal-justificativa').modal({backdrop: 'static', keyboard: false});
                    $('#txJustificativa').parent().parent().removeClass('error');
                    $('#txJustificativa').parent().find('p.help-block').remove();
                    $('#txJustificativa').val('');
                }else{
                    $('#form-pessoa').submit();
                }
            }
        });
    },

    init: function(){
        PessoaFisicaJustificativa.concluir();
        PessoaFisicaJustificativa.initJustificativa();
    }
}

$(document).ready(function(){
    PessoaFisicaJustificativa.init();
});