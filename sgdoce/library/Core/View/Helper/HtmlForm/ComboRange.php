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
 * @name       ComboRange
 * @category   View Helper
 */
class Core_View_Helper_HtmlForm_ComboRange extends Core_View_Helper_HtmlForm_ComboCustom
{
    /**
     * @var integer
     */
    protected $_min;

    /**
     * @var integer
     */
    protected $_max;

    /**
     * @param string $name
     * @param mixed  $value
     * @param array  $attr
     */
    public function comboRange($name = null, $value = null, array $attr = array())
    {
        if (0 === func_num_args()) {
            return $this;
        }

        return $this->comboCustom(
            $name,
            $value,
            $attr,
            $this->range()
        );
    }

    /**
     * @return array
     */
    public function range()
    {
        $data = array();

        if (null === $this->getMin()) {
            throw new RuntimeException('Atribua o valor minimo.');
        }

        if (null === $this->getMax()) {
            throw new RuntimeException('Atribua o valor máximo.');
        }

        foreach (range($this->getMax(), $this->getMin()) as $value) {
            $data[$value] = $this->_rangeValue($value);
        }

        return $data;
    }

    /**
     * @param  integer $value
     * @return mixed
     */
    protected function _rangeValue($value)
    {
        return $value;
    }

    /**
     * @param  integer $min
     * @throws InvalidArgumentException
     * @return Core_View_Helper_SelectMonths fluent interface
     */
    public function setMin($min)
    {
        $max = $this->getMax();

        if ($min < 1) {
            throw new InvalidArgumentException('O valor mínimo deve ser maior que 1.');
        }

        $this->_min = (int) $min;
        return $this;
    }

    /**
     * @return integer
     */
    public function getMin()
    {
        return $this->_min;
    }

    /**
     * @param  integer $max
     * @throws InvalidArgumentException
     * @return Core_View_Helper_SelectMonths fluent interface
     */
    public function setMax($max)
    {
        $min = $this->getMin();

        if ($min >= $max) {
            throw new InvalidArgumentException('O valor mínimo deve ser maior que o valor máximo');
        }

        $this->_max = (int) $max;
        return $this;
    }

    /**
     * @return integer
     */
    public function getMax()
    {
        return $this->_max;
    }
}
