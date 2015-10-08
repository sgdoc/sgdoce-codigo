loadJs('js/library/jquery.nestedAccordion.js', function() {
    MenuSystem.init();
}); // load cdn

var MenuSystem = {
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

$(document).ready(function(){
	$('[rel=popover]').popover();
	$('[rel=tooltip]').tooltip();
});
