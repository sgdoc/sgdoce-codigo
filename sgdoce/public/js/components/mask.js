$(document).ready(function(){
    $.mask.options = $.extend($.mask.options,{
        attr: 'mask'
    });

    $.mask.masks = $.extend($.mask.masks,{
        digital     : { mask : '99999999999' },
        numeric     : { mask : '9',type: 'repeat'},
        money       : { mask : '99,999.999.999.9', type : 'reverse'},
        porcentagem : { mask : '99,999', type:'reverse'},
       'phone-sp'   : { mask : '9999-99999?' }
    });
    $('input:text').setMask();
});
