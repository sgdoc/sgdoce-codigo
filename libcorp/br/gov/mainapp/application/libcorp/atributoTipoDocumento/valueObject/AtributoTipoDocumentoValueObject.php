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
namespace br\gov\mainapp\application\libcorp\atributoTipoDocumento\valueObject;
use br\gov\sial\core\valueObject\ValueObjectAbstract as ParentValueObject,
    br\gov\mainapp\application\libcorp\parent\mvcb\business\BusinessAbstract,
    br\gov\mainapp\application\libcorp\tipoDocumento\mvcb\business\TipoDocumentoBusiness,
    br\gov\mainapp\application\libcorp\atributoDocumento\mvcb\business\AtributoDocumentoBusiness;

/**
  * SISICMBio
  *
  * @name AtributoTipoDocumentoValueObject
  * @package br.gov.icmbio.sisicmbio.application.libcorp.atributoTipoDocumento
  * @subpackage valueObject
  * @schema(name="corporativo")
  * @entity(name="atributo_tipo_documento")
  * @author J. Augusto <augustowebd@gmail.com>
  * @version $Id$
  * @log(name="all")
  * */
class AtributoTipoDocumentoValueObject extends ParentValueObject
{
    /**
     * @attr (
     *  name="sqAtributoTipoDocumento",
     *  database="sq_atributo_tipo_documento",
     *  primaryKey="TRUE",
     *  type="integer",
     *  nullable="FALSE",
     *  get="getSqAtributoTipoDocumento",
     *  set="setSqAtributoTipoDocumento"
     * )
     * */
     private $_sqAtributoTipoDocumento;

    /**
     * @attr (
     *  name="sqAtributoDocumento",
     *  database="sq_atributo_documento",
     *  type="integer",
     *  nullable="FALSE",
     *  get="getSqAtributoDocumento",
     *  set="setSqAtributoDocumento"
     * )
     * */
     private $_sqAtributoDocumento;

    /**
     * @attr (
     *  name="sqTipoDocumento",
     *  database="sq_tipo_documento",
     *  type="integer",
     *  nullable="FALSE",
     *  get="getSqTipoDocumento",
     *  set="setSqTipoDocumento"
     * )
     * */
     private $_sqTipoDocumento;

    /**
     * @param integer $sqAtributoTipoDocumento
     * @param integer $sqAtributoDocumento
     * @param integer $sqTipoDocumento
     * */
    public function __construct ($sqAtributoTipoDocumento = NULL,
                                 $sqAtributoDocumento = NULL,
                                 $sqTipoDocumento = NULL)
    {
        parent::__construct();
        $this->setSqAtributoTipoDocumento($sqAtributoTipoDocumento)
             ->setSqAtributoDocumento($sqAtributoDocumento)
             ->setSqTipoDocumento($sqTipoDocumento)
             ;
    }

    /**
     * @return integer
     * */
    public function getSqAtributoTipoDocumento ()
    {
        return $this->_sqAtributoTipoDocumento;
    }

    /**
     * @return AtributoDocumentoValueObject
     * */
    public function getSqAtributoDocumento ()
    {
        if (!($this->_sqAtributoDocumento instanceof parent)) {
            $this->_sqAtributoDocumento = AtributoDocumentoBusiness::factory(NULL, 'libcorp')->find($this->_sqAtributoDocumento);
        }
        return $this->_sqAtributoDocumento;
    }

    /**
     * @return TipoDocumentoValueObject
     * */
    public function getSqTipoDocumento ()
    {
        if (!($this->_sqTipoDocumento instanceof parent)) {
            $this->_sqTipoDocumento = TipoDocumentoBusiness::factory(NULL, 'libcorp')->find($this->_sqTipoDocumento);
        }
        return $this->_sqTipoDocumento;
    }

    /**
     * @param integer $sqAtributoTipoDocumento
     * @return AtributoTipoDocumentoValueObject
     * */
    public function setSqAtributoTipoDocumento ($sqAtributoTipoDocumento = NULL)
    {
        $this->_sqAtributoTipoDocumento = $sqAtributoTipoDocumento;
        return $this;
    }

    /**
     * @param integer $sqAtributoDocumento
     * @return AtributoTipoDocumentoValueObject
     * */
    public function setSqAtributoDocumento ($sqAtributoDocumento = NULL)
    {
        $this->_sqAtributoDocumento = $sqAtributoDocumento;
        return $this;
    }

    /**
     * @param integer $sqTipoDocumento
     * @return AtributoTipoDocumentoValueObject
     * */
    public function setSqTipoDocumento ($sqTipoDocumento = NULL)
    {
        $this->_sqTipoDocumento = $sqTipoDocumento;
        return $this;
    }
}