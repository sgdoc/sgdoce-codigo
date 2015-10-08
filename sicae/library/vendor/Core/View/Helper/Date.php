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
class Core_View_Helper_Date extends Zend_View_Helper_Abstract
{
    /**
     * @var Zend_Date
     */
    protected $_date;

    /**
     * @param string $date
     * @return Sgca_View_Helper_Date provides a fluent interface
     */
    public function date($date = null, $part = 'yyyy-MM-dd', $output = 'dd/MM/yyyy')
    {
        if ($date instanceof Zend_Date) {
            return $date->get($output);
        }

        if (null !== $date && null !== $part) {
            $this->setDate(new Zend_Date($date, $part));
        }

        if (null !== $output && null !== $date) {
            return $this->_date->get($output);
        }

        return $this;
    }

    /**
     * @param Zend_Date $date
     * @return Core_View_Helper_Date provides a fluent interface
     */
    public function setDate(Zend_Date $date)
    {
        $this->_date = $date;
        return $this;
    }

    /**
     * @return Zend_Date
     */
    public function getDate()
    {
        return $this->_date;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if ($this->_date instanceof Zend_Date) {
            return $this->_date->__toString();
        }

        return '';
    }

    /**
     * @param string $method
     * @param null|array $args
     * @return mixed
     * @throws Zend_View_Exception
     */
    public function __call($method, array $args = NULL)
    {
        if (method_exists($this->_date, $method)) {
            return call_user_func_array(array($this->_date, $method), $args);
        }

        throw new Zend_View_Exception('Method not exists!');
    }
}
