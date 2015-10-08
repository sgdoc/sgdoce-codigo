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
/**
 * SISICMBio
 *
 * Classe Controller Unidade Organizacional interna
 *
 * @package    Principal
 * @subpackage Controller
 * @version    1.0.0
 * @author     J. Augusto <augustowebd@gmail.com>
 */
class Principal_UnidadeOrganizacionalInternaController extends Core_Controller_Action_CrudDto
{
    protected $_service = 'UnidadeOrganizacionalInterna';

    public function formSearchDadosUnidadeAction ()
    {
        // $activeUorg  =
        // $this->getService()
        //      ->comboCategoriaUnidadeOrganizacao();

        // $this->view->data = new \stdClass;
        // $this->view->data->activeUorg = $activeUorg;

        $this->_helper
             ->layout()
             ->disableLayout();
    }

    public function formSearchCorrecaoCodigoAction ()
    {
        $this->_helper->layout()->disableLayout();
    }

    public function formSearchEnderecoAction ()
    {
        $this->_helper->layout()->disableLayout();
    }

    public function formSearchBaseLegalAction ()
    {
        $this->_helper->layout()->disableLayout();
    }
}