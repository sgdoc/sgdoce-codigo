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

use Principal\Service\Laioute;

/**
 * SISICMBio
 *
 * Classe Controller Index
 *
 * @package      Principal
 * @subpackage   Controller
 * @name         Index
 * @version      1.0.0
 * @since        2012-07-24
 */
class Principal_IframeController extends Core_Controller_Action_CrudDto
{

    protected $_service = 'Sistema';

    /**
     * Action para sistemas que utilizam arquitetura diferente.
     * @return void
     */
    public function sistemaAction()
    {
        $sqSistema = base64_decode($this->_getParam('sys'));
        $entity = $this->getService()->find($sqSistema);

        if ($entity->getSqLeiaute()->getSqLeiaute() == Laioute::LAYOUT_DEFAULT) {
//            if(!isset(\Core_Integration_Sica_User::get()->{$entity->getSgSistema()})){
//                $this->_redirect('/index/home');
//            }

            $this->view->txUrl = $entity->getTxUrl();
            $this->_helper->layout->setLayout('iframe');

        } else {
            $this->_redirect('/index/home');
        }
    }

}