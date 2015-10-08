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

/**
 * Sgdoce\Model\Entity\VwEnderecoCep
 * @ORM\Table(name="vw_endereco_cep")
 * @ORM\Entity(repositoryClass="\Sgdoce\Model\Repository\VwEnderecoCep", readOnly=true)
 */
class VwEnderecoCep extends \Core_Model_Entity_Abstract
{

    /**
     * @var integer $coCep
     * @ORM\Id
     * @ORM\Column(name="co_cep", type="integer", nullable=true)
     */
    private $coCep;

    /**
     * @var integer $sqUfLocalidade
     * @ORM\Column(name="sg_uf_localidade", type="integer", nullable=true)
     */
    private $sqUfLocalidade;

    /**
     * @var integer $coIbge
     * @ORM\Column(name="co_ibge", type="integer", nullable=true)
     */
    private $coIbge;

    /**
     * @var string $noBairro
     * @ORM\Column(name="no_bairro", type="string", nullable=true)
     */
    private $noBairro;

    /**
     * @var string $noAbreviaturaBairro
     * @ORM\Column(name="no_abreviatura_bairro", type="string", nullable=true)
     */
    private $noAbreviaturaBairro;

    /**
     * @var string $noLogradouro
     * @ORM\Column(name="no_logradouro", type="string", nullable=true)
     */
    private $noLogradouro;

    /**
     * @var string $txComplemento
     * @ORM\Column(name="tx_complemento", type="string", nullable=true)
     */
    private $txComplemento;

    /**
     * @var string $txAbreviacao
     * @ORM\Column(name="tx_abreviacao", type="string", nullable=true)
     */
    private $txAbreviacao;

    /**
     * @var string $noMunicipio
     * @ORM\Column(name="no_municipio", type="string", nullable=true)
     */
    private $noMunicipio;

    /**
     * @var Sgdoce\Model\Entity\VwMunicipio
     *
     * @ORM\ManyToOne(targetEntity="\Sgdoce\Model\Entity\VwMunicipio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_municipio", referencedColumnName="sq_municipio")
     * })
     */    
    private $sqMunicipio;

    public function getCoCep()
    {
        return $this->coCep;
    }

    public function setCoCep($setCoCep)
    {
        $this->setCoCep = $setCoCep;
    }

    public function getSqUfLocalidade()
    {
        return $this->sqUfLocalidade;
    }

    public function setSqUfLocalidade($setSqUfLocalidade)
    {
        $this->setSqUfLocalidade = $setSqUfLocalidade;
    }

    public function getCoIbge()
    {
        return $this->coIbge;
    }

    public function setCoIbge($setCoIbge)
    {
        $this->setCoIbge = $setCoIbge;
    }

    public function getNoBairro()
    {
        return $this->noBairro;
    }

    public function setNoBairro($setNoBairro)
    {
        $this->setNoBairro = $setNoBairro;
    }

    public function getNoAbreviaturaBairro()
    {
        return $this->noAbreviaturaBairro;
    }

    public function setNoAbreviaturaBairro($setNoAbreviaturaBairro)
    {
        $this->setNoAbreviaturaBairro = $setNoAbreviaturaBairro;
    }

    public function getNoLogradouro()
    {
        return $this->noLogradouro;
    }

    public function setNoLogradouro($setNoLogradouro)
    {
        $this->setNoLogradouro = $setNoLogradouro;
    }

    public function getTxComplemento()
    {
        return $this->txComplemento;
    }

    public function setTxComplemento($setTxComplemento)
    {
        $this->setTxComplemento = $setTxComplemento;
    }

    public function getTxAbreviacao()
    {
        return $this->txAbreviacao;
    }

    public function setTxAbreviacao($setTxAbreviacao)
    {
        $this->setTxAbreviacao = $setTxAbreviacao;
    }

    public function getNoMunicipio()
    {
        return $this->noMunicipio;
    }

    public function setNoMunicipio($setNoMunicipio)
    {
        $this->setNoMunicipio = $setNoMunicipio;
    }

    public function getSqMunicipio()
    {
        return $this->sqMunicipio;
    }

    public function setSqMunicipio($setSqMunicipio)
    {
        $this->setSqMunicipio = $setSqMunicipio;
    }
}