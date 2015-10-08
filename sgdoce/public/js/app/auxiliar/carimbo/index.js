Carimbo = {
    deletar: function(codigo){
        var callBack = function(){
            window.location = '/auxiliar/carimbo/delete/id/' + codigo;
        }
                
        Message.showConfirmation({
            'body': 'Tem certeza que deseja realizar a exclusão?',
            'yesCallback': callBack
        });
    },
    
    visualizar: function(codigo){
        window.location = '/auxiliar/carimbo/visualizar/id/' + codigo;
    }    
}

$(function(){

    $('#modalCarimbo').on('hidden', function () {
        $(this).find('.modal-body').html('')
    // do something…
    })

    Grid.load($('#form-carimbo'), $('#table-grid-carimbo'));

     $("body").on('click','a[data-toggle=modal]',function (e) {
      lv_target = $(this).attr('data-target')
      lv_url = $(this).attr('href')
      $(lv_target).find('.modal-body').load(lv_url)
    })

//    setTimeout(function(){  $('#table-grid-carimbo').find('tr:not(:first)').remove(); }, 1000);
});
