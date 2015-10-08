$(document).ready(function(){
    $('#cancelar').click(function(){
        window.location = 'auxiliar/tipodoc';
    });
    $('input:radio').parent().addClass('inline').addClass('radio'); //adiciona as classes inline e radio
    $('input:radio').parent().next().remove(); //remove o br
});
