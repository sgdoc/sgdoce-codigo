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
use Doctrine\ORM\Mapping as ORM;
use Core\Model\OWM\Mapping as OWM;
/**
 * SISICMBio
 *
 * Classe para Entity ArtefatoMinuta
 *
 * @package     Model
 * @subpackage  Entity
 * @name         ArtefatoMinuta
 * @version     1.0.0
 * @since       2013-02-07
 */

/**
 * Sgdoce\Model\Entity\ArtefatoMinuta
 *
 * @ORM\Table(name="artefato_minuta")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\ArtefatoMinuta")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class ArtefatoMinuta extends \Core_Model_Entity_Abstract
{
    /**
     * @var Sgdoce\Model\Entity\Artefato
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\Artefato", mappedBy="sqArtefato")
     * @ORM\JoinColumn(name="sq_artefato", referencedColumnName="sq_artefato")
     */
    private $sqArtefato;

    /**
     * @var Sgdoce\Model\Entity\ModeloDocumento
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\ModeloDocumento")
     * @ORM\JoinColumn(name="sq_modelo_documento", referencedColumnName="sq_modelo_documento")
     */
    private $sqModeloDocumento;

    /**
     * @var string txTextoArtefato
     * @ORM\Column(name="tx_texto_artefato", type="string", nullable=true)
     */
    private $txTextoArtefato;

    /**
     * @var string $txReferencia
     * @ORM\Column(name="tx_referencia", type="string", length=150, nullable=true)
     */
    private $txReferencia;

    /**
     * @var string $txEmenta
     * @ORM\Column(name="tx_ementa", type="string", length=250, nullable=true)
     */
    private $txEmenta;

    /**
     * @var Sgdoce\Model\Entity\VwMunicipio
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwMunicipio")
     * @ORM\JoinColumn(name="sq_municipio", referencedColumnName="sq_municipio")
     */
    private $sqMunicipio;

    /**
     * Get sqArtefato
     *
     * @return Artefato
     */
    public function getSqArtefato()
    {
        return $this->sqArtefato ? $this->sqArtefato : new Artefato();
    }

    /**
     * Set sqArtefato
     *
     * @param object $sqArtefato
     * @return Artefato
     */
    public function setSqArtefato(Artefato $sqArtefato)
    {
        $this->sqArtefato = $sqArtefato;
        return $this;
    }

    /**
     * Get sqModeloDocumento
     *
     * @return integer
     */
    public function getSqModeloDocumento()
    {
        return $this->sqModeloDocumento ? $this->sqModeloDocumento : new ModeloDocumento();
    }

    /**
    * Set sqModeloDocumento
    *
    * @param integer $sqModeloDocumento
    * @return ModeloDocumento
    */
    public function setSqModeloDocumento(ModeloDocumento $sqModeloDocumento)
    {
        $this->sqModeloDocumento = $sqModeloDocumento;
        return $this;
    }

    /**
     * Get txTextoArtefato
     *
     * @return string
     */
    public function getTxTextoArtefato()
    {
        return $this->txTextoArtefato;
    }

    /**
    * Set txTextoArtefato
    *
    * @param string $txTextoArtefato
    * @return txTextoArtefato
    */
    public function setTxTextoArtefato($txTextoArtefato)
    {
        $this->txTextoArtefato = $txTextoArtefato;
        return $this;
    }

    /**
     * Get txReferencia
     *
     * @return string
     */
    public function getTxReferencia()
    {
        return $this->txReferencia;
    }

    /**
    * Set txReferencia
    *
    * @param string txReferencia
    * @return txReferencia
    */
    public function setTxReferencia($txReferencia)
    {
        $this->txReferencia = $txReferencia;
        return $this;
    }

    /**
     * Get txEmenta
     *
     * @return string
     */
    public function getTxEmenta()
    {
        return $this->txEmenta;
    }

    /**
    * Set txEmenta
    *
    * @param string txEmenta
    * @return txEmenta
    */
    public function setTxEmenta($txEmenta)
    {
        $this->txEmenta = $txEmenta;
        return $this;
    }

    /**
     * Get sqMunicipio
     *
     * @return VwMunicipio
     */
    public function getSqMunicipio()
    {
        return $this->sqMunicipio ? $this->sqMunicipio : new VwMunicipio();
    }

    /**
    * Set sqMunicipio
    *
    * @param object $sqMunicipio
    * @return VwMunicipio
    */
    public function setSqMunicipio(VwMunicipio $sqMunicipio)
    {
        $this->sqMunicipio = $sqMunicipio;
        return $this;
    }
}