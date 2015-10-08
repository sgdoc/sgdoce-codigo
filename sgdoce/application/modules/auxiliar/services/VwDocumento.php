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
 * Classe Service Documento
 *
 * @package      Principal
 * @subpackage   Services
 * @name         Documento
 * @version      1.0.0
 * @since         2012-08-17
 */

namespace Auxiliar\Service;

class VwDocumento extends \Core_ServiceLayer_Service_CrudDto
{
    /**
     * @var mixed
     */
    protected $adapter;

    /**
     * Nome da entidade
     * @var string
     */
    protected $_entityName = 'app:VwDocumento';

    public function listGrid(\Core_Dto_Search $dto)
    {
        $repository = $this->getEntityManager()->getRepository($this->_entityName);
        $result = $repository->searchPageDto('listGrid', $dto);

        return $result;
    }

    public function saveWs($repository, $method, $data,$dadosLogger = null)
    {
        if ($method == 'libCorpDeleteDocumento'){
            $arrDoc['sqDocumento'] = $dadosLogger->getSqDocumento();
            $arrDoc['sqAtributoDocumento'] = $dadosLogger->getSqAtributoTipoDocumento()->getSqAtributoTipoDocumento();
            $arrDoc['sqPessoa'] = $dadosLogger->getSqPessoa()->getSqPessoa();
            $arrDoc['txValor'] = $dadosLogger->getTxValor();
            $arrDocumento['documento'] = $arrDoc;
        }
        /**
         * Informações obrigatórias para o log de auditoria do webservice.
         */
        $userCredential = \Core_Integration_Sica_User::getUserCredential();
        $entityManager = $this->getEntityManager('ws')->getRepository($repository);
        $result        = $entityManager->{$method}($data, $userCredential);
        $resultXml     = \Core_Integration_Abstract_Soap::xmlToArray($result);

        if(isset($resultXml['status']) && $resultXml['status'] == 'success') {
            if ($method != 'libCorpDeleteDocumento'){
                $resultXml['response']['documento']['sqPessoa'] =  $resultXml['response']['documento']['sqPessoa']['sqPessoa'];
                $resultXml['response']['documento']['sqAtributoTipoDocumento'] =
                $resultXml['response']['documento']['sqAtributoTipoDocumento']['sqAtributoTipoDocumento'];
            }
            return $resultXml['response'];
        }
    }

    /**
     * Metódo que realiza o upload do arquivo.
     */
    private function _upload()
    {
        $upload   = $this->getCoreUpload();

        if (is_string($upload->getFileName())) {
            $upload->setOptions(array('ignoreNoFile' => true));

            $upload->addValidator('Extension',
                true,
                array(
                    'extensions' => 'png,jpg',
                    'messages'   => str_replace('<extensão>', 'png,jpg', \Core_Registry::getMessage()->_('MN076'))
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
        $configs  = \Core_Registry::get('configs');

        return new \Core_Upload('Http', false, $configs['upload']['documento']);
    }

    public function uploadArquivo()
    {
        return $filename = $this->_upload();
    }

    public function findOneBy(array $criteria)
    {
        return $this->getEntityManager()->getRepository($this->_entityName)->findOneBy($criteria);
    }

    public function findDocumento($sqPessoa)
    {
        $result = $this->_getRepository()->findBy($sqPessoa);
        if(!$result){
            $result = $this->_newEntity('app:VwDocumento');
        }
        return $result;
    }

    public function getDocumento($dto)
    {
        return $this->_getRepository()->getDocumento($dto);
    }
}