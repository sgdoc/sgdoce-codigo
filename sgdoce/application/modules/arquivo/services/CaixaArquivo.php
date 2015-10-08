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

namespace Arquivo\Service;

/**
 * Classe para Service de CaixaArquivo
 *
 * @package  Arquivo
 * @category Service
 * @name     CaixaArquivo
 * @version  1.0.0
 */
class CaixaArquivo extends \Core_ServiceLayer_Service_Crud
{

    /**
     * Variavel para receber o nome da entidade
     *
     * @var string
     * @access protected
     * @name $_entityName
     */
    protected $_entityName = 'app:Caixa';
    protected $_msgNumero = false;

    public function listGrid (\Core_Dto_Search $dto)
    {
        return $this->_getRepository()->searchPageDto('listGrid', $dto);
    }

    public function listGridCaixaAbertaPorClassificacao (\Core_Dto_Search $dto)
    {
        return $this->_getRepository()->searchPageDto('listGridCaixaAbertaPorClassificacao', $dto);
    }

    public function listGridArtefatoArquivado (\Core_Dto_Search $dto)
    {
        return $this->_getRepository('app:CaixaArtefato')->searchPageDto('listGrid', $dto);
    }

    /**
     * Metódo que retorna os dados da Unidade
     * @return array
     */
    public function searchClassificacaoCaixa($arrParans)
    {
        return $this->_getRepository('app:Classificacao')->searchClassificacaoParaCaixa($arrParans);
    }

    /**
     * Método que popula os objetos para serem salvos no banco
     * @return void
     */
    public function setOperationalEntity ($entityName = NULL)
    {
        $userUnit = \Core_Integration_Sica_User::getUserUnit();
        $personId   = \Core_Integration_Sica_User::getPersonId();
        $this->_data['sqUnidadeUsuario' ] = $this->_getRepository('app:VwUnidadeOrg' )->find($userUnit);
        $this->_data['sqPessoaCadastro' ] = $this->_getRepository('app:VwPessoa'     )->find($personId);
        $this->_data['sqClassificacao'  ] = $this->_getRepository('app:Classificacao')->find($this->_data['sqClassificacao']);
        $this->_data['sqUnidadeOrg'     ] = $this->_getRepository('app:VwUnidadeOrg' )->find($this->_data['sqUnidadeOrg']);
        $this->_data['stFechamento'     ] = false;
        $this->_data['dtCadastro'       ] = \Zend_Date::now();
        $this->_data['stAtivo'          ] = true;
    }

    public function preSave ($service)
    {
        try {
            if (!$this->_data['sqClassificacao']) {
                throw new \Core_Exception_ServiceLayer_Verification('O campo Classificação é de preenchimento obrigatório.');
            }
            if (!$this->_data['nuAno']) {
                throw new \Core_Exception_ServiceLayer_Verification('O campo Ano é de preenchimento obrigatório.');
            }
            if (!$this->_data['sqUnidadeOrg']) {
                throw new \Core_Exception_ServiceLayer_Verification('O campo Unidade é de preenchimento obrigatório.');
            }

            $dto = \Core_Dto::factoryFromData(array(
                    'sqUnidadeOrg' => $service->getEntity()->getSqUnidadeOrg()->getSqUnidadeOrg(),
                    'sqCaixa' => $service->getEntity()->getSqCaixa()
                ), 'search');

            if (!$service->getEntity()->getSqCaixa()){
                $nuCaixa = $this->_getRepository('app:Caixa')->getNextBoxNumber($dto);
                $service->getEntity()->setNuCaixa($nuCaixa);

                $this->getMessaging()->addSuccessMessage('Número da caixa: <b>' . $nuCaixa . '</b>', 'User');
            }else{
                //Se existir artefato arquivado na caixa, a mesma não pode ser alterada;
                if (!$this->verificaArtefatoArquivadoCaixa($dto)) {
                    $msg = sprintf(\Core_Registry::getMessage()->translate('MN157'),'alterada');
                    throw new \Core_Exception_ServiceLayer_Verification($msg);
                }
            }

        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function preDelete ($id)
    {
        try {
            $dto = \Core_Dto::factoryFromData(array('sqCaixa' => $id), 'search');

            //Se existir artefato arquivado na caixa, a mesma não pode ser alterada;
            if (!$this->verificaArtefatoArquivadoCaixa($dto)) {
                $msg = sprintf(\Core_Registry::getMessage()->translate('MN157'),'excluida');
                throw new \Core_Exception_ServiceLayer_Verification($msg);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     *
     * @param \Core_Dto_Abstract $dto
     * @throws \Core_Exception_ServiceLayer_Verification
     * @return void
     */
    public function openBox (\Core_Dto_Abstract $dto)
    {
        $entityCaixa = $this->find($dto->getSqCaixa());

        if (!$entityCaixa) {
            throw new \Core_Exception_ServiceLayer_Verification('Caixa não localiza para abertura.');
        }

        if (false === $entityCaixa->getStFechamento()){
            throw new \Core_Exception_ServiceLayer_Verification('Caixa já encontra-se aberta.');
        }

        $entityCaixa->setStFechamento(false);

        $this->getEntityManager()->persist($entityCaixa);
        $this->getEntityManager()->flush($entityCaixa);
    }

    /**
     *
     * @param \Core_Dto_Abstract $dto
     * @throws \Core_Exception_ServiceLayer_Verification
     * @return void
     */
    public function closeBox (\Core_Dto_Abstract $dto)
    {
        $entityCaixa = $this->find($dto->getSqCaixa());

        if (!$entityCaixa) {
            throw new \Core_Exception_ServiceLayer_Verification('Caixa não localiza para fechamento.');
        }

        if (true === $entityCaixa->getStFechamento()){
            throw new \Core_Exception_ServiceLayer_Verification('Caixa já encontra-se fechada.');
        }

        if ($entityCaixa->getSqCaixaArtefato()->count() === 0){
            throw new \Core_Exception_ServiceLayer_Verification('Caixa não possui nenhum artefato arquivado. Não pode ser fechada');
        }

        $entityCaixa->setStFechamento(true);

        $this->getEntityManager()->persist($entityCaixa);
        $this->getEntityManager()->flush($entityCaixa);
    }


    /**
     * Verifica se uma caixa pode ser editada e/ou excluida
     *
     * @param \Core_Dto_Abstract $dto
     * @return boolean
     */
    public function verificaArtefatoArquivadoCaixa(\Core_Dto_Abstract $dto)
    {
        $entityCaixa = $this->find($dto->getSqCaixa());

        return ($entityCaixa->getSqCaixaArtefato()->count() === 0);
    }

}
