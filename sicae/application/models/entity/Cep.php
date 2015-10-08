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
namespace Sica\Model\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
 * SISICMBio
 *
 * Classe para Entity Cep
 *
 * @package      Model
 * @subpackage     Entity
 * @name         Cep
 * @version     1.0.0
 * @since        2012-06-26
 */

/**
 * Sica\Model\Entity\Cep
 *
 * @ORM\Table(name="vw_endereco_cep")
 * @ORM\Entity(repositoryClass="Sica\Model\Repository\Cep", readOnly=true)
 */
class Cep extends \Core_Model_Entity_Abstract
{

    /**
     * @var integer $coCep
     *
     * @ORM\Column(name="co_cep", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $coCep;

    /**
     * @var integer $sgUf
     *
     * @ORM\Column(name="sg_uf_localidade", type="string", nullable=false)
     *
     */
    private $sgUf;

    /**
     * @var integer $coIbge
     *
     * @ORM\Column(name="co_ibge", type="integer", nullable=false)
     *
     */
    private $coIbge;

    /**
     * @var integer $noBairro
     *
     * @ORM\Column(name="no_bairro", type="string", nullable=false)
     *
     */
    private $noBairro;

    /**
     * @var integer $noAbreviaturaBairro
     *
     * @ORM\Column(name="no_abreviatura_bairro", type="string", nullable=false)
     *
     */
    private $noAbreviaturaBairro;

    /**
     * @var integer $noLogradouro
     *
     * @ORM\Column(name="no_logradouro", type="string", nullable=false)
     *
     */
    private $noLogradouro;

    /**
     * @var integer $txAbreviacao
     *
     * @ORM\Column(name="tx_abreviacao", type="string", nullable=false)
     *
     */
    private $txAbreviacao;

    /**
     * @var Sica\Model\Entity\Municipio
     *
     * @ORM\OneToOne(targetEntity="Sica\Model\Entity\Municipio", inversedBy="cep")
     *   @ORM\JoinColumn(name="sq_municipio", referencedColumnName="sq_municipio")
     * })
     */
    private $sqMunicipio;

    public function setCoCep($coCep)
    {
        $this->coCep = $coCep;
        return $this;
    }

    public function getCoCep()
    {
        return $this->coCep;
    }

    public function getSgUf()
    {
        return $this->sgUf;
    }

    public function setSgUf($sgUf)
    {
        $this->sgUf = $sgUf;
        return $this;
    }

    public function getCoIbge()
    {
        return $this->coIbge;
    }

    public function setCoIbge($coIbge)
    {
        $this->coIbge = $coIbge;
        return $this;
    }

    public function getNoBairro()
    {
        return $this->noBairro;
    }

    public function setNoBairro($noBairro)
    {
        $this->noBairro = $noBairro;
        return $this;
    }

    public function getNoAbreviaturaBairro()
    {
        return $this->noAbreviaturaBairro;
    }

    public function setNoAbreviaturaBairro($noAbreviaturaBairro)
    {
        $this->noAbreviaturaBairro = $noAbreviaturaBairro;
        return $this;
    }

    public function getNoLogradouro()
    {
        return $this->noLogradouro;
    }

    public function setNoLogradouro($noLogradouro)
    {
        $this->noLogradouro = $noLogradouro;
        return $this;
    }

    public function getTxAbreviacao()
    {
        return $this->txAbreviacao;
    }

    public function setTxAbreviacao($txAbreviacao)
    {
        $this->txAbreviacao = $txAbreviacao;
        return $this;
    }

    public function getSqMunicipio()
    {
        if (NULL === $this->sqMunicipio) {
            $this->setSqMunicipio(new Municipio());
        }

        return $this->sqMunicipio;
    }

    public function setSqMunicipio(Municipio $sqMunicipio)
    {
        $this->sqMunicipio = $sqMunicipio;
        return $this;
    }

}