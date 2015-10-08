loadJs('js/library/nestedAccordionCompressed.js', function() {
    Menu.init();
}); // load cdn

var Menu = {
    init: function(){
        $("html").addClass("js");

        $.fn.menu.defaults.container = false;

        $(function() {
            $("#acc1").menu({
                initShow : "#current"
            });
        });

        $('.dropdown-perfil ul.dropdown-menu li').click(function(){
            $('body').click();
        });
    }
}