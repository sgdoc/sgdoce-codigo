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
 * Classe para Entity EnderecoSgdoce
 *
 * @package      Model
 * @subpackage     Entity
 * @name         EnderecoSgdoce
 * @version     1.0.0
 * @since        2013-02-08
 */

/**
 * Sgdoce\Model\Entity\EnderecoSgdoce
 *
 * @ORM\Table(name="endereco_sgdoce")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\EnderecoSgdoce")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class EnderecoSgdoce extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqEnderecoSgdoce
     *
     * @ORM\Id
     * @ORM\Column(name="sq_endereco_sgdoce", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqEnderecoSgdoce;

    /**
     * @var Sgdoce\Model\Entity\PessoaSgdoce
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\PessoaSgdoce" ,inversedBy="sqPessoaEndereco")
     * @ORM\JoinColumn(name="sq_pessoa_sgdoce", referencedColumnName="sq_pessoa_sgdoce")
     */
    private $sqPessoaSgdoce;

    /**
     * @var zenddate $dtCadastro
     * @ORM\Column(name="dt_cadastro", type="zenddate", nullable=false)
     */
    private $dtCadastro;

    /**
     * @var Sgdoce\Model\Entity\VwTipoEndereco
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwTipoEndereco")
     * @ORM\JoinColumn(name="sq_tipo_endereco", referencedColumnName="sq_tipo_endereco")
     */
    private $sqTipoEndereco;

    /**
     * @var Sgdoce\Model\Entity\VwMunicipio
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwMunicipio")
     * @ORM\JoinColumn(name="sq_municipio", referencedColumnName="sq_municipio")
     */
    private $sqMunicipio;

    /**
     * @var string $coCep
     *
     * @ORM\Column(name="co_cep", type="string", nullable=true)
     */
    private $coCep;

    /**
     * @var string $noBairro
     *
     * @ORM\Column(name="no_bairro", type="string", nullable=true)
     */
    private $noBairro;

    /**
     * @var string $txEndereco
     *
     * @ORM\Column(name="tx_endereco", type="string", nullable=true)
     */
    private $txEndereco;

    /**
     * @var string $nuEndereco
     *
     * @ORM\Column(name="nu_endereco", type="string", nullable=true)
     */
    private $nuEndereco;

    /**
     * @var string $txComplemento
     *
     * @ORM\Column(name="tx_complemento", type="string", nullable=true)
     */
    private $txComplemento;

    /**
     * @var Sgdoce\Model\Entity\AnexoComprovante
     *
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\AnexoComprovante", mappedBy="sqEnderecoSgdoce")
     */
    private $sqAnexoComprovante;

    /**
     * @var string $noContato
     *
     * @ORM\Column(name="no_contato", type="string", nullable=true)
     */
    private $noContato;

    public function getSqEnderecoSgdoce() {
        return $this->sqEnderecoSgdoce;
    }

    public function setSqEnderecoSgdoce($sqEnderecoSgdoce) {
        $this->sqEnderecoSgdoce = $sqEnderecoSgdoce;
        return $this;
    }

    public function getSqPessoaSgdoce() {
        return $this->sqPessoaSgdoce ? $this->sqPessoaSgdoce : new PessoaSgdoce();
    }

    public function setSqPessoaSgdoce(PessoaSgdoce $sqPessoaSgdoce) {
        $this->sqPessoaSgdoce = $sqPessoaSgdoce;
        return $this;
    }

    public function getDtCadastro() {
        return $this->dtCadastro;
    }

    public function setDtCadastro($dtCadastro) {
        $this->dtCadastro = $dtCadastro;
        return $this;
    }

    public function getSqTipoEndereco() {
        return $this->sqTipoEndereco ? $this->sqTipoEndereco : new VwTipoEndereco();
    }

    public function setSqTipoEndereco(VwTipoEndereco $sqTipoEndereco) {
        $this->sqTipoEndereco = $sqTipoEndereco;
        return $this;
    }

    public function getSqMunicipio() {
        return $this->sqMunicipio ? $this->sqMunicipio : new VwMunicipio();
    }

    public function setSqMunicipio(VwMunicipio $sqMunicipio = NULL) {
        $this->sqMunicipio = $sqMunicipio;
        return $this;
    }

    public function getCoCep() {
        return $this->coCep;
    }

    public function setCoCep($coCep) {
        $this->coCep = $coCep;
        return $this;
    }

    public function getNoBairro() {
        return $this->noBairro;
    }

    public function setNoBairro($noBairro) {
        $this->noBairro = $noBairro;
        return $this;
    }

    public function getTxEndereco() {
        return $this->txEndereco;
    }

    public function setTxEndereco($txEndereco) {
        $this->txEndereco = $txEndereco;
        return $this;
    }

    public function getNuEndereco() {
        return $this->nuEndereco;
    }

    public function setNuEndereco($nuEndereco) {
        $this->nuEndereco = $nuEndereco;
        return $this;
    }

    public function getTxComplemento() {
        return $this->txComplemento;
    }

    public function setTxComplemento($txComplemento) {
        $this->txComplemento = $txComplemento;
        return $this;
    }

    public function getSqAnexoComprovante() {
        return $this->sqAnexoComprovante ? : new AnexoComprovante();
    }

    public function setSqAnexoComprovante($sqAnexoComprovante) {
        $this->sqAnexoComprovante = $sqAnexoComprovante;

        return $this;
    }

    public function getNoContato() {
        return $this->noContato;
    }

    public function setNoContato($noContato) {
        $this->noContato = $noContato;

        return $this;
    }
}