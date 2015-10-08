<?php

/*
 * Copyright 2012 ICMBio
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

namespace Sgdoce\Model\Entity;

use Doctrine\ORM\Mapping as ORM,
    Core\Model\OWM\Mapping as OWM;

/**
 * SISICMBio
 *
 * Classe para Entity Documento
 *
 * @package      Model
 * @subpackage     Entity
 * @name         Documento
 * @version     1.0.0
 * @since        2012-06-26
 */

/**
 * Sgdoce\Model\Entity\VwDocumento
 *
 * @ORM\Table(name="vw_documento")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\VwDocumento", readOnly=true)
 * @OWM\Endpoint(configKey="libcorp", repositoryClass="Sgdoce\Model\Repository\VwDocumentoWs")
 */
class VwDocumento extends \Core_Model_Entity_Abstract
{

    /**
     * @var integer $sqDocumento
     *
     * @ORM\Column(name="sq_documento", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqDocumento;

    /**
     * @var string $txValor
     *
     * @ORM\Column(name="tx_valor", type="string", length=50, nullable=false)
     */
    private $txValor;

    /**
     * @var Sgdoce\Model\Entity\VwPessoa
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwPessoa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_pessoa", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqPessoa;

    /**
     * @var Sgdoce\Model\Entity\VwAtributoTipoDocumento
     *
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\VwAtributoTipoDocumento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_atributo_tipo_documento", referencedColumnName="sq_atributo_tipo_documento")
     * })
     */
    private $sqAtributoTipoDocumento;

    /**
     * Set sqDocumento
     *
     * @return integer
     */
    public function setSqDocumento($sqDocumento)
    {
        $this->sqDocumento = $sqDocumento;

        return $sqDocumento;
    }

    /**
     * Get sqDocumento
     *
     * @return integer
     */
    public function getSqDocumento()
    {
        return $this->sqDocumento ? $this->sqDocumento : new VwDocumento();
    }

    /**
     * Set txValor
     *
     * @param string $txValor
     * @return Documento
     */
    public function setTxValor($txValor)
    {
        $this->assert('txValor',$txValor,$this);
        $this->txValor = $txValor;
        return $this;
    }

    /**
     * Get txValor
     *
     * @return string
     */
    public function getTxValor()
    {
        return $this->txValor;
    }

    /**
     * Set sqPessoa
     *
     * @param Sgdoce\Model\Entity\VwPessoa $sqPessoa
     * @return Documento
     */
    public function setSqPessoa(VwPessoa $sqPessoa = NULL)
    {
        $this->sqPessoa = $sqPessoa;
        return $this;
    }

    /**
     * Get sqPessoa
     *
     * @return Sgdoce\Model\Entity\VwPessoa
     */
    public function getSqPessoa()
    {
        return $this->sqPessoa ? $this->sqPessoa : new VwPessoa();
    }

    /**
     * Set sqPessoa
     *
     * @param Sgdoce\Model\Entity\PessoaSgdoce $sqPessoaSgdoce
     * @return VwDocumento
     */
    public function setSqPessoaSgdoce(PessoaSgdoce $sqPessoaSgdoce = NULL)
    {
        $this->sqPessoaSgdoce = $sqPessoaSgdoce;

        return $this;
    }

    /**
     * Get sqPessoa
     *
     * @return Sgdoce\Model\Entity\PessoaSgdoce
     */
    public function getSqPessoaSgdoce()
    {
        return $this->sqPessoaSgdoce ? $this->sqPessoaSgdoce : new PessoaSgdoce();
    }

    /**
     * Set sqAtributoTipoDocumento
     *
     * @param Sgdoce\Model\Entity\VwAtributoTipoDocumento $sqAtributoTipoDocumento
     * @return Documento
     */
    public function setSqAtributoTipoDocumento(VwAtributoTipoDocumento $sqAtributoTipoDocumento = NULL)
    {
        $this->sqAtributoTipoDocumento = $sqAtributoTipoDocumento;

        return $this;
    }

    /**
     * Get sqAtributoTipoDocumento
     *
     * @return Sgdoce\Model\Entity\VwAtributoTipoDocumento
     */
    public function getSqAtributoTipoDocumento()
    {
        return $this->sqAtributoTipoDocumento ? $this->sqAtributoTipoDocumento : new VwAtributoTipoDocumento();
    }

}
