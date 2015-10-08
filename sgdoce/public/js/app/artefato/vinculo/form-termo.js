/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var formTermoJuntada = {

    init: function () {

        this.visibilits(false);

        this.eventGetDespachoInter();

        this.dateInit();
        
        this.acAssinatura();

        $('#sqDespachoInterlocutorio').setMask();

        $(".infolink").tooltip();
    },

    dateInit: function () {
        $('.date').datepicker({
            format: 'dd/mm/yyyy',
            language: 'pt-BR',
            autoclose: true
        });
    },

    eventGetDespachoInter: function () {

        $('.btn-search-despacho-interlocutorio').on('click', function () {
            var despacho = $('#sqDespachoInterlocutorio');
            var filter = despacho.val().trim();

//            if (!$('#form-termo-juntada').valid()) { //para mostrar a barra vermelha de validação
            if (!despacho.valid()) {
                return false;
            }

            $.ajax({
                type: "get",
                dataType: "json",
                url: "/artefato/despacho-interlocutorio/auto-complete-get-from-termo",
                data: {"sqDespacho": filter}

            }).done(function (result) {

                if(result.status) {

                    $('.dtDespacho').val(result.data.dtDespacho);
                    $('.noUnidadeAssinatura').val(result.data.noUnidadeAssinatura);                    

                    $('#stCargoFuncao1').on('click', formTermoJuntada.handleOnCargao);
                    $('#stCargoFuncao2').on('click', formTermoJuntada.handleOnFuncao);
                    
                    formTermoJuntada.visibilits(true);

                } else {
                    Message.showAlertNotification('Nenhum despacho encontrado', false, $('.modal-body:visible'));
                    formTermoJuntada.visibilits(false);
                }
            });
        });
    },

    acAssinatura: function () {
        $('#sqAssinante' ).simpleAutoComplete("/artefato/despacho-interlocutorio/search-pessoa-unidade/", {
            autoCompleteClassName: 'autocomplete',
            selectedClassName    : 'sel'
        });
    },

    visibilits: function (status)
    {
        var action = status ? 'removeClass' : 'addClass';
        $('.div-content-body')[action]('hide');
    },
    
    handleOnCargao: function(){
        var isChecked = $(this).is(':checked');
        
        if( isChecked ) {
            $("#divCargo").removeClass('hidden').show();
            $("#sqCargo").addClass('required');
            $("#divFuncao").addClass('hidden').hide();
        } else {            
            $("#divFuncao").addClass('hidden').hide();
            $("#sqFuncao").removeClass('required');
            $("#divCargo").removeClass('hidden').show();
            
        }
    },
    handleOnFuncao: function(){
        var isChecked = $(this).is(':checked');
        
        if( isChecked ) {
            $("#divFuncao").removeClass('hidden').show();
            $("#sqFuncao").addClass('required');
            $("#divCargo").addClass('hidden').hide();
        } else {
            $("#divCargo").addClass('hidden').hide();
            $("#sqCargo").removeClass('required');
            $("#divFuncao").removeClass('hidden').show();
        }
    }
};

$(document).ready(function(){ formTermoJuntada.init(); });