var lastResponse;
$(document).ready(function(){
    loadJs('js/library/jquery.simpleautocomplete.js', function() {
        $('#noResponsavel').simpleAutoComplete(BASE + '/principal/sistema/find-responsible', {
            attrCallBack: 'class',
            autoCompleteClassName: 'autocomplete',
            selectedClassName: 'sel',
            hiddenName: 'sqPessoaResponsavel'
        }, function(li, el, hidden) {
            var label = el.text();
                label = label.substr(label.indexOf(' - ') + 3);

            $('#noResponsavel').val(label);
            $('.responsavel').val('');
            $('form[type=submit]').attr('disabled', false);

            $.getJSON(BASE + '/principal/pessoa-fisica/get-data-institucional/id/' + hidden.val(), function(response) {
                lastResponse = null;
                if (Message.hasMessage(response) && (Message.isError(response) || Message.isAlert(response))) {
                    Message.showMessage(response);
                    lastResponse = response;
                    return;
                }

                $.each(response.content, function(index, value) {
                    $('#' + index).val(value);
                });
            });
        });
    });

    $('form').submit(function() {
        if (lastResponse) {
            Message.showMessage(lastResponse);
            return false;
        }
    });
});