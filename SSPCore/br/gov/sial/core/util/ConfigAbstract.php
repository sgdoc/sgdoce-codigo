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
namespace br\gov\sial\core\util;
use br\gov\sial\core\SIALAbstract;
use br\gov\sial\core\util\stack\FIFO;
use br\gov\sial\core\exception\IllegalArgumentException;

/* classes necessarias para execuacao do SIALAplication */
require_once 'Config.php';
require_once 'stack' . DIRECTORY_SEPARATOR . 'FIFO.php';

/**
 * SIAL
 *
 * @package br.gov.imcbio.sial
 * @subpackage util
 * @author J. Augusto <augustowebd@gmail.com>
 * */
abstract class ConfigAbstract extends SIALAbstract
{
    /**
     * @var string
     * */
    const T_CONFIG_UNREADABLE_FILE = 'não é possível ler o arquivo informado';

    /**
     * encapsulamento do objeto Zend_Config
     *
     * @var Zend_Config
     * @access protected
     * */
    protected $_resource = NULL;

    /**
     * sessão de valores de configuração baseado no ambiente de execução
     * (produção, desenvolvimento, etc.)
     *
     * @var string
     * @access private
     * */
    private $_section = NULL;

    /**
     * @param mixed $config
     * @param string $section
     * @throws IllegalArgumentException
     * */
    public function __construct ($config, $section)
    {
        if (! $this->isSuported($config)) {
            throw IllegalArgumentException::invalidArgument($config);
        }

        $this->_section = $section;

        $this->load($config, $section);
    }

    /**
     * retorna true se existir uma elemento registrado com o indice informado
     *
     * @param string
     * @return bool
     * */
    public final function exists ($name)
    {
        return (bool) $this->_resource->__isset($name);
    }

    /**
     * obtem o valor de algum atributo de configuração
     *
     * @param string
     * @return string
     * */
    public final function get ($name)
    {
        $element  = NULL;
        $resource = $this->_resource;
        $stack    = FIFO::factory()->init(explode('.', $name));

        while (!$stack->isEmpty()) {
            $strElement = $stack->pop();
            $element = $resource->get($strElement);

            if (is_object($element) || $element instanceof \Zend_Config) {
               $element = $resource = new Config($element->toArray(), $this->_section );
            }
        }

        return $element;
    }

    /**
     * recupera a 'section' carregada
     * */
    public function section ()
    {
        return $this->_section;
    }

    /**
     * verifica se o $config é um arquivo suportado.
     *
     * @param string
     * @return bool
     * */
    public abstract function isSuported ($config);

    /**
     * carrega as configurações do arquivo $config para a sessão $section
     *
     * @param string[]
     * @param string
     * @throws IllegalArgumentException
     * */
    protected abstract function load ($config, $section);

    /**
     * converte os valores existentes no _resource em um array
     *
     * @return mixed[]
     * */
    public function toArray ()
    {
        return $this->_resource
                    ->toArray();
    }

    /**
     * retorna representacao JSon
     *
     * @return string[]
     * */
    public function toJSon()
    {
        return json_encode($this->toArray());
    }
}