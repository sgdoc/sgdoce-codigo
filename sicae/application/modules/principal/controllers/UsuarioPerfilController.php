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
 * Classe Controller Unidade Organizacional
 *
 * @package      Principal
 * @subpackage   Controller
 * @name         UnidadeOrganizacional
 * @version      1.0.0
 * @since        2012-07-24
 */
class Principal_UsuarioPerfilController extends Core_Controller_Action_CrudDto
{

    protected $_service = 'UsuarioPerfil';

    /**
     *
     * @var array
     */
    protected $_optionsDtoEntity = array(
        'entity' => 'Sica\Model\Entity\UsuarioPerfil',
        'mapping' => array(
            'sqUsuario' => 'Sica\Model\Entity\Usuario',
            'sqUnidadeOrgPessoa' => array('sqPessoa' => 'Sica\Model\Entity\UnidadeOrg'),
            'sqPerfil' => array('sqPeril' => 'Sica\Model\Entity\Perfil')
        )
    );

    /**
     * Action
     */
    public function userUnitAction()
    {
        $this->_helper->layout->disableLayout();

        $this->view->id = $this->_getParam('sqSistema');
        $this->view->callback = $this->_getParam('callback', '');
        $user = Zend_Auth::getInstance()->getIdentity();
        if (FALSE === isset($user->sqUsuario)){
            return $this->view->response = '[]';
        }

        $params = array(
            'sqUsuario' => $user->sqUsuario,
            'sqSistema' => $this->_getParam('sqSistema'),
            'sqUnidadeOrgPessoa' => NULL
        );

        $map = array('sqUsuario', 'sqSistema', 'sqUnidadeOrgPessoa');
        $dto = Core_Dto::factoryFromData($params, 'Core_Dto_Mapping', $map);

        $unit = $this->getService()->userUnit($dto);

        $session = new Core_Session_Namespace('USER', FALSE, TRUE);

        if (count($unit) == 1) {
            $unidade = current($unit);

            $params = array(
                'sqUsuario' => $user->sqUsuario,
                'sqSistema' => $this->_getParam('sqSistema'),
                'sqUnidadeOrgPessoa' => $unidade['sqPessoa']
            );
            $dto = Core_Dto::factoryFromData($params, 'Core_Dto_Mapping', $map);
            $data = $this->getService()->unitProfile($dto);

            if (!\Core_Integration_Sica_User::getUserProfileExternal()) {
                $session->sqUnidadeOrg = current($data)->getSqUnidadeOrgPessoa()->getSqPessoa();
                $session->noUnidadeOrg = current($data)->getSqUnidadeOrgPessoa()->getNoPessoa();
            }

            $session->sqPerfil = current($data)->getSqPerfil()->getSqPerfil();
            $session->noPerfil = current($data)->getSqPerfil()->getNoPerfil();
            $session->sqSistema = $dto->getSqSistema();
            $session->profiles = array();

            $mappingPerfil = new Core_Dto_Mapping(
                array('sqPerfil' => $session->sqPerfil,
                    'noPerfil' => $session->noPerfil
                ),
                array('noPerfil', 'sqPerfil')
            );

            $acl = $this->getService('Usuario')->mountAcl($mappingPerfil);

            if ($acl instanceof \Core_Acl_AclSession) {
                $session->acl = $acl;
            }

            $session->allProfile = $unit;
        } else {
            $session->profile = count($unit);
            $session->allProfile = $unit;
        }

        $this->view->response = \Zend_Json::encode($unit);
    }

    public function userProfileAction()
    {
        $this->_helper->layout->disableLayout(TRUE);
        $params = $this->_getAllParams();

        $perfil = $this->getService('Perfil')->find($params['sqPerfil']);
        $session = new Core_Session_Namespace('USER', FALSE, TRUE);

        if (!\Core_Integration_Sica_User::getUserProfileExternal()) {
            $pessoa = $this->getService('Pessoa')->find($params['sqUnidadeOrg']);
            $session->sqUnidadeOrg = $params['sqUnidadeOrg'];
            $session->noUnidadeOrg = $pessoa->getNoPessoa();
        }

        $session->sqPerfil = $params['sqPerfil'];
        $session->noPerfil = $perfil->getNoPerfil();

        $sqSistema = $this->getService('Sistema')->find($params['systemId']);
        $session->sqSistema = $sqSistema->getSqSistema();
        $session->sqLeiaute = $sqSistema->getSqLeiaute()->getSqLeiaute();

        $mappingPerfil = new Core_Dto_Mapping(
            array('sqPerfil' => $session->sqPerfil,
                'noPerfil' => $session->noPerfil
            ),
            array('noPerfil', 'sqPerfil')
        );

        $acl = $this->getService('Usuario')->mountAcl($mappingPerfil);

        if ($acl instanceof \Core_Acl_AclSession) {
            $session->acl = $acl;
        }

        $this->view->response = \Zend_Json::encode(TRUE);
    }

    public function perfilUnidadeAction()
    {
        $params = $this->_getAllParams();
        $params = array(
            'sqSistema' => $this->_getParam('sqSistema'),
            'sqUnidadeOrgPessoa' => $this->_getParam('sqUnidadeOrgPessoa'),
            'sqUsuario' => Core_Integration_Sica_User::get()->sqUsuario
        );

        $map = array('sqSistema', 'sqUnidadeOrgPessoa');
        $dto = Core_Dto::factoryFromData($params, 'Core_Dto_Mapping', $map);
        $data = $this->getService()->findProfileByUnit($dto);

        $this->view->response = \Zend_Json::encode($data);
    }

}
