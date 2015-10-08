PessoaJuridicaView = {
    init: function() {
        $('.requiredLegend')
                .removeClass('.pull-left')
                .parent('div')
                .html('<div class="btn-group pull-right"><a class="btn btn_voltar" href="/principal/pessoa">Voltar</a></div>');

        $('button[id=btnConcluir], button[id=btnSalvar], .btn-mini').hide();
        $('.btnCancelar').html('Voltar');
        $('.tab-content div form a.btn, .tab-content div form button.btn').attr('title', '');
        $('#form-dado-bancario a.disabled span').addClass('icon-white');
        $('#form-dado-bancario a.disabled').addClass('btn-primary buttons').removeClass('disabled').attr('href', '#vinculo-sistemico');
        $('a.btnCancelar:last').addClass('btn-primary');

        $('#form-pessoa input:visible, #form-pessoa select:visible').each(function(index) {
            var label = $.trim($(this).parent('div').parent('div').find('label.control-label').text().replace('*', ''));
            var html = '<p class="span11"><b class="span3">';

            var value = $(this).val();

            if ($(this).is('select')) {
            	value = $('#' + $(this).attr('id')).find(':selected').text();
            }
            
            if ($(this).hasClass('cnpj')) {
                value = $('<input value="' + $(this).val() + '" />').setMask('cnpj').val();
            }

            html = html + label;
            html = html + '</b> ';
            html = html + value;
            html = html + '</p>';

//            $(this).parent('div').parent('div').remove();
//
//            if (index === 0) {
//                $("#form-pessoa fieldset").html("<br>");
//            }

            $("#form-pessoa fieldset").append(html);
        });

        $('.formularioPj').html('');

        $(document).ajaxStop(function() {
            $('.icon-pencil').each(function() {
                $(this).parent('a').parent('td').hide();
                $(this).parents('table').find('th:last').hide();
            });

            var tr = $('#table-endereco tr:not("tr:first"):contains("Institucional")');

            if (tr.size()) {
                $('#table-endereco tr:not("tr:first")').not(':contains("Institucional")').remove();
            } else {
                $('#table-endereco tr:not("tr:first")').not(':last').remove();
            }

            $('#table-endereco th').each(function(index) {
                var position = index + 1;
                var value = $('#table-endereco tr:not("tr:first") td:nth-child(' + position + ')').text();

                if (position <= 7 && value != MessageUI.get('MN016')) {
                    var div = '<div><p class="span11">';
                    div = div + '<b class="span3">' + $(this).text() + '</b>';
                    div = div + value;
                    div = div + '</p></div>';

                    $('#table-endereco').parent('div').append(div);
                }
            });

            $('#table-endereco').parent('div').prev().prev().remove();
            $('#table-endereco').remove();

            $('#table-telefone th:last, #table-email th:last, #table-dado-bancario th:last, #table-pessoa-vinculo th:last').remove();
        });


    },
    iniAbas: function() {
        $('.buttons').click(function() {

            if ($(this).attr('href') == '#documento') {
                $('li a[href=#pessoa-vinculo]').tab('show');
            } else {
                $('li a[href=' + $(this).attr('href') + ']').tab('show');
            }

            return false;
        });

        $('ul.nav-tabs li a').click(function() {
            $('.alert-success').hide();
        });

        if ($('#aba').val()) {
            $('ul.nav-tabs li:nth-child(' + $('#aba').val() + ') a').tab('show');
        }
    }
}

$(document).ready(function() {
    setTimeout(function(){
    	PessoaJuridicaView.init();
    },1000);    

    PessoaJuridicaView.iniAbas();
});