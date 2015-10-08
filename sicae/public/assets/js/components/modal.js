$(document).ready(function(){
        var windowHeight = $(window).height() -80;
        var windowWidth = $(window).width() -60;
        var modalBodyHeight = $(window).height();

        $('.modal').css('max-height', windowHeight);
        $('.modal .modal-body').css('height', 'auto');
        $('.modal .modal-body').css('max-height', modalBodyHeight -220);

        $('.modal').css('width', '562px');
        $('.modal').css('max-width', '562px');
        $('.modal').css('margin-left', -(562/2));
         
        $('.modal.modal-medium').css('width', '700px');
        $('.modal.modal-medium').css('max-width', '700px');
        $('.modal.modal-medium').css('margin-left', -(650/2));
         
        $('.modal.modal-large').css('width', '843px');
        $('.modal.modal-large').css('max-width', '843px');
        $('.modal.modal-large').css('margin-left', -(843/2));
         
        $('.modal.modal-full').css('width', windowWidth);
        $('.modal.modal-full').css('max-width', windowWidth);
        $('.modal.modal-full').css('margin-left', -(windowWidth/2));
});