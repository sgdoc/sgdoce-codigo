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

use Core_Component_Treeview_Items as Items;
use Core_Component_Treeview_Item as Item;

/**
 * @package    Core
 * @subpackage Component
 * @subpackage Treeview
 * @name       Item
 * @category   Component
 */
class Core_Component_Treeview_Item
{
    /**
     * @var string
     */
    private $_text = '';
    /**
     * @var mixed
     */
    private $_value = '';
    /**
     * @var Core_Component_Treeview_Items
     */
    private $_children = null;

    /**
     * @param string $text
     * @param mixed $value
     * @param Core_Component_Treeview_Items $children
     */
    public function __construct ($text, $value = null, Items $children = null)
    {
        $this->_text = $text;
        $this->_value = $value ?: $text;
        $this->_children = $children ?: new Items();
    }
    
    /**
     * @return string
     */    
    public function getText ()
    {
        return $this->_text;
    }

    /**
     * @param string
     * @return Core_Component_Treeview_Item
     */
    public function setText ($text)
    {
        $this->_text = $text;
        return $this;
    }

    /**
     * @return mixed
     */        
    public function getValue ()
    {
        return $this->_value;
    }

    /**
     * @param mixed
     * @return Core_Component_Treeview_Item
     */
    public function setValue ($value)
    {
        $this->_value = $value;
        return $this;
    }

    /**
     * @return Core_Component_Treeview_Items
     */        
    public function getChildren ()
    {
        return $this->_children;
    }

    /**
     * @param Core_Component_Treeview_Items $children
     * @return Core_Component_Treeview_Item
     */
    public function setChildren (Items $children)
    {
        $this->_children = $children;
        return $this;
    }

    /**
     * @param Core_Component_Treeview_Item $child
     * @return void
     */
    public function addChild (Item $child)
    {
        return $this->_children->append($child);
    }

}
