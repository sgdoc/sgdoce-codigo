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
 * @category   Filter
 * @package    Core
 * @subpackage Filter
 * @name       Date
 */
class Core_Filter_Date implements Zend_Filter_Interface
{
    /**
     * @var string
     */
    protected $_formatInput = 'dd/MM/yyyy';

    /**
     * @var string
     */
    protected $_formatOutput = 'yyyy-MM-dd';

    /**
     * @param  array|Zend_Config $options
     * @return void
     */
    public function __construct($options = NULL)
    {
        if (NULL !== $options) {
            $this->setOptions($options);
        }
    }

    /**
     * @param  array|Zend_Config $options
     * @return Core_Filter_Date fornece uma fluente interface
     */
    public function setOptions($options)
    {
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        }

        if (isset($options['formatInput'])) {
            $this->setFormatInput($options['formatInput']);
        }

        if (isset($options['formatOutput'])) {
            $this->setFormatOutput($options['formatOutput']);
        }

        return $this;
    }

    /**
     * @param  string $formatInput
     * @return Core_Filter_Date fornece uma fluente interface
     */
    public function setFormatInput($formatInput)
    {
        $this->_formatInput = (string) $formatInput;
        return $this;
    }

    /**
     * @return string
     */
    public function getFormatInput()
    {
        return $this->_formatInput;
    }

    /**
     * @param  string $formatOutput
     * @return Core_Filter_Date fornece uma fluente interface
     */
    public function setFormatOutput($formatOutput)
    {
        $this->_formatOutput = (string) $formatOutput;
        return $this;
    }

    /**
     * @return string
     */
    public function getFormatOutput()
    {
        return $this->_formatOutput;
    }

    /**
     * (non-PHPdoc)
     * @see library/Zend/Filter/Zend_Filter_Interface::filter()
     */
    public function filter($value)
    {
        $value = (string) $value;
        try {
            $date  = new Zend_Date($value, $this->getFormatInput());
            return $date->get($this->getFormatOutput());
        } catch (Zend_Date_Exception $e) {
            return $value;
        }
    }
}
