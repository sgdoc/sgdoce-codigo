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
 * Classe para Service de Tipo Pessoa
 *
 * @package  Auxiliar
 * @category Service
 * @name     TipoPessoa
 * @version  1.0.0
 */
class VwPessoaJuridicaExtensao extends \Core_ServiceLayer_Service_CrudDto
{
    /**
     * @var string
     */
    protected $_entityName = 'app:VwPessoaJuridica';

    /**
     * @var integer
     */
    protected $sqPessoa = null;

    /**
     * @var integer
     */
    protected $sqPessoaSgdoce = null;

    /**
     * @var integer
     */
    protected $sqDocumento = null;

    public function postUpdate($entity, $dto = null)
    {
        $entitySgdoce = $this->getServiceLocator()->getService('PessoaSgdoce')
            ->findPessoaBySqCorporativo(new \Core_Dto_Search(
                array(
                    'sqPessoaCorporativo' => $entity
                )
            ));

        if(!$dto[1]->getSqPessoaSgdoce() && !$entitySgdoce) {
            $this->sqPessoaSgdoce = $this->savePessoaSgdoce($entity, $dto);
        }
    }

    public function postInsert($entity, $dto = null)
    {
        $this->sqPessoaSgdoce = $this->savePessoaSgdoce($entity, $dto);
    }

    /**
     * Salva dados via webservice
     *
     * @param type $repository
     * @param type $method
     * @param array $data
     * @return boolean
     */
    public function saveLibCorp($repository = 'app:VwPessoaJuridica', $method, array $data)
    {
        /**
         * Informações obrigatórias para o log de auditoria do webservice.
         */
        $userCredential = \Core_Integration_Sica_User::getUserCredential();

        $entityManager = $this->getEntityManager('ws')->getRepository($repository);
        $result        = $entityManager->{$method}($data, $userCredential);
        $resultXml     = \Core_Integration_Abstract_Soap::xmlToArray($result);

        if(isset($resultXml['status']) && $resultXml['status'] == 'success' && $repository == 'app:VwPessoaJuridica') {
            if (!empty($resultXml['response']['pessoa_juridica']['sqPessoa']['sqPessoa'])){
                $sqPessoa = $resultXml['response']['pessoa_juridica']['sqPessoa']['sqPessoa'];
                $resultXml['response']['pessoa_juridica']['sqPessoa'] = $sqPessoa;
            }
        } else if (isset($resultXml['status']) && $resultXml['status'] == 'success' && $repository == 'app:VwDocumento') {
            $sqPessoa = $resultXml['response']['documento']['sqPessoa']['sqPessoa'];
            $resultXml['response']['documento']['sqPessoa'] = $sqPessoa;
            $resultXml['response']['documento']['sqAtributoTipoDocumento'] = $resultXml['response']['documento']['sqAtributoTipoDocumento']['sqAtributoTipoDocumento'];
        }
        return $resultXml['response'];

    }

    /**
     * Salva Documento
     *
     * @param type $entity
     * @param type $dto
     */
    protected function saveDocumento(array $arrValues, $sqPessoa)
    {
        $arrValues['sqPessoa'] = $sqPessoa;

        if(isset($arrValues['txValor']) && $arrValues['txValor']) {
        	$methodDocumento = 'libCorpSaveDocumento';
        	if(isset($arrValues['sqDocumento']) && $arrValues['sqDocumento']){
        		$methodDocumento = 'libCorpUpdateDocumento';
        	}

            $response = $this->saveLibCorp('app:VwDocumento', $methodDocumento, $arrValues);

            $this->sqDocumento = $response['documento']['sqDocumento'];
        } else {
            if(isset($arrValues['sqDocumento']) && $arrValues['sqDocumento']) {
                $methodDocumento = 'libCorpDeleteDocumento';

                unset($arrValues['sqDocumento']);

                $response = $this->saveLibCorp('app:VwDocumento', $methodDocumento, $arrValues);
            }
        }
    }

    /**
     * Configura os dados a serem salvos pelo ws
     *
     * @param $arrEntities
     * @return array
     */
    public function getArrPessoaJuridica($entity, $arrEntities)
    {
        $arrData = array();
        $arrData['pessoa'] = array(
            'sqPessoa'           => $entity->getSqPessoa() ? : null,
            'sqPessoaSgdoce'     => $arrEntities[1]->getSqPessoaSgdoce(),
            'nuCnpj'             => \Zend_Filter::filterStatic($arrEntities[1]->getNuCnpj(), 'Digits'),
            'sqNaturezaJuridica' => $arrEntities[1]->getSqNaturezaJuridica(),
            'noPessoa'           => $arrEntities[1]->getNoPessoa(),
            'noFantasia'         => $arrEntities[1]->getNoFantasia(),
            'sqTipoPessoa'       => $arrEntities[1]->getSqTipoPessoa(),
            'stRegistroAtivo'    => true
        );

        $arrData['documento'] = array(
            'sqDocumento'             => $arrEntities[1]->getSqDocumento(),
            'sqAtributoTipoDocumento' => $arrEntities[1]->getSqAtributoTipoDocumento(),
            'txValor'                 => $arrEntities[1]->getTxValor(),
        );

        foreach($arrData as $key => $value) {
            $arrData[$key] = array_filter($value);
        }

        if(isset($arrData['pessoa']['sqPessoaSgdoce']) && $arrData['pessoa']['sqPessoaSgdoce']) {
            $this->sqPessoaSgdoce = $arrData['pessoa']['sqPessoaSgdoce'];
        }

        return $arrData;
    }

    public function searchCnpj($criteria)
    {
        $pessoaFisica = $this->_getRepository('app:VwPessoaJuridica')->findOneBy($criteria);

        $arrReturn = array();

        if($pessoaFisica) {
            $arrReturn['sqPessoa'] = $pessoaFisica->getSqPessoaJuridica()->getSqPessoa();
            $arrReturn['noPessoa'] = $pessoaFisica->getNoPessoa();
        }

        return $arrReturn;
    }

    public function searchRazaoSocial($criteria)
    {
        $pessoaFisica = $this->_getRepository('app:VwPessoaJuridica')->searchRazaoSocial($criteria);

        $arrReturn = array();

        if($pessoaFisica) {
            $arrReturn['sqPessoa'] = $pessoaFisica->getSqPessoaJuridica()->getSqPessoa();
            $arrReturn['noPessoa'] = $pessoaFisica->getNoPessoa();
        }

        return $arrReturn;
    }

    public function getMatrizFilial(\Core_Dto_Search $dto)
    {
        $criteria     = array();
        $matrizFilial = null;

        if($dto->getSqPessoa()) {
            $entityJuridica       = $this->_getRepository('app:VwPessoaJuridica')->find($dto->getSqPessoa());
            $criteria['sqPessoa'] = $dto->getSqPessoa();

            if($entityJuridica) {
                $criteria['nuCgc']  = \Zend_Filter::filterStatic($entityJuridica->getNuCnpj(), 'Digits');
                $criteria['nuRaiz'] = substr(\Zend_Filter::filterStatic($entityJuridica->getNuCnpj(), 'Digits'), 0, 8);
                $matrizFilial       = $this->_getRepository('app:VwPessoaJuridica')->getMatrizFilial($criteria);
            }

            return $matrizFilial;
        }

        if($dto->getNuCnpj()) {
            $criteria['nuCgc']  = \Zend_Filter::filterStatic($dto->getNuCnpj(), 'Digits');
            $criteria['nuRaiz'] = substr(\Zend_Filter::filterStatic($dto->getNuCnpj(), 'Digits'), 0, 8);

            $matrizFilial = $this->_getRepository('app:VwPessoaJuridica')->getMatrizFilial($criteria);

            return $matrizFilial;
        }
    }

    public function gerarDocMatrizFilial($criteria)
    {
        // GetConfig
        $registry     = \Zend_Registry::get('configs');
        $options      = array('path' => $registry['upload']['pessoaJuridica']);

        // Get MatrizFilial e nomeia o arquivo que será feito o download

        if($criteria->getSqPessoa()){
        	$noArquivo = $criteria->getSqPessoa();
        }else{
        	$noArquivo = \Zend_Filter::filterStatic($criteria->getNuCnpj(), 'Digits');
        }
        $noArquivo .= '.pdf';

        $matrizFilial = $this->getMatrizFilial($criteria);

        $data = array(
            'matrizFilial' => $matrizFilial
        );
        $this->setPathDoc($data, $options['path'], $noArquivo);

        return $noArquivo;
    }

    public function setPathDoc($data, $path, $noArquivo)
    {
        \Core_Doc_Factory::setFilePath(APPLICATION_PATH . '/modules/auxiliar/views/scripts/pessoa-juridica');
        \Core_Doc_Factory::write('doc-matriz-filial', $data, $path, $noArquivo);
    }

    public function searchPessoaJuridica(\Core_Dto_Search $dto, $retornaCpf = TRUE)
    {
        return $this->_getRepository()->searchPessoaJuridica($dto, $retornaCpf);
    }

    public function findPessoaJuridica($criteria)
    {
        return $this->_getRepository('app:VwPessoaJuridica')->find($criteria['sqPessoa']);
    }

    public function findOneBy(array $criteria)
    {
        return $this->_getRepository('app:VwPessoaJuridica')->findOneBy($criteria);
    }
}
