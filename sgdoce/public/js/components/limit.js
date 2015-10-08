var Limit = {
    init: function(){
        $('textarea[maxlength]').each(function(){
            var limit = $(this).attr('maxlength');

            $(this).jqEasyCounter({
                'msgTextAlign': 'left',
                'maxChars': limit,
                'maxCharsWarning': limit - 5
            });
        });
    }
};

$(document).ready(function(){
    Limit.init();
});