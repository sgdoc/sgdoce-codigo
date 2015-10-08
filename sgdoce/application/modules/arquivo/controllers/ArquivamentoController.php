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
 * Classe para Controller de Arquivamento
 *
 * @package  Arquivo
 * @category Controller
 * @name     Arquivamento
 * @version  1.0.0
 */
class Arquivo_ArquivamentoController extends \Core_Controller_Action_Crud
{

    /**
     * Serviço
     * @var string
     */
    protected $_service = 'CaixaArtefato';

    public function modalArquivarAction()
    {
        $this->getHelper('layout')->disableLayout();
        $entityArtefato = $this->getService('Artefato')->find($this->_getParam('sqArtefato'));

        $this->view->entityArtefato = $entityArtefato;
    }

    public function arquivarAction ()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        try{
            $data   = $this->_getAllParams();
            $entity = Core_Dto::factoryFromData(
                    array(),
                    'entity',
                    array('entity' => '\Sgdoce\Model\Entity\CaixaArtefato')
            );

            if (isset($data['sqArtefato'])) {
                $entity->setSqArtefato( $this->getService('Artefato')->find($data['sqArtefato']) );
            }

            if (isset($data['sqCaixa'])) {
                $entity->setSqCaixa($this->getService('CaixaArquivo')->find($data['sqCaixa']));
            }

            $this->getService()
                 ->arquivar($entity);

            $this->_helper->json(array(
                  "error"  => false,
                  "message" => \Core_Registry::getMessage()->translate('MN013')
            ));

        } catch (Exception $e) {
            $this->_helper->json(array(
                  "error"  => true,
                  "message" => $e->getMessage()
            ));
        }
    }

    public function desarquivarAction()
    {
        try {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(TRUE);

            $dto = Core_Dto::factoryFromData($this->_getAllParams(), 'search');
            $this->getService()->desarquivar($dto);

            $this->_helper->json(array(
                'error' => false,
                'msg'   => Core_Registry::getMessage()->translate('MN013'),
                'type'=> 'Sucesso')
            );
        } catch (\Exception $e) {
            $this->_helper->json(array('error' => true, 'msg' => $e->getMessage(), 'type'=> 'Erro'));
        }
    }

    public function emprestarAction()
    {

    }

    public function devolverAction()
    {

    }


}
