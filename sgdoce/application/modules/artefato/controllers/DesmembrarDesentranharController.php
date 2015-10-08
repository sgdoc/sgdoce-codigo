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
 * Classe para Controller de Processo Desemembramento.
 *
 * @package  Artefato
 * @category Controller
 * @name     ProcessoDesmembrar
 * @version  1.0.0
 */
class Artefato_DesmembrarDesentranharController extends Artefato_ArtefatoController
{
    /**
     * Serviço
     * @var string
     */
    protected $_service = 'DesmembramentoDesentranhamento';

    /**
     * @var array
     */
    protected $_optionsDtoEntity = array(
        'entity' => 'Sgdoce\Model\Entity\DesmembramentoDesentranhamento',
        'mapping' => array(
            'sqArtefato'           => 'Sgdoce\Model\Entity\ArtefatoProcesso',
            'sqArtefatoDestino'    => 'Sgdoce\Model\Entity\ArtefatoProcesso',
            'sqUnidadeSolicitacao' => 'Sgdoce\Model\Entity\VwUnidadeOrg',
            'sqUnidadeOrg'         => 'Sgdoce\Model\Entity\VwUnidadeOrg',
            'sqPessoaAssinatura'   => 'Sgdoce\Model\Entity\VwPessoa',
            'sqPessoa'             => 'Sgdoce\Model\Entity\VwPessoa',
            'sqCargo'              => 'Sgdoce\Model\Entity\VwCargo',
        )
    );

    /**
     * (non-PHPdoc)
     * @see Core_Controller_Action_Crud::combo()
     */
    public function combos()
    {
        $this->view->arrCargos = $this->getService('VwCargo')->comboCargo();
    }

    /**
     * (non-PHPdoc)
     * @see Core_Controller_Action_Crud::indexAction()
     */
    public function indexAction()
    {
    }

    /**
     * (non-PHPdoc)
     * @see Core_Controller_Action_Crud::createAction()
     */
    public function desentranharAction()
    {
        $this->_helper->layout->disableLayout();
    	// VERIFICA SE FOI ENVIADO SQ ARTEFATO PARA O DESMEMBRAMENTO.
    	$sqArtefato = $this->_getParam('id', false);

    	$ArtefatoProcesso = $this->getService('ArtefatoProcesso')
                                 ->isProcesso($sqArtefato);

        $this->view->ArtefatoProcesso    = $ArtefatoProcesso;
        $this->_formatarNrProcesso($ArtefatoProcesso->getSqArtefato());

        parent::createAction();

        $this->combos();
    }

    /**
     * @return void
     */
    public function desmembrarAction()
    {
        $this->_helper->layout->disableLayout();
    	// VERIFICA SE FOI ENVIADO SQ ARTEFATO PARA O DESMEMBRAMENTO.
    	$sqArtefato = $this->_getParam('id', false);

    	$ArtefatoProcesso = $this->getService('ArtefatoProcesso')
                                 ->isProcesso($sqArtefato);

        $this->view->ArtefatoProcesso = $ArtefatoProcesso;
        $this->_formatarNrProcesso($ArtefatoProcesso->getSqArtefato());

        parent::createAction();

        $this->combos();
    }

    private function _formatarNrProcesso(Sgdoce\Model\Entity\Artefato $entityArtefato)
    {
        $this->view->nuArtefatoFormatado = $this->getService('Processo')
                    ->formataProcessoAmbitoFederal($entityArtefato);
        return $this->view->nuArtefatoFormatado;
    }

    /**
     * (non-PHPdoc)
     * @see Core_Controller_Action_CrudDto::editAction()
     */
    public function editAction()
    {
    	$sqDesmemDesentra = $this->_getParam('id', false);
    	if( !$sqDesmemDesentra ) {  }

    	parent::editAction();

        if( $this->view->data->getStDesmembramento() ) {
            $form = "desmembrar";
        } else {
            $form = "desentranhar";
        }

        $this->combos();

        $objDtoSearch = Core_Dto::factoryFromData(array(
        		'sqProcessoDesmembramento' => $this->view->data->getSqProcessoDesmembramento()),
        		'search');

        $arrDados = $this->getService('ProcessoDesmembramentoDesentranhamento')->getData($objDtoSearch);

        $this->render($form);
    }

    /**
     * Termo de Desmembramento.
     *
     * @return void
     */
    public function termoAction()
    {
    	$sqDesmemDesentra = $this->_getParam('id', false);
    	if( !$sqDesmemDesentra ) { $this->_redirect(); }

    	$objEntity = $this->getService()->find($sqDesmemDesentra);

        $termo = null;

        $nuArtefatoDestino = null;
        if($objEntity->getStDesmembramento()){
            $termo = 'termo-desmembramento';
            $nuArtefatoDestino = $this->_formatarNrProcesso($objEntity->getSqArtefatoDestino()->getSqArtefato());
        } else {
            $termo = 'termo-desentranhamento';
        }

        $sufix = str_shuffle($sqDesmemDesentra.time());
    	$fname = sprintf($termo . '-%d.pdf', $sufix);
        $path  = APPLICATION_PATH . '/modules/artefato/views/scripts/desmembrar-desentranhar';
        $noUnidadeOrg = $this->_helper->changeCase->toupper(\Core_Integration_Sica_User::getUserUnitName());

        $params = array(
            'objDesmemDesentra' => $objEntity,
            'nuArtefatoDestino' => $nuArtefatoDestino,
            'noUnidadeOrg' => $noUnidadeOrg
        );

        $helper = $this->_helper
                    ->termo
                    ->setParams($params)
                    ->setDateFormatPrint("dd 'de' MMMM 'de' yyyy");

        if($objEntity->getSqArtefato()->getCoAmbitoProcesso() == 'F' &&
                strlen($objEntity->getSqArtefato()->getSqArtefato()->getNuArtefato()) == 17 ){
                $helper->setNuArtefatoMask('99999.999999/9999-99');
        }

        return $helper->gerar($termo, $fname, $path);
    }

    /**
     * Retorna as unidades organizacionais cadastrados em formato json
     *
     * @return void
     */
    public function searchUnidadeOrgAction()
    {
        $result =  $this->getService('VwUnidadeOrg')
                        ->searchUnidadesOrganizacionais($this->_getAllParams());
        $this->_helper->json($result);
    }

    /**
     * (non-PHPdoc)
     * @see Core_Controller_Action_CrudDto::saveAction()
     */
    public function saveAction()
    {
    	$this->_request->setPost('sqPessoa', Core_Integration_Sica_User::getPersonId());
    	$this->_request->setPost('sqUnidadeOrg', Core_Integration_Sica_User::getUserUnit());

    	if (!$this->_request->isPost()) {
            throw new RuntimeException('A requisição deve ser POST');
    	}

    	$objEntity = $this->_save();
    	$this->getService()->finish();
    	$this->_addMessageSave();
        $url = "/artefato/desmembrar-desentranhar/termo/id/" . $objEntity->getSqDesmembramentoDesentra();
    	$this->_redirect( $url );
    }

    /**
     * Retorna usuários assinantes
     *
     * @return void
     */
    public function searchPessoaAssinaturaAction()
    {
    	$nuSqUnidadeOrg = Core_Integration_Sica_User::getUserUnit();
    	$query          = $this->getRequest()->getParam('query', '');
    	$objDtoSearch   = Core_Dto::factoryFromData(array('sqUnidadeOrg' => $nuSqUnidadeOrg, 'query' => $query), 'search');
    	$arrPessoas     =  $this->getService('VwPessoa')->searchPessoaUnidade($objDtoSearch);

    	$this->_helper->json($arrPessoas);
    }

    /**
     * Retorna usuários assinantes
     *
     * @return void
     */
    public function searchArtefatoAreaTrabalhoAction()
    {
    	$nuSqPessoa   = Core_Integration_Sica_User::getPersonId();
    	$query        = $this->getRequest()->getParam('query', '');
    	$objDtoSearch = Core_Dto::factoryFromData(array('sqPessoa' => $nuSqPessoa, 'query' => $query), 'search');
    	$arrPessoas   =  $this->getService('VwPessoa')->searchPessoaUnidade($objDtoSearch);

    	$this->_helper->json($arrPessoas);
    }

    /**
     * @return void
     */
    public function searchArtefatoDestinoAction()
    {
    	$sqArtefato = $this->getRequest()->getParam('extraParam');
        
        $objZFAlpha = new \Zend_Filter_Alnum(true);
        $query      = $objZFAlpha->filter($this->getRequest()->getParam('query'));

        $criteria = array(
            'sqTipoArtefato'       => \Core_Configuration::getSgdoceTipoArtefatoProcesso(),
            'sqPessoaRecebimento'  => \Core_Integration_Sica_User::getPersonId(),
            'sqUnidadeRecebimento' => \Core_Integration_Sica_User::getUserUnit(),
            'nuArtefato'           => $query,
            'sqArtefato'           => $sqArtefato,
        );

        $listInMyDashboard = $this->getService('ProcessoEletronico')->searchInMyDashboard( $criteria );

        $this->_helper->json($listInMyDashboard);
    }

}