<?php
/*
 * Copyright 2011 ICMBio
 * Este arquivo é parte do programa SIAL
 * O SIAL é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos
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
namespace br\gov\sial\core\output\screen\component;
use br\gov\sial\core\output\screen\ElementAbstract;

/**
 * SIAL
 *
 * @package br.gov.sial.core.output.screen
 * @subpackage component
 * @name GridColumnAbstract
 * @author J. Augusto <augustowebd@gmail.com>
 * */
abstract class GridColumnAbstract extends ElementAbstract
{
    /**
     * @var ElementAbstract
     * */
    protected $_element;

    /**
     * nome de exibicao da coluna
     *
     * @var string
     * */
    protected $_label;

    /**
     * nome da entrada no dataSource
     *
     * @var string
     * */
    protected $_dindex;

    /**
     * @var string
     * */
    protected $_legend;

    /**
     * @var string
     * */
    protected $_CSSClass;

    /**
     * @var Function
     * */
    protected $_callbackServer = NULL;

    /**
     * @var string
     * */
    protected $_callbackClient = NULL;

    /**
     * @var boolean
     * */
    protected $_sorter = FALSE;

    /**
     * @param string[] $config
     * */
    public function __construct (array $config)
    {
        foreach ($config as $method => $val) {
            $setter = 'set' . ucfirst($method);

            if (method_exists($this, $setter)) {
                $this->$setter($val);
            }
        }
    }

    /**
     * @return GridColumn
     * */
    public abstract function build ();

    /**
     * @return string
     * */
    public function label ()
    {
        return $this->_label;
    }

    /**
     * @return string
     * */
    public function dindex ()
    {
        return $this->_dindex;
    }

    /**
     * @return string
     * */
    public function legend ()
    {
        return $this->_legend;
    }

    /**
     * @return strng
     * */
    public function CSSClass ()
    {
        return $this->_CSSClass;
    }

    /**
     * @return Function
     * */
    public function callbackServer ()
    {
        return $this->_callbackServer;
    }

    /**
     * @return string
     * */
    public function callbackClient ()
    {
        return $this->_callbackClient;
    }

    /**
     * @return boolean
     * */
    public function isSorter ()
    {
        return $this->_sorter;
    }

    /**
     * @param string $label
     * @return GridColumnAbstract
     * */
    public function setLabel ($label)
    {
        $this->_label = (string) $label;
        return $this;
    }

    /**
     * @param string $dindex
     * @return GridColumnAbstract
     * */
    public function setDIndex ($dindex)
    {
        $this->_dindex = (string) $dindex;
        return $this;
    }

    /**
     * @param string $legend
     * @return GridColumnAbstract
     * */
    public function setLegend ($legend)
    {
        $this->_legend = $legend;
        return $this;
    }

    /**
     * @param string $class
     * @param return GridColumnAbstract
     * */
    public function setCSSClass ($class)
    {
        $this->_CSSClass = $class;
        return $this;
    }

    /**
     * @param Function $callback
     * @return GridColumnAbstract
     * */
    public function setCallbackServer ($callback)
    {
        $this->_callbackServer = $callback;
        return $this;
    }

    /**
     * @param Function $callback
     * @return GridColumnAbstract
     * */
    public function setCallbackClient ($callback)
    {
        $this->_callbackClient = $callback;
        return $this;
    }

    /**
     * @param boolean $sorter
     * @return GridColumnAbstract
     * */
    public function setSorter ($sorter)
    {
        $this->_sorter = (boolean) $sorter;
        return $this;
    }
}