(function($) {
    $.extend($.fn, {
        spellChecker: function(options){
            var element = this[0];
            var spell = $.data(element, 'spell'); 
            
            if(spell){
                return spell;
            }
            
            spell = new $.spell(element, options);
            $.data(element, 'spell', spell);
            
            return spell;
        }   
    });
})(jQuery);

$.spell = function(element, options){
    this.init(element, options);
};

$.extend($.spell, {
    options: {
        lang: 'pt_BR',
        parser: 'html',
        
        webservice: {
            path: "/auxiliar/corretor-ortografico/index",
            driver: 'pspell'
        },
        
        local: {
            requestError: 'Houve um erro ao processar a requisição.',
            ignoreWord: 'Ignorar palavra',
            ignoreAll: 'Ignorar todas',
            ignoreForever: 'Adicionar ao dicionário',
            loading: 'Carregando...',
            noSuggestions: '(Não há sugestão)'
        },
        
        suggestBox: {
            position: 'below'
        },
        
        getText: function(){
            var wysihtml5 = window.editor;
                        
            if(!wysihtml5){
                return null;
            }
                        
            return $(wysihtml5.composer.iframe).contents().find('body').html();
        }
    },
        
    optionsWysihtml5: {
        toolbar: {
            "spellchecker":
            "<li>" +
        "<a class='btn' data-wysihtml5-command='spellcheck'>" +
        "<i class='icon-spellchecker spellchecker-button-icon'></i></a>" +
        "</li>"
        },
        
        stylesheets: ["./css/jquery.spellchecker.css"],
        
        parserRules : {
        	tags : {
        		b : {},
        		i : {},
        		s : {},
        		u : {},
        		blockquote : {},
        		ol : {},
        		ul : {},
        		li : {},
        		h1 : {},
        		h2 : {},
        		h3 : {},
        		div : {}
        	}
        }
    },
    
    callSuccessSpellChecker: function(){
    },
        
    callFailSpellChecker: function(){
    },
        
    prototype:{
        
        spellchecker: null,
        
        init: function(element, options){
            var spell = this;
            
            $(element).wysihtml5($.spell.optionsWysihtml5);
            
            $(window).load(function(){
                var body = $(window.editor.composer.iframe).contents().find('body');
                
                if(options == undefined){
                    options = $.spell.options;
                }
                
                $('[data-wysihtml5-command="spellcheck"]').click(function(){
                    spell.toogle(body, options);
                })
            });
        },
        
        toogle: function(body, options){
            if(this.spellchecker){
                this.destroy();
            }
            
            return this.create(body, options);
        },
        
        create: function(body, options) {
            this.spellchecker = new $.SpellChecker(body, options);
            this.spellchecker.on('check.success', $.spell.callSuccessSpellChecker());
            this.spellchecker.on('check.fail', $.spell.callFailSpellChecker());
            this.spellchecker.check();
        },
        
        destroy: function(){
            this.spellchecker.destroy();
            this.spellchecker = null;
        }
    }
});
