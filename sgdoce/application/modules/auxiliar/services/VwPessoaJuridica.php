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
class VwPessoaJuridica extends VwPessoaJuridicaExtensao
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

    public function preSave($entity, $dto = null)
    {
        $return   = true;
        $criteria = array(
            'nuCnpj'   => \Zend_Filter::filterStatic($entity->getNuCnpj(), 'Digits'),
            'noPessoa' => mb_strtolower($entity->getNoPessoa(), 'utf8')
        );

        if(!$entity->getSqPessoa()) {
            if($this->searchCnpj($criteria)) {
                throw new \Core_Exception_ServiceLayer_Verification('MN119');
            }

            if($this->searchRazaoSocial($criteria)) {
                throw new \Core_Exception_ServiceLayer_Verification('MN120');
            }
        }
    }

    /**
     * Realiza a validação da pessoa juridica
     * @param array
     */
    public function validMethodJuridica($arrValues)
    {
        $sqPessoa = null;

        if(isset($arrValues['pessoa']['sqPessoa']) && $arrValues['pessoa']['sqPessoa']){
            $methodPessoa = 'libCorpUpdatePessoaJuridica';
        } else {
            $methodPessoa = 'libCorpSavePessoaJuridica';
        }

        $response = $this->saveLibCorp('app:VwPessoaJuridica', $methodPessoa, $arrValues['pessoa']);

        if($response) {
            $sqPessoa = $response['pessoa_juridica']['sqPessoa'];
        }

        if (!$sqPessoa) {
            $this->getMessaging()->addErrorMessage('Erro na operação (' . $methodPessoa . ').');

            throw new \Core_Exception_ServiceLayer_Verification;
        }

        $this->saveDocumento($arrValues['documento'], $sqPessoa);

        return $sqPessoa;
    }

    /**
     * Realiza operação de save
     * @param \Core_Dto_Entity $entity
     */
    public function save(\Core_Dto_Entity $entity)
    {
        $args     = func_get_args();

        $this->_triggerPreSave($entity, $args);

        $arrValues = $this->getArrPessoaJuridica($entity, $args);

        if(isset($arrValues['pessoa']['sqPessoa']) && $arrValues['pessoa']['sqPessoa'] != ''){
            $isUpdate = TRUE;
        } else {
            $isUpdate = FALSE;
        }

        $sqPessoa = $this->validMethodJuridica($arrValues);
        $this->_triggerPostSave($isUpdate, array($sqPessoa, $args));

        if(isset($arrValues['pessoa']['sqPessoaSgdoce']) && $arrValues['pessoa']['sqPessoaSgdoce']) {
            $return = $arrValues['pessoa']['sqPessoaSgdoce'];
        }else{
            $return = $this->sqPessoaSgdoce;
        }

        return array(
            'sqPessoa'       => $sqPessoa,
            'sqDocumento'    => $this->sqDocumento,
            'sqPessoaSgdoce' => $return,
            'campoPessoa' => $args[1]->getCampoPessoa(),
            'campoCnpj' => $args[1]->getCampoCnpj(),
            'form' => $args[1]->getForm(),
            'nuCnpj' => $args[0]->getNuCnpj(),
            'noPessoa' => $args[0]->getNoPessoa()
        );
    }

    public function savePessoaSgdoce($entity, $dto = null)
    {
        $this->getEntityManager()->clear();

        $pessoaSgdoce      = $this->_newEntity('app:PessoaSgdoce');
        $pessoaCorporativo = $this->getEntityManager()->find('app:VwPessoa', $entity);
        $tipoPessoa        = $this->getEntityManager()->getPartialReference('app:VwTipoPessoa', $dto[1]->getSqTipoPessoa());

        $cnpj = NULL;
        if($dto[1]->getNuCnpj()){
            $cnpj = \Zend_Filter::filterStatic($dto[1]->getNuCnpj(), 'Digits');
        }

        $pessoaSgdoce->setSqTipoPessoa($tipoPessoa)
            ->setNuCpfCnpjPassaporte($cnpj)
            ->setNoPessoa($dto[1]->getNoPessoa())
            ->setSqPessoaCorporativo($pessoaCorporativo);

        $this->getEntityManager()->persist($pessoaSgdoce);
        $this->getEntityManager()->flush($pessoaSgdoce);

        return $pessoaSgdoce->getSqPessoaSgdoce();
    }
}
