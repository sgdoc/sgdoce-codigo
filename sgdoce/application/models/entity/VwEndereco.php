<?php

/**
 * Copyright 2012 do ICMBio
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro
 * dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre
 * (FSF); na versão 2 da Licença.
 * Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA;
 * sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR.
 * Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
 * junto com este programa, se não, acesse o Portal do Software Público Brasileiro no
 * endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
 */

namespace Sgdoce\Model\Entity;

use Doctrine\ORM\Mapping as ORM,
    Core\Model\OWM\Mapping as OWM;

/**
 * Sgdoce\Model\Entity\Endereco
 * @ORM\Table(name="vw_endereco")
 * @ORM\Entity(repositoryClass="\Sgdoce\Model\Repository\VwEndereco")
 * @OWM\Endpoint(configKey="libcorp", repositoryClass="Sgdoce\Model\Repository\VwEnderecoWs")
 */
class VwEndereco extends \Core_Model_Entity_Abstract {

    /**
     * @var integer $sqEndereco
     * @ORM\Id
     * @ORM\Column(name="sq_endereco", type="integer", nullable=true)
     */
    private $sqEndereco;

    /**
     * @var Sgdoce\Model\Entity\VwMunicipio
     *
     * @ORM\ManyToOne(targetEntity="\Sgdoce\Model\Entity\VwMunicipio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_municipio", referencedColumnName="sq_municipio")
     * })
     */
    private $sqMunicipio;

    /**
     * @var Sgdoce\Model\Entity\TipoEndereco
     *
     * @ORM\ManyToOne(targetEntity="\Sgdoce\Model\Entity\VwTipoEndereco")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_tipo_endereco", referencedColumnName="sq_tipo_endereco")
     * })
     */
    private $sqTipoEndereco;

    /**
     * @var integer $sqCep
     * @ORM\Column(name="sq_cep", type="integer", nullable=true)
     */
    private $sqCep;

    /**
     * @var Sgdoce\Model\Entity\VwPessoa
     *
     * @ORM\ManyToOne(targetEntity="\Sgdoce\Model\Entity\VwPessoa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_pessoa", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqPessoa;

    /**
     * @var string $txEndereco
     * @ORM\Column(name="tx_endereco", type="string", nullable=true)
     */
    private $txEndereco;

    /**
     * @var integer $nuEndereco
     * @ORM\Column(name="nu_endereco", type="integer", length=6, nullable=true)
     */
    private $nuEndereco;

    /**
     * @var string $txComplemento
     * @ORM\Column(name="tx_complemento", type="string", length=100, nullable=true)
     */
    private $txComplemento;

    /**
     * @var boolean $inCorrespondencia
     * @ORM\Column(name="in_correspondencia", type="boolean", nullable=true)
     */
    private $inCorrespondencia;

    /**
     * @var string $noBairro
     * @ORM\Column(name="no_bairro", type="string", length=100, nullable=true)
     */
    private $noBairro;

    public function setSqEndereco($sqEndereco)
    {
        $this->sqEndereco = $sqEndereco;
    }

    public function setSqMunicipio($sqMunicipio)
    {
        $this->sqMunicipio = $sqMunicipio;
    }

    public function setSqTipoEndereco($sqTipoEndereco)
    {
        $this->sqTipoEndereco = $sqTipoEndereco;
    }

    public function setSqCep($sqCep)
    {
        $this->sqCep = $sqCep;
    }

    public function setSqPessoa($sqPessoa)
    {
        $this->sqPessoa = $sqPessoa;
    }

    public function setTxEndereco($txEndereco)
    {
        $this->assert('txEndereco',$txEndereco,$this);
        $this->txEndereco = $txEndereco;
    }

    public function setNuEndereco($nuEndereco)
    {
        $this->assert('nuEndereco',$nuEndereco,$this);
        $this->nuEndereco = $nuEndereco;
    }

    public function setTxComplemento($txComplemento)
    {
        $this->assert('txComplemento',$txComplemento,$this);
        $this->txComplemento = $txComplemento;
    }

    public function setInCorrespondencia($inCorrespondencia)
    {
        $this->inCorrespondencia = $inCorrespondencia;
    }

    public function getSqEndereco()
    {
        return $this->sqEndereco;
    }

    public function getSqMunicipio()
    {
        return $this->sqMunicipio ? $this->sqMunicipio : new VwMunicipio();
    }

    public function getSqTipoEndereco()
    {
        return $this->sqTipoEndereco ? $this->sqTipoEndereco : new VwTipoEndereco();
    }

    public function getSqCep()
    {
        return $this->sqCep;
    }

    public function getSqPessoa()
    {
        return $this->sqPessoa ? $this->sqPessoa : new Pessoa();
    }

    public function getTxEndereco()
    {
        return $this->txEndereco;
    }

    public function getNuEndereco()
    {
        return $this->nuEndereco;
    }

    public function getTxComplemento()
    {
        return $this->txComplemento;
    }

    public function getInCorrespondencia()
    {
        return $this->inCorrespondencia;
    }

    public function setNoBairro($noBairro)
    {
        $this->assert('noBairro',$noBairro,$this);
        $this->noBairro = $noBairro;
    }

    public function getNoBairro()
    {
        return $this->noBairro;
    }
}