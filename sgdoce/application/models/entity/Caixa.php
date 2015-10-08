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
 * Sgdoce\Model\Entity\Caixa
 *
 * @ORM\Table(name="caixa")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\Caixa")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class Caixa extends \Core_Model_Entity_Abstract {

    /**
     * @var integer $sqCaixa
     *
     * @ORM\Id
     * @ORM\Column(name="sq_caixa", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqCaixa;

    /**
     * @var Sgdoce\Model\Entity\Classificacao
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\Classificacao")
     * @ORM\JoinColumns({@ORM\JoinColumn(name="sq_classificacao", referencedColumnName="sq_classificacao")})
     */
    private $sqClassificacao;

    /**
     * @var Sgdoce\Model\Entity\VwUnidadeOrg
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwUnidadeOrg")
     * @ORM\JoinColumns({@ORM\JoinColumn(name="sq_unidade_org", referencedColumnName="sq_pessoa")})
     */
    private $sqUnidadeOrg;

    /**
     * @var Sgdoce\Model\Entity\VwUnidadeOrg
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwUnidadeOrg")
     * @ORM\JoinColumns({@ORM\JoinColumn(name="sq_unidade_usuario", referencedColumnName="sq_pessoa")})
     */
    private $sqUnidadeUsuario;

    /**
     * @var Sgdoce\Model\Entity\VwPessoa
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwPessoa")
     * @ORM\JoinColumns({@ORM\JoinColumn(name="sq_pessoa_cadastro", referencedColumnName="sq_pessoa")})
     */
    private $sqPessoaCadastro;

    /**
     * @var string $nuCaixa
     *
     * @ORM\Column(name="nu_caixa", type="string", length=7, nullable=false)
     */
    private $nuCaixa;

    /**
     * @var \Zend_Date $dtCadastro
     *
     * @ORM\Column(name="dt_cadastro", type="zenddate", nullable=false)
     */
    private $dtCadastro;

    /**
     * @var integer $nuAno
     *
     * @ORM\Column(name="nu_ano", type="integer", nullable=false)
     */
    private $nuAno;

    /**
     * @var boolean $stAtivo
     *
     * @ORM\Column(name="st_ativo", type="boolean", nullable=false)
     */
    private $stAtivo;

    /**
     * @var boolean $stAtivo
     *
     * @ORM\Column(name="st_fechamento", type="boolean", nullable=false)
     */
    private $stFechamento;



    /**
     * @var Sgdoce\Model\Entity\CaixaArtefato
     *
     * @ORM\OneToMany(targetEntity="Sgdoce\Model\Entity\CaixaArtefato", mappedBy="sqCaixa")
     * @ORM\JoinColumns({@ORM\JoinColumn(name="sq_caixa", referencedColumnName="sq_caixa")})
     */
    private $sqCaixaArtefato;


    /**
     *
     * @return integer
     */
    public function getSqCaixa ()
    {
        return $this->sqCaixa;
    }

    /**
     *
     * @return Sgdoce\Model\Entity\Classificacao
     */
    public function getSqClassificacao ()
    {
        return $this->sqClassificacao;
    }

    /**
     *
     * @return VwUnidadeOrg
     */
    public function getSqUnidadeOrg ()
    {
        return $this->sqUnidadeOrg;
    }

    /**
     *
     * @return VwUnidadeOrg
     */
    public function getSqUnidadeUsuario ()
    {
        return $this->sqUnidadeUsuario;
    }

    /**
     *
     * @return VwPessoa
     */
    public function getSqPessoaCadastro ()
    {
        return $this->sqPessoaCadastro;
    }

    /**
     *
     * @return string
     */
    public function getNuCaixa ()
    {
        return $this->nuCaixa;
    }

    /**
     *
     * @return \Zend_Date
     */
    public function getDtCadastro ()
    {
        return $this->dtCadastro;
    }

    /**
     *
     * @return integer
     */
    public function getNuAno ()
    {
        return $this->nuAno;
    }

    /**
     *
     * @return boolean
     */
    public function getStAtivo ()
    {
        return $this->stAtivo;
    }

    /**
     *
     * @return boolean
     */
    public function getStFechamento ()
    {
        return $this->stFechamento;
    }

    public function setSqCaixa ($sqCaixa)
    {
        $this->sqCaixa = $sqCaixa;
        return $this;
    }

    public function setSqClassificacao (\Sgdoce\Model\Entity\Classificacao $sqClassificacao)
    {
        $this->sqClassificacao = $sqClassificacao;
        return $this;
    }

    public function setSqUnidadeOrg (\Sgdoce\Model\Entity\VwUnidadeOrg $sqUnidadeOrg)
    {
        $this->sqUnidadeOrg = $sqUnidadeOrg;
        return $this;
    }

    public function setSqUnidadeUsuario (\Sgdoce\Model\Entity\VwUnidadeOrg $sqUnidadeUsuario)
    {
        $this->sqUnidadeUsuario = $sqUnidadeUsuario;
        return $this;
    }

    public function setSqPessoaCadastro (\Sgdoce\Model\Entity\VwPessoa $sqPessoaCadastro)
    {
        $this->sqPessoaCadastro = $sqPessoaCadastro;
        return $this;
    }

    public function setNuCaixa ($nuCaixa)
    {
        $this->nuCaixa = $nuCaixa;
        return $this;
    }

    public function setDtCadastro (\Zend_Date $dtCadastro)
    {
        $this->dtCadastro = $dtCadastro;
        return $this;
    }

    public function setNuAno ($nuAno)
    {
        $this->nuAno = $nuAno;
        return $this;
    }

    public function setStAtivo ($stAtivo)
    {
        $this->stAtivo = $stAtivo;
        return $this;
    }

    public function setStFechamento ($stFechamento)
    {
        $this->stFechamento = $stFechamento;
        return $this;
    }



    public function getSqCaixaArtefato ()
    {
        return $this->sqCaixaArtefato;
    }

    public function setSqCaixaArtefato (\Sgdoce\Model\Entity\CaixaArtefato $sqCaixaArtefato)
    {
        $this->sqCaixaArtefato = $sqCaixaArtefato;
        return $this;
    }




}