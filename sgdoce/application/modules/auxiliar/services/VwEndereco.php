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
 * Classe para Serviço Endereco
 *
 * @package      Auxiliar
 * @subpackage   Servico
 * @name         Endereco
 * @version      1.0.0
 * @since        2012-06-26
 */

namespace Auxiliar\Service;

class VwEndereco extends \Core_ServiceLayer_Service_CrudDto
{

    /**
     * Variavel para receber o nome da entidade
     * @var string
     * @access protected
     * @name $_entityName
     */
    protected $_entityName = 'app:VwEndereco';

    public function save(\Core_Dto_Entity $entity)
    {
        $sqEndereco = null;
        $arrEntity  = null;

        $method = ($entity->getSqEndereco())
            ? 'libCorpUpdateEndereco'
            : 'libCorpSaveEndereco';

        $isUpdate = ($entity->getSqEndereco())
            ? true
            : false;

        $arrEntity = $this->getArrEndereco($entity);

        $sqEndereco = $this->saveWs('app:VwEndereco', $method, $arrEntity);

        return array(
            'sqEndereco' => $sqEndereco
        );
    }

    public function delete($dto)
    {
        $this->saveWs('app:VwEndereco', 'libCorpDeleteEndereco', array(
            'sqEndereco' => $dto->getSqEndereco()
        ));
    }

    public function deleteEnderecoSgdoce($dto)
    {
        $entity = $this->_getRepository('app:EnderecoSgdoce')->findOneBy(array(
            'sqEnderecoSgdoce' => $dto->getSqEnderecoSgdoce()
        ));

        if($entity) {
            $this->getEntityManager()->remove($entity);
            $this->getEntityManager()->flush();
        }
    }

    public function saveEnderecoSgdoce($params)
    {
        $this->getEntityManager()->clear();

        $params['coCep']          = $params['sqCep'];
        $params['sqPessoaSgdoce'] = $this->getPessoaSgdoce($params['sqPessoa']);

        $dto    = null;
        $entity = $this->factoryFromData($params);

        $enderecoSgdoce = $this->getServiceLocator()->getService('EnderecoSgdoce')->saveEnderecoDestinatario($entity, $params);

        return $enderecoSgdoce->getSqEnderecoSgdoce();
    }

    private function getPessoaSgdoce($sqPessoa)
    {
        $pessoaSgdoce = $this->getServiceLocator()->getService('PessoaSgdoce')->findPessoaBySqCorporativo(
            new \Core_Dto_Search(array(
                'sqPessoaCorporativo' => $sqPessoa
            ))
        );

        return $pessoaSgdoce->getSqPessoaSgdoce();
    }

    private function factoryFromData($params)
    {
        $dto = \Core_Dto::factoryFromData($params, 'entity', array(
            'entity'=> 'Sgdoce\Model\Entity\EnderecoSgdoce',
            'mapping' => array(
                'sqPessoaSgdoce' => 'Sgdoce\Model\Entity\PessoaSgdoce',
                'sqMunicipio'    => 'Sgdoce\Model\Entity\VwMunicipio',
                'sqTipoEndereco' => 'Sgdoce\Model\Entity\VwTipoEndereco')
            )
        );

        return $dto;
    }

    private function saveWs($repository, $method, $data)
    {
        $dadosLogger = null;
        if ($method == 'libCorpDeleteEndereco') {
            $dadosLogger = $this->_getRepository('app:VwEndereco')->find($data['sqEndereco']);
            $arrDadoEnd['sqEndereco'] = $dadosLogger->getSqEndereco();
            $arrDadoEnd['sqMunicipio'] = $dadosLogger->getSqMunicipio()->getSqMunicipio();
            $arrDadoEnd['sqPessoa'] = $dadosLogger->getSqPessoa()->getSqPessoa();
            $arrDadoEnd['sqTipoEndereco'] = $dadosLogger->getSqTipoEndereco()->getSqTipoEndereco();
            $arrDadoEnd['noBairro'] = $dadosLogger->getNoBairro();
            $arrDadoEnd['txEndereco'] = $dadosLogger->getTxEndereco();
            $arrDadoEnd['nuEndereco'] = $dadosLogger->getNuEndereco();
            $arrDadoEndereco['endereco'] = $arrDadoEnd;
        }
        /**
         * Informações obrigatórias para o log de auditoria do webservice.
         */
        $userCredential = \Core_Integration_Sica_User::getUserCredential();
        $entityManager  = $this->getEntityManager('ws')->getRepository($repository);
        $result         = $entityManager->{$method}($data, $userCredential);
        $resultXml      = $this->getSoapResultArray($result);

        if(isset($resultXml['status']) && $resultXml['status'] == 'success') {
            if(isset($resultXml['response']['endereco']['sqEndereco']) && $resultXml['response']['endereco']['sqEndereco']) {
                $resultXml['response']['endereco']['sqPessoa'] =  !empty($resultXml['response']['endereco']['sqPessoa']['sqPessoa']) ?
                    $resultXml['response']['endereco']['sqPessoa']['sqPessoa'] : null;
                $resultXml['response']['endereco']['sqTipoEndereco'] = !empty($resultXml['response']['endereco']['sqTipoEndereco']['sqTipoEndereco']) ?
                    $resultXml['response']['endereco']['sqTipoEndereco']['sqTipoEndereco'] : null;
                $sqPessoa = $resultXml['response']['endereco']['sqEndereco'];

                return $sqPessoa;
            } else {
                return true;
            }
        }
    }

    public function getSoapResultArray($result)
    {
        return \Core_Integration_Abstract_Soap::xmlToArray($result);
    }

    private function getArrEndereco($entity)
    {
        $arr = array(
            'sqEndereco'     => $entity->getSqEndereco(),
            'sqMunicipio'    => $entity->getSqMunicipio()->getSqMunicipio(),
            'sqTipoEndereco' => $entity->getSqTipoEndereco()->getSqTipoEndereco(),
            'sqCep'          => \Zend_Filter::filterStatic($entity->getSqCep(), 'Digits'),
            'sqPessoa'       => $entity->getSqPessoa()->getSqPessoa(),
            'txEndereco'     => $entity->getTxEndereco(),
            'nuEndereco'     => $entity->getNuEndereco(),
            'txComplemento'  => $entity->getTxComplemento(),
            'noBairro'       => $entity->getNoBairro(),
        );

        $arr = array_filter($arr);

        return $arr;
    }

    /**
     * Retorna os municipios
     * @param array $estado
     * @return array
     */
    public function comboMunicipio($estado = NULL,$fEstado = FALSE)
    {
        return $this->_getRepository('app:VwMunicipio')->comboMunicipio($estado, $fEstado);
    }

    /**
     * Retorna os municipios
     * @param int $pais
     * @param int $estado
     * @return array
     */
    public function comboEstado($pais = NULL, $estado = NULL)
    {
        return $this->_getRepository('app:VwEstado')->comboEstado($pais, $estado);
    }

    /**
     * Retorna o endereco conforme cep
     * @param type $cep
     * @return array
     */
    public function searchCep($cep)
    {
        return $this->_getRepository()->searchCep($cep);
    }

    public function listGrid(\Core_Dto_Search $dto)
    {
        $result     = $this->_getRepository()->listGrid($dto);

        return $result;
    }

    /**
     * Retorna o endereco conforme cep
     * @param type $cep
     * @return array
     */
    public function findEndereco($sqPessoa)
    {
        $result = $this->_getRepository()->findEndereco($sqPessoa);
        if(!$result){
            $result = $this->_newEntity('app:VwEndereco');
        }
        return $result;
    }

    /**
     * Retorna o endereco conforme cep
     * @param type $cep
     * @return array
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $result = $this->_getRepository()->findBy($criteria);
        if(!$result){
            $result = $this->_newEntity('app:VwEndereco');
        }
        return $result;
    }

    /**
     * Retorna o endereco conforme cep
     * @param type $cep
     * @return array
     */
    public function findId($sqEndereco)
    {
        $result = $this->_getRepository()->find($sqEndereco);
        if(!$result){
            $result = $this->_newEntity('app:VwEndereco');
        }
        return $result;
    }

    /**
     * Metódo que realiza o upload do arquivo.
     */
    private function _upload()
    {
        $upload   = $this->getCoreUpload();

        if (is_string($upload->getFileName())) {
            $upload->setOptions(array('ignoreNoFile' => true));

            $upload->addValidator(
                'Extension',
                true,
                array(
                    'extensions' => 'png',
                    'messages'   => str_replace('<extensão>', 'png', \Core_Registry::getMessage()->_('MN076'))
                )
            );

            $this->validatorImageSize($upload);

            return $upload->upload();
        }
    }

    protected function validatorImageSize($upload) {
        $upload->addValidator(
            'ImageSize',
            true,
            array(
                'minwidth'  => 120,
                'minheight' => 120,
                'maxwidth'  => 1000,
                'maxheight' => 1000,
                'messages'  => 'A dimensão da imagem deve ser de no mínimo de %minwidth%x%minheight% pixels e máximo %maxwidth%x%maxheight% pixels.'
            )
        );
    }

    public function getCoreUpload()
    {
        $configs = \Core_Registry::get('configs');

        return new \Core_Upload('Http', false, $configs['upload']['endereco']);
    }

    public function uploadArquivo()
    {
        return $filename = $this->_upload();
    }

}
