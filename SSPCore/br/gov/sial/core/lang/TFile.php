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
namespace br\gov\sial\core\lang;
use br\gov\sial\core\valueObject\ValueObjectAbstract;

/**
 * SIAL
 *
 * ValueObject responsável por manter arquivos.
 *
 * @package br.gov.sial.core
 * @subpackage lang
 * @name TFile
 * @author Fabio Lima <fabioolima@gmail.com>
 * @entity(name="file")
 * */
class TFile extends ValueObjectAbstract
{
    /**
     * @attr (
     *  name="name",
     *  file="name",
     *  type="string",
     *  get="getName",
     *  set="setName"
     * )
     * */
    private $_name;

    /**
     * @attr (
     *  name="type",
     *  file="type",
     *  type="string",
     *  get="getType",
     *  set="setType"
     * )
     * */
    private $_type;

    /**
     * @attr (
     *  name="source",
     *  file="source",
     *  type="string",
     *  get="getSource",
     *  set="setSource"
     * )
     * */
    private $_source;

    /**
     * @attr (
     *  name="error",
     *  file="error",
     *  type="integer",
     *  get="getError",
     *  set="setError"
     * )
     * */
    private $_error;

    /**
     * @attr (
     *  name="size",
     *  file="size",
     *  type="integer",
     *  get="getSize",
     *  set="setSize"
     * )
     * */
    private $_size;

    /**
     * Contrutor.
     *
     * @param string $name
     * @param string $type
     * @param string $source
     * @param integer $error
     * @param integer $size
     * */
    public function __construct ($name = NULL,
                                 $type = NULL,
                                 $source = NULL,
                                 $error = NULL,
                                 $size = NULL)
    {
        parent::__construct();
        $this->setName($name)
             ->setType($type)
             ->setSource($source)
             ->setError($error)
             ->setSize($size);
    }

    /**
     * compara o tipo do arquivo
     * se o segundo param for informado a comparação sera pelo tipo completo, do contrario
     * verifica apenas a extensão do arquivo e não o tipo propriamente dito
     *
     * @param string $type
     * @param boolean $verbose
     * */
    public function isType ($type, $verbose = FALSE)
    {
        if (TRUE == $verbose) {
            return $type == $this->_type;
        }

        $info = (object) pathinfo($this->_name);
        return $info->extension == $type;
    }

    /**
     * Recupera o nome do arquivo.
     *
     * @return string
     * */
    public function getName ()
    {
        return $this->_name;
    }

    /**
     * Recupera o tipo do arquivo.
     *
     * @return string
     * */
    public function getType ()
    {
        return $this->_type;
    }

    /**
     * Recupera a origem do arquivo.
     *
     * @return string
     * */
    public function getSource ()
    {
        return $this->_source;
    }

    /**
     * Recupera o código de erro relacionado ao arquivo.
     *
     * @return integer
     * */
    public function getError ()
    {
        return $this->_error;
    }

    /**
     * Recupera em bytes o tamanho do arquivo.
     *
     * @return integer
     * */
    public function getSize ()
    {
        return $this->_size;
    }

    /**
     * Atribui nome.
     *
     * @param string $name
     * @return \br\gov\sial\core\lang\FileValueObject
     * */
    public function setName ($name = NULL)
    {
        $this->_name = (string) $name;
        return $this;
    }

    /**
     * Atribui tipo do arquivo.
     *
     * @param string $type
     * @return \br\gov\sial\core\lang\FileValueObject
     * */
    public function setType ($type = NULL)
    {
        $this->_type = (string) $type;
        return $this;
    }

    /**
     * Atribui a origem do arquivo.
     *
     * @param string $source
     * @return \br\gov\sial\core\lang\FileValueObject
     * */
    public function setSource ($source = NULL)
    {
        $this->_source = (string) $source;
        return $this;
    }

    /**
     * Atribui o código de erro do arquivo.
     *
     * @param integer $error
     * @return \br\gov\sial\core\lang\FileValueObject
     * */
    public function setError ($error = NULL)
    {
        $this->_error = (integer) $error;
        return $this;
    }

    /**
     * Atribui o tamanho do arquivo.
     *
     * @param integer $size
     * @return \br\gov\sial\core\lang\FileValueObject
     * */
    public function setSize ($size = NULL)
    {
        $this->_size = (integer) $size;
        return $this;
    }
}