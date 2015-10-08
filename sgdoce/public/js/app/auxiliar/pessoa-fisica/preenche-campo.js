var CampoPessoa = {
    preencheCampo : function(){
        campoPessoa = localStorage.getItem('campoPessoa');
        valorPessoa = localStorage.getItem('valorPessoa');
        form = localStorage.getItem('form');
        noPessoa = localStorage.getItem('noPessoa');
        campoCpf = localStorage.getItem('campoCpf');
        valorCpf = localStorage.getItem('valorCpf');
        campoCnpj = localStorage.getItem('campoCnpj');
        valorCnpj = localStorage.getItem('valorCnpj');

        if(form){
            $("#"+form+" #"+campoPessoa+"_hidden").val(valorPessoa);
            $("#"+form+" #"+campoPessoa).val(noPessoa);
            $("#"+form+" #"+campoCpf).val(valorCpf);
            $("#"+form+" #"+campoCnpj).val(valorCnpj);
            this.initCampos(form,campoCpf,campoPessoa,campoCnpj);
            setTimeout(function(){
                localStorage.clear();
            },'2000');
        }
    },
    initCampos :function(form,campoCpf,campoPessoa,campoCnpj){
        if(form == 'form-destinatario-modal'){
            if($('#sqPessoaDestinatarioPf').val() || $('#nuCPF').val())
            {
                $('#alterarPF').attr('href','/auxiliar/pessoa-fisica/edit/id/' + $('#sqPessoaDestinatarioPf_hidden').val()
                    +'/form/'+form+'/campoPessoa/'+campoPessoa+'/campoCpf/'+campoCpf);
                $('#alterarPF,#visualizarPFDest').parent().removeClass('disabled');

            }else{
                $('#alterarPF,#visualizarPFDest').parent().addClass('disabled');
            }
            if($('#sqPessoaDestinatario').val() || $('#nuCPF').val())
            {
                $('#alterarPJ').attr('href','/auxiliar/pessoa-juridica/edit/id/' + $('#sqPessoaDestinatario_hidden').val()
                    +'/form/'+form+'/campoPessoa/'+campoPessoa+'/campoCpf/'+campoCnpj);
                $('#alterarPJ,#visualizarPJDest').parent().removeClass('disabled');
            }else{
                $('#alterarPJ,#visualizarPJDest').parent().addClass('disabled');

            }
        }

        if(form == 'form-interessado-modal'){
            if($('#sqPessoaInteressadoPf').val() || $('#nuCPF').val())
            {
                $('#alterarPFInter').attr('href','/auxiliar/pessoa-fisica/edit/id/' + $('#sqPessoaInteressadoPf_hidden').val());
                $('#alterarPFInter,#visualizarPFInter').parent().removeClass('disabled');

            }else{
                $('#alterarPFInter,#visualizarPFInter').parent().addClass('disabled');
            }
            if($('#sqPessoaInteressado').val() || $('#nuCPF').val())
            {
                $('#alterarPJInter').attr('href','/auxiliar/pessoa-juridica/edit/id/' + $('#sqPessoaInteressado_hidden').val());
                $('#alterarPJInter,#visualizarPJInter').parent().removeClass('disabled');
            }else{
                $('#alterarPJInter,#visualizarPJInter').parent().addClass('disabled');

            }
        }
    }
};

$(window).ready(function(){
    $(window).focus(function(){
       CampoPessoa.preencheCampo();
    });
});