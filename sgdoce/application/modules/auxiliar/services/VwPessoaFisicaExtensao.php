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

use Doctrine\Common\Util\Debug;
use Doctrine\ORM\Query\ParameterTypeInferer;

/**
 * Classe para Service de Tipo Pessoa
 *
 * @package  Auxiliar
 * @category Service
 * @name     TipoPessoa
 * @version  1.0.0
 */
class VwPessoaFisicaExtensao extends \Core_ServiceLayer_Service_CrudDto
{
    /**
     * Salva Justificatva
     * @param type $entity
     * @param type $dto
     */
    public function postInsert($entity, $dto = null)
    {
        $this->sqPessoaSgdoce = $this->savePessoaSgdoce($entity, $dto);

        if(!$dto[1]->getNuCpf()) {
            $arrValue = array(
                'sqPessoaAutora'  => $dto[2]->getSqPessoaAutora(),
                'sqPessoaAutoriza'=> $dto[2]->getSqPessoaAutoriza(),
                'sqPessoa'        => $entity,
                'dtInclusao'      => \Zend_Date::now()->get('dd/MM/YYYY'),
                'txJustificativa' => $dto[2]->getTxJustificativa()
            );

            $result = $this->saveWs('app:VwCadastroSemCpf', 'libCorpSaveCadastroSemCPF', $arrValue);
            if(!$result) {
                $this->getMessaging()->addErrorMessage('Erro na operação (libCorpSaveCadastroSemCPF).');

                throw new \Core_Exception_ServiceLayer_Verification;
            }
        }
    }

    private function getUserSica()
    {
        return \Core_Integration_Sica_User::getUserId();
    }

    public function postUpdate($entity, $dto = null)
    {
        if(!$dto[1]->getSqPessoaSgdoce()) {
            $this->sqPessoaSgdoce = $this->savePessoaSgdoce($entity, $dto);
        } else {
            if($dto[1]->getTxInformacaoComplementar()) {
                $_em = \Zend_Registry::get('doctrine')->getEntityManager();

                $result = $_em->createQueryBuilder()
                    ->update('app:PessoaSgdoce', 'ps')
                    ->set('ps.txInformacaoComplementar', $_em->createQueryBuilder()->expr()->literal($dto[1]->getTxInformacaoComplementar()))
                    ->where('ps.sqPessoaSgdoce = :sqPessoaSgdoce')
                    ->setParameter('sqPessoaSgdoce', $dto[1]->getSqPessoaSgdoce())
                    ->getQuery()
                    ->execute();
            }
        }
    }

    /**
     * Save Pessoa Fisica via webservice
     * @param type $method
     * @param type $arrValues
     * @return type
     */
    public function saveWs($repository, $method, $arrValues)
    {
        return $this->saveLibCorp($repository, $method, $arrValues);
    }

    /**
     * Salva dados via webservice
     *
     * @param type $repository
     * @param type $method
     * @param array $data
     * @return boolean
     */
    public function saveLibCorp($repository = 'app:VwPessoaFisica', $method, array $data)
    {
        /**
         * Informações obrigatórias para o log de auditoria do webservice.
         */
        $userCredential = \Core_Integration_Sica_User::getUserCredential();

        $entityManager = $this->getEntityManager('ws')->getRepository($repository);
        $result        = $entityManager->{$method}($data, $userCredential);
        $resultXml     = $this->getSoapXmlResult($result);

        if(isset($resultXml['status'])
            && $resultXml['status'] == 'success'
            && $repository == 'app:VwPessoaFisica'
        ) {
            $sqPessoa = $resultXml['response']['pessoa_fisica']['sqPessoa']['sqPessoa'];
            $resultXml['response']['pessoa_fisica']['sqNaturalidade'] =
                !empty($resultXml['response']['pessoa_fisica']['sqNaturalidade']['sqMunicipio']) ?
                    $resultXml['response']['pessoa_fisica']['sqNaturalidade']['sqMunicipio'] : null;

            $resultXml['response']['pessoa_fisica']['sqPessoa'] = $sqPessoa;

            $resultXml['response']['pessoa_fisica']['sqNacionalidade'] =
                !empty($resultXml['response']['pessoa_fisica']['sqNacionalidade']['sqPais']) ?
                    $resultXml['response']['pessoa_fisica']['sqNacionalidade']['sqPais'] : 1;
            return $sqPessoa;
        } else if(isset($resultXml['status'])
            && $resultXml['status'] == 'success'
            && $repository == 'app:VwCadastroSemCpf'
        ){
            $resultXml['response']['cadastro_sem_cpf']['sqPessoa'] =
                $resultXml['response']['cadastro_sem_cpf']['sqPessoa']['sqPessoa'];
            $resultXml['response']['cadastro_sem_cpf']['sqCadastroSemCpf'] =
                $resultXml['response']['cadastro_sem_cpf']['sqCadastroSemCPF'];
            unset($resultXml['response']['cadastro_sem_cpf']['sqCadastroSemCPF']);
            return $resultXml['response'];
        } else {
            return $resultXml['response'];
        }
    }

    public function getSoapXmlResult($result)
    {
        return \Core_Integration_Abstract_Soap::xmlToArray($result);
    }

    /**
     * Configura os dados a serem salvos pelo ws
     * @param $arrEntities
     * @return array
     */
    public function getPessoaArr(&$arrData, $arrEntities)
    {
        if(isset($arrData['nuCpf'])) {
            $criteria = array('nuCpf' => $arrData['nuCpf']);
            $sqPessoaFisica = $this->_getRepository('app:VwPessoaFisica')->findOneBy($criteria);

            if ($sqPessoaFisica) {
                $arrData['sqPessoa'] = $sqPessoaFisica->getSqPessoaFisica()->getSqPessoa();
            }
        }

        if($arrEntities[1]->getSqPessoa()) {
            $arrData['sqPessoa'] = $arrEntities[1]->getSqPessoa();
        }

        if($arrEntities[1]->getSqPessoaSgdoce()) {
            $arrData['sqPessoaSgdoce'] = $arrEntities[1]->getSqPessoaSgdoce();
        }
    }

    /**
     * Configura os dados a serem salvos pelo ws
     * @param $arrEntities
     * @return array
     */
    public function getArrPessoaFisica($arrEntities)
    {
        if($arrEntities[1]->getNuCpf()){
            $nuCpf =  \Zend_Filter::filterStatic($arrEntities[1]->getNuCpf(), 'Digits');
        }else{
            $nuCpf = NULL;
        }

        $arrData = array(
            'nuCpf'           => $nuCpf,
            'noMae'           => $arrEntities[1]->getNoMae(),
            'dtNascimento'    => $arrEntities[1]->getDtNascimento(),
            'noPessoa'        => $arrEntities[1]->getNoPessoaFisica(),
            'stRegistroAtivo' => true
        );

        $this->getPessoaArr($arrData, $arrEntities);

        if($arrEntities[0]->getSqPais()) {
            $arrData['sqNacionalidade'] = \Sgdoce_Constants::NACIONALIDADE_BRASILEIRA;
            if($arrEntities[0]->getSqPais()->getSqPais()){
                $arrData['sqNacionalidade'] = $arrEntities[0]->getSqPais()->getSqPais();
            }
        }

        $this->verificarUsuarioPossuiCpf($arrData);

        return $arrData;
    }

    public function searchCpf($criteria)
    {
        $pessoaFisica = $this->_getRepository('app:VwPessoaFisica')->findOneBy($criteria);

        $arrReturn = array();

        if($pessoaFisica) {
            $arrReturn['sqPessoa'] = $pessoaFisica->getSqPessoaFisica()->getSqPessoa();
            $arrReturn['noPessoa'] = $pessoaFisica->getSqPessoaFisica()->getNoPessoa();
        }

        return $arrReturn;
    }

    protected function verificarUsuarioPossuiCpf(&$array)
    {
        if(isset($array['sqPessoa']) && $array['sqPessoa']) {
            $entityPessoa = $this->findBy(array(
                'sqPessoaFisica' => $array['sqPessoa']
            ));

            if($entityPessoa->getNuCpf()) {
                unset($array['nuCpf']);
            }
        }
    }

    public function getDadosPessoaFisica($criteria)
    {
       $return['pessoaFisica'] = $this->_getRepository('app:VwPessoaFisica')->find($criteria['sqPessoa']);
       $return['endereco']     = $this->_getRepository('app:VwEndereco')
                                               ->findBy(array('sqPessoa' => $criteria['sqPessoa']));
       $return['telefone']     = $this->_getRepository('app:VwTelefone')
                                               ->findBy(array('sqPessoa' => $criteria['sqPessoa']));
       $return['email']        = $this->_getRepository('app:VwEmail')
                                               ->findBy(array('sqPessoa' => $criteria['sqPessoa']));

       $return['documento']        = $this->_getRepository('app:VwDocumento')
                                               ->findDocumentoPessoaFisica($criteria['sqPessoa']);

       return $return;
    }

    public function searchPessoaFisica(\Core_Dto_Search $dto)
    {
        return $this->_getRepository()->searchPessoaFisica($dto);
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->_getRepository('app:VwPessoaFisica')->findOneBy($criteria);
    }
}