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
namespace br\gov\mainapp\application\libcorp\documento\valueObject;
use br\gov\sial\core\valueObject\ValueObjectAbstract as ParentValueObject,
    br\gov\mainapp\application\libcorp\pessoa\mvcb\business\PessoaBusiness,
    br\gov\mainapp\application\libcorp\atributoTipoDocumento\mvcb\business\AtributoTipoDocumentoBusiness;

/**
  * SISICMBio
  *
  * @name DocumentoValueObject
  * @package br.gov.icmbio.sisicmbio.application.libcorp.documento
  * @subpackage valueObject
  * @schema(name="corporativo")
  * @entity(name="documento")
  * @author J. Augusto <augustowebd@gmail.com>
  * @version $Id$
  * @log(name="all")
  * */
class DocumentoValueObject extends ParentValueObject
{
    /**
     * @attr (
     *  name="sqDocumento",
     *  database="sq_documento",
     *  primaryKey="TRUE",
     *  type="integer",
     *  nullable="FALSE",
     *  get="getSqDocumento",
     *  set="setSqDocumento"
     * )
     * */
    private $_sqDocumento;

    /**
     * @attr (
     *  name="sqAtributoTipoDocumento",
     *  database="sq_atributo_tipo_documento",
     *  type="integer",
     *  nullable="FALSE",
     *  get="getSqAtributoTipoDocumento",
     *  set="setSqAtributoTipoDocumento"
     * )
     * */
     private $_sqAtributoTipoDocumento;

    /**
     * @attr (
     *  name="sqPessoa",
     *  database="sq_pessoa",
     *  type="integer",
     *  nullable="FALSE",
     *  get="getSqPessoa",
     *  set="setSqPessoa"
     * )
     * */
    private $_sqPessoa;

    /**
     * @attr (
     *  name="txValor",
     *  database="tx_valor",
     *  type="string",
     *  nullable="FALSE",
     *  get="getTxValor",
     *  set="setTxValor"
     * )
     * */
    private $_txValor;

    /**
     * @param integer $sqDocumento
     * @param integer $sqAtributoTipoDocumento
     * @param integer $sqPessoa
     * @param string $txValor
     * */
    public function __construct ($sqDocumento = NULL,
                                 $sqAtributoTipoDocumento = NULL,
                                 $sqPessoa = NULL,
                                 $txValor = NULL)
    {
        parent::__construct();
        $this->setSqDocumento($sqDocumento)
             ->setSqAtributoTipoDocumento($sqAtributoTipoDocumento)
             ->setSqPessoa($sqPessoa)
             ->setTxValor($txValor)
             ;
    }

    /**
     * @return integer
     * */
    public function getSqDocumento ()
    {
        return $this->_sqDocumento;
    }

    /**
     * @return AtributoTipoDocumentoValueObject
     * */
    public function getSqAtributoTipoDocumento ()
    {
        if (!($this->_sqAtributoTipoDocumento instanceof parent)) {
            $this->_sqAtributoTipoDocumento =
            AtributoTipoDocumentoBusiness::factory(NULL, 'libcorp')->find($this->_sqAtributoTipoDocumento);
        }
        return $this->_sqAtributoTipoDocumento;
    }

    /**
     * @return PessoaValueObject
     * */
    public function getSqPessoa ()
    {
        if (!($this->_sqPessoa instanceof parent)) {
            $this->_sqPessoa = PessoaBusiness::factory(NULL, 'libcorp')->find($this->_sqPessoa);
        }
        return $this->_sqPessoa;
    }

    /**
     * @return string
     * */
    public function getTxValor ()
    {
        return $this->_txValor;
    }

    /**
     * @param integer $sqDocumento
     * @return DocumentoValueObject
     * */
    public function setSqDocumento ($sqDocumento = NULL)
    {
        $this->_sqDocumento = $sqDocumento;
        return $this;
    }

    /**
     * @param integer $sqAtributoTipoDocumento
     * @return DocumentoValueObject
     * */
    public function setSqAtributoTipoDocumento ($sqAtributoTipoDocumento = NULL)
    {
        $this->_sqAtributoTipoDocumento = $sqAtributoTipoDocumento;
        return $this;
    }

    /**
     * @param integer $sqPessoa
     * @return DocumentoValueObject
     * */
    public function setSqPessoa ($sqPessoa = NULL)
    {
        $this->_sqPessoa = $sqPessoa;
        return $this;
    }

    /**
     * @param string $txValor
     * @return DocumentoValueObject
     * */
    public function setTxValor ($txValor = NULL)
    {
        $this->_txValor = $txValor;
        return $this;
    }
}