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
namespace br\gov\mainapp\application\libcorp\bioma\valueObject;
use br\gov\sial\core\valueObject\ValueObjectAbstract as ParentValueObject;

/**
  * SISICMBio
  *
  * @package br.gov.mainapp.application.libcorp.bioma
  * @subpackage valueObject
  * @schema(name="corporativo")
  * @entity(name="bioma")
  * @author Fabio Lima <fabioolima@gmail.com>
  * @author J. Augusto <augustowebd@gmail.com>
  * @log(name="all")
  * */
class BiomaValueObject extends ParentValueObject
{
    /**
     * @attr (
     *  name="sqBioma",
     *  database="sq_bioma",
     *  primaryKey="TRUE",
     *  type="integer",
     *  nullable="FALSE",
     *  get="getSqBioma",
     *  set="setSqBioma"
     * )
     * */
     private $_sqBioma;

    /**
     * @attr (
     *  name="noBioma",
     *  database="no_bioma",
     *  type="string",
     *  nullable="FALSE",
     *  get="getNoBioma",
     *  set="setNoBioma"
     * )
     * */
     private $_noBioma;

    /**
     * @param integer $sqBioma
     * @param string $noBioma
     * */
    public function __construct ($sqBioma = NULL,
                                 $noBioma = NULL)
    {
        parent::__construct();
        $this->setSqBioma($sqBioma)
             ->setNoBioma($noBioma)
             ;
    }

    /**
     * @return integer
     * */
    public function getSqBioma ()
    {
        return $this->_sqBioma;
    }

    /**
     * @return string
     * */
    public function getNoBioma ()
    {
        return $this->_noBioma;
    }

    /**
     * @param integer $sqBioma
     * @return BiomaValueObject
     * */
    public function setSqBioma ($sqBioma = NULL)
    {
        $this->_sqBioma = $sqBioma;
        return $this;
    }

    /**
     * @param string $noBioma
     * @return BiomaValueObject
     * */
    public function setNoBioma ($noBioma = NULL)
    {
        $this->_noBioma = $noBioma;
        return $this;
    }
}