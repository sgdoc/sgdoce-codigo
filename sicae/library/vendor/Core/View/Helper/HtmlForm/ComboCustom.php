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
 * Componente de auxílio para formulários em HTML
 *
 * @package    Core
 * @subpackage View
 * @subpackage Helper
 * @subpackage HtmlForm
 * @name       ComboCustom
 * @category   View Helper
 */

class Core_View_Helper_HtmlForm_ComboCustom extends Zend_View_Helper_Abstract
{
    /**
     * @var array
     */
    protected $_prefix = array('' =>'label-select');

    /**
     * @see   Zend_View_Helper_FormSelect::formSelect()
     * @param string|array $name
     * @param mixed        $value
     * @param array|string $attr
     * @param array        $options
     */
    public function comboCustom($name, $value = null, array $attr = array(), array $options = array())
    {
        return $this->view->formSelect($name, $value, $attr, $this->_translate($this->getPrefix()) + $options);
    }

    /**
     * @param  string|array $prefix
     * @return array
     */
    public function setPrefix($prefix)
    {
        if (!is_array($prefix)) {
            $prefix = array($prefix);
        }

        $this->_prefix = $prefix;
        return $this;
    }

    /**
     * @param mixed $value
     * @param int|string $key
     * @return Core_View_Helper_HtmlForm_ComboCustom
     */
    public function addPrefix($value, $key = null)
    {
        if (null === $key) {
            $key = count($this->_prefix);
        }

        $this->_prefix[$key] = $value;

        return $this;
    }

    /**
     * @param  array $prefix
     * @return array
     */
    protected function _translate($prefix)
    {
        $data = array();
        foreach ($prefix as $key => $value) {
            $data[$key] = $this->view->translate($value);
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getPrefix()
    {
        return $this->_prefix;
    }
}
