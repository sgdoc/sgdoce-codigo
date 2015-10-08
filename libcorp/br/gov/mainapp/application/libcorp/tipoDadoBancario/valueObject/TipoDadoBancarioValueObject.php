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
namespace br\gov\mainapp\application\libcorp\tipoDadoBancario\valueObject;
use br\gov\sial\core\valueObject\ValueObjectAbstract as ParentValueObject;

/**
  * SISICMBio
  *
  * Generated By SIAL Generator - vs 0.2.0
  *
  * @name TipoDadoBancarioValueObject
  * @package br.gov.icmbio.sisicmbio.application.libcorp.tipoDadoBancario
  * @subpackage valueObject
  * @schema(name="corporativo")
  * @entity(name="tipo_dado_bancario")
  * @author Fabio Lima <fabioolima@gmail.com>
  * @author J. Augusto <augustowebd@gmail.com>
  * @version $Id$
  * @log(name="all")
  * */
class TipoDadoBancarioValueObject extends ParentValueObject
{
    /**
     * @attr (
     *  name="noTipoDadoBancario",
     *  database="no_tipo_dado_bancario",
     *  type="string",
     *  nullable="FALSE",
     *  get="getNoTipoDadoBancario",
     *  set="setNoTipoDadoBancario"
     * )
     * */
     private $_noTipoDadoBancario;

    /**
     * @attr (
     *  name="sqTipoDadoBancario",
     *  database="sq_tipo_dado_bancario",
     *  primaryKey="TRUE",
     *  type="integer",
     *  nullable="FALSE",
     *  get="getSqTipoDadoBancario",
     *  set="setSqTipoDadoBancario"
     * )
     * */
     private $_sqTipoDadoBancario;

    /**
     * @param string $noTipoDadoBancario
     * @param integer $sqTipoDadoBancario
     * */
    public function __construct ($noTipoDadoBancario = NULL,
                                 $sqTipoDadoBancario = NULL)
    {
        parent::__construct();
        $this->setNoTipoDadoBancario($noTipoDadoBancario)
             ->setSqTipoDadoBancario($sqTipoDadoBancario)
             ;
    }

    /**
     * @return string
     * */
    public function getNoTipoDadoBancario ()
    {
        return $this->_noTipoDadoBancario;
    }

    /**
     * @return integer
     * */
    public function getSqTipoDadoBancario ()
    {
        return $this->_sqTipoDadoBancario;
    }

    /**
     * @param string $noTipoDadoBancario
     * @return br\gov\mainapp\application\libcorp\tipoDadoBancario\valueObject\TipoDadoBancarioValueObject
     * */
    public function setNoTipoDadoBancario ($noTipoDadoBancario = NULL)
    {
        $this->_noTipoDadoBancario = $noTipoDadoBancario;
        return $this;
    }

    /**
     * @param integer $sqTipoDadoBancario
     * @return br\gov\mainapp\application\libcorp\tipoDadoBancario\valueObject\TipoDadoBancarioValueObject
     * */
    public function setSqTipoDadoBancario ($sqTipoDadoBancario = NULL)
    {
        $this->_sqTipoDadoBancario = $sqTipoDadoBancario;
        return $this;
    }
}