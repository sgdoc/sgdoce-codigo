var Combo = {
    init: function(){
        
    },
    
    defaultCombo: function(url, combo, combo2, callBack){
        $(combo2).remoteChained({
            parent: combo,
            url: url,
            after: callBack
        });
    }
}

$(document).ready(function(){
    Combo.init();
});