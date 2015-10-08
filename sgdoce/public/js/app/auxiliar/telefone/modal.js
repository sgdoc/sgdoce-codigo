TelefoneModal = {
    
    concluir: function(){
        $('#nuDdd, #nuTelefone').setMask('numeric');
        $('.btnAdicionarTelefone').off('click').on('click', function() {  
            if($('#form-telefone-modal').valid()) {
                $('#nuTelefone').val(
                    $('#nuTelefone').val().replace('-', '')
                );
                
                if($('#form-telefone-modal #sqTelefone').val()) {
                    PessoaForm.saveFormWebService(
                        'app:VwTelefone',
                        'libCorpUpdateTelefone',
                        $('#form-telefone-modal'),
                        $('#form-telefone')
                    );
                } else {
                    PessoaForm.saveFormWebService(
                        'app:VwTelefone',
                        'libCorpSaveTelefone',
                        $('#form-telefone-modal'),
                        $('#form-telefone')
                    );
                }
            } else {
                $('.alert-error').addClass('hide');
                $('.alert-error', '#modalContatoTelefone').removeClass('hide');
                
                return false;
            }
        });
    },
    
    init: function(){
        TelefoneModal.concluir();
    },
    
    initCampo : function() {
        var element = $('#nuTelefone');
        
        $.mask.options.autoTab = false;
        
        var maskPhone = function() {
            element.setMask({
                mask : '9999-9999',
                onOverflow : function(key, value) {
                    maskPhoneSp.call(undefined, {
                        key : key, 
                        value : value
                    });
                }
            }).addClass('phone').removeClass('phone-sp');
        };
        
        var maskPhoneSp = function(obj) {
            element.unsetMask().setMask({
                mask : '99999-9999',
                onValid : function(key, value) {
                    if(element.val().length == '10' && key === 'backspace') {
                        var lastValue = element.val().replace(/[^0-9]*/ig, '');
                        
                        element.unsetMask();
                        setTimeout(function() {
                            element.val(lastValue);
                            maskPhone.call();
                        }, 50);
                    }
                }
            }).addClass('phone-sp').removeClass('phone');
            
            element.val(element.val() + obj.key);
        };
        
        $('#nuTelefone').val().length > 8
            ? maskPhoneSp.call()
            : maskPhone.call();
    },
    
    addMethod : function() {
        jQuery.validator.addMethod("ddd", function(value, element) {
            if(value.length < 3){
                return false;
            }
            
            return true;

        }, "DDD inválido.");
        
        jQuery.validator.addMethod("telefone", function(value, element) {
            if(value.length < 9){
                return false;
            }
            
            return true;

        }, "Telefone inválido.");
    }
};

(function() {
    TelefoneModal.init();
    TelefoneModal.addMethod();
    TelefoneModal.initCampo();
})();