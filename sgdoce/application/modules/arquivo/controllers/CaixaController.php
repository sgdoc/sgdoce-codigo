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
 * Classe para Controller de Caixa de Arquivo
 *
 * @package    Arquivo
 * @category   Controller
 * @name       Caixa
 * @version    1.0.0
 */

/**
 * Classe para Controller de Caixa de arquivo
 *
 * @package    Arquivo
 * @category   Controller
 * @name       CaixaController
 * @version    1.0.0
 */
class Arquivo_CaixaController extends \Core_Controller_Action_Crud
{

    /**
     * Variavel para receber o nome da service
     * @var    string
     * @access protected
     * @name   $_service
     */
    protected $_service = 'CaixaArquivo';


    /**
     * Ação inicial da funcionalidade
     */
    public function indexAction()
    {
        parent::indexAction();
        $this->view->arrSituacao = array(0 => 'Aberta', 1 => 'Fechada');
    }

    public function openBoxAction ()
    {
        try {
            $retorno = array('error'=>false, 'msg'=>  Core_Registry::getMessage()->translate('MN013'));

            $params = $this->_getAllParams();
            $params['sqCaixa'] = $params['id'];

            $dto = \Core_Dto::factoryFromData($params, 'search');

            $this->getService()->openBox($dto);

        } catch (\Exception $e) {

            $retorno['error']= true;
            $retorno['msg']= $e->getMessage();
        }

        echo $this->_helper->json($retorno);
    }

    public function closeBoxAction ()
    {
        try {
            $retorno = array('error'=>false, 'msg'=>  Core_Registry::getMessage()->translate('MN013'));

            $params = $this->_getAllParams();
            $params['sqCaixa'] = $params['id'];

            $dto = \Core_Dto::factoryFromData($params, 'search');

            $this->getService()->closeBox($dto);

        } catch (\Exception $e) {

            $retorno['error']= true;
            $retorno['msg']= $e->getMessage();
        }

        echo $this->_helper->json($retorno);
    }

    public function modalListArtefatoArquivadoAction ()
    {
        $this->getHelper('layout')->disableLayout();
        $this->view->caixa = $this->getService()->find($this->_getParam('id'));
    }

    public function listArtefatoArquivadoAction ()
    {
        // desabilitando layout
        $this->getHelper('layout')->disableLayout();
        // retornando valor pra grid
        $this->view->grid = new Core_Grid(array(
            'columns' => array(
                 array('alias' => 'tx_classificacao')
                ,array('alias' => 'nu_artefato')
                ,array('alias' => 'no_pessoa_origem')
                ,array('alias' => 'tx_assunto')
                ,array('alias' => 'no_tipo_documento')
            )
        ));

        $params = $this->view->grid->mapper($this->_getAllParams());

        // tratando parametros
        $this->view->dto = Core_Dto::factoryFromData($params, 'search');
        // retornando valores pra view
        $this->view->result = $this->getService()->listGridArtefatoArquivado($this->view->dto);

    }

    public function listCaixaAbertaAction ()
    {
        // desabilitando layout
        $this->getHelper('layout')->disableLayout();
        // retornando valor pra grid
        $this->view->grid = new Core_Grid(array(
                 array('alias' => 'nuCaixa')
                ,array('alias' => 'txClassificacao')
                ,array('alias' => 'noUnidadeOrg')
                ,array('alias' => 'nuAno')
                ,array('alias' => 'dtCadastro')
                ,array('alias' => 'qtdeArtefatoCaixa')
            )
        );

        $params = $this->view->grid->mapper($this->_getAllParams());

        // tratando parametros
        $this->view->dto = Core_Dto::factoryFromData($params, 'search');
        // retornando valores pra view
        $this->view->result = $this->getService()->listGridCaixaAbertaPorClassificacao($this->view->dto);

    }



    /**
     * retorna dados da grid
     * @param array $params
     * @return array
     */
    public function getResultList($params)
    {
        $dtoSearch = \Core_Dto::factoryFromData($params, 'search');
        return $this->getService()->listGrid($dtoSearch);
    }

    /**
     * metodo que ordena grid
     * @return array
     */
    public function getConfigList()
    {
        $array = array(
            'columns' => array(
                array('alias' => 'ca.nuCaixa'),
                array('alias' => 'cl.txClassificacao'),
                array('alias' => 'u.noUnidadeOrg'),
                array('alias' => 'ca.nuAno'),
                array('alias' => 'ca.stFechamento'),
            )
        );

        return $array;
    }

    /**
     * Retorna as unidades organizacionais cadastrados em formato json
     * @return void
     */
    public function searchClassificacaoCaixaAction()
    {
        $result =  $this->getService()
                        ->searchClassificacaoCaixa($this->_getAllParams());
        $this->_helper->json($result);
    }

    /**
     * Retorna as unidades organizacionais cadastrados em formato json
     * @return void
     */
    public function searchUnidadeOrgAction()
    {
        $result =  $this->getService('VwUnidadeOrg')
                        ->searchUnidadesOrganizacionais($this->_getAllParams());
        $this->_helper->json($result);
    }

}