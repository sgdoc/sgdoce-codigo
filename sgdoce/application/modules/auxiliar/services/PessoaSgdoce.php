<?php
/**
 * Copyright 2012 do ICMBio
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro
 * dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre
 * (FSF); na versão 2 da Licença.
 * Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA;
 * sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR.
 * Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
 * junto com este programa, se não, acesse o Portal do Software Público Brasileiro no
 * endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
 */

namespace Auxiliar\Service;

use Doctrine\ORM\Query\ParameterTypeInferer;

/**
 * Classe para Service de PessoaSgdoce
 *
 * @package  Auxiliar
 * @category Service
 * @name     PessoaSgdoce
 * @version  1.0.0
 */
class PessoaSgdoce extends \Core_ServiceLayer_Service_CrudDto
{
    /**
     * @var string
     */
    protected $_entityName = 'app:PessoaSgdoce';

    /**
     * Recupera as informacoes do profissinal by sqPessoa
     * @param type $dtoSearch
     * @return null
     */
    public function recuperarInformacaoProfissional($dtoSearch)
    {
        $entityProfissional = $this->_getRepository('app:VwProfissional')->recuperaDadosProfissinal($dtoSearch);

        if(count($entityProfissional)){
            $return = $entityProfissional[0]->getSqPessoaCorporativo()->getSqProfissional();
            return $return[0]->getSqUnidadeExercicio()->getSqUnidadeOrg();
        }
        return NULL;
    }

    public function searchCargoCorporativo($dtoSearch)
    {
        return $this->_getRepository('app:VwProfissional')->searchCargoCorporativo($dtoSearch);
    }

    public function searchNomeCargo($dtoSearch)
    {
        return $this->_getRepository('app:VwProfissional')->searchNomeCargo($dtoSearch);
    }

    public function searchPessoaProfissinal($dtoSearch)
    {
        return $this->_getRepository('app:VwProfissional')->searchPessoaProfissinal($dtoSearch);
    }

    public function searchPessoaPorSetorOuUnidade($dtoSearch)
    {
        return $this->_getRepository('app:VwProfissional')->searchPessoaPorSetorOuUnidade($dtoSearch);
    }

    public function findPessoa($dtoSearch)
    {
        return $this->_getRepository('app:PessoaSgdoce')->find($dtoSearch->getSqPessoaSgdoce());
    }

    public function findPessoaBySqCorporativo($dtoSearch)
    {
        return $this->_getRepository('app:PessoaSgdoce')->findOneBy(array(
            'sqPessoaCorporativo' => $dtoSearch->getSqPessoaCorporativo()
        ));
    }

    public function searchDadosPessoa($dtoSearch)
    {
        return $this->_getRepository('app:PessoaSgdoce')->find($dtoSearch->getSqPessoaSgdoce());
    }

    /**
     * Obtém lista de destinatários
     * @param \Core_Dto_Search $search
     * @return json
     */
    public function listDestinatario(\Core_Dto_Search $search)
    {
        $repository = $this->getEntityManager()->getRepository($this->_entityNameSgdoce);
        $result     = $repository->searchPageDto('listDestinatario', $search);
        return $result;
    }

    public function findPessoaDestinatarioArtefato($dtoSearch)
    {
        return $this->_getRepository($this->_entityNameSgdoce)->findPessoaDestinatarioArtefato($dtoSearch);
    }

    /**
     * Metódo que recupera o Dto do Rodape
     */
    public function getDtoRodape($data)
    {
        unset($data['sqTipoAssinante']);
        unset($data['noProfissao']);
        $data['sqPessoaCorporativo']  = $data['sqPessoaRodape'];
        $data['sqTipoPessoa']         = \Core_Configuration::getSgdoceTipoPessoaPessoaFisica();
        $data['sqPessoaFuncao']       = \Core_Configuration::getSgdocePessoaFuncaoDadosRodape();
        $data['noPessoa']             = $data['sqPessoaRodape_autocomplete'];
        $data['txEndereco']           = $data['txEnderecoRodape'];
        $data['coCep']                = str_replace('.','',str_replace('-', '', $data['coCepRodape']));
        $data['txEnderecoEletronico'] = $data['txEmailRodape'];
        $data['nuTelefone']           = trim(str_replace('-','',$data['txTelefoneRodape']));
        $data['sqPessoaSgdoce']       = $data['sqTbPessoa'];

        $dtoPessoaSgdoce['PessoaSgdoce'] = \Core_Dto::factoryFromData($data,
                'entity', array('entity'=> 'Sgdoce\Model\Entity\PessoaSgdoce',
                        'mapping' => array(
                                'sqTipoPessoa'         => 'Sgdoce\Model\Entity\VwTipoPessoa'
                                ,'sqPessoaCorporativo'  => array('sqPessoa' => 'Sgdoce\Model\Entity\VwPessoa'))));

        //pessoaArtefato
        $dtoPessoaSgdoce['PessoaArtefato']  = \Core_Dto::factoryFromData($data,
                'entity', array('entity'=> 'Sgdoce\Model\Entity\PessoaArtefato',
                        'mapping' => array(
                                'sqArtefato'           => 'Sgdoce\Model\Entity\Artefato'
                                ,'sqPessoaSgdoce'       => 'Sgdoce\Model\Entity\PessoaSgdoce'
                                ,'sqEnderecoSgdoc'      => 'Sgdoce\Model\Entity\EnderecoSgdoc'
                                ,'sqPessoaFuncao'       => 'Sgdoce\Model\Entity\PessoaFuncao'
                                ,'sqTratamentoVocativo' => 'Sgdoce\Model\Entity\TratamentoVocativo')));


        return $dtoPessoaSgdoce;
    }

    public function getPessoaInternoDados(\Core_Dto_Search $dtoSearch)
    {
        $result = $this->_getRepository('app:VwProfissional')->validaDadosInterno($dtoSearch);

        if($result) {
            $result[0]['nuCpf'] = $dtoSearch->getNuCpfCnpjPassaporte();
            $result[0]['nuPassaporte'] = $dtoSearch->getNuCpfCnpjPassaporte();

            return $result[0];
        }

        return $result;
    }

    public function getDadosPessoa($dtoSearch = NULL)
    {
        return $this->_getRepository('app:VwPessoa')->getDadosPessoa($dtoSearch);
    }

    /**
     * Método que retorna os dados da pessoa
     * @return array
     */
    public function getPessoaDados($dtoSearch = NULL)
    {
        $dadosCorporativo = $this->_getRepository($this->_entityNameCorp)->getPessoaDados($dtoSearch);

        //fazer uma pesquisa no sgdoce também;
        $dadosSgdoce      = $this->findPessoaDestinatarioArtefato($dtoSearch);

        if(!$dadosCorporativo){
            return array();
        }

        if(!$dadosSgdoce){
            $criteria     = array('sqPessoaCorporativo' => $dadosCorporativo->getSqPessoa());
            $pessoaSgdoce =  $this->_getRepository($this->_entityNameSgdoce)->findOneBy($criteria);
            if($pessoaSgdoce){
                $dadosSgdoce  = $this->getServiceLocator()->getService('EnderecoSgdoce')->findByArray($pessoaSgdoce);
            }
        }

        $criteria = array(
            'sqPessoa' => $dadosCorporativo->getSqPessoa()
        );
        $endereco  = $this->getServiceLocator()->getService('VwEndereco')->findBy($criteria);

        $criteria = array(
            'sqPessoa' => $dadosCorporativo->getSqPessoa(),
            'sqAtributoTipoDocumento' => \Core_Configuration::getSgdoceSqAtributoTipoDocNumeroPassaporte()
        );

        $documento = $this->_getRepository('app:VwDocumento')->findBy($criteria);

        if(!$documento){
            $documento = $this->_newEntity('app:VwDocumento');
        }else{
            $documento = $documento[0];
        }

        if(!$endereco){
            $endereco = $this->_newEntity('app:VwEndereco');
        }

        $corporativo = $this->validaCorporativo($endereco,$dadosCorporativo,$dtoSearch);
        $corporativo = $this->validaSgdoce($dadosSgdoce, $endereco,$corporativo);

        if ($dtoSearch->getSqTipoPessoa() == \Core_Configuration::getSgdoceTipoPessoaPessoaFisica()){
            $base['nuCpf']           = $this->getTipoPessoa($dtoSearch, $dadosCorporativo);
        } else {
            $base['nuCnpj']           = $this->getTipoPessoa($dtoSearch, $dadosCorporativo);
        }

        $base['nuPassaporte']    = $documento->getTxValor();
        $base['sqPessoa']        = $dadosCorporativo->getSqPessoa();
        $base['noPessoa']        = $dadosCorporativo->getNoPessoa();
        $base['corporativo']     = $corporativo;
        $base['sgdoce']          = $dadosSgdoce;

        return $base;
    }

    public function validaCorporativo($endereco,$dadosCorporativo,$dtoSearch)
    {
        $corporativo = array();
        foreach ($endereco as $key => $value) {
            $cep = \Zend_Filter::filterStatic($value->getSqCep(), 'MaskNumber', array('cep'), array('Core_Filter'));
            $corporativo[] = array('sqPessoa' => $dadosCorporativo->getSqPessoa(),
                    'noPessoa' => $dadosCorporativo->getNoPessoa(),
                    'nuCpfCnpjPassaporte' => $this->getTipoPessoa($dtoSearch, $dadosCorporativo),
                    'sqEndereco' => $value->getSqEndereco(),
                    'txEndereco' => $value->getTxEndereco() . ' ' . $value->getNuEndereco(),
                    'coCep'  => $cep,
                    'sqEstadoDestinatarioId' => $value->getSqMunicipio()->getSqEstado()->getSqEstado(),
                    'sqEstadoDestinatario'   => $value->getSqMunicipio()->getSqEstado()->getNoEstado(),
                    'sqMunicipioDestinatarioHidden' => $value->getSqMunicipio()->getSqMunicipio(),
                    'sqMunicipioDestinatario' => $value->getSqMunicipio()->getNoMunicipio(),
                    'sqTipoEndereco' => $value->getSqTipoEndereco()->getSqTipoEndereco(),
                    'noTipoEndereco' => $value->getSqTipoEndereco()->getNoTipoEndereco()
            );
        }

        if(!count($corporativo)){
            $corporativo[] = array('sqPessoa' => '',
                    'noPessoa' => '',
                    'nuCpfCnpjPassaporte' => '',
                    'sqEndereco' =>'',
                    'txEndereco' => '',
                    'coCep'  => '',
                    'sqEstadoDestinatarioId' => '',
                    'sqEstadoDestinatario'   => '',
                    'sqMunicipioDestinatarioHidden' => '',
                    'sqMunicipioDestinatario' => '',
                    'sqTipoEndereco' => '',
                    'noTipoEndereco' => ''
            );
        }
        return $corporativo;
    }

    public function validaSgdoce($dadosSgdoce, $endereco,$corporativo)
    {
        foreach ($dadosSgdoce as $keySgdoce => $result) {
            foreach ($endereco as $key => $value) {
                $cepSgdoce = str_replace('.','',str_replace('-', '',$result['coCep']));
                $cepCorpo = str_replace('.','',str_replace('-', '',$value->getSqCep()));
                if ( $cepSgdoce == $cepCorpo &&
                        $result['sqEstado'] == $value->getSqMunicipio()->getSqEstado()->getSqEstado() &&
                        $result['sqMunicipio'] == $value->getSqMunicipio()->getSqMunicipio() &&
                        $result['txEndereco'] == $value->getTxEndereco() &&
                        $result['sqTipoEndereco'] == $value->getSqTipoEndereco()->getSqTipoEndereco())
                {
                    unset($corporativo[$key]);
                }
            }
        }
        return $corporativo;
    }

    /**
     * Obtém dados do rodapé da minuta
     * @param  $dtoSearch
     * @return array
     */
    public function getPessoaDadosRodape($dtoSearch = NULL)
    {

        $out = array();
        $result = $this->_getRepository($this->_entityNameCorp)->getPessoaDadosRodape($dtoSearch);

        foreach($result as $value) {
            if ($value->getSqPessoa()){
                $endereco = $this->_getRepository('app:VwEndereco')->findEndereco($value);
                $telefone         = $this->getServiceLocator()->getService('VwTelefone')->getDadosTelefone($dtoSearch);
                $email            = $this->getServiceLocator()->getService('VwEmail')->getDadosEmail($dtoSearch);

                $nuTelefone = '';
                $txEndereco = '';
                $coCep      = '';

                if($endereco) {
                    $txEndereco = $endereco->getTxEndereco();
                    $coCep      = substr($endereco->getSqCep(), 0 , 5) . '-' . substr($endereco->getSqCep(), 5 , 3);
                }

                $out[] = array(
                    'sqPessoa'   => $value->getSqPessoa(),
                    'noPessoa'   => $value->getNoPessoa(),
                    'txTelefone' => $telefone->getNuTelefone(),
                    'txEndereco' => $txEndereco,
                    'coCep'      => $coCep,
                    'nuDdd'      => $telefone->getNuDdd(),
                    'txEmail'    => $email->getTxEmail()
                );

                break;
            } else {
                return NULL;
            }
        }

        return $out;
    }

    public function getPessoaCorporativo($dtoSearch,$params)
    {
        $repository = $this->_getRepository($this->_entityNameCorp);
        $atributoTipoDocumento = $repository->getDocumento();
        $result = $repository->getPessoaDados($dtoSearch);

        if(is_object($result)) {
            $params['sqPessoaCorporativo'] = $result->getSqPessoa();
            $params['noPessoa'] = $result->getNoPessoa();
        }

        switch ($dtoSearch->getSqTipoPessoa()) {
            case '1':
                if($result->getSqPessoaFisica()->getSqNacionalidade()->getSqPais() == 1 ||
                $result->getSqPessoaFisica()->getSqNacionalidade()->getSqPais() == NULL){
                    $params['nuCpfCnpjPassaporte'] = $result->getSqPessoaFisica()->getNuCpf();
                    $params['tipoDoc']   = 'Cpf';
                }else{
                    $repositoryDoc = $this->_getRepository('app:VwDocumento');
                    $nuDocumento  = $repositoryDoc->findBy(array('sqPessoa' =>
                            $result->getSqPessoa(),
                            'sqAtributoTipoDocumento' => $atributoTipoDocumento['sqAtributoTipoDocumento']));
                    if(count($nuDocumento)){
                        $params['nuCpfCnpjPassaporte'] = $nuDocumento[0]->getTxValor();
                        $params['tipoDoc']   = 'Passaporte';
                    }
                }
                break;
            case '2':
                $params['nuCpfCnpjPassaporte'] = $result->getSqPessoaJuridica()->getNuCnpj();
                $params['tipoDoc']   = 'Cnpj';
                break;
            default:
                ;
                break;
        }
        return $params;
    }
}
