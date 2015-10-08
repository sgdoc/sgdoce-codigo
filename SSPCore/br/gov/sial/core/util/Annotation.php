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
use br\gov\sial\core\SIALAbstract,
    br\gov\sial\core\util\AnnotationCache;

/**
 * SIAL
 *
 * manipulacao de anotacao
 *
 * @package br.gov.sial.core
 * @subpackage util
 * @author J. Augusto <augustowebd@gmail.com>
 * @todo retornar comments de elementos STATIC
 * */
class Annotation extends SIALAbstract
{
    /**
     * ReflectionClass da objeto passado no construtor
     *
     * @var \ReflectionClass
     * */
    protected $_reflection = NULL;

    /**
     * objeto da classe de cache
     *
     * @var AnnotationCache
     * */
    private $_cache = NULL;

    /**
     * Construtor.
     *
     * Recebe o objeto de uma subclasse de SIALAbstract e constroi o objeto ReflectionClass
     *
     * @param SIALAbstract $sial
     * */
    public function __construct(SIALAbstract $sial)
    {
        $this->_reflection = new \ReflectionClass($sial);
    }

    /**
     * @return AnnotationCache
     * */
    public function cache ()
    {
        if (NULL == $this->_cache) {
            $this->_cache = new AnnotationCache();
        }

        return $this->_cache;
    }

    /**
     * retorna o nome da classe
     *
     * @param Annotation | null
     * @return string
     * */
    public function getClassName (self $sial = NULL)
    {
        return $this->_reflection->getName();
    }

    /**
     * retorna o caminho do arquivo
     *
     * @return string
     * */
    public function getFileName ()
    {
        return $this->_reflection->getFileName();
    }

    /**
     * retorna o bloco de comentario da classe
     *
     * @access public
     * @return string
     * */
    public function getClassDoc ()
    {
       return $this->_reflection->getDocComment();
    }

    /**
     * retorna todos os blocos de comentarios dos atributos da classe
     *
     * @return string[]
     * */
    public function getAttrsDoc ()
    {
        $docs = array();
        $attrs = $this->_reflection->getProperties();

        foreach ($attrs as $attr) {
           array_push($docs, $attr->getDocComment());
        }

        return $docs;
    }

    /**
     * retorna true se existir um atributo com o nome informado
     *
     * @param string $attr
     * @param string|null $persistType
     * @param boolean
     * */
    public function hasAttr ($attr, $persistType = NULL)
    {
        $attrs = $this->getAttrsDoc();
        return isset($attrs[$attr]) && (NULL !== $persistType ? isset($attrs[$attr][$persistType]) : TRUE);
    }

    /**
     * retorna o nome do atributo do tipo de persistencia informada
     *
     * @param string $attr
     * @param string $persistType
     * @return string
     * */
    public function getAttr ($attr, $persistType)
    {
        $attrName = '';

        if ($this->hasAttr($attr, $persistType)) {
            $attrs = $this->getAttrsDoc();
            $attrName = $attrs[$attr][$persistType];
        }

        return $attrName;
    }

    /**
     * retorna todos os blocos de comentarios dos metodos da classe
     *
     * @access public
     * @name getMethodsDoc
     * @return string[]
     * */
    public function getMethodsDoc ()
    {
        $docs = array();
        $methods = $this->_reflection->getMethods();

        foreach ($methods as $method) {
           array_push($docs, $method->getDocComment());
        }

        return $docs;
    }
}