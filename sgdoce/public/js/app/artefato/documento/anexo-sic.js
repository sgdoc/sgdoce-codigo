var AnexoSic = {
    _uploader:null,

    init: function(){
        AnexoSic.ajustarCamposCadastroSIC().uploaderHandler().grid();
        plupload.addI18n({"Error: Invalid file extension: ": UI_MSG.MN129 + ' '});
    },

    ajustarCamposCadastroSIC: function() {
        //desabilita o radioButton de origem interna e destino Externo
        //SIC SÓ PODE SER DE ORIGEM EXTERNO E DESTINO INTERNO
        $('#chekProcedenciaInterno,#chekProcedenciaExterno,#chekDestinoExterno').prop('disabled','disabled');

        return AnexoSic;
    },

    uploaderHandler:function(){
        $('#uploader').empty();

        alert = Message.showError;

        AnexoSic._uploader = new Uploader({
            selector: '#uploader',
            mode: 'list',
            url: '/auxiliar/upload/tmp',
            filters:[{
                title: "Arquivo PDF",
                extensions: "pdf"
            }],
            max_file_size: '110mb'
            // ,debugNoise: true
        });

        AnexoSic._uploader.addEventListener({
            Error: function(up, error){
                var message = error.message || 'Ocorreu um erro';
                if (!$('.modal:visible').length && message !== 'Arquivo Inválido.') {
                    Message.showError(message, function(){
                        up.destroy();
                        AnexoSic.uploaderHandler();//recria o componente
                    });
                }
            },
            FilesAdded: function(up, files){
                setTimeout(function(){
                    $('.plupload_filelist li.plupload_delete a').attr('title','Excluir');
                },200);
            },
            FilesRemoved: function(up, files){
                setTimeout(function(){
                    $('.plupload_filelist li.plupload_delete a').attr('title','Excluir');
                },200);
            },
            BeforeUpload: function (up, file) {
                if (!$('#jquery-msg-overlay').length) {
                    Message.wait(UI_MSG.MN014);
                }
            },
//            FileUploaded: function (uploader, file, info) {},
            UploadComplete: function (up, files) {
                Message.waitClose();
            }
        });
        AnexoSic._uploader.init();

        return AnexoSic;
    },

    reloadGrid: function() {
        $('#tableAnexoSic').dataTable().fnDraw(false);
        return AnexoSic;
    },

    grid: function(){
        Grid.load('/artefato/documento/list-anexo-sic/sqArtefato/'+$('#sqArtefato').val(), $('#tableAnexoSic'));
        return AnexoSic;
    },

    delete:function(sqAnexoSic){
        var callBack = function() {
            $.get('artefato/documento/delete-anexo-sic/id/' + sqAnexoSic , function(data){
                console.log(data);

                var tipo = 'Erro';
                if (!data.error) {
                    tipo = 'Sucesso';
                    AnexoSic.reloadGrid();
                }
                Message.show(tipo, data.msg);
            });
        };
        Message.showConfirmation({
            'body': UI_MSG.MN018,
            'yesCallback': callBack
        });
    }
};

$(AnexoSic.init);