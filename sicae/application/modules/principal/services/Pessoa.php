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

namespace Principal\Service;

use Doctrine\Common\Util\Debug;

class Pessoa extends \Sica_Service_Crud
{

    CONSt SQ_TIPO_PESSOA_FISICA = 1;
    CONSt SQ_TIPO_PESSOA_JURIDICA = 2;
    CONSt SEM_CLASSIFICACAO = 3;

    /**
     * Nome da entidade
     * @var string
     */
    protected $_entityName = 'app:Pessoa';

    /**
     * Realiza consulta por cpf ou cnpj
     * @param array $criteria
     * @return string
     */
    public function searchCpfCnpj(array $criteria)
    {
        if (isset($criteria['nuCpf'])) {
            $type = 'CPF';
            $result = $this->getServiceLocator()->getService('PessoaFisica')->findOneBy($criteria);
        } else {
            $type = 'CNPJ';
            $result = $this->getServiceLocator()->getService('PessoaJuridica')->findOneBy($criteria);
        }

        $data['sqPessoa'] = $result ? $result->getSqPessoa()->getSqPessoa() : '';
        $data['noPessoa'] = $result ? $result->getSqPessoa()->getNoPessoa() : '';

        $data['total'] = count($result);
        $data['type'] = $type;

        return $data;
    }

    /**
     * Realiza consulta por nome para autocomplete
     * @param \Core_Dto_Search  $dto
     */
    public function searchPessoa(\Core_Dto_Search $dto)
    {
        return $this->_getRepository()->searchPessoa($dto);
    }

    /**
     * Realiza consulta por cpf e nome
     * @param type $dto
     */
    public function searchCpf($dto)
    {
        return $this->_getRepository()->searchCpf($dto);
    }

    /**
     *
     * @param type $repository
     * @param type $method
     * @param array $data
     * @return type
     */
    public function saveFormWebService($repository = 'app:PessoaFisica', $method, array $data, $filters = array())
    {
        $data = $this->parseData($data, $filters);
        return $this->saveLibCorp($repository, $method, $data);
    }

    public function stripslashes_array($data)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = self::stripslashes_array($value);
            } else {
                $data[$key] = stripslashes($value);
            }
        }
        return $data;
    }

    /**
     * Salva dados via webservice
     * @param type $repository
     * @param type $method
     * @param array $data
     * @return boolean
     */
    public function saveLibCorp($repository = 'app:PessoaFisica', $method, array $data,$dadosLogger = NULL)
    {
        $data = $this->stripslashes_array($data);

        if ($repository == 'app:Documento' && $method == 'libCorpDeleteDocumento'){
            $arrDoc['sqDocumento'] = $dadosLogger->getSqDocumento();
            $arrDoc['sqAtributoTipoDocumento'] = $dadosLogger->getSqAtributoTipoDocumento()->getSqAtributoTipoDocumento();
            $arrDoc['sqPessoa'] = $dadosLogger->getSqPessoa()->getSqPessoa();
            $arrDoc['txValor'] = $dadosLogger->getTxValor();
            $arrDocumento['documento'] = $arrDoc;
        }
        if ($repository == 'app:Endereco' && $method == 'libCorpDeleteEndereco'){
            $dadosLogger = $this->_getRepository('app:Endereco')->find($data['sqEndereco']);
            $arrDadoEnd['sqEndereco'] = $dadosLogger->getSqEndereco();
            $arrDadoEnd['sqMunicipio'] = $dadosLogger->getSqMunicipio()->getSqMunicipio();
            $arrDadoEnd['sqPessoa'] = $dadosLogger->getSqPessoa()->getSqPessoa();
            $arrDadoEnd['sqTipoEndereco'] = $dadosLogger->getSqTipoEndereco()->getSqTipoEndereco();
            $arrDadoEnd['noBairro'] = $dadosLogger->getNoBairro();
            $arrDadoEnd['txEndereco'] = $dadosLogger->getTxEndereco();
            $arrDadoEnd['txComplemento'] = $dadosLogger->getTxComplemento();
            $arrDadoEnd['nuEndereco'] = $dadosLogger->getNuEndereco();
            $arrDadoEnd['inCorrespondencia'] = $dadosLogger->getInCorrespondencia();
            $arrDadoEnd['sqCep'] = $dadosLogger->getSqCep();
            $arrDadoEndereco['endereco'] = $arrDadoEnd;
        }
        if ($repository == 'app:Telefone' && $method == 'libCorpDeleteTelefone'){
            $dadosLogger = $this->_getRepository('app:Telefone')->find($data['sqTelefone']);
            $arrTel['sqTelefone'] = $dadosLogger->getSqTelefone();
            $arrTel['sqTipoTelefone'] = $dadosLogger->getSqTipoTelefone()->getSqTipoTelefone();
            $arrTel['sqPessoa'] = $dadosLogger->getSqPessoa()->getSqPessoa();
            $arrTel['nuDdd'] = $dadosLogger->getNuDdd();
            $arrTel['nuTelefone'] = $dadosLogger->getNuTelefone();
            $arrTelefone['telefone'] = $arrTel;
        }
        if ($repository == 'app:Email' && $method == 'libCorpDeleteEmail'){
            $dadosLogger = $this->_getRepository('app:Email')->find($data['sqEmail']);
            $arrMail['sqEmail'] = $dadosLogger->getSqEmail();
            $arrMail['sqTipoEmail'] = $dadosLogger->getSqTipoEmail()->getSqTipoEmail();
            $arrMail['sqPessoa'] = $dadosLogger->getSqPessoa()->getSqPessoa();
            $arrMail['txEmail'] = $dadosLogger->getTxEmail();
            $arrEmail['email'] = $arrMail;
        }
        if ($repository == 'app:DadoBancario' && $method == 'libCorpDeleteDadoBancario'){
            $dadosLogger = $this->_getRepository('app:DadoBancario')->find($data['sqDadoBancario']);
            $arrDadoBanc['sqDadoBancario'] = $dadosLogger->getSqDadoBancario();
            $arrDadoBanc['sqAgencia'] = $dadosLogger->getSqAgencia()->getSqAgencia();
            $arrDadoBanc['sqPessoa'] = $dadosLogger->getSqPessoa()->getSqPessoa();
            $arrDadoBanc['sqTipoDadoBancario'] = $dadosLogger->getSqTipoDadoBancario()->getSqTipoDadoBancario();
            $arrDadoBanc['nuConta'] = $dadosLogger->getNuConta();
            $arrDadoBanc['nuContaDv'] = $dadosLogger->getNuContaDv();
            $arrDadoBanc['coOperacao'] = $dadosLogger->getCoOperacao();
            $arrDadoBancario['dado_bancario'] = $arrDadoBanc;
        }

        /**
         * Informações obrigatórias para o log de auditoria do webservice.
         */
        $userCredential = \Core_Integration_Sica_User::getUserCredential();

        $entityManager  = $this->getEntityManager('ws')->getRepository($repository);

        $result         = $entityManager->{$method}($data, $userCredential);

        $resultXml      = \Core_Integration_Abstract_Soap::xmlToArray($result);
        if (isset($resultXml['status']) && $resultXml['status'] == 'success') {
            if ($repository == 'app:PessoaFisica') {
                return $resultXml['response']['pessoa_fisica']['sqPessoa']['sqPessoa'];
            }
            if ($repository == 'app:PessoaJuridica') {
                return $resultXml['response']['pessoa_juridica']['sqPessoa']['sqPessoa'];
            }
            if ($repository == 'app:PessoaVinculo') {
                return $resultXml['response']['pessoa_vinculo']['sqPessoa']['sqPessoa'];
            }
            if ($repository == 'app:Pessoa') {
                $sqPessoa = null;
                if(!empty($resultXml['response']['pessoa'])) {
                    $sqPessoa = $resultXml['response']['pessoa']['sqPessoa'];
                } else if(!empty($resultXml['response']['pessoa_fisica'])){
                    $sqPessoa = $resultXml['response']['pessoa_fisica']['sqPessoa']['sqPessoa'];
                } elseif ($resultXml['response']['pessoa_juridica']){
                    $sqPessoa = $resultXml['response']['pessoa_juridica']['sqPessoa']['sqPessoa'];
                }
                return $sqPessoa;
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

        if (!empty($filters['skyp'])){
            return $params;
        }
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
            //Informações obrigatórias para o log de auditoria do webservice.
            $userCredential = \Core_Integration_Sica_User::getUserCredential();

            $method        = "libCorpInfoconvBy" . ucfirst($typeDoc);
            $entityManager = $this->getEntityManager('ws')->getRepository('app:IntegracaoPessoaInfoconv');
            $result        = $entityManager->{$method}(array($typeDoc => $doc), $userCredential);

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
}