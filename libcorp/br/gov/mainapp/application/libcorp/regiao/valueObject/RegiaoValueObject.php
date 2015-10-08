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
namespace br\gov\mainapp\application\libcorp\regiao\valueObject;
use br\gov\sial\core\valueObject\ValueObjectAbstract as ParentValueObject;

/**
  * SISICMBio
  *
  * @name RegiaoValueObject
  * @package br.gov.mainapp.application.libcorp.regiao
  * @subpackage valueObject
  * @schema(name="corporativo")
  * @entity(name="regiao")
  * @version $Id$
  * @log(name="all")
  * */
class RegiaoValueObject extends ParentValueObject
{
    /**
     * @attr (
     * name="sqRegiao",
     * database="sq_regiao",
     * primaryKey="TRUE",
     * type="integer",
     * nullable="FALSE",
     * get="getSqRegiao",
     * set="setSqRegiao"
    */
     private $_sqRegiao;

     /**
     * @attr (
     * name="noRegiao",
     * database="no_regiao",
     * type="string",
     * nullable="FALSE",
     * get="getSqRegiao",
     * set="setSqRegiao"
    */
     private $_noRegiao;

    /**
     * @param integer $sqRegiao
     * @param string $noRegiao
     * */
    public function __construct ($sqRegiao = NULL,
                                 $noRegiao = NULL)
    {
        parent::__construct();
        $this->setSqRegiao($sqRegiao)
             ->setNoRegiao($noRegiao)
             ;
    }

    /**
     * @return integer
     */
    public function getSqRegiao ()
    {
        return $this->_sqRegiao;
    }

    /**
     * @return string
     */
    public function getNoRegiao ()
    {
        return $this->_noRegiao;
    }

    /**
     * @param integer $sqRegiao
     * @return RegiaoValueObject
     */
    public function setSqRegiao ($sqRegiao = NULL)
    {
        $this->_sqRegiao = $sqRegiao;
        return $this;
    }

    /**
     * @param string $noRegiao
     * @return RegiaoValueObject
     */
    public function setNoRegiao ($noRegiao = NULL)
    {
        $this->_noRegiao = $noRegiao;
        return $this;
    }
}