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
 * Classe para Controller de Classificação de Artefato
 *
 * @package  Arquivo
 * @category Controller
 * @name     Classificacao
 * @version  1.0.0
 */
class Arquivo_ArtefatoClassificacaoController extends \Core_Controller_Action_Crud
{
    protected $_redirect = 'index/tipoArtefato/1/caixa/minhaCaixa';

    /**
     * Serviço
     * @var string
     */
    protected $_service = 'ArtefatoClassificacao';

    public function modalClassificacaoAction()
    {
        $this->getHelper('layout')->disableLayout();
        $params = $this->_getAllParams();

        $entityArtefato = $this->getService('Artefato')->find($params['sqArtefato']);
        $sqTipoArtefato = $entityArtefato->getSqTipoArtefatoAssunto()->getSqTipoArtefato()->getSqTipoArtefato();

        if ($sqTipoArtefato == \Core_Configuration::getSgdoceTipoArtefatoProcesso()) {
            $nuArtefato = $this->getService('Processo')->formataProcessoAmbitoFederal($entityArtefato);
        }else{
            $nuArtefato = $entityArtefato->getNuDigital()->getNuEtiqueta();
        }

        $this->view->nuArtefato = $nuArtefato;
        $this->view->entityArtefato = $entityArtefato;

        if (isset($params['back'])) {
            $this->view->backUrl = str_replace('.','/',$params['back']);
        }
    }

    /**
     * Retorna as unidades organizacionais cadastrados em formato json
     * @return void
     */
    public function searchClassificacaoArtefatoAction()
    {
        $result =  $this->getService()
                        ->searchClassificacaoArtefato($this->_getAllParams());
        $this->_helper->json($result);
    }

    public function saveAction()
    {
        $this->getRequest()->setModuleName('artefato');
        $this->getRequest()->setControllerName('area-trabalho');

        $back = $this->getRequest()->getParam('back', null);

        if ($back) {
            $this->_redirect = str_replace('.','/', $back);
        }


        parent::saveAction();
    }

    public function getSaveSuccessRoute()
    {
        return $this->_redirect;
    }

}
