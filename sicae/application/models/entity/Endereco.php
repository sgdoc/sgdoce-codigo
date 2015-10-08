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

use Doctrine\ORM\Mapping as ORM,
    Core\Model\OWM\Mapping as OWM;

/**
 * SISICMBio
 *
 * Classe para Entity Endereco
 *
 * @package      Model
 * @subpackage     Entity
 * @name         Endereco
 * @version     1.0.0
 * @since        2012-06-26
 */

/**
 * Sica\Model\Entity\Endereco
 *
 * @ORM\Table(name="vw_endereco")
 * @ORM\Entity(repositoryClass="Sica\Model\Repository\Endereco", readOnly=true)
 * @OWM\Endpoint(configKey="libcorp" , repositoryClass="Sica\Model\Repository\EnderecoWs")
 */
class Endereco extends \Core_Model_Entity_Abstract
{

    /**
     * @var integer $sqEndereco
     *
     * @ORM\Column(name="sq_endereco", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqEndereco;

    /**
     * @var integer $sqCep
     *
     * @ORM\Column(name="sq_cep", type="integer", nullable=false)
     */
    private $sqCep;

    /**
     * @var string $noBairro
     *
     * @ORM\Column(name="no_bairro", type="string", length=100, nullable=true)
     */
    private $noBairro;

    /**
     * @var string $txEndereco
     *
     * @ORM\Column(name="tx_endereco", type="string", length=200, nullable=false)
     */
    private $txEndereco;

    /**
     * @var string $nuEndereco
     *
     * @ORM\Column(name="nu_endereco", type="string", length=6, nullable=false)
     */
    private $nuEndereco;

    /**
     * @var string $txComplemento
     *
     * @ORM\Column(name="tx_complemento", type="string", length=100, nullable=true)
     */
    private $txComplemento;

    /**
     * @var boolean $inCorrespondencia
     *
     * @ORM\Column(name="in_correspondencia", type="boolean", nullable=true)
     */
    private $inCorrespondencia;

    /**
     * @var Sica\Model\Entity\TipoEndereco
     *
     * @ORM\ManyToOne(targetEntity="Sica\Model\Entity\TipoEndereco")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_tipo_endereco", referencedColumnName="sq_tipo_endereco")
     * })
     */
    private $sqTipoEndereco;

    /**
     * @var Sica\Model\Entity\Pessoa
     *
     * @ORM\ManyToOne(targetEntity="Sica\Model\Entity\Pessoa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_pessoa", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqPessoa;

    /**
     * @var Sica\Model\Entity\Municipio
     *
     * @ORM\ManyToOne(targetEntity="Sica\Model\Entity\Municipio",fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_municipio", referencedColumnName="sq_municipio")
     * })
     */
    private $sqMunicipio;

    /**
     * Get sqEndereco
     *
     * @return integer
     */
    public function getSqEndereco()
    {
        return $this->sqEndereco;
    }

    /**
     * Set sqCep
     *
     * @param integer $sqCep
     * @return Endereco
     */
    public function setSqCep($sqCep)
    {
        $this->sqCep = $sqCep;
        return $this;
    }

    /**
     * Get sqCep
     *
     * @return integer
     */
    public function getSqCep()
    {
        return $this->sqCep;
    }

    /**
     * Set noBairro
     *
     * @param string $noBairro
     * @return Endereco
     */
    public function setNoBairro($noBairro)
    {
        $this->noBairro = $noBairro;
        return $this;
    }

    /**
     * Get noBairro
     *
     * @return string
     */
    public function getNoBairro()
    {
        return $this->noBairro;
    }

    /**
     * Set txEndereco
     *
     * @param string $txEndereco
     * @return Endereco
     */
    public function setTxEndereco($txEndereco)
    {
        $this->txEndereco = $txEndereco;
        return $this;
    }

    /**
     * Get txEndereco
     *
     * @return string
     */
    public function getTxEndereco()
    {
        return $this->txEndereco;
    }

    /**
     * Set nuEndereco
     *
     * @param string $nuEndereco
     * @return Endereco
     */
    public function setNuEndereco($nuEndereco)
    {
        $this->nuEndereco = $nuEndereco;
        return $this;
    }

    /**
     * Get nuEndereco
     *
     * @return string
     */
    public function getNuEndereco()
    {
        return $this->nuEndereco;
    }

    /**
     * Set txComplemento
     *
     * @param string $txComplemento
     * @return Endereco
     */
    public function setTxComplemento($txComplemento)
    {
        $this->txComplemento = $txComplemento;
        return $this;
    }

    /**
     * Get txComplemento
     *
     * @return string
     */
    public function getTxComplemento()
    {
        return $this->txComplemento;
    }

    /**
     * Set inCorrespondencia
     *
     * @param boolean $inCorrespondencia
     * @return Endereco
     */
    public function setInCorrespondencia($inCorrespondencia)
    {
        $this->inCorrespondencia = $inCorrespondencia;
        return $this;
    }

    /**
     * Get inCorrespondencia
     *
     * @return string
     */
    public function getInCorrespondencia()
    {
        return $this->inCorrespondencia;
    }

    /**
     * Set sqTipoEndereco
     *
     * @param Sica\Model\Entity\TipoEndereco $sqTipoEndereco
     * @return Endereco
     */
    public function setSqTipoEndereco(TipoEndereco $sqTipoEndereco = NULL)
    {
        $this->sqTipoEndereco = $sqTipoEndereco;
        return $this;
    }

    /**
     * Get sqTipoEndereco
     *
     * @return Sica\Model\Entity\TipoEndereco
     */
    public function getSqTipoEndereco()
    {
        if (NULL === $this->sqTipoEndereco) {
            $this->setSqTipoEndereco(new TipoEndereco());
        }

        return $this->sqTipoEndereco;
    }

    /**
     * Set sqPessoa
     *
     * @param Sica\Model\Entity\Pessoa $sqPessoa
     * @return Endereco
     */
    public function setSqPessoa(Pessoa $sqPessoa = NULL)
    {
        $this->sqPessoa = $sqPessoa;
        return $this;
    }

    /**
     * Get sqPessoa
     *
     * @return Sica\Model\Entity\Pessoa
     */
    public function getSqPessoa()
    {
        if (NULL === $this->sqPessoa) {
            $this->setSqPessoa(new Pessoa());
        }

        return $this->sqPessoa;
    }

    /**
     * Set sqMunicipio
     *
     * @param Sica\Model\Entity\Municipio $sqMunicipio
     * @return Endereco
     */
    public function setSqMunicipio(Municipio $sqMunicipio = NULL)
    {
        $this->sqMunicipio = $sqMunicipio;
        return $this;
    }

    /**
     * Get sqMunicipio
     *
     * @return Sica\Model\Entity\Municipio
     */
    public function getSqMunicipio()
    {
        return $this->sqMunicipio ? $this->sqMunicipio : new Municipio();
    }

}
