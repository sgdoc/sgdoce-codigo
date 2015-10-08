CaixaMinuta = {

    blockKeyEnterSearch: function() {
        $('#dataSearch').keypress(function(e){
            tecla = e.keyCode?e.keyCode:e.which;
            if(tecla == 13){
                $('#filtrar').click();
                return false;
            }
        });
    },

    acoesMultiplas: function() {

        $('.btnAcoesMult').click(function(){
            if($('input[id=sqArtefato]:checked').length <= 0){
                $('.minutas-nao-selecionadas').removeClass('hidden').show();
                return false;
            }
            else{
                $('.minutas-nao-selecionadas').addClass('hidden').hide();
            }
        });

        $('.dropdown-menu').hover(function(){
            $('.dropdown-menu').attr('style','cursor: pointer;');
        });

    },

    initBlockCheckboxCaixaEnviadas: function(){
        if($('#view').val() == 2){
            $('#allSqArtefato,  .sorting_1 > #sqArtefato').attr('readonly', 'readonly').attr('disabled', 'disabled');
        }
    },

    init: function() {
            CaixaMinuta.blockKeyEnterSearch();
            CaixaMinuta.acoesMultiplas();
            CaixaMinuta.initBlockCheckboxCaixaEnviadas();
    }
}

GridLoad = {

        initGrid: function() {
            Grid.load($('#form-visualizar-caixa-minuta'), $('#table-grid-visualizar-caixa-minuta'));
        }
}

AjustaGrid = {

        initSelectAll: function() {

            $('#allSqArtefato').click(function() {
                if(this.checked == true){
                    $("input[id=sqArtefato]").each(function() {
                        this.checked = true;
                    });
                } else {
                    $("input[id=sqArtefato]").each(function() {
                        this.checked = false;
                    });
                }
            });

            $('#sqArtefato').live('click', function(){
                   if(this.checked == false){
                       $('#allSqArtefato').removeAttr('checked');
                   }
            });
        },

        initAjustaTitulosGrid: function() {
            $('.header:nth-child(1)').css('width', '10px');
            $('.header:nth-child(2)').css('width', '160px');
            $('.header:nth-child(3)').css('width', '100px');
            $('.header:nth-child(4)').css('width', '100px');
            $('.header:nth-child(5)').css('width', '100px');
            $('.header:nth-child(6)').css('width', '100px');
            $('.header:nth-child(7)').css('width', '80px');
            $('.header:nth-child(8)').css('width', '100px');
            $('.header:nth-child(9)').css('width', '100px');
        },

        initAjustaAcoesGrid: function(){},

        msgNenhumaMinutaNaCaixa: function() {

            caixa = $('#view').val();
            tdEmpty = $('td:contains(Nenhum registro encontrado.)');

            if(caixa == 1 || caixa == 0){
                tdEmpty.text('Não há minutas na sua caixa recebidas');
            }
            else if(caixa == 2){
                tdEmpty.text('Não há minutas na sua caixa enviadas');
            }
            else if(caixa == 4){
                tdEmpty.text('Não há minutas na sua caixa em acompanhamento');
            }
            else if(caixa == 3){
                tdEmpty.text('Não há minutas na sua caixa produzidas');
            }

        },

        init: function() {
            AjustaGrid.initSelectAll();
            AjustaGrid.initAjustaTitulosGrid();
            AjustaGrid.msgNenhumaMinutaNaCaixa();
            AjustaGrid.initAjustaAcoesGrid();
        }
}

AcoesMinuta = {

        alterarMinuta: function(sqArtefato, sqTipoDocumento, sqAssunto, view) {
            window.location = '/artefato/minuta-eletronica/edit/id/' + sqArtefato + '/sqTipoDocumento/' + sqTipoDocumento + '/sqAssunto/' + sqAssunto + '/view/' + view;
        },

        excluirMinuta: function(sqArtefato, view) {

            var callBack = function(){
                window.location = '/artefato/visualizar-caixa-minuta/excluir-minuta/sqArtefato/'+ sqArtefato + '/view/' + view;
            }

            Message.showConfirmation({
                'body': 'Tem certeza que deseja realizar a exclusão?',
                'yesCallback': callBack
            });

        },

        assinarMinuta: function(sqArtefato, viewFor, view) {
            window.location = '/artefato/visualizar-caixa-minuta/visualizar-minuta/sqArtefato/' + sqArtefato + '/viewFor/' + viewFor + '/view/' + view;
        },

        encaminharMinutaAnalise: function() {
            $('#form-minuta-acoes-multiplas').attr('action', '/artefato/visualizar-caixa-minuta/encaminhar-minuta-analise');
            $('#sqOcorrencia').val(8);
            $('#form-minuta-acoes-multiplas > #view').val($('input[id=view]:first').val());
            $('#form-minuta-acoes-multiplas > #sqPessoa').val($('#noPessoa_hidden').val());
            AcoesMinuta.submitFormAcoesMultiplas();
        },

        encaminharMinutaAssinatura: function(sqHistoricoArtefato, sqArtefato, view) {
            $('#form-minuta-acoes-multiplas').attr('action', '/artefato/visualizar-caixa-minuta/encaminhar-minuta-assinatura');
            $('#sqOcorrencia').val(7);
            $('#form-minuta-acoes-multiplas > #view').val($('input[id=view]:first').val());
            $('#form-minuta-acoes-multiplas > #sqPessoa').val($('#noPessoa_hidden').val());
            $('#form-minuta-acoes-multiplas > #sqUnidadeOrgCorp').val($('#sqUnidadeOrg_hidden').val());
            $('#sqArtefato').val($('#sqArtefatoAssinatura').val());
            
            AcoesMinuta.submitFormAcoesMultiplas();
        },

        acompanharMinuta: function(sqArtefato, view) {
            $('input[id=sqArtefato]').attr('checked', false);
            $('input[id=sqArtefato][value=' + sqArtefato +']').attr('checked', true);
            $('#form-minuta-acoes-multiplas').attr('action', '/artefato/visualizar-caixa-minuta/acompanhar-minuta');
            $('#sqOcorrencia').val(13);
            $('#form-minuta-acoes-multiplas > #view').val($('input[id=view]:first').val());
            AcoesMinuta.submitFormAcoesMultiplas();
        },

        desacompanharMinuta: function(sqArtefato, view) {
            var callBack = function(){
                $('input[id=sqArtefato]').attr('checked', false);
                $('input[id=sqArtefato][value=' + sqArtefato +']').attr('checked', true);
                $('#form-minuta-acoes-multiplas').attr('action', '/artefato/visualizar-caixa-minuta/desacompanhar-minuta');
                $('#sqOcorrencia').val(12);
                $('#form-minuta-acoes-multiplas > #view').val($('input[id=view]:first').val());
                AcoesMinuta.submitFormAcoesMultiplas();
            }

            Message.showConfirmation({
                'body': 'Tem certeza que deseja desacompanhar a minuta?',
                'yesCallback': callBack
            });
        },

        submitFormAcoesMultiplas: function() {
            $('#form-minuta-acoes-multiplas').submit();
        },
}

AcoesMultiplas = {

        initAcompanharMinutas: function() {
            $('#acaoMultAcompMin').click(function(){
                $('#form-minuta-acoes-multiplas').attr('action', '/artefato/visualizar-caixa-minuta/acompanhar-minuta');
                $('#sqOcorrencia').val(13);
                $('#form-minuta-acoes-multiplas > #view').val($('input[id=view]:first').val());
                AcoesMinuta.submitFormAcoesMultiplas();
            });
        },

        initEncaminharMinutaAnalise: function() {
            $('#acaoMultEncAnalise').click(function(){
                $('#modal').find('.modal-body').load('/artefato/visualizar-caixa-minuta/encaminhar-minuta-analise');
                $('#modal-title').text('Selecionar Usuário');
                $('#modal').modal();
                Modal.configFooterModal('footerEncaminharMinutaAnalise');
            });
        },

        initEncaminharMinutaAssinatura: function() {
            $('#acaoMultEncAssin').click(function(){
                $('#modal').find('.modal-body').load('/artefato/visualizar-caixa-minuta/encaminhar-minuta-assinatura');
                $('#modal-title').text('Selecionar Usuário');
                $('#modal').modal();
                Modal.configFooterModal('footerEncaminharMinutaAssinatura');
            });
        },

        initDesacompanharMinuta: function() {
            $('#acaoMultDesacompMuin').click(function(){
                var callBack = function(){
                    $('#form-minuta-acoes-multiplas').attr('action', '/artefato/visualizar-caixa-minuta/desacompanhar-minuta');
                    $('#sqOcorrencia').val(12);
                    $('#form-minuta-acoes-multiplas > #view').val($('input[id=view]:first').val());
                    AcoesMinuta.submitFormAcoesMultiplas();
                }

                Message.showConfirmation({
                    'body': 'Tem certeza que deseja desacompanhar a minuta?',
                    'yesCallback': callBack
                });

            });
        },

        init: function() {
            AcoesMultiplas.initAcompanharMinutas();
            AcoesMultiplas.initEncaminharMinutaAnalise();
            AcoesMultiplas.initEncaminharMinutaAssinatura();
            AcoesMultiplas.initDesacompanharMinuta();
        }
}

Modal = {

        encaminharMinutaAnalise: function(sqHistoricoArtefato, sqArtefato, view){
            $('input[id=sqArtefato]').attr('checked', false);
            $('#allSqArtefato').attr('checked', false);
            $('input[id=sqArtefato][value=' + sqArtefato +']').attr('checked', true);
            $('#modal').find('.modal-body').load('/artefato/visualizar-caixa-minuta/encaminhar-minuta-analise/sqHistoricoArtefato/' + sqHistoricoArtefato + '/sqArtefato/' + sqArtefato + '/view/' + view);
            $('#modal-title').text('Selecionar Usuário');
            $('#modal').modal();
            Modal.configFooterModal('footerEncaminharMinutaAnalise');
        },

        encaminharMinutaAssinatura: function(sqHistoricoArtefato, sqArtefato, view){
            $('input[id=sqArtefato]').attr('checked', false);
            $('#allSqArtefato').attr('checked', false);
            $('input[id=sqArtefato][value=' + sqArtefato +']').attr('checked', true);
            $('#modal').find('.modal-body').load('/artefato/visualizar-caixa-minuta/encaminhar-minuta-assinatura/sqHistoricoArtefato/' + sqHistoricoArtefato + '/sqArtefato/' + sqArtefato + '/view/' + view);
            $('#modal-title').text('Selecionar Usuário');
            $('#modal').modal();
            Modal.configFooterModal('footerEncaminharMinutaAssinatura');
        },

        devolverMinuta: function(id, idHistArt, idPessoa) {
            $('#modal').find('.modal-body').load('/artefato/visualizar-caixa-minuta/devolver-minuta/sqArtefato/' + id + '/sqHistoricoArtefato/' + idHistArt + '/sqPessoa/' + idPessoa);
            $('#modal-title').text('Devolver Minuta');
            $('#modal').modal();
            Modal.configFooterModal('footerDevolverMinuta');
        },

        configObrigatoriedadeDevolveMinuta: function() {
            $('#infoCampoObrig').addClass('hidden');
            $('#txJustificativa').keypress(function(){
                $('#infoCampoObrig').addClass('hidden');

                    $("#txJustificativa")
                                         .css('border', '1px solid #CCCCCC')
                                         .css('box-shadow', '0 1px 1px rgba(0, 0, 0, 0.075) inset')
                                         .css('color', '#555555');

                    $(".control-label[for=txJustificativa]").css('color', '#333333');
            });

            $('#closeModal').click(function(){
                $('.campos-obrigatorios').hide();
            });
            $('#btDevolver').click(function(){

                if($("#txJustificativa").val() == ''){

                    $('#infoCampoObrig').removeClass('hidden');

                    $("#txJustificativa")
                                         .css('border-color', '#B94A48')
                                         .css('box-shadow', '0 1px 1px rgba(0, 0, 0, 0.075) inset')
                                         .css('color', '#B94A48');

                    $(".control-label[for=txJustificativa], #infoCampoObrig").css('color', '#B94A48');

                    return false;
                }

                $('#form-devolver-minuta').submit();
                $('.campos-obrigatorios').hide();
            });
            $('#btFechar').click(function(){
                $('.campos-obrigatorios').hide();
            });
        },

        configFooterModal:  function(action){
            switch(action){
                case 'footerDevolverMinuta':
                        $('#footerEncaminharMinutaAnalise').attr('hidden', 'true');
                        $('#footerEncaminharMinutaAssinatura').attr('hidden', 'true');
                        $('#footerDevolverMinuta').removeAttr('hidden');
                    break;
                case 'footerEncaminharMinutaAnalise':
                        $('#footerDevolverMinuta').attr('hidden', 'true');
                        $('#footerEncaminharMinutaAssinatura').attr('hidden', 'true');
                        $('#footerEncaminharMinutaAnalise').removeAttr('hidden');
                    break;
                case 'footerEncaminharMinutaAssinatura':
                        $('#footerDevolverMinuta').attr('hidden', 'true');
                        $('#footerEncaminharMinutaAnalise').attr('hidden', 'true');
                        $('#footerEncaminharMinutaAssinatura').removeAttr('hidden');
                    break;
            }
        },

        init: function() {
            Modal.configObrigatoriedadeDevolveMinuta();
        }
}

$(document).ready(function() {
    if ($('#semUnidadeExercicio').val()){
        Message.showError('Não é possível o cadastro ou alteração das informações. Favor procurar a coordenação para o cadastro da sua unidade de exercício.');
    }
    $('a[data-dismiss="modal"]').click(function(){
        location.href = '/';
    });
    $(document).ajaxStop(function(){
        CaixaMinuta.init();
        AjustaGrid.init();
        AcoesMultiplas.init();
        Modal.init();
    });

    GridLoad.initGrid();
});