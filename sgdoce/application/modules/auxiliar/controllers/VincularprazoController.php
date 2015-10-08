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
 * Classe para Controller de Vincularprazo
 *
 * @package	Auxiliar
 * @category	Controller
 * @name	Vincularprazo
 * @version	1.0.0
 */
class Auxiliar_VincularprazoController extends \Core_Controller_Action_Crud
{
    /**
     * @var string
     */
    protected $_service = 'Vincularprazo';

    /**
     * Cria o formulário para inclusão
     */
    public function createAction ()
    {
        $this->view->data = $this->getService()->getNewEntity();
    }

    /**
     * Checa se existe vinculação do prazo para o assunto e/ou tipo de documento.
     */
    public function checkDuplicatePrazoAction ()
    {
        $sqAssunto = $this->_getParam('sqAssunto');
        $sqTipoDocumento = $this->_getParam('sqTipoDocumento');
        $sqIndicacaoPrazo = $this->_getParam('sqIndicacaoPrazo');
        $this->getHelper('json')->sendJson(
                $this->getService()->hasVinculacaoPrazo($sqAssunto, $sqTipoDocumento, $sqIndicacaoPrazo));
    }

    /**
     * Desvincula do prazo para o assunto e/ou tipo de documento.
     */
    public function unlinkPrazoAction ()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $sqAssunto = $this->_getParam('sqAssunto');
        $sqTipoDocumento = $this->_getParam('sqTipoDocumento');
        $sqIndicacaoPrazo = $this->_getParam('sqIndicacaoPrazo');
        $this->getService()->unlinkPrazo($sqAssunto, $sqTipoDocumento, $sqIndicacaoPrazo);
    }

    /**
     * Popula o formulário para edição.
     */
    public function editAction ()
    {
        $sequence = $this->_getParam('id');
        $this->view->data = $this->getService()->find($sequence);
    }

    /**
     * Faz a consulta para grid.
     * @param array $params
     * @return QueryBuilder
     */
    public function getResultList($params)
    {
        $params = \Core_Dto::factoryFromData($params, 'search');

        return $this->getService()->listGrid($params);
    }

    /**
     * Método responsável por retornar quais colunas da grid serão ordenadas.
     * @return array
     */
    public function getConfigList()
    {
        $array = array(
            'columns' => array(0 => array('alias' => 'tp.noTipoDocumento'),
                               1 => array('alias' => 'a.txAssunto'),
                               2 => array('alias' => 'ip.inPrazoObrigatorio'),
                               3 => array('alias' => 'ip.nuDiasPrazo'))
        );

        return $array;
    }
}
