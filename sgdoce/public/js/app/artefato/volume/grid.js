var VolumeGrid = 
{
    sqArtefato: null,
    
    urlDelete: '/artefato/volume/delete-volume/id/%d',
    
    urlEdit: '/artefato/volume/edit/id/%d',
    
    urlViewVolume: 'artefato/volume/detail/id/%d',
    
    urlPrintOpen: 'artefato/volume/termo/id/%d/abertura/1',
    
    urlPrintClose: 'artefato/volume/termo/id/%d/abertura/0',
    
    init : function()
    {
        try 
        {
            VolumeGrid.sqArtefato = $('#sqArtefato').val();

            this.hasArtefato();
            this.grid();
            
            //$('#btn_imprimir').click(VolumeGrid.print);

        } catch (e) {
            Message.showError(e.message);
            return;
        }
        
        $('#cancelar').click(function(e){
            e.preventDefault();
            $("#modal_container_xl_size").modal('hide').html('').css('display', 'none');
            $('.modal-backdrop').remove();
            return false;
        });
    },
    
    hasArtefato: function() 
    {
        if (!VolumeGrid.sqArtefato) {
            throw {"code": 301, "message": "Nenhum artefato localizado."};
        }
    },
    
    grid: function() 
    {
        Grid.load($('#volume-artefato-grid-form'), $('#grid_volume'));
    },
    
    edit: function(sqVolume) 
    {
        AreaTrabalho.initModal(sprintf(VolumeGrid.urlEdit, sqVolume));
    },
    
    confirmDelete: function(sqVolume) 
    {
        Message.showConfirmation({
            body: UI_MSG.MN202,
            yesCallback: function() {
                VolumeGrid.doDelete(sqVolume);
            }
        });
        return;
    },
    
    doDelete: function(sqVolume) 
    {
        request = $.ajax({
            url: sprintf(VolumeGrid.urlDelete, sqVolume),
            type: "post",
            datatype: 'json'
        }).success(function(result) {
            if (result.status) {
                var _url = sprintf(
                        '/artefato/volume/grid/id/%d/back/%s',
                        $('#sqArtefato').val(),
                        AreaTrabalho.getUrlBack()
                        );
                AreaTrabalho.initModal(_url);
            }else{
                Message.showError(result.message);
            }
        }).error(function(result) {
            Message.showError("Ocorreu um erro desconhecido e a operação solicitada não pode ser realizada.");
        });
    },

    viewVolume: function (sqVolume) {
        var target = sprintf(VolumeGrid.urlViewVolume, sqVolume);
        AreaTrabalho.initModal(target, $("#modal_container_medium"));
    },

    printOpen: function (sqVolume) {
        var target = sprintf(VolumeGrid.urlPrintOpen, sqVolume);
        window.location = target;
    },

    printClose: function (sqVolume) {
        var target = sprintf(VolumeGrid.urlPrintClose, sqVolume);
        window.location = target;
    }
}

$(document).ready(function() {
    VolumeGrid.init();
});