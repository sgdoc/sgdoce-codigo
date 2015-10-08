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
class VwPessoaFisica extends VwPessoaFisicaExtensao
{
    /**
     * @var string
     */
    protected $_entityName = 'app:VwPessoaFisica';

    public function preSave($entity, $dto = null)
    {
        $this->validarDataNascimento($entity);

        if (!\Zend_Validate::is($entity->getNoPessoaFisica(), 'NotEmpty')) {
            $this->getMessaging()->addErrorMessage('MN001');

            throw new \Core_Exception_ServiceLayer_Verification('MN001');
        }
    }

    protected function validarDataNascimento($dto)
    {
        if($dto->getDtNascimento()) {
            $dtNascimento = $dto->getDtNascimento();
            $arrayData    = explode('/', $dtNascimento);
            $datetime     = \DateTime::createFromFormat('d/m/Y', $dtNascimento);
            $result       = true;

            if(
                (int)$arrayData[0] > 31
                || (int)$arrayData[1] > 12
                || (int)$arrayData[2] < 1900
            ) {
                $result = false;
            }

            if(!$result) {
                $this->getMessaging()->addErrorMessage('MN023');

                throw new \Core_Exception_ServiceLayer_Verification('MN023');
            }
        }
    }

    /**
     * Realiza a validação do method
     * @param array
     */
    public function validMethod($arrValues)
    {
        $sqPessoa = null;

        $method = 'libCorpSavePessoaFisica';
        if(!empty($arrValues['sqPessoa'])){
            $method = 'libCorpUpdatePessoaFisica';
        }

        $sqPessoa = $this->saveWs('app:VwPessoaFisica', $method, $arrValues);

        if(!$sqPessoa) {
            $this->getMessaging()->addErrorMessage('Erro na operação (' . $method . ').');

            throw new \Core_Exception_ServiceLayer_Verification('Erro na operação (' . $method . ').');
        }

        return $sqPessoa;
    }

    /**
     * Realiza operação de save
     * @param \Core_Dto_Entity $entity
     */
    public function save(\Core_Dto_Entity $entity)
    {
        $args     = func_get_args();
        $isUpdate = false;

        $this->_triggerPreSave($entity, $args);

        $arrValues = $this->getArrPessoaFisica($args);

        if(isset($arrValues['sqPessoa']) && $arrValues['sqPessoa'] != ''){
            $isUpdate = true;
        }

        $sqPessoa = $this->validMethod($arrValues);
        $this->_triggerPostSave($isUpdate, array($sqPessoa, $args));

        if(isset($arrValues['sqPessoaSgdoce']) && $arrValues['sqPessoaSgdoce']){
            $return = $arrValues['sqPessoaSgdoce'];
        } else {
            $return = $this->sqPessoaSgdoce;
        }

        return array(
            'sqPessoa'       => $sqPessoa,
            'sqPessoaSgdoce' => $return,
            'campoPessoa' => $args[1]->getCampoPessoa(),
            'campoCpf' => $args[1]->getCampoCpf(),
            'form' => $args[1]->getForm(),
            'nuCpf' => $args[0]->getNuCpf(),
            'noPessoa' => $args[0]->getNoPessoaFisica()
        );
    }

    public function savePessoaSgdoce($entity, $dto = null)
    {
        $this->getEntityManager()->clear();

        $pessoaSgdoce      = $this->_newEntity('app:PessoaSgdoce');
        $pessoaCorporativo = $this->getEntityManager()->find('app:VwPessoa', $entity);
        $tipoPessoa        = $this->getEntityManager()->getPartialReference('app:VwTipoPessoa', $dto[1]->getSqTipoPessoa());

        $cpf = NULL;
        if($dto[1]->getNuCpf()){
            $cpf = \Zend_Filter::filterStatic($dto[1]->getNuCpf(), 'Digits');
        }

        $pessoaSgdoce->setSqTipoPessoa($tipoPessoa)
            ->setNuCpfCnpjPassaporte($cpf)
            ->setNoPessoa($dto[1]->getNoPessoaFisica())
            ->setNoMae($dto[1]->getNoMae())
            ->setNoProfissao($dto[1]->getNoProfissao())
            ->setTxInformacaoComplementar($dto[1]->getTxInformacaoComplementar())
            ->setSqPessoaCorporativo($pessoaCorporativo);

        if($dto[0]->getSqEstadoCivil()->getSqEstadoCivil()) {
            $estadoCivil = $this->getEntityManager()->getPartialReference(
                'app:VwEstadoCivil',
                $pessoaCorporativo->getSqPessoaFisica()->getSqEstadoCivil()->getSqEstadoCivil()
            );

            $pessoaSgdoce->setSqEstadoCivil($estadoCivil);
        }

        $this->getEntityManager()->persist($pessoaSgdoce);
        $this->getEntityManager()->flush($pessoaSgdoce);

        return $pessoaSgdoce->getSqPessoaSgdoce();
    }
}