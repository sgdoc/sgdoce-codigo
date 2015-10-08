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

class PessoaFisica extends \Sica_Service_Crud
{
    /**
     * Nome da entidade
     * @var string
     */
    protected $_entityName = 'app:PessoaFisica';

    /**
     * Valida dados a serem salvos
     * @param type $entityPessoaFisica
     * @param type $entityPessoa
     */
    public function preSave($entityPessoaFisica, $entityPessoa = NULL)
    {
        $validate = new \Core_Validate_Cpf();
        $status = TRUE;

        if ($entityPessoaFisica->getNuCpf()) {
            if (!$validate->isValid($entityPessoaFisica->getNuCpf())) {
                $this->getMessaging()->addErrorMessage('MN010');
                $status = FALSE;
            }
        }

        if (!\Zend_Validate::is($entityPessoa->getNoPessoa(), 'NotEmpty') ||
                !\Zend_Validate::is($entityPessoaFisica->getSgSexo(), 'NotEmpty')) {
            $this->getMessaging()->addErrorMessage('MN001');

            $status = FALSE;
        }

        if (!$status) {
            throw new \Core_Exception_ServiceLayer_Verification();
        }
    }

    /**
     * Realiza operação de save
     * @param \Core_Dto_Entity $entity
     */
    public function save(\Core_Dto_Entity $entity)
    {
        $args = func_get_args();
        $this->_triggerPreSave($entity, $args);

        $arrValues = $this->getArrPessoaFisica($args);

        if (isset($arrValues['sqPessoa']) && $arrValues['sqPessoa'] != '') {
            $method = 'libCorpUpdatePessoaFisica';
        } else {
            $method = 'libCorpSavePessoaFisica';
        }

        try {
            $sqPessoa = $this->saveWs('app:PessoaFisica', $method, $arrValues);
            if (!$sqPessoa) {
                $this->getMessaging()->addErrorMessage('Erro na operação (' . $method . ').');
                throw new \Core_Exception_ServiceLayer_Verification;
            }

            $arrValues['sqIntegracaoPessoaInfoconv']['sqPessoa'] = $sqPessoa;
            $this->saveWs('app:IntegracaoPessoaInfoconv'
                    , 'libCorpSaveIntegracaoPessoaInfoconv'
                    , $arrValues['sqIntegracaoPessoaInfoconv']);

            if ($sqPessoa && $method != 'libCorpUpdatePessoaFisica' && !$entity->getNuCpf()) {
                $this->_triggerPostSave(FALSE, array($sqPessoa, $args[4]));
            }
        } catch (Exception $exc) {
            throw new \Core_Exception_ServiceLayer_Verification;
        }

        return $sqPessoa;
    }

    /**
     * Save Pessoa Fisica via webservice
     * @param type $method
     * @param type $arrValues
     * @return type
     */
    public function saveWs($repository, $method, $arrValues)
    {
        return $this->getServiceLocator()->getService('Pessoa')->saveLibCorp($repository, $method, $arrValues);
    }

    /**
     * Salva Justificatva
     * @param type $entity
     * @param type $dto
     */
    public function postInsert($entity, $dto = NULL)
    {
        $arrValues = array(
            'sqPessoaAutora' => \Core_Integration_Sica_User::getPersonId(),
            'sqPessoaAutoriza' => \Core_Integration_Sica_User::getPersonId(),
            'sqPessoa' => $entity,
            'txJustificativa' => $dto->getTxJustificativa(),
            'dtInclusao' => \Zend_Date::now()->get('dd/MM/YYYY')
        );

        try {
            $result = $this->saveWs('app:CadastroSemCpf', 'libCorpSaveCadastroSemCPF', $arrValues);

            if (!$result) {
                $this->getMessaging()->addErrorMessage('Erro na operação (libCorpSaveCadastroSemCPF).');
                throw new \Core_Exception_ServiceLayer_Verification;
            }
        } catch (\Exception $exc) {
            throw new \Core_Exception_ServiceLayer_Verification;
        }
    }

    /**
     * Configura os dados a serem salvos pelo ws
     * @param $arrEntities
     * @return array
     */
    public function getArrPessoaFisica($arrEntities)
    {
        $arrData = array(
            'nuCpf' => \Zend_Filter::filterStatic($arrEntities[0]->getNuCpf(), 'Digits'),
            'noMae' => $arrEntities[0]->getNoMae(),
            'noPai' => $arrEntities[0]->getNoPai(),
            'sgSexo' => $arrEntities[0]->getSgSexo(),
            'dtNascimento' => $arrEntities[0]->getDtNascimento(),
            'sqEstadoCivil' => $arrEntities[0]->getSqEstadoCivil()->getSqEstadoCivil(),
            'noPessoa' => $arrEntities[1]->getNoPessoa(),
            'stRegistroAtivo' => $arrEntities[0]->getSqPessoa()->getSqPessoa() ? $arrEntities[5]['stRegistroAtivo'] : true,
            'sqNacionalidade' => null,
            'sqNaturalidade' => null
        );

        if ($arrEntities[3]->getSqPais()) {
            $arrData['sqNacionalidade'] = $arrEntities[3]->getSqPais();
        } else {
            $arrData['sqNacionalidade'] = \Core_Configuration::getSgdocePaisBrasil();
            $arrData['sqNaturalidade'] = $arrEntities[2]->getSqMunicipio();
        }

        foreach ($arrData as $key => $value) {
            if (trim($value) == "") {
                unset($arrData[$key]);
            }
        }

        if ($arrEntities[1]->getSqPessoa()) {
            $criteria = array('sqPessoa' => $arrEntities[1]->getSqPessoa());
            $sqPessoaFisica = $this->_getRepository('app:PessoaFisica')->findOneBy($criteria);

            $arrData['sqPessoa'] = $sqPessoaFisica->getSqPessoa()->getSqPessoa();
            //$arrData['noPessoa'] = $sqPessoaFisica->getSqPessoa()->getNoPessoa();

            if ($sqPessoaFisica->getSqPessoa()->getSqPessoaFisica()->getNuCpf()) {
                $arrData['nuCpf'] = $sqPessoaFisica->getSqPessoa()->getSqPessoaFisica()->getNuCpf();
            }
        }

        $arrData['sqIntegracaoPessoaInfoconv']['dtIntegracao']    = $arrEntities[6]->getDtIntegracao();
        $arrData['sqIntegracaoPessoaInfoconv']['txJustificativa'] = $arrEntities[6]->getTxJustificativa();
        $arrData['sqIntegracaoPessoaInfoconv']['sqPessoaAutora']  = $arrEntities[6]->getSqPessoaAutora()->getSqPessoa();

        return $arrData;
    }

    public function findDataInstitucional($codigo)
    {
        $result = $this->_getRepository('app:PessoaFisica')->findDataInstitucional($codigo);

        if (!isset($result['nuCpf'], $result['nuDdd'], $result['nuTelefone'], $result['txEmail'])) {
            $this->getMessaging()->addInfoMessage('MN121');
        }

        $filter = new \Core_Filter_MaskNumber(array('mask' => 'cpf'));
        $result['nuCpf'] = $filter->filter($result['nuCpf']);
        return $result;
    }

}