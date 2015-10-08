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
namespace br\gov\sial\core\mvcb\view;
use br\gov\sial\core\SIALAbstract,
    br\gov\sial\core\util\Location,
    br\gov\sial\core\mvcb\view\ViewAbstract,
    br\gov\sial\core\mvcb\view\exception\SlotException,
    br\gov\sial\core\exception\IllegalArgumentException;

/**
 * SIAL
 *
 * Permite o reuso de viewScript
 *
 * @package br.gov.sial.core.mvcb
 * @subpackage view
 * @name View
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class Slot extends SIALAbstract
{
    /**
     * @var string
     * */
    const T_SLOT_RECORDING = '--slot::recording--';

    /**
     * @var string
     * */
    const T_SLOT_PATTERN_PATH = '/^(?P<root>::|:)?+(?P<fof>:?[^$]+)$/';

    /**
     * @var string
     * */
    const T_SLOT_ROOT_PATH = '::';

    /**
     * @var string
     * */
    const T_SLOT_FOLDER_SEPARATOR = ':';

    /**
     * Define a profundidade do diretório dos viewScripts na estrutura do SIAL.
     * [module]/mvcb/view/scripts/[View::T_TYPE]
     *
     * @var integer
     * */
    const T_SLOT_DEEP_VIEWSCRIPT_FOLDER = 5;

    /**
     * @var string
     * */
    const T_SLOT_STRUCTURE_MVCB_VIEW_SCRIPT_SQUELETON = ':%s:mvcb:view:scripts:%s:';

    /**
     * @var string
     * */
    const T_SLOT_STR_EXTEND_UNAVAILABLE = 'Slot::extend(%s) arquivo informado inválido/indisponível';

    /**
     * @var string
     * */
    const T_SLOT_STR_SLOT_UNAVAILABLE = 'O "%s" é inválido/indisponível';

    /**
     * @var string[]
     * */
    private $_slots = array();

    /**
     * @var ViewAbstract
     * */
    private $_view;

    /**
     * @var string
     * */
    private $_type = NULL;

    /**
     * @var string
     * */
    private $_extension = NULL;

    /**
     * @var string[]
     * */
    private $_scriptPath;

    /**
     * @var string
     * */
    private $_applicationPath;

    /**
     * Construtor.
     *
     * @param ViewAbstract $view
     * */
    public function __construct (ViewAbstract $view)
    {
        # configura a pasta de templates da app
        $this->_applicationPath = $view->config()->get('app.namespace') . self::NAMESPACE_SEPARATOR;
        $this->_applicationPath = Location::realpathFromNamespace($this->_applicationPath);
        $this->_scriptPath      = $view->getScriptPaths();

        $this->_type      = $view::T_TYPE;
        $this->_extension = ('.' == substr($view::T_EXTENSION, 0, 1)) ? $view::T_EXTENSION : '.' . $view::T_EXTENSION;
        $this->_view      = $view;
    }

    /**
     * Resolução de contexto.
     *
     * @param string $key
     * @return mixed
     * */
    public function __get ($key)
    {
        if ('slot' == $key) {
            return $this;
        }

        return $this->_view->$key;
    }

    /**
     * Resolução de chamada de método.
     *
     * @param string $method
     * @param string[] $args
     * @return mixed the return value of the callback, or <b>FALSE</b> on error.
     * */
    public function __call ($method, $args)
    {
        return call_user_func_array(array(&$this->_view, $method), $args);
    }

    /**
     * Retorna TRUE se existir um slot com o identificador informado.
     *
     * @param string $key
     * @return boolean
     * */
    public function has ($key)
    {
        return isset($this->_slots[$key]);
    }

    /**
     * Retorna o slot informado.
     *
     * @param string $key
     * @return string
     * @throws IllegalArgumentException
     * */
    public function get ($key)
    {
        IllegalArgumentException::throwsExceptionIfParamIsNull($this->has($key), sprintf(self::T_SLOT_STR_SLOT_UNAVAILABLE, $key));
        return $this->_slots[$key];
    }

    /**
     * Inicia a criação de um slot
     *
     * @param string $key
     * @return Slot
     * */
    public function start ($key)
    {
        $this->_slots[self::T_SLOT_RECORDING] = $key;
        ob_start();
        return $this;
    }

    /**
     * Finaliza o slot em curso.
     *
     * @return Slot
     * */
    public function stop ()
    {
        $this->_slots[$this->_slots[self::T_SLOT_RECORDING]] = ob_get_contents();
        unset($this->_slots[self::T_SLOT_RECORDING]);
        ob_end_clean();
        return ;
    }

    /**
     * Extende o script em que este metodo for chamadado com os recursos de view script (VScript informado
     *
     * Para fins de localizacao:
     * :: = aponta para pasta application
     * :  = separador de caminho
     *
     * @code
     * <?php
     *  # Para exemplicar o uso desta metodo, consideremos a seguinte arvare:
     *  # appname/application/
     *  #                    - templates/TemplateMaster.[ext]
     *  #                    -       foo/mvcb/view/scripts/[View::T_TYPE]/Foo.[ext]
     *  #                    -       bar/mvcb/view/scripts/[View::T_TYPE]/Bar.[ext]
     *  #                    -    fooBar/mvcb/view/scripts/[View::T_TYPE]/FooBar.[ext]
     *  #
     *  # appname/application/templates/TemplateMaster.[ext]
     *  $this>extend('::templates:templateMaster');
     *
     * # appname/application/foo/mvcb/view/scripts/[View::T_TYPE]/Foo.[ext]
     * $this->extend(':foo:foo');
     *
     * # appname/application/bar/mvcb/view/scripts/[View::T_TYPE]/Bar.[ext]
     * $this->extend(':bar:bar');
     *
     * # appname/application/foobar/mvcb/view/scripts/[View::T_TYPE]/FooBar.[ext]
     * $this->extend(':fooBar:FooBar');
     *
     * # appname/application/bar/mvcb/view/scripts/[View::T_TYPE]/Bar.[ext]
     * $this->extend(':bar:Bar');
     *
     * # appname/application/[CURRENT_MODULE]/mvcb/view/scripts/[View::T_TYPE]/ViewScript.[ext]
     * $this->extend('ViewScript');
     * ?>
     * @endcode
     *
     * @param string $VScript
     * @return Engine
     * @throws SlotException
     * */
    public function extend ($VScript)
    {
        require $this->_preparePath($VScript);
    }

    /**
     * Recupera o diretório do script do slot.
     * @param string $curDir
     * @param integer $level
     * @return string
     * */
    public static function changeDir ($curDir, $level)
    {
        $level  = (integer) $level * self::T_SLOT_DEEP_VIEWSCRIPT_FOLDER;
        $curDir = explode(DIRECTORY_SEPARATOR, $curDir);

        while (0 < $level) {
            array_pop($curDir);
            --$level;
        }

        return implode(DIRECTORY_SEPARATOR, $curDir);
    }

    /**
     * @param string $VScript
     * @return string
     * @throws SlotException
     * */
    private function _preparePath ($VScript)
    {
        preg_match(self::T_SLOT_PATTERN_PATH, $VScript, $result);

        SlotException::throwsExceptionIfParamIsNull(!empty($result), sprintf(self::T_SLOT_STR_EXTEND_UNAVAILABLE, $VScript));

        $fof      = explode(self::T_SLOT_FOLDER_SEPARATOR, $result['fof']);
        $filename = array_pop($fof);
        $path     = '--no-file-or-directory--';
        $curDir   = end($this->_scriptPath);

        # $this>extend('::templates:templateMaster');
        if (self::T_SLOT_ROOT_PATH == $result['root']) {
            # root dir
            $path = $this->_applicationPath
                  . implode(DIRECTORY_SEPARATOR, $fof) . DIRECTORY_SEPARATOR
                  . ucfirst($filename)                 . $this->_extension;

        } elseif (self::T_SLOT_FOLDER_SEPARATOR == $result['root']) {
            # change dir
            $path = self::changeDir($curDir, count($fof))
                  . self::T_SLOT_STRUCTURE_MVCB_VIEW_SCRIPT_SQUELETON . $filename;
            $path = sprintf($path, current($fof), $this->_type) . $this->_extension;

        } else {
            # current dir
            $path = $curDir . DIRECTORY_SEPARATOR . ucfirst($result['fof']) . $this->_extension;
        }

        $path =  str_replace(self::T_SLOT_FOLDER_SEPARATOR, DIRECTORY_SEPARATOR, $path);

        SlotException::throwsExceptionIfParamIsNull(is_file($path), sprintf(self::T_SLOT_STR_EXTEND_UNAVAILABLE, $VScript));

        return $path;
    }
}