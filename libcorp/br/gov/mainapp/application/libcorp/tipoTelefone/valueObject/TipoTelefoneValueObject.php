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
namespace br\gov\mainapp\application\libcorp\tipoTelefone\valueObject;
use br\gov\sial\core\valueObject\ValueObjectAbstract as ParentValueObject;

/**
  * SISICMBio
  *
  * Generated By SIAL Generator - vs 0.2.0
  *
  * @name TipoTelefoneValueObject
  * @package br.gov.icmbio.sisicmbio.application.libcorp.tipoTelefone
  * @subpackage valueObject
  * @schema(name="corporativo")
  * @entity(name="tipo_telefone")
  * @author Fabio Lima <fabioolima@gmail.com>
  * @author J. Augusto <augustowebd@gmail.com>
  * @version $Id$
  * @log(name="all")
  * */
class TipoTelefoneValueObject extends ParentValueObject
{
    /**
     * @attr (
     *  name="noTipoTelefone",
     *  database="no_tipo_telefone",
     *  type="string",
     *  nullable="FALSE",
     *  get="getNoTipoTelefone",
     *  set="setNoTipoTelefone"
     * )
     * */
     private $_noTipoTelefone;

    /**
     * @attr (
     *  name="sqTipoTelefone",
     *  database="sq_tipo_telefone",
     *  primaryKey="TRUE",
     *  type="integer",
     *  nullable="FALSE",
     *  get="getSqTipoTelefone",
     *  set="setSqTipoTelefone"
     * )
     * */
     private $_sqTipoTelefone;

    /**
     * @param string $noTipoTelefone
     * @param integer $sqTipoTelefone
     * */
    public function __construct ($noTipoTelefone = NULL,
                                 $sqTipoTelefone = NULL)
    {
        parent::__construct();
        $this->setNoTipoTelefone($noTipoTelefone)
             ->setSqTipoTelefone($sqTipoTelefone)
             ;
    }

    /**
     * @return string
     * */
    public function getNoTipoTelefone ()
    {
        return $this->_noTipoTelefone;
    }

    /**
     * @return integer
     * */
    public function getSqTipoTelefone ()
    {
        return $this->_sqTipoTelefone;
    }

    /**
     * @param string $noTipoTelefone
     * @return TipoTelefoneValueObject
     * */
    public function setNoTipoTelefone ($noTipoTelefone = NULL)
    {
        $this->_noTipoTelefone = $noTipoTelefone;
        return $this;
    }

    /**
     * @param integer $sqTipoTelefone
     * @return TipoTelefoneValueObject
     * */
    public function setSqTipoTelefone ($sqTipoTelefone = NULL)
    {
        $this->_sqTipoTelefone = $sqTipoTelefone;
        return $this;
    }
}