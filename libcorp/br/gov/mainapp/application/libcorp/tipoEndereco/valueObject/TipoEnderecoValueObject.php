<?php
/*
 * Copyright 2011 ICMBio
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos
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
namespace br\gov\mainapp\application\libcorp\tipoEndereco\valueObject;
use br\gov\sial\core\valueObject\ValueObjectAbstract as ParentValueObject;

/**
  * SISICMBio
  *
  * Generated By SIAL Generator - vs 0.2.0
  *
  * @name TipoEnderecoValueObject
  * @package br.gov.icmbio.sisicmbio.application.libcorp.tipoEndereco
  * @subpackage valueObject
  * @schema(name="corporativo")
  * @entity(name="tipo_endereco")
  * @author Fabio Lima <fabioolima@gmail.com>
  * @author J. Augusto <augustowebd@gmail.com>
  * @version $Id$
  * @log(name="all")
  * */
class TipoEnderecoValueObject extends ParentValueObject
{
    /**
     * @attr (
     *  name="noTipoEndereco",
     *  database="no_tipo_endereco",
     *  type="string",
     *  nullable="FALSE",
     *  get="getNoTipoEndereco",
     *  set="setNoTipoEndereco"
     * )
     * */
     private $_noTipoEndereco;

    /**
     * @attr (
     *  name="sqTipoEndereco",
     *  database="sq_tipo_endereco",
     *  primaryKey="TRUE",
     *  type="integer",
     *  nullable="FALSE",
     *  get="getSqTipoEndereco",
     *  set="setSqTipoEndereco"
     * )
     * */
     private $_sqTipoEndereco;

    /**
     * @param string $noTipoEndereco
     * @param integer $sqTipoEndereco
     * */
    public function __construct ($noTipoEndereco = NULL,
                                 $sqTipoEndereco = NULL)
    {
        parent::__construct();
        $this->setNoTipoEndereco($noTipoEndereco)
             ->setSqTipoEndereco($sqTipoEndereco)
             ;
    }

    /**
     * @return string
     * */
    public function getNoTipoEndereco ()
    {
        return $this->_noTipoEndereco;
    }

    /**
     * @return integer
     * */
    public function getSqTipoEndereco ()
    {
        return $this->_sqTipoEndereco;
    }

    /**
     * @param string $noTipoEndereco
     * @return TipoEnderecoValueObject
     * */
    public function setNoTipoEndereco ($noTipoEndereco = NULL)
    {
        $this->_noTipoEndereco = $noTipoEndereco;
        return $this;
    }

    /**
     * @param integer $sqTipoEndereco
     * @return TipoEnderecoValueObject
     * */
    public function setSqTipoEndereco ($sqTipoEndereco = NULL)
    {
        $this->_sqTipoEndereco = $sqTipoEndereco;
        return $this;
    }
}