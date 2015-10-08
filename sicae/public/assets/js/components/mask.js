$(document).ready(function(){
    $.mask.options = $.extend($.mask.options,{
        attr: 'mask'
    });

    $.mask.masks = $.extend($.mask.masks,{
        numeric : {mask: '9',type: 'repeat'},
        porcentagem : { mask : '99,999', type:'reverse'},
        money       : {mask : '99,999.999.999', type : 'reverse'},
        "phone-sp"  : {mask : '99999-9999?'},
        "foneBR"  : {mask : '(999) 9999-99999?'}
    });
    $('input:text').setMask();
});