var Vinculo = {
    _urlImageView: '/artefato/imagem/view/id/%d/view/migracao',
    init: function(){
        setInterval(function(){ location.reload(); }, 120000);
    },
    imageView: function(sqArtefato) {
        var modal = window.open(sprintf(Vinculo._urlImageView, sqArtefato), 'imageView'+sqArtefato,'fullscreen=yes,location=no,menubar=no,scrollbars=yes');
        modal.focus();
    }
};

$(document).ready(function(){
    Vinculo.init();
});