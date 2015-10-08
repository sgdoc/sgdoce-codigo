<?php

require_once __DIR__ . "/ArtefatoController.php";
/**
 * Copyright 2012 do ICMBio
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro
 * dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre
 * (FSF); na versão 2 da Licença.
 *
 * Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA;
 * sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR.
 * Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
 * junto com este programa, se não, acesse o Portal do Software Público Brasileiro no
 * endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
 */

/**
 * SISICMBio
 *
 * Classe Controller de Visualizar Caixa de Minuta
 *
 * @package      Artefato
 * @subpackage   Controller
 * @name         VisualizarArtefato
 * @version      1.0.0
 * @since        2013-04-26
 */
class Artefato_VisualizarArtefatoController extends Artefato_ArtefatoController
{

    /**
     * caminho da logo a partir da pasta public/
     *
     * @var string
     * */
    const T_VISUALIZAR_ARTEFATO_IMG_LOGO_PATH = '/img/marcaICMBio.png';

    /**
     * quantidade limite de caracteres na grid de despachos
     *
     * @var integer
     * */
    const T_ARTEFATO_DESPACHO_INTERLOCUTORIO_LIMIT_COMMENT_GRID = 50;

    /**
     * quantidade limite de caracteres na grid de comentário
     *
     * @var integer
     * */
    const T_ARTEFATO_COMENTARIO_LIMIT_COMMENT_GRID = 250;

    /**
     * Variavel para receber o nome da service
     * @var    string
     * @access protected
     * @name   $_service
     */
    protected $_service = 'Artefato';

    /**
     * Método padrão
     */
    public function indexAction ()
    {
        $this->_helper->layout->setLayout('modal');

        $data['sqArtefato'] = $this->_getParam("sqArtefato");

        $dtoArtefato = Core_Dto::factoryFromData($data, 'search');
        $this->view->entityArtefato = $this->getService()->find($data['sqArtefato']);

        $artefatoInconsistente = $this->getService()->isInconsistent($this->view->entityArtefato, FALSE, TRUE);

        if ($artefatoInconsistente) {
            $noTipoArtefato = mb_strtolower($this->view->entityArtefato->getSqTipoArtefatoAssunto()->getSqTipoArtefato()->getNoTipoArtefato(), 'utf-8');
            $this->getMessaging()->addAlertMessage("Os dados deste {$noTipoArtefato} estão inconsistentes. A correção deve ser feita pelo usuário que estiver com o {$noTipoArtefato} em sua Área de Trabalho.", 'User');
            $this->getMessaging()->dispatchPackets();
        }

        $sqTipoArtefato = $this->view->entityArtefato->getSqTipoArtefatoAssunto()->getSqTipoArtefato()->getSqTipoArtefato();

        //monta a arvore de vinculos
        $this->view->vinculos = $this->getService('ArtefatoVinculo')->mostarArvore((integer) $data['sqArtefato']);
        $this->view->urlBack = str_replace(".", "/", $this->getRequest()->getParam('back', ""));
        $this->view->dadosOrigem = $this->returnDadosOrigem($dtoArtefato);
        $this->view->dadosDestino = $this->returnDadosDestino($dtoArtefato);

        if ($sqTipoArtefato == \Core_Configuration::getSgdoceTipoArtefatoProcesso()) {
            $criteria = array('sqArtefatoPai' => $dtoArtefato->getSqArtefato(),
                'sqTipoVinculoArtefato' => \Core_Configuration::getSgdoceTipoVinculoArtefatoAutuacao());

            $this->view->autuacao = $this->getService('ArtefatoVinculo')->findBy($criteria);
            $this->view->dadosInteressado = $this->getService('PessoaInterassadaArtefato')->getPessoaInteressadaArtefato($dtoArtefato);
        }

        $this->view->nacionalidadeDestino = NULL;
        if ($this->view->dadosDestino) {
            $sqTipoPessoa = $this->view->dadosDestino[0]->getSqPessoaSgdoce()->getSqTipoPessoa()->getSqTipoPessoa();
            if ($sqTipoPessoa == \Core_Configuration::getSgdoceTipoPessoaPessoaFisica()) {
                $this->view->nacionalidadeDestino = $this->returnNacionalidade($this->view->dadosDestino);
            }
        }
        $this->view->nacionalidadeOrigem = NULL;
        if ($this->view->dadosOrigem) {
            $sqTipoPessoa = $this->view->dadosOrigem[0]->getSqPessoaSgdoce()->getSqTipoPessoa()->getSqTipoPessoa();
            if ($sqTipoPessoa == \Core_Configuration::getSgdoceTipoPessoaPessoaFisica()) {
                $this->view->nacionalidadeOrigem = $this->returnNacionalidade($this->view->dadosOrigem);
            }
        }
        if ($this->view->urlBack == "") {
            $this->view->urlBack = "javascript:window.close();";
        }

        // REGISTRO DE VISUALIZAÇÃO DE ARTEFATO. #HistoricoArtefato::save();
//            $strMessage = sprintf($this->getServiceLocator()
//                            ->getService('HistoricoArtefato')
//                            ->getMessage('MH003'), Core_Integration_Sica_User::getUserName());
//
//            $this->_salvarHistoricoArtefato($dtoArtefato->getSqArtefato(),
//                   \Core_Configuration::getSgdoceSqOcorrenciaVisualizar(),
//                   $strMessage);
    }

    /**
     * Método que retorna se a nacionalidade e Brasileira ou nao
     */
    public function returnNacionalidade ($dados)
    {
        $sqPessoa = $dados[0]->getSqPessoaSgdoce()->getSqPessoaCorporativo()->getSqPessoa();
        $pessoaFisica = $this->getService('Pessoa')->findByPessoaFisica(array('sqPessoaFisica' => $sqPessoa));
        if ($pessoaFisica) {
            $sqNacionalidade = $pessoaFisica[0]->getSqNacionalidade() ? $pessoaFisica[0]->getSqNacionalidade()->getSqPais() : NULL;

            if ($sqNacionalidade == \Sgdoce_Constants::NACIONALIDADE_BRASILEIRA || $sqNacionalidade == NULL) {
                return \Sgdoce_Constants::NACIONALIDADE_BRASILEIRA;
            }
            return \Sgdoce_Constants::NACIONALIDADE_ESTRANGEIRA;
        }
    }

    public function getPersonId ()
    {
        return \Core_Integration_Sica_User::getPersonId();
    }

    public function getUser ()
    {
        return \Core_Integration_Sica_User::get();
    }

    public function getNuNup ()
    {
        $dtoOrigem = Core_Dto::factoryFromData(array('sqProfissional' => $this->getPersonId()), 'search');
        $unidadeOrg = $this->getService('Dossie')->unidadeOrigemPessoa($dtoOrigem);

        if ($unidadeOrg) {
            $unidadeExercicio = $unidadeOrg->getSqUnidadeExercicio();
        }

        if (!empty($unidadeExercicio)) {
            return $unidadeExercicio->getNuNup();
        }
    }

    /**
     * Metodo que retorna os dados do Artefato
     * @param unknown_type $dtoArtefato
     */
    public function returnDadosArtefato ($dtoArtefato)
    {
        return $this->getService()->findVisualizarArtefato($dtoArtefato);
    }

    /**
     * Método que retorna os dados da Origem
     * @param unknown_type $dtoArtefato
     */
    public function returnDadosOrigem ($dtoArtefato)
    {
        return self::_dadosPessoaDocumento($dtoArtefato, \Core_Configuration::getSgdocePessoaFuncaoOrigem());
    }

    /**
     * Método que retorna os dados do Destino
     * @param unknown_type $dtoArtefato
     */
    public function returnDadosDestino ($dtoArtefato)
    {
        return self::_dadosPessoaDocumento($dtoArtefato, \Core_Configuration::getSgdocePessoaFuncaoDestinatario());
    }

    /**
     * Método que retonra os dados da Assinatura
     * @param unknown_type $dtoArtefato
     */
    public function returnDadosAssinatura ($dtoArtefato)
    {
        return $this->getService()->findAssinaturaArtefato($dtoArtefato);
    }

    /**
     * Método que lista o Grid de Interessados
     */
    public function listVisualizarInteressadoAction ()
    {
        $this->_helper->layout->disableLayout();
        $configArray = array(
            array('alias' => 'ps.noPessoa'),
            array('alias' => 'ps.nuCpfCnpjPassaporte')
        );
        $this->view->grid = new Core_Grid($configArray);
        $params = $this->view->grid->mapper($this->_getAllParams());
        $this->view->dto = Core_Dto::factoryFromData($params, 'search');
        $this->view->result = $this->getService('PessoaInterassadaArtefato')
                ->listGridInteressadosArtefato($this->view->dto);
    }

    /**
     * Método que lista o Grid de Referencias
     */
    public function listVisualizarReferenciaAction ()
    {
        $this->_helper->layout->disableLayout();

        $configArray = array('columns' => array(
                array('alias' => 'no_tipo_artefato'),
                array('alias' => 'nu_artefato'),
                array('alias' => 'nu_digital'),
                array('alias' => 'dt_vinculo')
            )
        );
        $this->view->grid = new Core_Grid($configArray);
        $params = $this->view->grid->mapper($this->_getAllParams());
        $this->view->dto = Core_Dto::factoryFromData($params, 'search');
        $this->view->result = $this->getService('ArtefatoVinculo')
                ->listGridVinculacaoReferencia($this->view->dto);
    }

    /**
     * Método que lista o Grid de Historico
     */
    public function listVisualizarHistoricoAction ()
    {
        $this->_helper->layout->disableLayout();

        $configArray = array(
            array('alias' => 'no_pessoa'),
            array('alias' => 'no_unidade_org'),
            array('alias' => 'tx_operacao'),
            array('alias' => 'dt_operacao')
        );

        $this->view->grid = new Core_Grid($configArray);
        $params = $this->view->grid->mapper($this->_getAllParams());
        $this->view->dto = Core_Dto::factoryFromData($params, 'search');
        $this->view->result = $this->getService('Artefato')->listGridHistorico($this->view->dto);
    }

    /**
     * Método que lista o Grid de Historico
     */
    public function listVisualizarHistoricoFisicoAction ()
    {
        $this->_helper->layout->disableLayout();

        $configArray = array(
            array('alias' => 'no_pessoa'),
            array('alias' => 'no_unidade_org'),
            array('alias' => 'tx_operacao'),
            array('alias' => 'dt_operacao')
        );

        $this->view->grid = new Core_Grid($configArray);
        $params = $this->view->grid->mapper($this->_getAllParams());
        $this->view->dto = Core_Dto::factoryFromData($params, 'search');
        $this->view->result = $this->getService('Artefato')->listGridHistoricoFisico($this->view->dto);

        $this->render('list-visualizar-historico');
    }

    /**
     * Método que lista o Grid de Volume
     */
    public function listVisualizarVolumeAction ()
    {
        $this->_helper->layout->disableLayout();

        $configArray = array(
            array('alias' => 'pv.nuVolume'),
            array('alias' => 'pv.nuFolhaInicial'),
            array('alias' => 'pv.nuFolhaFinal'),
        );

        $this->view->grid = new Core_Grid($configArray);
        $params = $this->view->grid->mapper($this->_getAllParams());
        $this->view->dto = Core_Dto::factoryFromData($params, 'search');
        $this->view->result = $this->getService('ProcessoVolume')->listGrid($this->view->dto);
    }

    /**
     * Metódo que retorna a lista com os temas tratados.
     * @return array
     */
    public function listTemaTratadoAction ()
    {
        $params = $this->_getAllParams();

        $this->_helper->layout->disableLayout();

        $params['sqPessoaFuncao'] = \Core_Configuration::getSgdocePessoaFuncaoDestinatario();
        $params['sqTipoPessoa'] = \Core_Configuration::getSgdoceTipoPessoaPessoaFisica();

        $configArray = array('columns' => array(
                array('alias' => 'vi.nome'),
                array('alias' => 'vi.tipo')));

        $this->view->grid = new Core_Grid($configArray);
        $params = $this->view->grid->mapper($params);
        $this->view->dto = Core_Dto::factoryFromData($params, 'search');
        $this->view->result = $this->getResultListTemaTratado($this->view->dto);
    }

    /**
     * Retorna dados da grid Tema Tratado
     * @param \Core_Dto_Search $dtoSearch
     */
    public function getResultListTemaTratado (\Core_Dto_Search $dtoSearch)
    {
        return $this->getService('ProcessoEletronico')->listGridTemaTratado($dtoSearch);
    }

    /**
     * SALVAR HISTORICO VISUALIZAR ARTEFATO.
     *
     * @param integer $sqArtefato
     * @param integer $sqOcorrencia
     * @param string $strMessage
     *
     * @return Sgdoce\Model\Entity\HistoricoArtefato
     */
    protected function _salvarHistoricoArtefato ($sqArtefato, $sqOcorrencia, $strMessage)
    {
        $this->getService('HistoricoArtefato')->registrar($sqArtefato, $sqOcorrencia, $strMessage);
    }

    public function printHistoricAction ()
    {
        $this->_helper->layout()->disableLayout();
        $sqArtefato = (integer) $this->getRequest()->getParam('sqArtefato');

        if (!$sqArtefato) {
            throw new Exception(\Core_Registry::getMessage()->translate('MN132'));
        }
        $params = array('sqArtefato' => $sqArtefato);
        $dto = Core_Dto::factoryFromData($params, 'search');

        $data = $this->getService('Artefato')->getHistoricoByArtefato($dto);

        $options = array(
            'fname' => sprintf('Historico-' . date('YmdHis') . '-%d.pdf', $sqArtefato),
            'path' => APPLICATION_PATH . '/modules/artefato/views/scripts/visualizar-artefato'
        );

        $logo = current(explode('application', __FILE__))
                . 'public' . DIRECTORY_SEPARATOR
                . ltrim(self::T_VISUALIZAR_ARTEFATO_IMG_LOGO_PATH, DIRECTORY_SEPARATOR);

        \Core_Doc_Factory::setFilePath($options['path']);

        \Core_Doc_Factory::download(
                'print-historic', /* .phtml */ array(
            'data' => $data,
            'logo' => $logo,
            'entityArtefato' => $this->getService('Artefato')->find($sqArtefato),
                ), $options['fname'], 'Pdf', 'L'
        );
    }

    /**
     * Método que lista o Grid de Despachos
     */
    public function listVisualizarDespachoAction ()
    {
        $this->_helper->layout->disableLayout();

        $configArray = array('columns' => array(
                array('alias' => 'sqDespachoInterlocutorio'),
                array('alias' => 'dtDespacho'),
                array('alias' => 'txDespacho'),
                array('alias' => 'noAssinatura'),
                array('alias' => 'noOrigem'),
                array('alias' => 'noEncaminhado')
            )
        );
        $this->view->grid = new Core_Grid($configArray);
        $params = $this->view->grid->mapper($this->_getAllParams());
        $params['order'] = array();
        $this->view->dto = Core_Dto::factoryFromData($params, 'search');
        $this->view->result = $this->getService('DespachoInterlocutorio')
                ->getGrid($this->view->dto);
        $this->view->limitComment = self::T_ARTEFATO_DESPACHO_INTERLOCUTORIO_LIMIT_COMMENT_GRID;
    }

    /**
     * Método que lista o Grid de Comentários
     */
    public function listVisualizarComentarioAction ()
    {
        $this->_helper->layout->disableLayout();

        $configArray = array('columns' => array(
                array('alias' => 'dtComentario'),
                array('alias' => 'txComentario'),
                array('alias' => 'noPessoa'),
                array('alias' => 'noUnidadeOrg')
            )
        );
        $this->view->grid = new Core_Grid($configArray);
        $params = $this->view->grid->mapper($this->_getAllParams());
        $params['order'] = array();
        $this->view->dto = Core_Dto::factoryFromData($params, 'search');
        $this->view->result = $this->getService('Comentario')
                ->listGrid($this->view->dto);
        $this->view->limitComment = self::T_ARTEFATO_COMENTARIO_LIMIT_COMMENT_GRID;
    }

}
