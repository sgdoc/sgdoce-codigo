<?php

/*
 * Copyright 2012 ICMBio
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos
 * da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão
 * 2 da Licença.
 *
 * Este programa é distribuído na esperança que possa ser útil, mas SEM NENHUMA GARANTIA; sem
 * uma garantia implícita de ADEQUAÇÃO a qualquer MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a
 * Licença Pública Geral GNU/GPL em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
 * junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço
 * www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF)
 * Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
 * */
/**
 * SISICMBio
 *
 * Classe Service Usuario
 *
 * @package      Principal
 * @subpackage   Services
 * @name         Perfil
 * @version      1.0.0
 * @since         2012-08-17
 */

namespace Auxiliar\Service;

class VwPessoa extends \Core_ServiceLayer_Service_CrudDto
{

    /**
     * Nome da entidade
     * @var string
     */
    protected $_entityName = 'app:VwPessoa';

    /**
     * Realiza consulta por cpf ou cnpj
     * @param array $criteria
     * @return string
     */
    public function searchCpfCnpj(array $criteria)
    {
        $result = '';

        if (isset($criteria['nuCpf'])) {
            $type   = 'CPF';
            $result = $this->getServiceLocator()->getService('VwPessoaFisica')
                ->findBy($criteria)
                ->getSqPessoaFisica()
                ->getSqPessoa();
        } else {
            $type   = 'CNPJ';
            $result = $this->getServiceLocator()->getService('VwPessoaJuridica')
                ->findOneBy($criteria)
                ->getSqPessoaJuridica()
                ->getSqPessoa();
        }

        $data['sqPessoa'] = $result;
        $data['total']    = count($result);
        $data['type']     = $type;

        return $data;
    }

//     public function listGrid(\Core_Dto_Search $dto)
//     {
//         $repository = $this->getEntityManager()->getRepository($this->_entityName);
//         $result = $repository->searchPageDto('listGrid', $dto);

//         return $result;
//     }

    /**
     * Realiza consulta por nome para autocomplete
     * @param \Core_Dto_Search  $dto
     */
//     public function searchPessoa(\Core_Dto_Search $dto)
//     {
//         return $this->_getRepository()->searchPessoa($dto);
//     }

    public function getPessoaDados(\Core_Dto_Search $dto)
    {
        return $this->_getRepository()->getPessoaDados($dto);
    }

    public function buscaPessoaPorDocumento(\Core_Dto_Search $dtoSearch)
    {
        //fazer uma pesquisa no sgdoce também;
        $dadosSgdoce      = $this->getServiceLocator()->getService('Pessoa')->findPessoaDestinatarioArtefato($dtoSearch);
        $objCorporativo = $this->_getRepository()->buscaPessoaPorDocumento($dtoSearch);

        if(!count($objCorporativo)){
            return array();
        }

        $endereco  = $this->_getRepository('app:VwEndereco')->findEndereco($objCorporativo->getSqPessoa());

        $criteria = array(
                     'sqPessoa' => $objCorporativo->getSqPessoa(),
                     'sqAtributoTipoDocumento' => \Core_Configuration::getSgdoceSqAtributoTipoDocNumeroPassaporte()
                  );

        $documento = $this->_getRepository('app:VwDocumento')->findBy($criteria);

        if(!$documento){
            $documento = $this->_newEntity('app:VwDocumento');
        }

        if(!$endereco){
            $endereco = $this->_newEntity('app:VwEndereco');
        }

        $cep = \Zend_Filter::filterStatic($endereco->getSqCep(), 'MaskNumber', array('cep'), array('Core_Filter'));

        $corporativo = array('sqPessoa' => $objCorporativo->getSqPessoa(),
                'noPessoa' => $objCorporativo->getNoPessoa(),
                'nuCpfCnpjPassaporte' => $this->getServiceLocator()->getService('Pessoa')
                                         ->getTipoPessoa($dtoSearch, $objCorporativo),
                'txEndereco' => $endereco->getTxEndereco(),
                'coCep'  => $cep,
                'sqEstadoDestinatarioId' => $endereco->getSqMunicipio()->getSqEstado()->getSqEstado(),
                'sqEstadoDestinatario'   => $endereco->getSqMunicipio()->getSqEstado()->getNoEstado(),
                'sqMunicipioDestinatarioHidden' => $endereco->getSqMunicipio()->getSqMunicipio(),
                'sqMunicipioDestinatario' => $endereco->getSqMunicipio()->getNoMunicipio()
        );

        foreach ($dadosSgdoce as $key => $result) {
            if($result['coCep'] == $endereco->getSqCep() &&
               $result['sqEstado'] == $endereco->getSqMunicipio()->getSqEstado()->getSqEstado() &&
               $result['sqMunicipio'] == $endereco->getSqMunicipio()->getSqMunicipio() &&
               $result['txEndereco'] == $endereco->getTxEndereco())
            {
                unset($corporativo);
                $corporativo = array();
            }
        }

        $base['nuPassaporte']    = $documento->getTxValor();
        $base['sqPessoa']        = $objCorporativo->getSqPessoa();
        $base['noPessoa']        = $objCorporativo->getNoPessoa();
        $base['corporativo']     = $corporativo;
        $base['sgdoce']          = $dadosSgdoce;
        $base['nuCpf']           = $this->getServiceLocator()->getService('Pessoa')->getTipoPessoa($dtoSearch,
                                                                                                   $objCorporativo);

        return $base;
    }

    /**
     * Este metodo realiza a busca pelo documento de uma determinada pessoa, seja fisica ou outros.
     * @param object $dto
     * @return type
     */
    public function getPessoaDocumento($dto)
    {
        $result = $this->_getRepository()->getPessoaDadosRodape($dto);

        if (count($result)) {
            $result = $result[0];

            switch ($result->getSqTipoPessoa()) {
                case 1:
                    $documento = $result->getSqPessoaFisica()->getNuCpf();
                    break;

                case 2:
                    $documento = $result->getSqPessoaFisica()->getNuCpf();
                    break;

                default:
                    break;
            }
        }

        return $documento;
    }

    /**
     *
     * @param type $repository
     * @param type $method
     * @param array $data
     * @return type
     */
    public function saveFormWebService($repository = 'app:VwPessoaFisica', $method, array $data, $filters = array())
    {
        $data = $this->parseData($data, $filters);
        return $this->saveLibCorp($repository, $method, $data);
    }

    /**
     * Salva dados via webservice
     * @param type $repository
     * @param type $method
     * @param array $data
     * @return boolean
     */
    public function saveLibCorp($repository = 'app:VwPessoaFisica', $method, array $data)
    {
        if ($repository == 'app:VwTelefone' && $method == 'libCorpDeleteTelefone'){
            $dadosLogger = $this->_getRepository('app:VwTelefone')->find($data['sqTelefone']);
            $arrTel['sqTelefone'] = $dadosLogger->getSqTelefone();
            $arrTel['sqTipoTelefone'] = $dadosLogger->getSqTipoTelefone()->getSqTipoTelefone();
            $arrTel['sqPessoa'] = $dadosLogger->getSqPessoa()->getSqPessoa();
            $arrTel['nuDdd'] = $dadosLogger->getNuDdd();
            $arrTel['nuTelefone'] = $dadosLogger->getNuTelefone();
            $arrTelefone['telefone'] = $arrTel;
        }
        if ($repository == 'app:VwEmail' && $method == 'libCorpDeleteEmail'){
            $dadosLogger = $this->_getRepository()->find($data['sqEmail']);
            $dadosLogger = $this->_getRepository('app:VwEmail')->find($data['sqEmail']);
            $arrMail['sqEmail'] = $dadosLogger->getSqEmail();
            $arrMail['sqTipoEmail'] = $dadosLogger->getSqTipoEmail()->getSqTipoEmail();
            $arrMail['sqPessoa'] = $dadosLogger->getSqPessoa()->getSqPessoa();
            $arrMail['txEmail'] = $dadosLogger->getTxEmail();
            $arrEmail['email'] = $arrMail;
        }

        /**
         * Informações obrigatórias para o log de auditoria do webservice.
         */
        $userCredential = \Core_Integration_Sica_User::getUserCredential();

        $entityManager = $this->getEntityManager('ws')->getRepository($repository);
        $result = $entityManager->{$method}($data, $userCredential);
        $resultXml = \Core_Integration_Abstract_Soap::xmlToArray($result);

        if (isset($resultXml['status']) && $resultXml['status'] == 'success') {
            if ($repository == 'app:VwPessoaFisica') {
                return $resultXml['response']['pessoa_fisica']['sqPessoa']['sqPessoa'];
            }
            if ($repository == 'app:VwPessoaJuridica') {
                return $resultXml['response']['pessoa_juridica']['sqPessoa']['sqPessoa'];
            }
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Organiza os dados a serem salvos pelo webservice
     * @param array $params
     * @return array
     */
    public function parseData(array $params, $filters = array())
    {
        $data = array();

        foreach ($params as $input) {
            if (is_array($filters) && array_key_exists($input['name'], $filters)) {
                $input['value'] = \Zend_Filter::filterStatic($input['value'], $filters[$input['name']]);
            }

            $data[$input['name']] = $input['value'];
        }

        return $data;
    }

    /**
     * Recupera dados para gerar pdf
     * @param \Core_Dto_Search $dto
     */
    public function getPdf(\Core_Dto_Search $dto)
    {
        $result = $this->listGrid($dto);
        return $result['total'] ? $this->listGrid($dto)->getQuery()->getArrayResult() : array();
    }

    public function findPessoa($dtoSearch)
    {
        return $this->_getRepository('app:VwPessoa')->find($dtoSearch->getSqPessoa());
    }

    public function autorizarJustificativa($dto)
    {
        if(!$this->_getRepository('app:VwPessoaFisica')->findBy(array('nuCpf' => $dto->getNuCpfResponsavel()))) {
            throw new \Core_Exception_ServiceLayer_Verification('MN126');
        }

        $this->isResponsavelSetor($dto);
        $this->verificarAutenticacaoUsuarioInterno($dto->getNuCpfResponsavel(), $dto->getTxSenhaResponsavel());

        return true;
    }

    /**
     * Verifica se o CPF informado é de um responsável de setor
     *
     * @param \Core_Dto_Abstract $dto
     * @return boolean
     */
    protected function isResponsavelSetor(\Core_Dto_Abstract $dto)
    {
        $result = $this->getResponsavelSetor($dto)
            ? true
            : false;

        if(!$result) {
            throw new \Core_Exception_ServiceLayer_Verification('MN124');
        }
    }

    protected function getResponsavelSetor($dto)
    {
        return $this->_getRepository('app:VwChefia')->isResponsavelSetor($dto);
    }

    protected function verificarAutenticacaoUsuarioInterno($usuario, $senha)
    {
        $entityManager    = $this->getEntityManager('ws')->getRepository('app:VwSicae');
        $resultWebService = $entityManager->verificaAutenticacaoUsuarioInterno(
            $usuario,
            $senha
        );

        if(!$resultWebService) {
            throw new \Core_Exception_ServiceLayer_Verification('MN126');
        }
    }

    public function returnCpfCnpjPassaporte($dtoPessoaSearch)
    {
        $vwPessoa = $this->getPessoaDados($dtoPessoaSearch);
        $nuCpfCnjPassaporte = '';

        switch ($vwPessoa->getSqTipoPessoa()) {
            case \Core_Configuration::getSgdoceTipoPessoaPessoaFisica():
                if($vwPessoa->getSqPessoaFisica()->getSqNacionalidade()->getSqPais() == \Core_Configuration::getSgdocePaisBrasil()
                || $vwPessoa->getSqPessoaFisica()->getSqNacionalidade()->getSqPais() == NULL){
                    $nuCpfCnjPassaporte =  $vwPessoa->getSqPessoaFisica()->getNuCpf();
                }else{
                            if( $vwPessoa->getSqPessoaDocumento()
                                && $vwPessoa->getSqPessoaDocumento()->getTxValor() != NULL ) {
                    $nuCpfCnjPassaporte = $vwPessoa->getSqPessoaDocumento()->getTxValor();
                            }
                        }
                break;
            case \Core_Configuration::getSgdoceTipoPessoaPessoaJuridica():
                $nuCpfCnjPassaporte = $vwPessoa->getSqPessoaJuridica()->getNuCnpj();
                break;
        }
        return $nuCpfCnjPassaporte;
    }

    public function searchPessoaUnidade( \Core_Dto_Abstract $objDtoSearch, $limit = 10)
    {
        $arrPessoas = $this->_getRepository()->searchPessoaUnidade($objDtoSearch, $limit);

        $arrOptions = array();
        foreach( $arrPessoas as $arrPessoa ) {
            $arrOptions[$arrPessoa['sqPessoa']] = $arrPessoa['noPessoa'];
        }

        return $arrOptions;
    }

    /**
     * Obtem dados do webservice.
     *
     * @param string $doc
     * @param string $typeDoc
     * @return array
     */
    public function getDataInfoconvBy( $doc, $typeDoc )
    {
    try {
            #Informações obrigatórias para o log de auditoria do webservice.
            $userCredential = \Core_Integration_Sica_User::getUserCredential();

            $method         = "libCorpInfoconvBy" . ucfirst($typeDoc);
            $entityManager  = $this->getEntityManager('ws')->getRepository('app:IntegracaoPessoaInfoconv');
            $result         = $entityManager->{$method}(array($typeDoc => $doc), $userCredential);

            $xmlResult = json_decode($result);

            if ('failure' == $result['status']) {
                throw new \Exception($result['response']);
            }

            return $result;
        } catch (\SoapFault $ex) {
            return json_encode( array('response'=>$ex->getMessage(), 'success'=>false, 'total'=>0, 'code' =>$ex->getCode()) );
        } catch (\Exception $ex) {
            return json_encode( array('response'=>$ex->getMessage(), 'success'=>false, 'total'=>0, 'code' =>$ex->getCode()) );
        }
    }

    /**
     * Método responsável por retornar os dados para o autocomplete de pessoa
     *
     * @param \Core_Dto_Search $dtoSearch
     */
    public function autocomplete(\Core_Dto_Search $dtoSearch, $limit = 10)
    {
        return $this->_getRepository()->autocomplete($dtoSearch, $limit);
    }
}