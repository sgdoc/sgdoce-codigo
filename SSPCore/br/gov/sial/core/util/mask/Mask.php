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
namespace br\gov\sial\core\util\mask;
use br\gov\sial\core\SIALAbstract,
    br\gov\sial\core\exception\IllegalArgumentException;

/**
 * SIAL
 *
 * @package br.gov.sial.core.util
 * @subpackage mask
 * @name Mask
 * @author J. Augusto <augustowebd@gmail.com>
 * */
abstract class Mask extends SIALAbstract
{
    /**
     * mascara
     *
     * @var string
     * */
    protected $_mask;

    /**
     * token de substituicao
     *
     * @var char
     * */
    protected $_tokenMask = '#';

    /**
     * contento que sera mascarado
     *
     * @var mixed
     * */
    protected $_content = NULL;

    /**
     * repositorio de mascaras
     *
     * @var string
     * */
    private $_repository = array();

    /**
     * construtor
     * */
    public function __construct ()
    {
        $this->registerRepo(__NAMESPACE__);
    }

    /**
     * aplica mascara
     *
     * @return string
     * */
    public function apply ()
    {
        $mask    = $this->_mask;
        $content = str_replace(' ', '', $this->_content);
        $length  = strlen($this->_content);
        for ($idx = 0; $idx < $length; $idx++) {
           $mask[strpos($mask, $this->_tokenMask)] = $this->_content[$idx];
        }
        return $mask;
    }

    /**
     * remove mascara
     *
     * @param mixed
     * @return string
     * */
    public abstract static function remove ($content);

    /**
     * registra namespace repositorio de mascaras
     *
     * @param string $namespace
     * @return br\gov\sial\core\util\mask\Mask
     * */
    public function registerRepo ($namespace)
    {
        $this->_repository[] = $namespace;
    }

    /**
     * retorna o namespace completo da mascara ou lanca um exception se a mesma nao for encontrada
     *
     * @param string $class
     * @return string
     * @throws br\gov\sial\core\exception\IllegalArgumentException
     * */
    private function _getNamespace ($class)
    {

    }
}