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
    br\gov\sial\core\output\screen\component\ComponentAbstract;

/**
 * SIAL
 *
 * @package br.gov.sial.core.output.screen
 * @subpackage component
 * @name GridAbstract
 * @author J. Augusto <augustowebd@gmail.com>
 * */
abstract class AutoCompleteAbstract extends ComponentAbstract implements IBuild
{
    /**
     * casa o nome do atributo, exemplo: ':input[name=valor]' e ':input[name=value[subvalue]]'
     *
     * @var string
     * */
    const T_AUTO_COMPLETE_CREATE_PATTERN = '/:(?P<type>\w+)\[(?P<property>[\w+=\[?\w+\]?,?]+)*\]/';

    /**
     * @var string
     * */
    const T_AUTO_COMPLETE_ERROR_NAME_IS_MANDATORY = 'O nome do campo de entrada é requerido';

    /**
     * @var string
     * */
    const T_AUTO_COMPLETE_DEFAULT_MESSAGE = 'Sua pesquisa não retornou resultado.';

    /**
     * @var string
     * */
    protected $_defaultCdn;

    /**
     * @var ElementAbstract
     * */
    protected $_autoComplete;

    /**
     * parte do id informado pelo usuario
     *
     * @var string
     * */
    protected $_ident;

    /**
     * qtd de char necessario para disparar o evento de pesquisa
     *
     * @var integer
     * */
    protected $_minChar = 3;

    /**
     * se informado, cria um campo oculto com o valor recuperado do JS
     *
     * @var string
     * */
    protected $_hidden;

    /**
     * qtd de registros recuperados por pesquisa
     *
     * @var integer
     * */
    protected $_limit = 10;

    /**
     * @var stdClass
     * */
    protected $_remoteDataServerConnection;

    /**
     * @var string
     * */
    protected $_extraFilter = NULL;

    /**
     * @var ElementAbstract
     * */
    protected $_filter = ':input[name=autocomplete]';

    /**
     * @var string
     * */
    protected $_displayValue;

    /**
     * @var string
     * */
    protected $_defaultValue;

    /**
     * nome da propriedade, no resultado, que sera usado como valor
     * na cortina
     *
     * @var string
     * */
    protected $_dindex;

    /**
     * Mensagem padrão
     *
     * @var string
     * */
    protected $_message;

    /**
     * @var CSS class
     */
    protected $_inputClass;

    /**
     * @param stdClass $param
     * */
    public function __construct (\stdClass $param)
    {
        $this->setId           ($this->safeToggle($param, 'id', 'SAFAutoComplete-' . $this->genId()))
             ->setLimit        ($this->safeToggle($param, 'limit'))
             ->setFilter       ($this->safeToggle($param, 'filter'))
             ->setDefaultCdn   ($this->safeToggle($param, 'cdn'))
             ->setMinChar      ($this->safeToggle($param, 'minChar'))
             ->setHidden       ($this->safeToggle($param, 'hidden'))
             ->setInputClass   ($this->safeToggle($param, 'inputClass'))
             ->setMessage      ($this->safeToggle($param, 'message'))
             ->setDefaultValue ($this->safeToggle($param, 'defaultValue'))
             ->setDisplayValue ($this->safeToggle($param, 'displayValue'))

             # propriedade essencial
             ->setDindex    ($param->dindex)

             # propriedade essenvial
             # se esta propriedade nao tiver presente, um erro deve ser mostrado
             # uma vez que o sem esta o componente nao funciona corretamente
             ->setRemoteDataServerConnection($param->httpRequestConf);
    }

    /**
     * @param string $value
     * */
    public function setDefaultValue ($value)
    {
        $this->_defaultValue = trim($value);

        return $this;
    }

    /**
     * @param string $value
     * */
    public function setDisplayValue ($value)
    {
        $this->_displayValue = trim($value);

        return $this;
    }

    /**
     * define o nome da propriedade que sera recuperada na consulta
     * para ser usada como campo oculto, util qndo deseja-se enviar
     * o ID do valor selecionado
     *
     *
     * @param string $hidden
     * @return AutoComplete
     * */
    public function setHidden ($hidden)
    {
        $this->_hidden = trim($hidden);

        return $this;
    }

    /**
     * @param string $dindex
     * @return AutoCompleteAbstract
     * */
    public function setDindex ($dindex)
    {
        $this->_dindex = trim($dindex);

        return $this;
    }

    /**
     * @param string $domain
     * @return AutoCompleteAbstract
     * */
    public function setDefaultCdn ($cdn)
    {
        $this->_defaultCdn = ('/' == substr($cdn, -1) ? $cdn : $cdn . '/');

        return $this;
    }

    /**
     * informa o objeto que configura o servidor de dados
     *
     * @param stdClass
     * @return AutoCompleteAbstract
     * */
    public function setRemoteDataServerConnection (\stdClass $serverConf)
    {
        $this->_remoteDataServerConnection = $serverConf;

        return $this;
    }

    /**
     * @param string $id
     * @return AutoCompleteAbstract
     * */
    public function setId ($ident)
    {
        $this->_ident = $ident;

        return $this;
    }

    /**
     * @param integer $limit
     * @return AutoCompleteAbstract
     * */
    public function setLimit ($limit)
    {
        $this->_limit = (integer) $limit;

        return $this;
    }

    /**
     * @param integer $min
     * @return AutoCompleteAbstract
     * */
    public function setMinChar ($min)
    {
        $this->_minChar = (integer) $min;

        return $this;
    }

    /**
     * @param mixed $filter
     * @return AutoCompleteAbstract
     * */
    public function setFilter ($filter)
    {
        $inputs = explode(',', $filter);

        $this->_filter = current($inputs);

        $this->_extraFilter = str_replace("{$this->_filter}," , "", $filter);

        return $this;
    }

    /**
     * @param mixed $inputClass
     * @return AutoCompleteAbstract
     * */
    public function setInputClass ($inputClass)
    {
        $this->_inputClass = $inputClass;

        return $this;
    }

    /**
     * @param mixed $message
     * @return AutoCompleteAbstract
     * */
    public function setMessage ($message)
    {
        $this->_message = (count($message) > 0) ? $message : self::T_AUTO_COMPLETE_DEFAULT_MESSAGE;

        return $this;
    }

    /**
     * @return stdClass
     * */
    public function infoToJS ()
    {
        $param = new \stdClass;
        $param->httpRequestConf = $this->_remoteDataServerConnection;
        $param->minChar         = $this->_minChar;
        $param->limit           = $this->_limit;
        $param->dindex          = $this->_dindex;
        $param->message         = $this->_message;
        $param->displayValue    = $this->_displayValue;
        $param->defaultValue    = $this->_defaultValue;
        $param->extraFilter     = $this->_extraFilter;

        if (NULL !== $this->_hidden) {
            $param->hidden = $this->_hidden;
        }

        return $param;
    }

    /**
     * @param string $filter
     * */
    public abstract function createInputFilter ();

    /**
     * @param stdClass $param
     * @param string $type
     * @return ElementContainerAbstract
     * */
    public static function factory (\stdClass $param, $type)
    {
        $namespace = self::NSComponent('autoComplete', $type);

        return new $namespace($param);
    }
}