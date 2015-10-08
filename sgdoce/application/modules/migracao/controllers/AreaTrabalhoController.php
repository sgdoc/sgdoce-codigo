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

/**
 * Classe para Controller de Processo
 *
 * @package  Artefato
 * @category Controller
 * @name     Artefato
 * @version     1.0.0
 */
class Migracao_AreaTrabalhoController extends \Core_Controller_Action_CrudDto
{
    /**
     * @var string
     */
    protected $_service = 'AreaTrabalhoMigracao';

    /**
    * Método que obtém os dados para grid
    * @param \Core_Dto_Search $dtoSearch
    * @return array
    */
    public function getResultList(\Core_Dto_Search $dtoSearch)
    {
        $this->view->perfil        = Core_Integration_Sica_User::getUserProfile();
        $dtoSearch->sqPessoa       = Core_Integration_Sica_User::getPersonId();
        $dtoSearch->sqUnidadeOrg   = Core_Integration_Sica_User::getUserUnit();
        $dtoSearch->sqTipoArtefato = $this->getRequest()->getParam('tipoArtefato') ?
                                     $this->getRequest()->getParam('tipoArtefato') : 1;
        $dtoSearch->search         = $this->getRequest()->getParam('search') ?
                                     $this->getRequest()->getParam('search') : null;

        $this->view->isUserSgi     = $this->_isUserSgi();
        $this->view->isUserPro     = ($this->view->perfil == \Core_Configuration::getSgdocePerfilProtocolo());

        $dtoUnidadeOrg = Core_Dto::factoryFromData(array('sqUnidadeOrg' => $dtoSearch->sqUnidadeOrg), 'search');
        $dtoSearch->currentUnitHasNUP = $this->getService('VwUnidadeOrg')->hasNUP($dtoUnidadeOrg);

        $caixa = $this->_getParam('migration_box');
        $dtoSearch->caixa = $caixa;

        $res = $this->getService()->getGrid($dtoSearch);
        return $res;
    }

    /**
     *
     * Método que configura os dados da grid
     * @return array
     */
    public function getConfigList()
    {
        return array();
    }

}