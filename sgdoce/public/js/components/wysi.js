(function(){
    Wysi = {

        loadConfigs: function(){
        var element = $('.txTextoArtefato');
            element.wysihtml5({

            toolbar: {
                "spellchecker":
                "<li>" +
                "<a class='btn btSpellchecker' data-wysihtml5-command='spellcheck'>" +
                "<i class='icon-spellchecker spellchecker-button-icon'></i></a>" +
                "</li>"
                },             
                
                stylesheets: [
                "./css/jquery.spellchecker.css"
                ],
            });

        var wysihtml5 = element.data('wysihtml5');
        var body = $(wysihtml5.editor.composer.iframe).contents().find('body');
        
        MinutaPasso.txTextoArea();

        }
    };
    
})();

Wysi.loadConfigs();    
