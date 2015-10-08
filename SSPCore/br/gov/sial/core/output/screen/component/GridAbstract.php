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
use br\gov\sial\core\output\screen\IBuild,
    br\gov\sial\core\output\screen\ElementAbstract,
    br\gov\sial\core\output\screen\component\ComponentAbstract;

/**
 * SIAL
 *
 * @package br.gov.sial.core.output.screen
 * @subpackage component
 * @name GridAbstract
 * @author J. Augusto <augustowebd@gmail.com>
 * */
abstract class GridAbstract extends ComponentAbstract implements IBuild
{
    /**
     * @var string
     * */
    protected $_name;

    /**
     * agrupador de todos os elementos que compoe a grid
     *
     * @var ElementAbstract
     * */
    protected $_grid;

    /**
     * componente que armazena as linhas da grid
     *
     * @var ElementAbstract
     * */
    protected $_gridRowSet;

    /**
     * @var ColumnManager[]
     * */
    protected $_columns;

    /**
     * @var DataSource
     * */
    protected $_dataSource;

    /**
     * define se sera ou nao exibido a coluna enumerando os registro
     *
     * @param boolean
     * */
    protected $_hasCountLine = TRUE;

    /**
     * @var boolean
     * */
    protected $_canEdit = TRUE;

    /**
     * @var boolean
     * */
    protected $_canDelete = TRUE;

    /**
     * @var boolean
     * */
    protected $_canDetail = TRUE;

    /**
     * @var boolean
     * */
    protected $_canPrint = TRUE;

    /**
     * @var boolean
     * */
    protected $_canChangeStatus = TRUE;

    /**
     * @var string
     * */
    protected $_legendDetail = 'Visualizar registro';

    /**
     * @var string
     * */
    protected $_legendEdit = 'Alterar registro';

    /**
     * @var string
     * */
    protected $_legendDelete = 'Remover registro';

    /**
     * @var string
     * */
    protected $_legendPrint = 'Imprimir registro';

    /**
     * @var string
     * */
    protected $_legendChangeStatus = 'Ativar/Reativar registro';
    
    /**
     * @var array
     * */
    protected $_selectorCount = array(
                             array('value' => 10,  'text' => 10),
                             array('value' => 25,  'text' => 25),
                             array('value' => 50,  'text' => 50),
                             array('value' => 100, 'text' => 100));

    /**
     * @return string
     * */
    public function name ()
    {
        return $this->_name;
    }

    /**
     * @return ElementAbstract
     * */
    public function grid ()
    {
        return $this->_grid;
    }

    /**
     * Retorna o conjunto de linhas da grid
     * @return ElementAbstract
     * */
    public function rowSet ()
    {
        return $this->_gridRowSet;
    }

    /**
     * @return GridAbstract
     * */
    public abstract function header ();

    /**
     * @return GridAbstract
     * */
    public abstract function attachPagination ();

    /**
     * @return GridAbstract
     * */
    public abstract function detachPagination ();

    /**
     * @return GridAbstract
     * */
    public abstract function body ();

    /**
     * @return GridAbstract
     * */
    public function build ()
    {
        $this->header();
        $this->body();
        return $this;
    }

    /**
     * @return string
     * */
    public function render ()
    {
        return $this->_grid->render();
    }

    /**
     * @param boolean
     */
    public function setCanEdit ($option = TRUE)
    {
        $this->_canEdit = $option;
        return $this;
    }

    /**
     * @param boolean
     */
    public function setCanDetail ($option = TRUE)
    {
        $this->_canDetail = $option;
        return $this;
    }

    /**
     * @param boolean
     */
    public function setCanDelete ($option = TRUE)
    {
        $this->_canDelete = $option;
        return $this;
    }

    /**
     * @param boolean
     */
    public function setCanPrint ($option = TRUE)
    {
        $this->_canPrint = $option;
        return $this;
    }

    /**
     * @param boolean
     */
    public function setCanChangeStatus ($option = TRUE)
    {
        $this->_canChangeStatus = $option;
        return $this;
    }

    /**
     * @param boolean
     */
    public function setHasCountLine ($option = TRUE)
    {
        $this->_hasCountLine = $option;
        return $this;
    }

    /**
     * @param array
     */
    public function setRecordsPerPage ($options = NULL)
    {
        $this->_selectorCount = $options;
        return $this;
    }

    /**
     * @param string $name
     * @param string $columns
     * @param string $dataSource
     * @param string $type
     * @return ComponentAbstract
     * */
    public static function factory ($name, $columns, $dataSource = NULL, $type = 'html', $extraParam = NULL, $cdn = NULL)
    {
        $namespace = self::NSComponent('grid', $type);
        return new $namespace($name, $columns, $dataSource, $extraParam, $cdn);
    }
}