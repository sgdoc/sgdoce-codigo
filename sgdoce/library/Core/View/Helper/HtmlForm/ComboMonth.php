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
 * @name       ComboMonth
 * @category   View Helper
 */
class Core_View_Helper_HtmlForm_ComboMonth extends Core_View_Helper_HtmlForm_ComboRange
{
    /**
     * @var string|Zend_Locale
     */
    protected $_locale;

    /**
     * @var bool
     */
    protected $_fullName = true;

    /**
     * @var array
     */
    private $_months;

    /**
     * Start locale default
     *
     * @return void
     */
    public function __construct()
    {
        $this->setMin(1)
            ->setMax(12)
            ->setLocale(null);
    }

    /**
     * @param string $name
     * @param mixed  $value
     * @param array  $attr
     */
    public function comboMonth($name = null, $value = null, array $attr = array())
    {
        if (0 === func_num_args()) {
            return $this;
        }

        return $this->comboRange($name, $value, $attr);
    }

    /**
     * @inheritdoc
     */
    public function setMax($max)
    {
        if ($max > 12) {
            throw new InvalidArgumentException('Mês inválido.');
        }

        return parent::setMax($max);
    }

    /**
     * @param  string|null|Zend_Locale $locale
     * @return Core_View_Helper_FormSelectMonths fluent interface
     */
    public function setLocale($locale)
    {
        $this->_locale = Zend_Locale::findLocale($locale);
        return $this;
    }

    /**
     * @param  string|int $value
     * @return string
     */
    protected function _rangeValue($value)
    {
        if (null === $this->_months) {
            $this->_months = Core_Util_Locale::getMonths($this->getLocale(), $this->_resolveFlagFullName());
        }

        return ucfirst($this->_months[$value]);
    }

    /**
     * @return string|Zend_Locale
     */
    public function getLocale()
    {
        return $this->_locale;
    }

    /**
     * @param bool $flag
     */
    public function setFullName($flag)
    {
        $this->_fullName = (bool) $flag;
        return $this;
    }

    /**
     * @return bool
     */
    public function getFullName()
    {
        return $this->_fullName;
    }

    /**
     * @return string
     */
    private function _resolveFlagFullName()
    {
        return true === $this->_fullName
               ? 'wide'
               : 'abbreviated';
    }
}
