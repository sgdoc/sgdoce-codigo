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
namespace br\gov\sial\core\persist\util;
use br\gov\sial\core\valueObject\ValueObjectAbstract,
    br\gov\sial\core\persist\exception\PersistException,
    br\gov\sial\core\util\Annotation as ParentAnnotation;

/**
 * SIAL
 *
 * @package br.gov.sial.core.persist
 * @subpackage util
 * @name Annotation
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class Annotation extends ParentAnnotation
{
    /**
     * @var string
     * */
    const ANNONTATION_COMMENT_NOT_FOUND = 'Comentário da classe %s não foi encontrada.';

    /**
     * @var string
     * */
    const ANNONTATION_ATTR_NOT_FOUND = 'Definição dos atributos de %s não foram encontradas.';

    /**
     * @var string
     * */
    const ANNONTATION_ATTR_NAME_NOT_FOUND = 'Falta o @name de um dos tributos na class %s.';

    /**
     * Recupera as definicoes da entidade e do schema
     *
     * @var string
     * */
    const ANNONTATION_ER_DEF_ENTITY_AND_SCHEMA = '!(?:@(?P<classDef>[^\W]+)\s*\([^\)]+\))!';

    /**
     * Construtor.
     *
     * @param ValueObjectAbstract $valueObject
     * */
    public function __construct (ValueObjectAbstract $valueObject)
    {
        parent::__construct($valueObject);
        $annon = $this->getClassDoc();
    }

    /**
     * Grava a anotação no cache
     *
     * @return Annotation
     * */
    public function cache ()
    {
        parent::cache()->save($this);
        return $this;
    }

    /**
     * Carrega o cache
     *
     * @return stdClass
     * */
    public function load ()
    {
       return parent::cache()->load($this);
    }

    /**
     * @return string[]
     * @throws PersistException
     * */
    public function getClassDoc ()
    {
        $comments = parent::getClassDoc();
        $pattern  = '/(?<pname>\w+)="(?P<pval>[^"]+)"/';

        $message = sprintf(self::ANNONTATION_COMMENT_NOT_FOUND, $this->_reflection->getName());
        PersistException::throwsExceptionIfParamIsNull(trim($comments), $message);

        $result = array();
        preg_match_all(self::ANNONTATION_ER_DEF_ENTITY_AND_SCHEMA, $comments, $result);
        $classDef = next($result);

        $comments = array();

        # identificador da definicao
        # @ident(...)
        foreach ($classDef as $key => $ident) {

            # recupera os valores definidos em cada propriedade
            # @...(name="value")
            preg_match_all($pattern, $result[0][$key], $resAnnon);

            if (1 < count($resAnnon['pname'])) {

                $attrs = array();
                foreach ($resAnnon['pval'] as $idx => $val) {
                     $attrs[$resAnnon['pname'][$idx]] = $val;
                }

                $comments[$ident][] = $attrs;

            } else {
                $comments[$ident] = $resAnnon['pval'][0];
            }
        }

        return $comments;
    }

    /**
     * @return string[]
     * @throws PersistException
     * */
    public function getAttrsDoc ()
    {
        $attrs = parent::getAttrsDoc();

        /* os attrs agora passam a ser opcionais */
        if (!count($attrs)) {
            return;
        }

        $attrList = array();
        foreach ($attrs as $attr) {
            $attr    = preg_replace('/(?:@attr\s*\(|[\/\*\n\r\t\s"]*|\)$)/m', '', $attr);
            $tmpAttr = array();
            $attr    = explode(',', $attr);

            foreach ($attr as $prop) {
                $data = explode('=', $prop);
                $name = current($data);
                $value = next($data);
                $tmpAttr[$name] = $value;
            }

            # ignora os campos que nao tenha anotacao definida
            if (!$this->_reflection->getName()) {
                continue;
            }


            if (isset($tmpAttr['name'])) {
                $attrList[$tmpAttr['name']] = $tmpAttr;
            }
        }

        $attrs = $attrList;

        return $attrs;
    }
}