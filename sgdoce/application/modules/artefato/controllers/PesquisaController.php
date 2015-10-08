<?php
use Bisna\Application\Resource\Doctrine;

require_once __DIR__ . '/ArtefatoController.php';

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
 * Classe para Controller de Processo Eletronico
 *
 * @package  Artefato
 * @category Controller
 * @name     ProcessoEletronico
 * @version     1.0.0
 */
class Artefato_PesquisaController extends \Core_Controller_Action_Crud
{
    /**
     * Define tipo de listagem ex: Processo / Documento / Etc
     *
     * @var string
     */
    protected $_fnConfigList = null;
    /**
     * Define tipo de listagem ex: Processo / Documento / Etc
     *
     * @var string
     */
    protected $_fnListGrid = null;

    /**
     * @var string
     */
    protected $_service = "ProcessoEletronico";

    /**
     * Página de pesquisa de processos.
     *
     * @return void
     */
    public function processoAction()
    {
    }

    /**
     * @return void
     */
    public function documentoAction()
    {
    }

    /**
     * Página de resultado da pesquisa
     *
     * @return void
     */
    public function listProcessoAction()
    {
        $this->_service = "ProcessoEletronico";
        $this->_fnConfigList = "_getConfigListProcesso";
        $this->_fnListGrid  = "listGrid";
        parent::listAction();
    }

    /**
     * Página de resultado da pesquisa
     *
     * @return void
     */
    public function listDocumentoAction()
    {
        $this->_service = "Documento";
        $this->_fnConfigList = "_getConfigListDocumento";
        $this->_fnListGrid  = "listGridDocumento";
        parent::listAction();
    }


    /**
     * Método para preencher os dados da pesquisa
     *
     * @param Core_Dto_Search $dto Dados da requisição
     */
    public function getResultList($params)
    {
        $listGrid = $this->_fnListGrid;

        if ($listGrid === 'listGridDocumento' && (strlen($params['nuDigital']) <= 7)) {
            $params['nuDigitalNumber'] = (integer) $params['nuDigital'];
        }

        $dtoSearch = \Core_Dto::factoryFromData($params, 'search');
        return $this->getService()->$listGrid($dtoSearch);
    }

    /**
     * Retorna array de configuração da pesquisa
     *
     * @return array
     */
    public function getConfigList()
    {
        $getConfigList = $this->_fnConfigList;
        return $this->$getConfigList();
    }

    /**
     * @return void
     */
    protected function _getConfigListProcesso()
    {
        return array('columns' => array(
                array('alias' => 'nu_artefato'),
                array('alias' => 'tx_assunto'),
                array('alias' => 'origem'),
                array('alias' => 'interessados'),
                array('alias' => 'tx_movimentacao'),
        ));
    }

    /**
     * @return void
     */
    protected function _getConfigListDocumento()
    {
        return array('columns' => array(
                array('alias' => 'nu_digital'),
                array('alias' => 'tx_assunto'),
                array('alias' => 'nu_artefato'),
                array('alias' => 'no_tipo_artefato'),
                array('alias' => 'origem'),
                array('alias' => 'dt_artefato'),
                array('alias' => 'tx_movimentacao'),
        ));
    }
}