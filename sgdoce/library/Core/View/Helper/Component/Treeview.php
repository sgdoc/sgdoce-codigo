<?php
/**
 * Copyright 2012 do ICMBio
 *
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
 * @package    Core
 * @subpackage View
 * @subpackage Helper
 * @subpackage Component
 * @name       Treeview
 * @category   View Helper
 */
class Core_View_Helper_Component_Treeview extends Zend_View_Helper_Abstract
{
    /**
     * @var \Core_Component_Treeview_Render
     */
    private $_maker = null;

    /**
     * @return \Core_Component_Component_Treeview
     */
    public function treeview ($data)
    {
        $this->_render = new \Core_Component_Treeview_Render($data);

        return $this;
    }

    /**
     * @return string
     */
    public function renderHTML ()
    {
        return $this->_render->toHTML();
    }

    /**
     * @return string
     */
    public function renderPlainText ()
    {
        return $this->_render->toPlainText();
    }

    /**
     * @return string
     */
    public function renderJSON ()
    {
        return $this->_render->toJSON();
    }
}
