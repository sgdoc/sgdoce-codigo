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
 * Classe para Entity PessoaSgdoce
 *
 * @package      Model
 * @subpackage     Entity
 * @name         PessoaSgdoce
 * @version     1.0.0
 * @since        2013-02-07
 */

/**
 * Sgdoce\Model\Entity\PessoaSgdoce
 *
 * @ORM\Table(name="pessoa_sgdoce")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\PessoaSgdoce")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class PessoaSgdoce extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqPessoaSgdoce
     *
     * @ORM\Id
     * @ORM\Column(name="sq_pessoa_sgdoce", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqPessoaSgdoce;

    /**
     * @var Sgdoce\Model\Entity\VwUnidadeOrg
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwUnidadeOrg")
     * @ORM\JoinColumn(name="sq_pessoa_corporativo", referencedColumnName="sq_pessoa")
     */
    private $sqVwPessoaUnidadeOrg;

    // RELACIONAMENTO ALTERADO DE OneToOne para OneToMany pois no cenario atual
    // uma pessoa_sgdoce pode estar em varios artefatos e tem casos, que com a carga
    // de todos os artefatos, fica com ate 59 mil registros em pessoa_artefato e isso
    // causa estouro de memoria
    /**
     * @var Sgdoce\Model\Entity\PessoaArtefato
     *
     * @ORM\OneToMany(targetEntity="Sgdoce\Model\Entity\PessoaArtefato", mappedBy="sqPessoaSgdoce")
    */
    private $sqPessoaArtefato;

    /**
     * @var Sgdoce\Model\Entity\VwTipoPessoa
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwTipoPessoa")
     * @ORM\JoinColumn(name="sq_tipo_pessoa", referencedColumnName="sq_tipo_pessoa")
     */
    private $sqTipoPessoa;

    /**
     * @var string $nuCpfCnpjPassaporte
     *
     * @ORM\Column(name="nu_cpf_cnpj_passaporte", type="string", length=18, nullable=true)
     */
    private $nuCpfCnpjPassaporte;

    /**
     * @var string $noPessoa
     *
     * @ORM\Column(name="no_pessoa", type="string", nullable=true)
     */
    private $noPessoa;

    /**
     * @var string $noMae
     *
     * @ORM\Column(name="no_mae", type="string", length=120, nullable=true)
     */
    private $noMae;

    /**
     * @var interger $sqEstadoCivil
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwEstadoCivil")
     * @ORM\JoinColumn(name="sq_estado_civil", referencedColumnName="sq_estado_civil")
     */
    private $sqEstadoCivil;

    /**
     * @var string $noProfissao
     *
     * @ORM\Column(name="no_profissao", type="string", nullable=true)
     */
    private $noProfissao;

    /**
     * @var Sgdoce\Model\Entity\VwPessoa
     *
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\VwPessoa")
     * @ORM\JoinColumn(name="sq_pessoa_corporativo", referencedColumnName="sq_pessoa")
     */
    private $sqPessoaCorporativo;

    /**
     * @var string $txInformacaoComplementar
     *
     * @ORM\Column(name="tx_informacao_complementar", type="string", length=180, nullable=true)
     */
    private $txInformacaoComplementar;

    /**
     * @var Sgdoce\Model\Entity\EnderecoSgdoce
     *
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\EnderecoSgdoce", mappedBy="sqPessoaSgdoce")
     */
    private $sqPessoaEndereco;

    /**
     * @ORM\OneToMany(targetEntity="Sgdoce\Model\Entity\AnexoComprovanteDocumento", mappedBy="sqPessoaSgdoce")
     */
    private $sqAnexoComprovanteDocumento;

    /**
     * Set sqPessoaSgdoce
     *
     * @param integer $sqPessoaSgdoce
     * @return integer
     */
    public function setSqPessoaSgdoce($sqPessoaSgdoce = NULL)
    {
        $this->sqPessoaSgdoce = $sqPessoaSgdoce;
        if(!$sqPessoaSgdoce){
            $this->sqPessoaSgdoce  = NULL;
        }
        return $this;
    }

    public function getSqPessoaSgdoce()
    {
        return $this->sqPessoaSgdoce;
    }

    public function getNoPessoa()
    {
        return $this->noPessoa;
    }

    public function setNoPessoa($noPessoa)
    {
        $this->noPessoa = $noPessoa;
        return $this;
    }

    public function getNoProfissao()
    {
        return $this->noProfissao;
    }

    public function setNoProfissao($noProfissao)
    {
        $this->noProfissao = $noProfissao;
        return $this;
    }

    public function setSqPessoaCorporativo(VwPessoa $sqPessoaCorporativo  = NULL)
    {
        $this->sqPessoaCorporativo = $sqPessoaCorporativo;
        return $this;
    }

    public function getSqPessoaCorporativo()
    {
        return $this->sqPessoaCorporativo ? $this->sqPessoaCorporativo : new VwPessoa();
    }

    public function getSqTipoPessoa()
    {
        return $this->sqTipoPessoa ? $this->sqTipoPessoa : new VwTipoPessoa();
    }

    public function setSqTipoPessoa(VwTipoPessoa $sqTipoPessoa)
    {
        $this->sqTipoPessoa = $sqTipoPessoa;
        return $this;
    }

    public function getNuCpfCnpjPassaporte()
    {
        return $this->nuCpfCnpjPassaporte;
    }

    public function setNuCpfCnpjPassaporte($nuCpfCnpjPassaporte)
    {
        $this->assert('nuCpfCnpjPassaporte',$nuCpfCnpjPassaporte,$this);
        $this->nuCpfCnpjPassaporte = $nuCpfCnpjPassaporte;
        return $this;
    }

    public function getNoMae()
    {
        return $this->noMae;
    }

    public function setNoMae($noMae)
    {
        $this->assert('noMae',$noMae,$this);
        $this->noMae = $noMae;
        return $this;
    }

    public function getTxInformacaoComplementar()
    {
        return $this->txInformacaoComplementar;
    }

    public function setTxInformacaoComplementar($txInformacaoComplementar)
    {
        $this->assert('txInformacaoComplementar',$txInformacaoComplementar,$this);
        $this->txInformacaoComplementar = $txInformacaoComplementar;
        return $this;
    }

    public function setSqEstadoCivil(VwEstadoCivil $sqEstadoCivil)
    {
        $this->sqEstadoCivil = $sqEstadoCivil;
        return $this;
    }

    public function getSqEstadoCivil()
    {
        return $this->sqEstadoCivil;
    }


    public function getSqPessoaArtefato()
    {
        return $this->sqPessoaArtefato;
    }


    public function setSqPessoaArtefato($sqPessoaArtefato)
    {
        $this->sqPessoaArtefato = $sqPessoaArtefato;
    }

    public function getSqPessoaUnidadeOrg()
    {
        return $this->sqPessoaUnidadeOrg;
    }


    public function setSqPessoaUnidadeOrg($sqPessoaUnidadeOrg)
    {
        $this->sqPessoaUnidadeOrg = $sqPessoaUnidadeOrg;
    }

    public function setSqAnexoComprovanteDocumento(AnexoComprovanteDocumento $sqAnexoComprovanteDocumento)
    {
        $this->sqAnexoComprovanteDocumento = $sqAnexoComprovanteDocumento;
    }

    public function getSqAnexoComprovanteDocumento()
    {
        return $this->sqAnexoComprovanteDocumento ? : new AnexoComprovanteDocumento();
    }
}