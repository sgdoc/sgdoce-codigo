ArtefatoDelete = {
    Cancelar: function(sqArtefato){
        $.post('/artefato/artefato/delete', {
            sqArtefato: $('#sqArtefato').val()
        })            
    },

    Deletar: function(sqArtefato,nuDigital,view){
        var callBack = function(){ 
            $.post('/artefato/artefato/delete', {
            sqArtefato: sqArtefato
            },function(data){
                if(data == 'true'){                    
                    Message.showSuccess(UI_MSG['MN013']);
                    $('.btn-primary').click(function(){
                        switch (view) {
                        case '2':
                            //window.location = '/artefato/consultar-artefato/consultar-artefato-padrao/';
                            window.location = '/artefato/area-trabalho/';
                            break;
                        case '3':
                            //window.location = '/artefato/consultar-artefato/consultar-artefato-padrao/';
                            window.location = '/artefato/area-trabalho/';
                            break;
                        case '4':
                            window.location = '/artefato/area-trabalho/';
                            break;
                        default:
                            //window.location = '/artefato/consultar-artefato/consultar-artefato-padrao/';
                            window.location = '/artefato/area-trabalho/';
                            break;
                        }
                    });
                }else if(data == 'false'){
                    var msg = 'A digital ' + nuDigital + ' não pode ser excluída porque existem vinculações. Retire as  vinculações para ser possível a exclusão. ';
                    Message.showAlert(msg);
                }
            })    
        }
          Message.showConfirmation({
          'body': 'Tem certeza que deseja realizar a exclusão?',
          'yesCallback': callBack
          });
    }
}

OpenLinkArtefato = {
    _url: '/artefato/visualizar-artefato/index/sqArtefato/%d',
    visualizar: function (sqArtefato) {
        var modal = window.open(sprintf(OpenLinkArtefato._url, sqArtefato), 'visualizarArtefato' + sqArtefato, 'fullscreen=yes,location=no,menubar=no,scrollbars=yes');
        modal.focus();
    }
};

$(document).ready(function() {
    
});

