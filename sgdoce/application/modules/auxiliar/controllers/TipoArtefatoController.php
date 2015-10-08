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
 * Classe para Controller de Tipo Artefato
 *
 * @package    Auxiliar
 * @category   Controller
 * @name       Tipo Artefato
 * @version    1.0.0
 */

class Auxiliar_TipoArtefatoController extends \Core_Controller_Action_Crud
{
    /**
     * @var string
     * */
    protected $_service = 'TipoArtefato';

    public function listItemAction ()
    {
        $this->_helper->layout->disableLayout();

        $this->_helper->viewRenderer->setNoRender(TRUE);

        $data = array();

        foreach ($this->getService()->listItems() as $key => $value) {
            $data[] = array('value' => $key, 'text' => $value);
        }

        $this->_helper->json($data);
    }

    public function listItemsVinculoArtefatoAction ()
    {
        $this->_helper->layout->disableLayout();

        $this->_helper->viewRenderer->setNoRender(TRUE);

        $data = array();

        # filtra o tipo de retorno conforme o tipo do documento base
        # Atentar para o tipo do documento base
        #          BASE -> Recupera
        # Regra-1: DOC  +  DOC
        # Regra-2: PROC +  PROC
        # Regra-3: PROC +  DOC
        $tipoArtefatoFiltro = array(
            # DOC + DOC
            \Core_Configuration::getSgdoceTipoArtefatoDocumento() => array(
                \Core_Configuration::getSgdoceTipoArtefatoDocumento(),
            ),

            # PROC + PROC && PROC + DOC
            \Core_Configuration::getSgdoceTipoArtefatoProcesso() => array(
                \Core_Configuration::getSgdoceTipoArtefatoDocumento(),
                \Core_Configuration::getSgdoceTipoArtefatoProcesso()
            )
        );

        $arrTipoArtefatoAceito = $tipoArtefatoFiltro[$this->getRequest()->getParam('sqTipoArtefatoParent')];

        foreach ($this->getService()->listItemsVinculoArtefatoAction() as $key => $value) {
            if(in_array($key, $arrTipoArtefatoAceito)){
                $data[] = array('value' => $key, 'text' => $value);
            }
        }

        $this->_helper->json($data);
    }
}