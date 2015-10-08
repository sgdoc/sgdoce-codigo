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
 * Classe para Controller de Arquivamento Setorial
 *
 * @package  Arquivo
 * @category Controller
 * @name     ArquivamentoSetorial
 * @version  1.0.0
 */
class Arquivo_ArquivamentoSetorialController extends \Core_Controller_Action_Crud
{

    /**
     * Serviço
     * @var string
     */
    protected $_service = 'ArquivamentoSetorial';

    public function archiveAction ()
    {
        $this->_doAction('archive', 'MN199');
    }

    public function unarchiveAction()
    {
        $this->_doAction('unarchive','MN200');
    }

    private function _doAction($actionName, $msgCode)
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $mixSqArtefato = $this->getRequest()->getParam('sqArtefato');

        if( !is_array($mixSqArtefato) ){
            $mixSqArtefato = array((integer)$mixSqArtefato);
        }

        $type  = 'Sucesso';
        $error = false;
        foreach($mixSqArtefato as $sqArtefato){
            try {
                $this->getService()->$actionName($sqArtefato);
                $msg  = \Core_Registry::getMessage()->translate($msgCode);
            } catch( \Core_Exception $e ) {
                $error= true;
                $type = 'Alerta';
                $msg  = $e->getMessage();
            } catch( \Exception $e ) {
                $error= true;
                $type = 'Alerta';
                $msg  = 'Ocorreu um erro na execução da operação. ' . $e->getMessage();
            }
        }

        $this->_helper->json(array('error' => $error,'type' => $type,'msg' => $msg));
    }
}
